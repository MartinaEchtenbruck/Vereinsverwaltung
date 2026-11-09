<?php
if (!defined('APP_ENTRY_POINT')) { http_response_code(404); exit; }
$appRoot = dirname(__DIR__, 4);
if (session_status() === PHP_SESSION_NONE) session_start();
require_once $appRoot . '/auth.php';
checkPermission('manage_members');
require_once $appRoot . '/db_connection.php';
require_once $appRoot . '/app/Core/MemberEncryption.php';
require_once $appRoot . '/app/Core/Mail.php';
require_once $appRoot . '/app/Modules/Mitgliederverwaltung/Includes/mail_placeholders.php';

function mc_json(array $data, int $status=200): void { http_response_code($status); header('Content-Type: application/json; charset=utf-8'); echo json_encode($data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); exit; }
function mc_h($v): string { return htmlspecialchars((string)$v, ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8'); }
if (empty($_SESSION['member_communication_csrf'])) $_SESSION['member_communication_csrf'] = bin2hex(random_bytes(32));
$csrf = $_SESSION['member_communication_csrf'];
try {
    $db=(new Database())->getConnection(); $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    $crypto=new MemberEncryption($appRoot);
    // AJAX-Aktionen: Batch anlegen und einzelne Empfänger kontrolliert versenden.
    if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['ajax_action'])) {
        if (!hash_equals($csrf,(string)($_POST['csrf_token']??''))) mc_json(['success'=>false,'error'=>'Sitzung abgelaufen. Bitte Seite neu laden.'],403);
        $action=(string)$_POST['ajax_action'];
        if ($action==='start_batch') {
            $subject=trim((string)($_POST['subject']??'')); $body=sanitizeMemberMailHtml((string)($_POST['body']??''));
            $ids=array_values(array_unique(array_filter(array_map('intval', (array)($_POST['member_ids']??[])),fn($v)=>$v>0)));
            if ($subject==='' || $body==='' || !$ids) mc_json(['success'=>false,'error'=>'Betreff, Nachricht und mindestens ein Empfänger sind erforderlich.'],422);
            if (mb_strlen($subject)>255) mc_json(['success'=>false,'error'=>'Der Betreff darf maximal 255 Zeichen lang sein.'],422);
            $unknown=findUnknownMailPlaceholders($subject,$body); if ($unknown) mc_json(['success'=>false,'error'=>'Unbekannte Platzhalter: '.implode(', ',array_map(fn($x)=>'{{'.$x.'}}',$unknown))],422);
            $in=implode(',',array_fill(0,count($ids),'?'));
            $stmt=$db->prepare("SELECT id,member_number,first_name,last_name,email_enc,start_date,end_date,monthly_fee,status FROM members WHERE id IN ($in) ORDER BY last_name,first_name"); $stmt->execute($ids); $rows=$stmt->fetchAll(PDO::FETCH_ASSOC);
            if (count($rows)!==count($ids)) mc_json(['success'=>false,'error'=>'Mindestens ein ausgewähltes Mitglied wurde nicht gefunden. Bitte Empfängerauswahl aktualisieren.'],422);
            $prepared=[];
            foreach($rows as $r){
                $email=$crypto->decrypt($r['email_enc']);
                if (!$email || !filter_var($email,FILTER_VALIDATE_EMAIL)) mc_json(['success'=>false,'error'=>'Mitglied '.$r['member_number'].' ('.$r['first_name'].' '.$r['last_name'].') hat keine gültige E-Mail-Adresse. Bitte Auswahl korrigieren.'],422);
                $prepared[]=$r;
            }
            $db->beginTransaction();
            $stmt=$db->prepare('INSERT INTO member_mail_batches (subject_enc,body_enc,recipient_count) VALUES (?,?,?)');
            $stmt->execute([$crypto->encrypt($subject),$crypto->encrypt($body),count($prepared)]); $batchId=(int)$db->lastInsertId();
            $ins=$db->prepare("INSERT INTO member_mail_recipients (batch_id,member_id,member_number,recipient_name_enc,recipient_email_enc,send_status) VALUES (?,?,?,?,?,'pending')");
            foreach($prepared as $r){$ins->execute([$batchId,$r['id'],$r['member_number'],$crypto->encrypt(trim($r['first_name'].' '.$r['last_name'])),$crypto->encrypt($crypto->decrypt($r['email_enc']))]);}
            $db->commit();
            mc_json(['success'=>true,'batch_id'=>$batchId,'count'=>count($prepared)]);
        }
        if ($action==='send_one') {
            $batchId=(int)($_POST['batch_id']??0); $memberId=(int)($_POST['member_id']??0);
            $stmt=$db->prepare("SELECT r.id AS recipient_id,r.member_number,r.recipient_email_enc,r.send_status,b.subject_enc,b.body_enc,m.first_name,m.last_name,m.start_date,m.end_date,m.monthly_fee FROM member_mail_recipients r JOIN member_mail_batches b ON b.id=r.batch_id LEFT JOIN members m ON m.id=r.member_id WHERE r.batch_id=? AND r.member_id=? LIMIT 1");
            $stmt->execute([$batchId,$memberId]); $r=$stmt->fetch(PDO::FETCH_ASSOC);
            if(!$r) mc_json(['success'=>false,'error'=>'Empfänger oder Versandauftrag nicht gefunden.'],404);
            if($r['send_status']!=='pending') mc_json(['success'=>false,'error'=>'Dieser Empfänger wurde bereits bearbeitet.'],409);
            $email=$crypto->decrypt($r['recipient_email_enc']);
            $member=['first_name'=>$r['first_name']??'','last_name'=>$r['last_name']??'','email'=>$email,'start_date'=>$r['start_date']??null,'end_date'=>$r['end_date']??null,'monthly_fee'=>$r['monthly_fee']??null];
            $subject=renderMemberMailText($crypto->decrypt($r['subject_enc']),$member);
            $body=renderMemberMailHtml($crypto->decrypt($r['body_enc']),$member);
            // Gemeinsames Stundenlimit wie beim bestehenden Bulk-Mail-Modul.
            $limitFile=$appRoot.'/bulk_mail_timestamps.log'; $now=time(); $recent=[];
            if(is_file($limitFile)){foreach(file($limitFile,FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES) as $line){$ts=(int)trim($line);if($ts>$now-3600)$recent[]=$ts;}}
            if(count($recent)>=240) mc_json(['success'=>false,'rate_limit'=>true,'error'=>'Stundenlimit erreicht (maximal 240 E-Mails innerhalb von 60 Minuten). Bitte später fortsetzen.'],429);
            $ok=false; $errorText='';
            try { $ok=sendEmailWithAttachments($email,$subject,$body,[],true); if(!$ok)$errorText='Der Mailserver hat den Versand nicht angenommen.'; }
            catch(Throwable $e){$errorText='Technischer Fehler beim Versand.';error_log('Mitglieder-Mailversand: '.$e->getMessage());}
            $status=$ok?'sent':'failed';
            $upd=$db->prepare('UPDATE member_mail_recipients SET send_status=?,sent_at=IF(?,NOW(),NULL),error_message=? WHERE id=?');
            $upd->execute([$status,$ok?1:0,$ok?null:$errorText,$r['recipient_id']]);
            $db->prepare("UPDATE member_mail_batches SET sent_count=sent_count+?,failed_count=failed_count+? WHERE id=?")->execute([$ok?1:0,$ok?0:1,$batchId]);
            if($ok) file_put_contents($limitFile,$now."\n",FILE_APPEND|LOCK_EX);
            mc_json(['success'=>$ok,'status'=>$status,'member_id'=>$memberId,'message'=>$ok?'Versand an '.$email.' angestoßen.':$errorText]);
        }
        mc_json(['success'=>false,'error'=>'Unbekannte Aktion.'],400);
    }

    $templates=$db->query('SELECT id,template_name,betreff,mailtext,recipient_rule FROM email_templates ORDER BY template_name')->fetchAll(PDO::FETCH_ASSOC);
    $members=$db->query("SELECT id,member_number,first_name,last_name,start_date,end_date,monthly_fee,status,email_enc FROM members ORDER BY last_name,first_name")->fetchAll(PDO::FETCH_ASSOC);
    $eligible=[];
    foreach($members as $m){
        $m['email']=$crypto->decrypt($m['email_enc']); unset($m['email_enc']);
        $m['has_email']=($m['email'] && filter_var($m['email'],FILTER_VALIDATE_EMAIL));
        $eligible[]=$m;
    }
} catch(Throwable $e){
    if (isset($db) && $db instanceof PDO && $db->inTransaction()) {
        $db->rollBack();
    }
    if(isset($_POST['ajax_action'])) {
        error_log('Mitgliederkommunikation AJAX ['.(string)($_POST['ajax_action']??'unbekannt').']: '.$e->getMessage());
        mc_json(['success'=>false,'error'=>'Die Aktion konnte nicht ausgeführt werden. Bitte Server-Fehlerprotokoll prüfen.'],500);
    }
    error_log('Mitgliederkommunikation: '.$e->getMessage()); $pageError='Die Mitgliederkommunikation konnte nicht geladen werden. Bitte Datenbankmigration und Verschlüsselungskonfiguration prüfen.'; $templates=[];$eligible=[];
}
$rules=[
'all_active'=>fn($m)=>($m['status']??'Aktiv')==='Aktiv',
'active_no_end'=>fn($m)=>($m['status']??'Aktiv')==='Aktiv' && empty($m['end_date']),
'active_end_next_year'=>fn($m)=>($m['status']??'Aktiv')==='Aktiv' && !empty($m['end_date']) && (int)substr($m['end_date'],0,4)>(int)date('Y'),
'active_end_this_year'=>fn($m)=>($m['status']??'Aktiv')==='Aktiv' && !empty($m['end_date']) && (int)substr($m['end_date'],0,4)===(int)date('Y'),
'active_or_terminated'=>fn($m)=>in_array(($m['status']??'Aktiv'),['Aktiv','Gekündigt'],true),
];
$membersForJs=[]; foreach($eligible as $m){$membersForJs[]=['id'=>(int)$m['id'],'number'=>(int)$m['member_number'],'first_name'=>$m['first_name'],'last_name'=>$m['last_name'],'email'=>$m['email'],'has_email'=>(bool)$m['has_email'],'status'=>$m['status']??'Aktiv','start_date'=>$m['start_date'],'end_date'=>$m['end_date'],'monthly_fee'=>$m['monthly_fee']];}
$placeholders=getAvailableMailPlaceholders();
?>
<!doctype html><html lang="de"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Mitgliederkommunikation – Förderverein intern</title><link rel="stylesheet" href="styles.css"><link rel="stylesheet" href="includes/navigation.css"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
.mc-wrap{max-width:1450px;margin:24px auto}.mc-card{background:#fff;border-radius:12px;padding:20px;margin:18px 0;box-shadow:0 2px 12px #0000000d}.mc-head{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap}.mc-grid{display:grid;grid-template-columns:1fr;gap:16px}.mc-editor-controls{display:flex;align-items:flex-start;justify-content:space-between;gap:18px;flex-wrap:wrap}.mc-editor-controls .mc-toolbar-format{flex:0 0 auto}.mc-editor-controls .mc-placeholder-block{flex:1 1 420px;min-width:260px}.mc-editor-controls .mc-placeholder-block .mc-field{gap:8px}.mc-field select,.mc-field input{max-width:100%}.mc-field{display:flex;flex-direction:column;gap:6px;font-weight:600}.mc-field input,.mc-field select,.mc-field textarea{width:100%;box-sizing:border-box;padding:10px;border:1px solid var(--border-color,#ddd);border-radius:7px;font:inherit;background:white}.mc-field textarea{min-height:240px;line-height:1.5;resize:vertical}.mc-editor{min-height:240px;max-height:600px;overflow:auto;padding:10px;border:1px solid var(--border-color,#ddd);border-radius:7px;background:#fff;font-weight:400;line-height:1.5}.mc-editor:focus{outline:2px solid color-mix(in srgb,var(--primary-color,#3498db) 35%,transparent);border-color:var(--primary-color,#3498db)}.mc-editor p{margin:.4em 0}.mc-toolbar-format{display:flex;gap:6px;flex-wrap:wrap;margin:6px 0}.mc-toolbar-format button{min-width:34px;padding:6px 9px;border:1px solid #cbd5e1;border-radius:6px;background:#f8fafc;cursor:pointer}.mc-btn{border:0;border-radius:7px;padding:9px 13px;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:7px;background:#e9edf2;color:#202938;font:inherit}.mc-btn.primary{background:var(--primary-color,#3498db);color:white}.mc-btn:disabled{opacity:.5;cursor:not-allowed}.mc-toolbar{display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin:12px 0}.mc-table-wrap{overflow:auto;max-height:520px;border:1px solid #e6e9ed;border-radius:8px}.mc-table{width:100%;border-collapse:collapse;white-space:nowrap}.mc-table th,.mc-table td{padding:10px 12px;border-bottom:1px solid #edf0f2;text-align:left}.mc-table th{position:sticky;top:0;background:#f7f9fb;z-index:1}.mc-table button.sort{border:0;background:none;font:inherit;font-weight:700;cursor:pointer}.mc-muted{color:#667085;font-size:.9rem}.mc-tag{display:inline-block;padding:3px 8px;border-radius:20px;font-size:.8rem;background:#e8f5e9;color:#246b35}.mc-tag.cancel{background:#fff3cd;color:#795b00}.mc-tag.inactive{background:#edf0f2;color:#59636e}.mc-alert{padding:12px 14px;border-radius:8px;margin:12px 0;background:#e8f5e9}.mc-alert.error{background:#fdeaea;color:#8b1e1e}.mc-preview{border:1px solid #e5e7eb;background:#fafafa;border-radius:8px;padding:14px;white-space:pre-wrap;max-height:260px;overflow:auto}.mc-progress{height:9px;background:#e9edf2;border-radius:20px;overflow:hidden}.mc-progress>span{display:block;height:100%;width:0;background:var(--primary-color,#3498db);transition:width .2s}.mc-placeholder-list{display:flex;gap:6px;flex-wrap:wrap}.mc-token{border:1px solid #cbd5e1;background:#f8fafc;border-radius:6px;padding:5px 8px;cursor:pointer;font: .82rem ui-monospace,monospace}@media(max-width:850px){.mc-card{padding:14px}.mc-editor-controls{display:block}.mc-editor-controls .mc-placeholder-block{min-width:0;margin-top:10px}}
</style></head><body><div class="container"><header class="app-page-header"><h1>Mitgliederkommunikation</h1><?php require $appRoot.'/includes/navigation.php'; ?></header><main class="mc-wrap">
<?php if(!empty($pageError)):?><div class="mc-alert error"><?=mc_h($pageError)?></div><?php else:?>
<div class="mc-card"><div class="mc-head"><h2 style="margin:0">E-Mail erstellen</h2><a class="mc-btn" href="mitglieder_versandhistorie.php"><i class="fa-solid fa-clock-rotate-left"></i> Versandhistorie</a></div>
<div class="mc-grid" style="margin-top:16px">
<label class="mc-field">Vorlage auswählen<select id="templateSelect"><option value="">Ohne Vorlage – alle aktiven Mitglieder vorauswählen</option><?php foreach($templates as $t):?><option value="<?= (int)$t['id']?>"><?=mc_h($t['template_name'])?></option><?php endforeach;?></select><span class="mc-muted">Die Vorlage legt die Vorauswahl fest. Empfänger können anschließend geändert werden.</span></label>
<label class="mc-field">Betreff<input id="subject" maxlength="255" required></label>
<div class="mc-field"><span>Nachricht</span><div class="mc-editor-controls"><div class="mc-toolbar-format" role="toolbar" aria-label="Textformatierung"><button type="button" data-command="bold" title="Fett"><strong>B</strong></button><button type="button" data-command="italic" title="Kursiv"><em>I</em></button><button type="button" data-command="underline" title="Unterstrichen"><u>U</u></button><button type="button" data-command="insertUnorderedList" title="Aufzählung">• Liste</button><button type="button" data-command="insertOrderedList" title="Nummerierte Liste">1. Liste</button></div><div class="mc-placeholder-block"><div class="mc-field"><span>Platzhalter einfügen</span><div class="mc-placeholder-list"><?php foreach($placeholders as $key=>$meta):?><button type="button" class="mc-token" data-token="{{<?=mc_h($key)?>}}" title="<?=mc_h($meta['description'])?>">{{<?=mc_h($key)?>}}</button><?php endforeach;?></div></div></div></div><div id="body" class="mc-editor" contenteditable="true" role="textbox" aria-multiline="true"></div></div>
</div>
<div class="mc-card"><div class="mc-head"><h2 style="margin:0">Empfänger auswählen</h2><strong id="selectedCount">0 ausgewählt</strong></div><div class="mc-toolbar"><input id="memberSearch" type="search" placeholder="Mitglied suchen …" style="padding:9px;border:1px solid #ddd;border-radius:7px;min-width:220px"><button class="mc-btn" type="button" id="selectAll">Alle auswählen</button><button class="mc-btn" type="button" id="selectNone">Auswahl aufheben</button><span class="mc-muted">Mitglieder ohne gültige E-Mail-Adresse sind nicht auswählbar.</span></div>
<div class="mc-table-wrap"><table class="mc-table"><thead><tr><th><input type="checkbox" id="headerCheck" aria-label="Alle sichtbaren auswählen"></th><th><button class="sort" data-sort="number">Mitglieds-Nr. ↕</button></th><th><button class="sort" data-sort="last_name">Nachname ↕</button></th><th><button class="sort" data-sort="first_name">Vorname ↕</button></th><th>Status</th><th>E-Mail</th><th>Mitglied bis</th></tr></thead><tbody id="recipientRows"></tbody></table></div></div>
<div class="mc-card"><h2 style="margin-top:0">Vorschau und Versand</h2><p class="mc-muted">Die Vorschau zeigt die Nachricht für den ersten ausgewählten Empfänger. Personalisierte Platzhalter werden beim Versand für jedes Mitglied einzeln aufgelöst.</p><div class="mc-preview" id="preview">Bitte Betreff und Nachricht eingeben und Empfänger auswählen.</div><div class="mc-toolbar"><button class="mc-btn" type="button" id="refreshPreview">Vorschau aktualisieren</button><button class="mc-btn primary" type="button" id="sendButton"><i class="fa-solid fa-paper-plane"></i> Versand starten</button></div><div id="sendStatus" class="mc-muted" aria-live="polite"></div><div class="mc-progress" style="margin-top:10px"><span id="progressBar"></span></div><div id="sendLog" class="mc-muted" style="margin-top:10px"></div></div>
<?php endif;?></main></div>
<script>
const MEMBERS=<?=json_encode($membersForJs,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)?>;
const TEMPLATES=<?=json_encode(array_map(fn($t)=>['id'=>(int)$t['id'],'subject'=>$t['betreff'],'body'=>$t['mailtext'],'rule'=>$t['recipient_rule']],$templates),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)?>;
const CSRF=<?=json_encode($csrf)?>;
const RULES={all_active:m=>m.status==='Aktiv',active_no_end:m=>m.status==='Aktiv'&&!m.end_date,active_end_next_year:m=>m.status==='Aktiv'&&m.end_date&&Number(m.end_date.slice(0,4))>new Date().getFullYear(),active_end_this_year:m=>m.status==='Aktiv'&&m.end_date&&Number(m.end_date.slice(0,4))===new Date().getFullYear(),active_or_terminated:m=>['Aktiv','Gekündigt'].includes(m.status),current_member:m=>false,new_member:m=>false};
let selected=new Set(),sortKey='last_name',sortAsc=true,activeField=document.getElementById('body');
const $=id=>document.getElementById(id); const esc=s=>String(s??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
function visibleMembers(){const q=$('memberSearch').value.trim().toLocaleLowerCase('de');return MEMBERS.filter(m=>(!q||(`${m.number} ${m.first_name} ${m.last_name} ${m.email}`).toLocaleLowerCase('de').includes(q))).sort((a,b)=>{let x=a[sortKey]??'',y=b[sortKey]??'';let c=typeof x==='number'?x-y:String(x).localeCompare(String(y),'de');return sortAsc?c:-c;});}
function render(){const list=visibleMembers();$('recipientRows').innerHTML=list.map(m=>`<tr><td><input type="checkbox" class="recipient-check" data-id="${m.id}" ${selected.has(m.id)?'checked':''} ${m.has_email?'':'disabled'}></td><td>${m.number}</td><td>${esc(m.last_name)}</td><td>${esc(m.first_name)}</td><td><span class="mc-tag ${m.status==='Gekündigt'?'cancel':m.status==='Inaktiv'?'inactive':''}">${esc(m.status)}</span></td><td>${esc(m.email||'Keine gültige Adresse')}</td><td>${m.end_date?new Date(m.end_date+'T00:00:00').toLocaleDateString('de-DE'):'–'}</td></tr>`).join('');document.querySelectorAll('.recipient-check').forEach(c=>c.addEventListener('change',()=>{const id=Number(c.dataset.id);c.checked?selected.add(id):selected.delete(id);updateCount();}));$('selectedCount').textContent=selected.size+' ausgewählt';$('headerCheck').checked=list.filter(m=>m.has_email).length>0&&list.filter(m=>m.has_email).every(m=>selected.has(m.id));$('headerCheck').indeterminate=list.some(m=>m.has_email&&selected.has(m.id))&&!$('headerCheck').checked;}
function updateCount(){$('selectedCount').textContent=selected.size+' ausgewählt';}
function fillTemplate(){const id=Number($('templateSelect').value);const t=TEMPLATES.find(x=>x.id===id);if(t){$('subject').value=t.subject;$('body').innerHTML=editorHtml(t.body);selected=new Set(MEMBERS.filter(m=>m.has_email&&(RULES[t.rule]?.(m)??false)).map(m=>m.id));}else{ $('subject').value='';$('body').innerHTML='';selected=new Set(MEMBERS.filter(m=>m.has_email&&m.status==='Aktiv').map(m=>m.id));}render();preview();}
function renderText(text,m){return text.replace(/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/g,(_,k)=>({vorname:m.first_name,nachname:m.last_name,email:m.email,mitglied_seit:dateFmt(m.start_date),mitglied_bis:dateFmt(m.end_date),beitrag:feeFmt(m.monthly_fee)})[k]??`{{${k}}}`);}
function dateFmt(d){return d?new Date(d+'T00:00:00').toLocaleDateString('de-DE'):'';}
function feeFmt(v){return v===null||v===undefined||v===''?'':Number(v).toLocaleString('de-DE',{minimumFractionDigits:2,maximumFractionDigits:2})+' €';}
function editorHtml(value){const allowed=/(?:p|div|br|strong|b|em|i|u|ul|ol|li)/i;if(/<(?:p|div|br|strong|b|em|i|u|ul|ol|li)\b/i.test(value))return value.replace(/<(\/?)([a-z0-9]+)\b[^>]*>/gi,(m,slash,tag)=>allowed.test(tag)?'<'+slash+tag.toLowerCase()+'>':'');return '<p>'+esc(value).replace(/\r?\n/g,'<br>')+'</p>'; }
function renderHtml(text,m){return text.replace(/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/g,(_,k)=>esc(({vorname:m.first_name,nachname:m.last_name,email:m.email,mitglied_seit:dateFmt(m.start_date),mitglied_bis:dateFmt(m.end_date),beitrag:feeFmt(m.monthly_fee)})[k]??`{{${k}}}`));}
function preview(){const m=MEMBERS.find(x=>selected.has(x.id));if(!m){$('preview').textContent='Bitte mindestens einen Empfänger auswählen.';return;}const s=renderText($('subject').value,m);const b=renderHtml($('body').innerHTML,m);$('preview').innerHTML='<div><strong>An:</strong> '+esc(m.first_name+' '+m.last_name+' <'+m.email+'>')+'</div><div><strong>Betreff:</strong> '+esc(s)+'</div><hr>'+b;}
$('templateSelect').addEventListener('change',()=>{if(($('subject').value||$('body').innerText)&&!confirm('Beim Vorlagenwechsel werden Betreff und Nachricht ersetzt. Fortfahren?'))return;fillTemplate();});$('memberSearch').addEventListener('input',render);$('selectAll').addEventListener('click',()=>{visibleMembers().filter(m=>m.has_email).forEach(m=>selected.add(m.id));render();});$('selectNone').addEventListener('click',()=>{selected.clear();render();});$('headerCheck').addEventListener('change',e=>{visibleMembers().filter(m=>m.has_email).forEach(m=>e.target.checked?selected.add(m.id):selected.delete(m.id));render();});document.querySelectorAll('[data-sort]').forEach(b=>b.addEventListener('click',()=>{const k=b.dataset.sort;if(sortKey===k)sortAsc=!sortAsc;else{sortKey=k;sortAsc=true;}render();}));$('refreshPreview').addEventListener('click',preview);$('subject').addEventListener('input',preview);$('body').addEventListener('input',preview);$('subject').addEventListener('focus',()=>activeField=$('subject'));$('body').addEventListener('focus',()=>activeField=$('body'));document.querySelectorAll('.mc-toolbar-format button').forEach(button=>button.addEventListener('mousedown',e=>e.preventDefault()));document.querySelectorAll('.mc-toolbar-format button').forEach(button=>button.addEventListener('click',()=>{$('body').focus();document.execCommand(button.dataset.command,false,null);preview();}));document.querySelectorAll('.mc-token').forEach(b=>b.addEventListener('mousedown',e=>e.preventDefault()));document.querySelectorAll('.mc-token').forEach(b=>b.addEventListener('click',()=>{if(activeField===$('body')){const sel=window.getSelection();if(sel&&sel.rangeCount&&$('body').contains(sel.anchorNode)){const range=sel.getRangeAt(0);range.deleteContents();const node=document.createTextNode(b.dataset.token);range.insertNode(node);range.setStartAfter(node);range.setEndAfter(node);sel.removeAllRanges();sel.addRange(range);}else $('body').append(document.createTextNode(b.dataset.token));$('body').focus();}else{const f=$('subject'),start=f.selectionStart??f.value.length,end=f.selectionEnd??f.value.length;f.value=f.value.slice(0,start)+b.dataset.token+f.value.slice(end);f.focus();f.setSelectionRange(start+b.dataset.token.length,start+b.dataset.token.length);}preview();}));
async function post(data){const fd=new FormData();Object.entries(data).forEach(([k,v])=>Array.isArray(v)?v.forEach(x=>fd.append(k+'[]',x)):fd.append(k,v));fd.append('csrf_token',CSRF);const r=await fetch(location.href,{method:'POST',body:fd,credentials:'same-origin'});return await r.json();}
$('sendButton').addEventListener('click',async()=>{const ids=[...selected];if(!ids.length){alert('Bitte Empfänger auswählen.');return;}if(!$('subject').value.trim()||!$('body').innerText.trim()){alert('Bitte Betreff und Nachricht ausfüllen.');return;}if(!confirm(`Es werden ${ids.length} E-Mails versendet. Der Versand kann nicht rückgängig gemacht werden. Möchtest du fortfahren?`))return;const btn=$('sendButton');btn.disabled=true;$('sendStatus').textContent='Versand wird vorbereitet …';$('sendLog').textContent='';$('progressBar').style.width='0%';try{const start=await post({ajax_action:'start_batch',subject:$('subject').value,body:$('body').innerHTML,member_ids:ids});if(!start.success)throw new Error(start.error||'Versand konnte nicht vorbereitet werden.');let sent=0,failed=0;for(let i=0;i<ids.length;i++){const result=await post({ajax_action:'send_one',batch_id:start.batch_id,member_id:ids[i]});if(result.rate_limit){$('sendStatus').textContent=result.error+' Noch nicht versendete Empfänger bleiben ausstehend.';break;}if(result.success)sent++;else failed++;$('progressBar').style.width=Math.round((i+1)/ids.length*100)+'%';$('sendStatus').textContent=`Bearbeitet: ${i+1} von ${ids.length} · Erfolgreich: ${sent} · Fehlgeschlagen: ${failed}`;$('sendLog').textContent+=(result.success?'✓ ':'✕ ')+(result.message||result.error||'Unbekannter Fehler')+'\n';if(i<ids.length-1)await new Promise(resolve=>setTimeout(resolve,16000));} $('sendStatus').textContent=`Versand abgeschlossen: ${sent} erfolgreich, ${failed} fehlgeschlagen. Details in der Versandhistorie.`;}catch(e){$('sendStatus').textContent=e.message;}finally{btn.disabled=false;}});
if($('templateSelect'))fillTemplate();
</script></body></html>
