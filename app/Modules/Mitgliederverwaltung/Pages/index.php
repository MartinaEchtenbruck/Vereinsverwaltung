<?php
if (!defined('APP_ENTRY_POINT')) { http_response_code(404); exit; }
$appRoot = dirname(__DIR__, 4);
if (session_status() === PHP_SESSION_NONE) session_start();
require_once $appRoot . '/auth.php';
checkPermission('manage_members');
require_once $appRoot . '/db_connection.php';
require_once $appRoot . '/app/Core/MemberEncryption.php';

if (empty($_SESSION['members_csrf'])) $_SESSION['members_csrf'] = bin2hex(random_bytes(32));
$csrf = $_SESSION['members_csrf'];
$error = '';
$notice = trim((string)($_GET['meldung'] ?? ''));
$editing = null;
$openId = (int)($_GET['open'] ?? 0);
function mh($v): string { return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function memberEndDate(string $start, ?int $duration): ?string {
    if (!$duration || $duration < 1) return null;
    $d = DateTimeImmutable::createFromFormat('!Y-m-d', $start);
    if (!$d) throw new RuntimeException('Bitte ein gültiges Eintrittsdatum angeben.');
    $kgStartYear = (int)$d->format('Y') - ((int)$d->format('n') < 8 ? 1 : 0);
    return sprintf('%04d-07-31', $kgStartYear + $duration);
}
function memberFormValues(array $src): array {
    $keys = ['member_number','last_name','first_name','street','postal_code','city','phone','mobile','email','monthly_fee','account_holder','bank_name','iban','bic','duration_years','start_date','end_date','mandate_reference','comment'];
    $out=[]; foreach($keys as $k) $out[$k]=trim((string)($src[$k]??'')); return $out;
}
try {
    $database = new Database(); $conn = $database->getConnection();
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $crypto = new MemberEncryption($appRoot);
    $conn->exec("INSERT INTO member_number_sequence (id,last_issued_number) VALUES (1,0) ON DUPLICATE KEY UPDATE id=VALUES(id)");
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!hash_equals($csrf, (string)($_POST['csrf_token'] ?? ''))) throw new RuntimeException('Sitzung abgelaufen. Bitte lade die Seite neu.');
        $action = (string)($_POST['action'] ?? '');
        if ($action === 'save') {
            $v = memberFormValues($_POST); $id = (int)($_POST['member_id'] ?? 0);
            $number = filter_var($v['member_number'], FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
            $fee = str_replace(',', '.', $v['monthly_fee']);
            if (!$number) throw new RuntimeException('Bitte eine positive Mitgliedsnummer angeben.');
            if ($v['last_name']==='' || $v['first_name']==='' || $v['street']==='' || $v['postal_code']==='' || $v['city']==='' || $v['account_holder']==='' || $v['bank_name']==='' || $v['iban']==='' || $v['bic']==='' || $v['start_date']==='') throw new RuntimeException('Bitte alle Pflichtfelder ausfüllen.');
            if (!is_numeric($fee) || (float)$fee <= 0) throw new RuntimeException('Der monatliche Beitrag muss größer als 0 sein.');
            $start = DateTimeImmutable::createFromFormat('!Y-m-d', $v['start_date']);
            if (!$start || $start->format('Y-m-d') !== $v['start_date']) throw new RuntimeException('Bitte ein gültiges Eintrittsdatum angeben.');
            $duration = $v['duration_years']==='' ? null : filter_var($v['duration_years'], FILTER_VALIDATE_INT, ['options'=>['min_range'=>1,'max_range'=>100]]);
            if ($v['duration_years']!=='' && !$duration) throw new RuntimeException('Die Dauer muss eine positive ganze Jahreszahl sein.');
            $end = $v['end_date'] !== '' ? $v['end_date'] : memberEndDate($v['start_date'], $duration);
            if ($end !== null && $end !== '') {
                $endDate = DateTimeImmutable::createFromFormat('!Y-m-d', $end);
                if (!$endDate || $endDate->format('Y-m-d') !== $end || $endDate->format('m-d') !== '07-31') throw new RuntimeException('Das Enddatum muss auf den 31.07. fallen.');
            }
            $iban = strtoupper(preg_replace('/\s+/', '', $v['iban']));
            if (!preg_match('/^[A-Z]{2}[0-9]{20}$/', $iban)) throw new RuntimeException('Bitte eine IBAN mit zwei Länderbuchstaben und genau 20 Ziffern eingeben.');
            $v['iban'] = implode(' ', [substr($iban,0,4),substr($iban,4,4),substr($iban,8,4),substr($iban,12,4),substr($iban,16,4),substr($iban,20,2)]);
            $dup=$conn->prepare('SELECT id FROM members WHERE member_number=? AND id<>?'); $dup->execute([$number,$id]);
            if ($dup->fetchColumn()) throw new RuntimeException('Diese Mitgliedsnummer ist bereits vergeben.');
            $enc = fn($key) => $crypto->encrypt($v[$key] === '' ? null : $v[$key]);
            $data = [$number,$v['last_name'],$v['first_name'],$enc('street'),$enc('postal_code'),$enc('city'),$enc('phone'),$enc('mobile'),$enc('email'),number_format((float)$fee,2,'.',''),$enc('account_holder'),$enc('bank_name'),$enc('iban'),$enc('bic'),$duration,$v['start_date'],$end,$enc('mandate_reference'),$enc('comment')];
            $conn->beginTransaction();
            if ($id > 0) {
                $check=$conn->prepare('SELECT id FROM members WHERE id=?');$check->execute([$id]);if(!$check->fetchColumn()) throw new RuntimeException('Mitglied nicht gefunden.');
                $sql='UPDATE members SET member_number=?,last_name=?,first_name=?,street_enc=?,postal_code_enc=?,city_enc=?,phone_enc=?,mobile_enc=?,email_enc=?,monthly_fee=?,account_holder_enc=?,bank_name_enc=?,iban_enc=?,bic_enc=?,duration_years=?,start_date=?,end_date=?,mandate_reference_enc=?,comment_enc=? WHERE id=?';
                $data[]=$id; $conn->prepare($sql)->execute($data);
            } else {
                $sql='INSERT INTO members (member_number,last_name,first_name,street_enc,postal_code_enc,city_enc,phone_enc,mobile_enc,email_enc,monthly_fee,account_holder_enc,bank_name_enc,iban_enc,bic_enc,duration_years,start_date,end_date,mandate_reference_enc,comment_enc) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
                $conn->prepare($sql)->execute($data);
            }
            $conn->prepare('UPDATE member_number_sequence SET last_issued_number=GREATEST(last_issued_number,?) WHERE id=1')->execute([$number]);
            $conn->commit();
            header('Location: mitglieder.php?meldung='.rawurlencode($id?'Mitglied wurde gespeichert.':'Mitglied wurde angelegt.')); exit;
        }
        if ($action === 'delete') {
            $id=(int)($_POST['member_id']??0); $conn->prepare('DELETE FROM members WHERE id=?')->execute([$id]);
            header('Location: mitglieder.php?meldung='.rawurlencode('Mitglied wurde gelöscht.')); exit;
        }
        throw new RuntimeException('Unbekannte Aktion.');
    }
    $q=$conn->query('SELECT last_issued_number FROM member_number_sequence WHERE id=1'); $lastIssued=(int)$q->fetchColumn();
    $q=$conn->query('SELECT COALESCE(MAX(member_number),0) FROM members'); $lastIssued=max($lastIssued,(int)$q->fetchColumn());
    $nextNumber=$lastIssued+1;
    if (isset($_GET['edit'])) {
        $q=$conn->prepare('SELECT * FROM members WHERE id=?');$q->execute([(int)$_GET['edit']]);$r=$q->fetch(PDO::FETCH_ASSOC);if(!$r)throw new RuntimeException('Mitglied nicht gefunden.');
        $editing=['id'=>$r['id'],'member_number'=>$r['member_number'],'last_name'=>$r['last_name'],'first_name'=>$r['first_name'],'street'=>$crypto->decrypt($r['street_enc']),'postal_code'=>$crypto->decrypt($r['postal_code_enc']),'city'=>$crypto->decrypt($r['city_enc']),'phone'=>$crypto->decrypt($r['phone_enc']),'mobile'=>$crypto->decrypt($r['mobile_enc']),'email'=>$crypto->decrypt($r['email_enc']),'monthly_fee'=>$r['monthly_fee'],'account_holder'=>$crypto->decrypt($r['account_holder_enc']),'bank_name'=>$crypto->decrypt($r['bank_name_enc']),'iban'=>$crypto->decrypt($r['iban_enc']),'bic'=>$crypto->decrypt($r['bic_enc']),'duration_years'=>$r['duration_years'],'start_date'=>$r['start_date'],'end_date'=>$r['end_date'],'mandate_reference'=>$crypto->decrypt($r['mandate_reference_enc']),'comment'=>$crypto->decrypt($r['comment_enc'])];
    }
    $rows=$conn->query('SELECT * FROM members ORDER BY member_number')->fetchAll(PDO::FETCH_ASSOC);
    foreach($rows as &$r){ if((int)$r['id']===$openId){foreach(['street'=>'street_enc','postal_code'=>'postal_code_enc','city'=>'city_enc','phone'=>'phone_enc','mobile'=>'mobile_enc','email'=>'email_enc','account_holder'=>'account_holder_enc','bank_name'=>'bank_name_enc','iban'=>'iban_enc','bic'=>'bic_enc','mandate_reference'=>'mandate_reference_enc','comment'=>'comment_enc'] as $k=>$col)$r[$k]=$crypto->decrypt($r[$col]);} } unset($r);
} catch (Throwable $e) {
    if (isset($conn) && $conn instanceof PDO && $conn->inTransaction()) $conn->rollBack();
    $error = $e->getMessage(); $rows=$rows??[]; $nextNumber=$nextNumber??1;
}
$blank=['member_number'=>$nextNumber??1,'last_name'=>'','first_name'=>'','street'=>'','postal_code'=>'','city'=>'','phone'=>'','mobile'=>'','email'=>'','monthly_fee'=>'','account_holder'=>'','bank_name'=>'','iban'=>'','bic'=>'','duration_years'=>'','start_date'=>'','end_date'=>'','mandate_reference'=>'','comment'=>''];
$showNewForm = $editing !== null || (isset($_GET['neu']) && $_GET['neu'] === '1');
$form=$editing??$blank;
if (!$editing && $form['account_holder']==='') $form['account_holder']=trim($form['last_name'].', '.$form['first_name'], ', ');
?>
<!doctype html><html lang="de"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Mitgliederübersicht – Förderverein intern</title><link rel="icon" type="image/png" sizes="32x32" href="/intern/assets/icons/favicon.png"><link rel="manifest" href="/intern/manifest.webmanifest"><link rel="stylesheet" href="styles.css"><link rel="stylesheet" href="includes/navigation.css"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"><style>
.member-wrap{max-width:1250px;margin:24px auto}.member-card{background:#fff;border-radius:12px;padding:20px;margin:18px 0;box-shadow:0 2px 12px #0000000d}.member-table{width:100%;border-collapse:collapse}.member-table th,.member-table td{padding:11px 12px;border-bottom:1px solid #e8e8e8;text-align:left}.member-row{cursor:pointer}.member-row:hover{background:#f5f7fa}.member-row.is-open td{background:#eef4fa;border-bottom:0;font-weight:600}.member-row.is-open td:first-child{border-left:3px solid #244e79}.member-row.is-open td:last-child{border-right:1px solid #d7e2ed}.member-details td{background:#f8fafc;padding:16px 18px;border:1px solid #d7e2ed;border-top:0;border-radius:0 0 10px 10px}.member-detail-blocks{display:grid;grid-template-columns:repeat(auto-fit,minmax(min(100%,260px),1fr));gap:12px;align-items:start}.member-detail-block{background:#fff;border:1px solid #e2e8f0;border-radius:9px;padding:13px;min-width:0}.member-detail-block h3{font-size:.95rem;margin:0 0 11px;color:#34445a}.member-detail-lines{display:flex;flex-direction:column;gap:9px}.member-detail-line{display:grid;grid-template-columns:minmax(0,1fr);gap:10px;align-items:start}.member-detail-line>div{min-width:0}.member-detail-line:not(.paired)>div{display:grid;grid-template-columns:minmax(115px,.65fr) minmax(0,1.35fr);gap:10px;align-items:baseline}.member-detail-line dt{font-size:.8rem;color:#667085}.member-detail-line dd{margin:0;overflow-wrap:break-word;word-break:normal;font-weight:500;min-width:0}.member-detail-line.paired{grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}.member-detail-line.paired>div{min-width:0}.member-form-sections{display:grid;grid-template-columns:repeat(auto-fit,minmax(min(100%,340px),1fr));gap:14px;align-items:start}.member-form-section{background:#f7f9fc;border:1px solid #e3e9f1;border-radius:10px;padding:15px;min-width:0}.member-form-section h3{font-size:1rem;margin:0 0 13px;color:#34445a}.member-form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}.member-form-grid label{display:flex;flex-direction:column;gap:5px;font-weight:600;min-width:0}.member-form-grid input,.member-form-grid textarea{width:100%;min-width:0;box-sizing:border-box;padding:9px 10px;border:1px solid #cbd0d8;border-radius:7px;font:inherit;background:#fff}.member-form-grid textarea{min-height:85px}.member-form-grid .full{grid-column:1/-1}.member-form-grid .member-number-field{max-width:115px}.member-form-grid .postal-field{max-width:none}.postal-city-row{grid-column:1/-1;display:grid;grid-template-columns:100px minmax(0,1fr);gap:12px;min-width:0}.postal-city-row label{display:flex;flex-direction:column;gap:5px;font-weight:600;min-width:0}.postal-city-row input{width:100%;min-width:0;box-sizing:border-box;padding:9px 10px;border:1px solid #cbd0d8;border-radius:7px;font:inherit;background:#fff}.member-form-grid .date-field{max-width:190px}.member-form-section.membership-section{background:#f8f8f3;border-color:#e9e7d8}.member-form-section.account-section{background:#f3f7fa;border-color:#dfe9f0}.member-form-section.personal-section{background:#f7f7fb;border-color:#e5e5ef}.member-actions{display:flex;gap:9px;margin-top:16px;flex-wrap:wrap}.member-btn{border:0;border-radius:7px;padding:10px 16px;cursor:pointer;text-decoration:none;display:inline-block;background:#e9edf2;color:#202938}.member-btn.primary{background:#244e79;color:white}.member-btn.danger{background:#a92e35;color:white}.member-alert{padding:12px 15px;border-radius:8px;margin:12px 0;background:#e8f5e9}.member-alert.error{background:#fdeaea;color:#8b1e1e}.member-muted{color:#667085;font-size:.9rem}@media(max-width:650px){.member-table th:nth-child(4),.member-table td:nth-child(4){display:none}.member-card{padding:12px}.member-table th,.member-table td{padding:9px 6px;font-size:.9rem}}
</style></head><body><div class="container"><header class="app-page-header"><h1>Mitgliederübersicht</h1><?php require $appRoot.'/includes/navigation.php'; ?></header><main class="member-wrap">
<?php if($notice):?><div class="member-alert"><?=mh($notice)?></div><?php endif;?><?php if($error):?><div class="member-alert error"><?=mh($error)?></div><?php endif;?>
<?php if (!$editing): ?><div class="member-actions" style="margin:18px 0"><a class="member-btn primary" href="mitglieder.php?neu=1"><i class="fa-solid fa-plus" aria-hidden="true"></i> Neues Mitglied anlegen</a></div><?php endif; ?>
<?php if ($showNewForm): ?><section class="member-card"><h2><?= $editing?'Mitglied bearbeiten':'Neues Mitglied anlegen' ?></h2><form method="post"><input type="hidden" name="csrf_token" value="<?=mh($csrf)?>"><input type="hidden" name="action" value="save"><input type="hidden" name="member_id" value="<?=mh($editing['id']??0)?>"><div class="member-form-sections">
<section class="member-form-section personal-section"><h3>Personen- und Stammdaten</h3><div class="member-form-grid">
<label class="full">Mitglieds-Nr.<input class="member-number-field" name="member_number" id="member_number" type="number" min="1" step="1" required value="<?=mh($form['member_number']??'')?>"></label>
<label>Nachname<input name="last_name" id="last_name" type="text" required value="<?=mh($form['last_name']??'')?>"></label>
<label>Vorname<input name="first_name" id="first_name" type="text" required value="<?=mh($form['first_name']??'')?>"></label>
<label class="full">Straße, Hausnummer<input name="street" type="text" required value="<?=mh($form['street']??'')?>"></label>
<div class="postal-city-row"><label class="postal-field">PLZ<input name="postal_code" type="text" inputmode="numeric" pattern="[0-9]{5}" maxlength="5" required value="<?=mh($form['postal_code']??'')?>"></label>
<label>Ort<input name="city" type="text" required value="<?=mh($form['city']??'')?>"></label></div>
<label>Telefon<input name="phone" type="tel" value="<?=mh($form['phone']??'')?>"></label>
<label>Mobil<input name="mobile" type="tel" value="<?=mh($form['mobile']??'')?>"></label>
<label class="full">E-Mail<input name="email" type="email" value="<?=mh($form['email']??'')?>"></label>
</div></section>
<section class="member-form-section account-section"><h3>Kontodaten</h3><div class="member-form-grid">
<label>Kontoinhaber<input name="account_holder" id="account_holder" type="text" required value="<?=mh($form['account_holder']??'')?>"></label>
<label>Kreditinstitut<input name="bank_name" type="text" required value="<?=mh($form['bank_name']??'')?>"></label>
<label class="full">IBAN<input name="iban" id="iban" type="text" inputmode="text" autocomplete="off" required maxlength="27" placeholder="DE## #### #### #### #### ##" value="<?=mh($form['iban']??'')?>"></label>
<label class="full">BIC<input name="bic" type="text" required value="<?=mh($form['bic']??'')?>"></label>
<label class="full date-field">Unterschrieben am<input name="start_date" id="start_date" type="date" required value="<?=mh($form['start_date']??'')?>"></label>
<label class="full">Mandatsreferenz<input name="mandate_reference" type="text" value="<?=mh($form['mandate_reference']??'')?>"></label>
</div></section>
<section class="member-form-section membership-section"><h3>Mitgliedschaftsangaben</h3><div class="member-form-grid">
<label>Beitrag (€)<input name="monthly_fee" type="number" min="0.01" step="0.01" required value="<?=mh($form['monthly_fee']??'')?>"></label>
<label>Dauer (Jahre)<input name="duration_years" id="duration_years" type="number" min="1" step="1" value="<?=mh($form['duration_years']??'')?>"></label>
<label class="full date-field">Ende<input name="end_date" id="end_date_display" type="date" value="<?=mh($form['end_date']??'')?>"></label>
<label class="full">Kommentar<textarea name="comment"><?=mh($form['comment']??'')?></textarea></label>
</div></section>
</div><div class="member-actions"><button class="member-btn primary" type="submit"> <?= $editing?'Änderungen speichern':'Mitglied anlegen' ?></button><a class="member-btn" href="mitglieder.php">Abbrechen</a></div></form></section><?php endif; ?>
<section class="member-card"><h2>Mitglieder (<?=count($rows??[])?>)</h2><?php if(empty($rows)):?><p class="member-muted">Noch keine Mitglieder angelegt.</p><?php else:?><div style="overflow-x:auto"><table class="member-table"><thead><tr><th>Mitglieds-Nr.</th><th>Name</th><th>Vorname</th><th>Unterschrieben am</th><th>Ende</th></tr></thead><tbody><?php foreach($rows as $r):?><tr class="member-row<?=$r['id']===$openId?' is-open':''?>" onclick="openMemberRow(<?=$r['id']===$openId?0:(int)$r['id']?>)"><td><?=mh($r['member_number'])?></td><td><?=mh($r['last_name'])?></td><td><?=mh($r['first_name'])?></td><td><?=date('d.m.Y',strtotime($r['start_date']))?></td><td><?=$r['end_date']?date('d.m.Y',strtotime($r['end_date'])):'–'?></td></tr><?php if((int)$r['id']===$openId):?><tr class="member-details"><td colspan="5"><div class="member-detail-blocks">
<section class="member-detail-block"><h3>Stammdaten</h3><dl class="member-detail-lines">
<div class="member-detail-line"><div><dt>Straße, Hausnummer</dt><dd><?=($r['street']??'')!==''?mh($r['street']):'–'?></dd></div></div>
<div class="member-detail-line paired"><div><dt>PLZ</dt><dd><?=($r['postal_code']??'')!==''?mh($r['postal_code']):'–'?></dd></div><div><dt>Ort</dt><dd><?=($r['city']??'')!==''?mh($r['city']):'–'?></dd></div></div>
<div class="member-detail-line paired"><div><dt>Telefon</dt><dd><?=($r['phone']??'')!==''?mh($r['phone']):'–'?></dd></div><div><dt>Mobil-Tel.</dt><dd><?=($r['mobile']??'')!==''?mh($r['mobile']):'–'?></dd></div></div>
<div class="member-detail-line"><div><dt>E-Mail</dt><dd><?=($r['email']??'')!==''?mh($r['email']):'–'?></dd></div></div>
</dl></section>
<section class="member-detail-block"><h3>Kontodaten</h3><dl class="member-detail-lines">
<?php foreach(['account_holder'=>'Kontoinhaber','bank_name'=>'Kreditinstitut','iban'=>'IBAN','bic'=>'BIC','mandate_reference'=>'Mandatsreferenz'] as $k=>$label):?><div class="member-detail-line"><div><dt><?=mh($label)?></dt><dd><?=($r[$k]??'')!==''?mh($r[$k]):'–'?></dd></div></div><?php endforeach;?>
<div class="member-detail-line"><div><dt>Unterschrieben am</dt><dd><?=date('d.m.Y',strtotime($r['start_date']))?></dd></div></div>
</dl></section>
<section class="member-detail-block"><h3>Mitgliedschaftsangaben</h3><dl class="member-detail-lines">
<div class="member-detail-line"><div><dt>Beitrag</dt><dd><?=number_format((float)$r['monthly_fee'],2,',','.')?> € / Monat</dd></div></div>
<div class="member-detail-line"><div><dt>Ende</dt><dd><?=$r['end_date']?date('d.m.Y',strtotime($r['end_date'])):'–'?></dd></div></div>
</dl></section></div><div class="member-actions"><a class="member-btn primary" href="mitglieder.php?edit=<?=(int)$r['id']?>">Editieren</a><form method="post" onsubmit="return confirm('Mitglied wirklich löschen? Die Mitgliedsnummer wird nicht wieder vergeben.');"><input type="hidden" name="csrf_token" value="<?=mh($csrf)?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="member_id" value="<?=(int)$r['id']?>"><button class="member-btn danger">Löschen</button></form></div></td></tr><?php endif;?><?php endforeach;?></tbody></table></div><?php endif;?></section></main></div>
<script>
function openMemberRow(id){sessionStorage.setItem('membersListScroll',String(window.scrollY));window.location.href='mitglieder.php?open='+id;}
const savedMembersScroll=sessionStorage.getItem('membersListScroll');if(savedMembersScroll!==null){sessionStorage.removeItem('membersListScroll');const restoreMembersScroll=()=>window.scrollTo(0,parseInt(savedMembersScroll,10)||0);requestAnimationFrame(()=>requestAnimationFrame(restoreMembersScroll));window.addEventListener('load',restoreMembersScroll,{once:true});}
const ln=document.getElementById('last_name'),fn=document.getElementById('first_name'),ah=document.getElementById('account_holder');let accountManuallyChanged=false;if(ah){ah.addEventListener('input',()=>accountManuallyChanged=true);[ln,fn].forEach(x=>x&&x.addEventListener('input',()=>{if(!accountManuallyChanged)ah.value=[ln.value.trim(),fn.value.trim()].filter(Boolean).join(', ')}));}
const endField=document.getElementById('end_date_display');let endManuallyChanged=!!(endField&&endField.value);endField?.addEventListener('input',()=>endManuallyChanged=true);function updateEnd(){const s=document.getElementById('start_date')?.value,d=parseInt(document.getElementById('duration_years')?.value||'0',10),out=endField;if(!out||endManuallyChanged)return;if(!s||!d){out.value='';return;}const x=new Date(s+'T12:00:00'),kgYear=x.getFullYear()-(x.getMonth()<7?1:0),endYear=kgYear+d;out.value=endYear+'-07-31';}['start_date','duration_years'].forEach(id=>document.getElementById(id)?.addEventListener('input',updateEnd));updateEnd();
const ibanField=document.getElementById('iban');function formatIban(){if(!ibanField)return;const raw=ibanField.value.toUpperCase().replace(/\s+/g,'');if(/^[A-Z]{2}[0-9]{20}$/.test(raw)){ibanField.setCustomValidity('');ibanField.value=raw.match(/.{1,4}/g).join(' ').replace(/ ([A-Z0-9]{2})$/,' $1');}else if(raw===''){ibanField.setCustomValidity('');}else{ibanField.setCustomValidity('Bitte zwei Länderbuchstaben und genau 20 Ziffern eingeben.');}}ibanField?.addEventListener('blur',formatIban);ibanField?.form?.addEventListener('submit',()=>{if(ibanField)ibanField.value=ibanField.value.toUpperCase().replace(/\s+/g,'');});
</script></body></html>
