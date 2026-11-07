<?php
require_once dirname(__DIR__).'/intern/db_connection.php';$db=(new Database())->getConnection();$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);if(session_status()===PHP_SESSION_NONE)session_start();if(empty($_SESSION['helper_csrf']))$_SESSION['helper_csrf']=bin2hex(random_bytes(32));$csrf=$_SESSION['helper_csrf'];function h($v){return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');}function sendHelperMail($to,$subject,$body){$headers="From: foerderverein@evkigarumeln.de\r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=UTF-8\r\n";return mail($to,'=?UTF-8?B?'.base64_encode($subject).'?=',$body,$headers);}
$err='';$msg='';$token=(string)($_GET['token']??'');$aid=(int)($_GET['aktion']??0);$reg=null;$current=[];$groupIds=[];if($token!==''){$q=$db->prepare("SELECT * FROM helper_registrations WHERE edit_token_hash=? AND status='active'");$q->execute([hash('sha256',$token)]);$reg=$q->fetch(PDO::FETCH_ASSOC);if($reg){$aid=(int)$reg['action_id'];$q=$db->prepare("SELECT id FROM helper_registrations WHERE action_id=? AND email=? AND status='active'");$q->execute([$aid,$reg['email']]);$groupIds=array_map('intval',$q->fetchAll(PDO::FETCH_COLUMN));if($groupIds){$marks=implode(',',array_fill(0,count($groupIds),'?'));$q=$db->prepare("SELECT day_id,station_id,slot_key FROM helper_assignments WHERE registration_id IN ($marks)");$q->execute($groupIds);foreach($q as $r)$current[$r['day_id'].'|'.$r['station_id'].'|'.$r['slot_key']]=true;}}else $err='Der Änderungslink ist ungültig oder nicht mehr aktiv.';}
try{if($_SERVER['REQUEST_METHOD']==='POST'){if(!hash_equals($csrf,(string)($_POST['csrf']??'')))throw new RuntimeException('Sitzung abgelaufen. Bitte neu laden.');$aid=(int)$_POST['aid'];$firstName=trim($_POST['first_name']??'');$lastName=trim($_POST['last_name']??'');$name=trim($firstName.' '.$lastName);$email=trim($_POST['email']??'');$sel=array_unique(array_map('strval',(array)($_POST['sel']??[])));if($firstName===''||$lastName===''||!filter_var($email,FILTER_VALIDATE_EMAIL)||(!$sel&&!$reg))throw new RuntimeException('Bitte Vorname, Nachname, eine gültige E-Mail-Adresse und mindestens einen Einsatz angeben.');$db->beginTransaction();$q=$db->prepare("SELECT * FROM helper_actions WHERE id=? AND status='active' AND end_date>=CURDATE() FOR UPDATE");$q->execute([$aid]);$action=$q->fetch(PDO::FETCH_ASSOC);if(!$action)throw new RuntimeException('Diese Aktion ist nicht geöffnet.');$valid=[];foreach($sel as $v){$p=explode('|',$v);if(count($p)!==3)throw new RuntimeException('Ungültige Auswahl.');[$day,$station,$slot]=$p;$q=$db->prepare('SELECT d.event_date,d.slots_json,ds.active_slots_json,s.capacity,s.name AS station_name FROM helper_days d JOIN helper_day_stations ds ON ds.day_id=d.id JOIN helper_stations s ON s.id=ds.station_id WHERE d.id=? AND d.action_id=? AND s.id=? AND s.action_id=?');$q->execute([(int)$day,$aid,(int)$station,$aid]);$r=$q->fetch(PDO::FETCH_ASSOC);if(!$r||!in_array($slot,array_column(json_decode($r['slots_json'],true)?:[],'key'),true)||!in_array($slot,json_decode($r['active_slots_json'],true)?:[],true))throw new RuntimeException('Ein Einsatz ist nicht mehr verfügbar.');$t=explode('-',$slot);$valid[]=['day'=>(int)$day,'station'=>(int)$station,'station_name'=>$r['station_name'],'slot'=>$slot,'cap'=>(int)$r['capacity'],'date'=>$r['event_date'],'start'=>$t[0],'end'=>$t[1]];}
 for($i=0;$i<count($valid);$i++)for($j=$i+1;$j<count($valid);$j++)if($valid[$i]['date']===$valid[$j]['date']&&$valid[$i]['start']<$valid[$j]['end']&&$valid[$j]['start']<$valid[$i]['end'])throw new RuntimeException('Du hast zeitlich überlappende Einsätze ausgewählt.');
 if($reg){if((int)$reg['action_id']!==$aid)throw new RuntimeException('Ungültige Aktion.');$rid=(int)$reg['id'];$q=$db->prepare("SELECT id FROM helper_registrations WHERE action_id=? AND email=? AND status='active' FOR UPDATE");$q->execute([$aid,$reg['email']]);$groupIds=array_map('intval',$q->fetchAll(PDO::FETCH_COLUMN));if(!$groupIds)$groupIds=[$rid];$marks=implode(',',array_fill(0,count($groupIds),'?'));$params=array_merge([$name,$email],$groupIds);$db->prepare("UPDATE helper_registrations SET full_name=?,email=? WHERE id IN ($marks)")->execute($params);$db->prepare("DELETE FROM helper_assignments WHERE registration_id IN ($marks)")->execute($groupIds);$newToken=$token;}else{$newToken=bin2hex(random_bytes(32));$db->prepare('INSERT INTO helper_registrations(action_id,full_name,email,edit_token_hash) VALUES(?,?,?,?)')->execute([$aid,$name,$email,hash('sha256',$newToken)]);$rid=(int)$db->lastInsertId();}
 $ins=$db->prepare('INSERT INTO helper_assignments(registration_id,day_id,station_id,slot_key) VALUES(?,?,?,?)');foreach($valid as $v){$q=$db->prepare("SELECT COUNT(*) FROM helper_assignments a JOIN helper_registrations r ON r.id=a.registration_id AND r.status='active' WHERE a.day_id=? AND a.station_id=? AND a.slot_key=?");$q->execute([$v['day'],$v['station'],$v['slot']]);if((int)$q->fetchColumn()>=$v['cap'])throw new RuntimeException('Ein Einsatz wurde gerade vollständig belegt. Bitte Seite neu laden.');$ins->execute([$rid,$v['day'],$v['station'],$v['slot']]);}$db->commit();$url='https://'.($_SERVER['HTTP_HOST']??'evkigarumeln.de').'/helfen/?token='.rawurlencode($newToken);$weekdayNames=['Sunday'=>'Sonntag','Monday'=>'Montag','Tuesday'=>'Dienstag','Wednesday'=>'Mittwoch','Thursday'=>'Donnerstag','Friday'=>'Freitag','Saturday'=>'Samstag'];$rows='';foreach($valid as $v){$weekday=$weekdayNames[date('l',strtotime($v['date']))];$rows.='<tr><td style="padding:8px;border:1px solid #ddd;">'.h($weekday).'</td><td style="padding:8px;border:1px solid #ddd;">'.h($v['station_name']).'</td><td style="padding:8px;border:1px solid #ddd;">'.h($v['start'].'–'.$v['end']).'</td></tr>';}$mailIntro=$valid?'<p>Wir haben dich für folgende Zeiten eingetragen:</p><table style="border-collapse:collapse;width:100%;max-width:620px;"><thead><tr><th style="padding:8px;border:1px solid #ddd;background:#f3f6f7;text-align:left;">Tag</th><th style="padding:8px;border:1px solid #ddd;background:#f3f6f7;text-align:left;">Station</th><th style="padding:8px;border:1px solid #ddd;background:#f3f6f7;text-align:left;">Uhrzeit</th></tr></thead><tbody>'.$rows.'</tbody></table>':'<p>Du bist nun für keine Schichten dieser Aktion mehr eingetragen. Deine bisherigen Eintragungen wurden entfernt.</p>';$mailBody='<div style="font-family:Arial,sans-serif;color:#333;line-height:1.5;max-width:680px;"><p>Hallo '.h($firstName).',</p><p>vielen Dank, dass du uns unterstützt!</p>'.$mailIntro.'<p>Du kannst deine Eintragungen jederzeit ändern:</p><p><a href="'.h($url).'" style="display:inline-block;padding:11px 16px;background:#3498db;color:#fff;text-decoration:none;border-radius:6px;">Meine Helferanmeldung bearbeiten</a></p><p>Vielen Dank für deine Unterstützung!</p><p>Dein Förderverein</p></div>';sendHelperMail($email,'Deine Helferanmeldung – '.$action['title'],$mailBody);$msg='Deine Anmeldung wurde gespeichert. Du erhältst eine E-Mail mit deinem persönlichen Änderungslink.';$reg=null;$current=[];$token='';$aid=(int)$action['id'];}}
catch(Throwable $e){if($db->inTransaction())$db->rollBack();$err=$e instanceof RuntimeException?$e->getMessage():'Speichern fehlgeschlagen.';if(!($e instanceof RuntimeException))error_log('Helferliste: '.$e->getMessage());}
$actions=$db->query("SELECT * FROM helper_actions WHERE status='active' AND end_date>=CURDATE() ORDER BY start_date")->fetchAll(PDO::FETCH_ASSOC);$action=null;if($aid){$q=$db->prepare("SELECT * FROM helper_actions WHERE id=? AND status='active'");$q->execute([$aid]);$action=$q->fetch(PDO::FETCH_ASSOC);}if(!$action&&count($actions)===1){$action=$actions[0];$aid=(int)$action['id'];}$days=$stations=$occ=[];if($action){$q=$db->prepare('SELECT * FROM helper_days WHERE action_id=? ORDER BY event_date');$q->execute([$aid]);$days=$q->fetchAll(PDO::FETCH_ASSOC);$q=$db->prepare('SELECT * FROM helper_stations WHERE action_id=? AND active=1 ORDER BY sort_order,id');$q->execute([$aid]);$stations=$q->fetchAll(PDO::FETCH_ASSOC);$q=$db->query("SELECT a.day_id,a.station_id,a.slot_key,COUNT(*) used,GROUP_CONCAT(CONCAT(SUBSTRING_INDEX(r.full_name,' ',1),' ',UPPER(LEFT(SUBSTRING_INDEX(r.full_name,' ',-1),1)),'.') SEPARATOR ', ') names FROM helper_assignments a JOIN helper_registrations r ON r.id=a.registration_id AND r.status='active' GROUP BY a.day_id,a.station_id,a.slot_key");foreach($q as $r)$occ[$r['day_id'].'|'.$r['station_id'].'|'.$r['slot_key']]=$r;}
?>
<!doctype html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="theme-color" content="#f4efdc">
<meta name="application-name" content="Helferbereich – Förderverein">
<meta name="description" content="Unterstütze den Förderverein des Evangelischen Kindergartens Rumeln: Entdecke offene Helferschichten und melde dich unkompliziert für passende Zeiten an.">

<!-- App-Icon / Favicon -->
<link rel="icon" type="image/png" href="https://evkigarumeln.de/intern/assets/foerderverein-logo.png?v=20261107-2">
<link rel="shortcut icon" href="https://foerderverein.evkigarumeln.de/intern/assets/foerderverein-logo.png?v=20261107-2">
<link rel="apple-touch-icon" href="https://foerderverein.evkigarumeln.de/intern/assets/foerderverein-logo.png?v=20261107-2">

<!-- Metadaten für Linkvorschauen (WhatsApp, iMessage und andere Messenger) -->
<meta property="og:type" content="website">
<meta property="og:locale" content="de_DE">
<meta property="og:site_name" content="Förderverein Evangelischer Kindergarten Rumeln">
<meta property="og:title" content="Helferbereich – Förderverein">
<meta property="og:description" content="Unterstütze den Förderverein des Evangelischen Kindergartens Rumeln: Entdecke offene Helferschichten und melde dich unkompliziert für passende Zeiten an.">
<meta property="og:url" content="https://foerderverein.evkigarumeln.de/helfen/">
<meta property="og:image" content="https://foerderverein.evkigarumeln.de/helfen/helfen-vorschau.png">
<meta property="og:image:secure_url" content="https://foerderverein.evkigarumeln.de/helfen/helfen-vorschau.png">
<meta property="og:image:type" content="image/png">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="Helferbereich des Fördervereins Evangelischer Kindergarten Rumeln">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Helferbereich – Förderverein">
<meta name="twitter:description" content="Unterstütze den Förderverein des Evangelischen Kindergartens Rumeln und melde dich für offene Helferschichten an.">
<meta name="twitter:image" content="https://foerderverein.evkigarumeln.de/helfen/helfen-vorschau.png">

<title>Helferbereich – Förderverein</title>
<style>
:root{--primary-color:#3498db;--success-color:#2ecc71;--warning-color:#f39c12;--danger-color:#e74c3c;--light-gray:#f5f5f5;--border-color:#ddd;--button-gray:#6c757d;--fv-ink:#333;--fv-muted:#6c757d;--fv-line:var(--border-color);--fv-bg:#f9f9f9;--fv-primary:var(--primary-color);--fv-card:#fff;--fv-shadow:0 2px 10px rgba(0,0,0,.1)}
*{box-sizing:border-box}
html{-webkit-text-size-adjust:100%;text-size-adjust:100%}
body{margin:0;padding:20px;background:var(--fv-bg);color:var(--fv-ink);font:15px/1.5 'Segoe UI',Tahoma,Geneva,Verdana,sans-serif}
.helper-shell{width:100%;max-width:1100px;margin:0 auto}
.helper-header{display:flex;align-items:center;min-height:82px;padding:13px 20px;background:var(--fv-card);border:1px solid var(--fv-line);border-radius:14px;box-shadow:var(--fv-shadow);margin-bottom:24px}
.helper-brand{display:inline-flex;align-items:center;gap:13px;color:var(--fv-ink);text-decoration:none}
.helper-brand img{display:block;width:60px;height:54px;object-fit:contain}
.helper-brand-text{display:flex;flex-direction:column;gap:2px}
.helper-brand-text strong{font-size:16px;line-height:1.2;font-weight:700}
.helper-brand-text small{font-size:13px;color:var(--fv-muted)}
.helper-card{background:var(--fv-card);border:1px solid var(--fv-line);border-radius:14px;padding:24px;margin:0 0 18px;box-shadow:var(--fv-shadow)}
.helper-card h1,.helper-card h2{margin:0 0 12px;color:var(--fv-ink);line-height:1.25}
.helper-card h1{font-size:24px}.helper-card h2{font-size:21px}
.helper-description{margin:0 0 8px;color:#4d5d65}
.helper-date{margin:0 0 22px;color:var(--fv-muted);font-weight:600}
.helper-person-row{display:flex;align-items:flex-end;flex-wrap:wrap;gap:14px 22px;margin:20px 0 24px;padding:16px 18px;background:#f8fafb;border:1px solid var(--fv-line);border-radius:10px}
.helper-person-title{align-self:center;font-weight:700;color:#344b57;white-space:nowrap}
.helper-field{display:flex;align-items:center;flex-wrap:wrap;gap:8px;min-width:230px}
.helper-field label{font-weight:600;color:#42545d;white-space:nowrap}
.helper-field input{width:clamp(170px,18vw,300px);max-width:100%;min-width:0;padding:10px 12px;border:1px solid #cbd5da;border-radius:8px;background:#fff;color:var(--fv-ink);font:inherit;outline:none}
.helper-field input:focus{border-color:#6d9aaa;box-shadow:0 0 0 3px rgba(70,125,145,.13)}
.helper-day{border:1px solid var(--fv-line);border-radius:10px;padding:0;margin:13px 0;background:#fff;overflow:hidden}
.helper-day summary{cursor:pointer;list-style:none;display:flex;align-items:center;justify-content:space-between;gap:14px;padding:14px 16px;font-weight:700;color:#333;background:var(--light-gray)}
.helper-day summary::-webkit-details-marker{display:none}
.helper-day summary:after{content:'+';margin-left:auto;color:var(--primary-color);font-size:18px;line-height:1;flex:0 0 auto}
.helper-day[open] summary:after{content:'−'}
.helper-day-title{flex:1;min-width:0}
.helper-day-status{display:inline-flex;align-items:center;justify-content:center;flex:0 0 auto;padding:5px 10px;border-radius:6px;font-size:13px;font-weight:600;white-space:nowrap}
.helper-day-status.open-high{background:rgba(231,76,60,.13);color:#b6382c}
.helper-day-status.open-low{background:rgba(243,156,18,.17);color:#8a5a00}
.helper-day-status.complete{background:rgba(46,204,113,.16);color:#218c4e}
.helper-table-wrap{overflow-x:auto;padding:0 12px 12px}
.helper-table{border-collapse:separate;border-spacing:0;width:100%;min-width:520px;margin-top:12px;font-size:14px}
.helper-table th,.helper-table td{border-right:1px solid var(--fv-line);border-bottom:1px solid var(--fv-line);padding:8px;text-align:center;vertical-align:middle}
.helper-table thead th{background:#f5f8f9;color:#435761;font-weight:700}
.helper-table tr>*:first-child{border-left:1px solid var(--fv-line);text-align:left;white-space:nowrap}
.helper-table thead tr:first-child th{border-top:1px solid var(--fv-line)}
.helper-table thead tr:first-child th:first-child{border-top-left-radius:7px}
.helper-table thead tr:first-child th:last-child{border-top-right-radius:7px}
.slot{position:relative;display:block;width:100%;min-width:90px;min-height:58px;padding:7px 6px;background:rgba(231,76,60,.13);border:2px solid transparent;border-radius:7px;color:#34434a;font:inherit;cursor:pointer;transition:border-color .12s,filter .12s}
.slot:hover:not(:disabled){filter:brightness(.97);border-color:var(--primary-color)}
.slot.partial{background:rgba(243,156,18,.17)}.slot.full{background:rgba(46,204,113,.16);cursor:not-allowed}.slot.selected{border-color:var(--fv-primary);box-shadow:inset 0 0 0 1px var(--fv-primary)}
.slot b{display:block;font-size:13px}.slot small{display:block;margin-top:3px;font-size:11px;line-height:1.25;overflow-wrap:anywhere}.slot span{display:block;min-height:15px;font-weight:700;color:var(--fv-primary)}
.helper-submit-row{display:flex;justify-content:flex-end;margin-top:22px}
.helper-submit{display:inline-flex;align-items:center;justify-content:center;width:auto;min-height:44px;padding:11px 20px;border:0;border-radius:8px;background:var(--fv-primary);color:#fff;font:inherit;font-weight:700;cursor:pointer;transition:background .15s,opacity .15s}
.helper-submit:hover:not(:disabled){background:#287fb8}.helper-submit:disabled{opacity:.45;cursor:not-allowed}
.helper-error{margin:0 0 16px;padding:12px 15px;border:1px solid #efcaca;border-radius:9px;background:#fff4f4;color:#9e2929}
.helper-success{margin:0 0 18px;padding:14px 16px;border:1px solid #cfe5d5;border-radius:10px;background:#f0faf2;color:#27643a}
.helper-action{padding:14px 0;border-bottom:1px solid var(--fv-line)}.helper-action:last-child{border-bottom:0;padding-bottom:0}
.helper-action a{color:var(--fv-primary);text-decoration:none}.helper-action a:hover{text-decoration:underline}
.helper-action p{margin:5px 0;color:#596970}
@media(min-width:761px){.helper-person-row{flex-wrap:nowrap;align-items:flex-end;gap:14px}.helper-field{display:flex;flex:1 1 0;flex-direction:column;align-items:stretch;flex-wrap:nowrap;gap:4px;min-width:0}.helper-field label{font-size:13px}.helper-field input{width:100%;padding:8px 10px;font-size:14px}.helper-person-title{font-size:14px;flex:0 0 auto}}
@media(max-width:760px){body{padding:12px 10px 28px}.helper-header{min-height:70px;padding:10px 13px;margin-bottom:15px;border-radius:11px}.helper-brand img{width:48px;height:44px}.helper-brand-text strong{font-size:15px}.helper-brand-text small{font-size:12px}.helper-card{padding:17px 14px;border-radius:11px}.helper-card h1{font-size:21px}.helper-card h2{font-size:19px}.helper-person-row{align-items:stretch;gap:12px;padding:13px}.helper-person-title{width:100%;align-self:auto}.helper-field{width:100%;min-width:0;display:block}.helper-field label{display:block;margin-bottom:5px}.helper-field input{width:min(100%,300px)}.helper-day summary{flex-wrap:wrap;justify-content:flex-start;row-gap:7px}.helper-day-title{flex:0 0 100%;width:100%;white-space:nowrap}.helper-day-status{align-self:flex-start;justify-content:flex-start;margin-right:auto}.helper-day summary:after{margin-left:auto}.helper-submit-row{justify-content:stretch}.helper-submit{width:100%}}
</style>
</head>
<body>
<div class="helper-shell">
<header class="helper-header">
    <a class="helper-brand" href="/helfen/" aria-label="Förderverein – Helferbereich">
        <img src="/intern/assets/foerderverein-logo.png" alt="Logo Förderverein">
        <span class="helper-brand-text"><strong>Förderverein</strong><small>Helferbereich</small></span>
    </a>
</header>
<?php if($err):?><p class="helper-error"><?=h($err)?></p><?php endif;?>
<?php if($msg):?><p class="helper-success"><?=h($msg)?></p><?php endif;?>
<?php if(!$action):?>
<section class="helper-card">
    <h1>Aktuelle Helferaktionen</h1>
    <?php if(!$actions):?>Zurzeit sind keine Helferlisten geöffnet.<?php else:foreach($actions as $a):?>
    <div class="helper-action">
        <h2><a href="?aktion=<?=$a['id']?>"><?=h($a['title'])?></a></h2>
        <p><?=nl2br(h($a['description']))?></p>
        <p><?=date('d.m.Y',strtotime($a['start_date']))?>–<?=date('d.m.Y',strtotime($a['end_date']))?></p>
    </div>
    <?php endforeach;endif;?>
</section>
<?php else:?>
<section class="helper-card">
    <h1><?=h($action['title'])?></h1>
    <?php if(trim((string)$action['description'])!==''):?><p class="helper-description"><?=nl2br(h($action['description']))?></p><?php endif;?>
    <p class="helper-date"><?=date('d.m.Y',strtotime($action['start_date']))?>–<?=date('d.m.Y',strtotime($action['end_date']))?></p>
    <?php if($reg):?><p class="helper-description">Du bearbeitest deine bestehende Anmeldung.</p><?php endif;?>
    <?php
        $prefillFirst='';$prefillLast='';
        if($reg){
            $storedName=trim((string)$reg['full_name']);
            if(strpos($storedName,',')!==false){
                [$legacyLast,$legacyFirst]=array_map('trim',explode(',',$storedName,2));
                $prefillFirst=$legacyFirst;$prefillLast=$legacyLast;
            }else{
                $nameParts=preg_split('/\s+/', $storedName, 2);
                $prefillFirst=$nameParts[0]??'';
                $prefillLast=$nameParts[1]??'';
            }
        }
    ?>
    <form method="post" id="form">
        <input type="hidden" name="csrf" value="<?=h($csrf)?>"><input type="hidden" name="aid" value="<?=$aid?>">
        <div class="helper-person-row">
            <span class="helper-person-title">Ich helfe:</span>
            <div class="helper-field"><label for="helper-first-name">Vorname</label><input id="helper-first-name" name="first_name" required autocomplete="given-name" value="<?=h($prefillFirst)?>"></div>
            <div class="helper-field"><label for="helper-last-name">Nachname</label><input id="helper-last-name" name="last_name" required autocomplete="family-name" value="<?=h($prefillLast)?>"></div>
            <div class="helper-field"><label for="helper-email">E-Mail-Adresse</label><input id="helper-email" type="email" name="email" required autocomplete="email" value="<?=h($reg['email']??'')?>"></div>
        </div>
        <?php
        $weekdayNames=['Sunday'=>'Sonntag','Monday'=>'Montag','Tuesday'=>'Dienstag','Wednesday'=>'Mittwoch','Thursday'=>'Donnerstag','Friday'=>'Freitag','Saturday'=>'Samstag'];
        foreach($days as $d):
            $slots=json_decode($d['slots_json'],true)?:[];
            $dayTotal=0;$dayOpen=0;
            foreach($stations as $st){
                $q=$db->prepare('SELECT active_slots_json FROM helper_day_stations WHERE day_id=? AND station_id=?');
                $q->execute([$d['id'],$st['id']]);
                $dayActive=json_decode((string)$q->fetchColumn(),true)?:[];
                foreach($slots as $daySlot){
                    if(!in_array($daySlot['key'],$dayActive,true)) continue;
                    $dayOcc=$occ[$d['id'].'|'.$st['id'].'|'.$daySlot['key']]??['used'=>0];
                    $capacity=(int)$st['capacity'];
                    $used=(int)$dayOcc['used'];
                    $dayTotal += $capacity;
                    $dayOpen += max(0, $capacity - $used);
                }
            }
            $dayStatusClass=$dayOpen===0?'complete':($dayOpen>3?'open-high':'open-low');
            $dayLabel=$weekdayNames[date('l',strtotime($d['event_date']))].', '.date('d.m.Y',strtotime($d['event_date']));
        ?>
        <details class="helper-day">
            <summary><span class="helper-day-title"><?=h($dayLabel)?></span><span class="helper-day-status <?=$dayStatusClass?>">Noch <?=$dayOpen?>/<?=$dayTotal?> Schichten zu besetzen</span></summary>
            <div class="helper-table-wrap"><table class="helper-table"><thead><tr><th>Zeit</th><?php foreach($stations as $s):?><th><?=h($s['name'])?></th><?php endforeach;?></tr></thead><tbody>
            <?php foreach($slots as $sl):?><tr><th><?=h($sl['label'])?></th><?php foreach($stations as $s):$k=$d['id'].'|'.$s['id'].'|'.$sl['key'];$q=$db->prepare('SELECT active_slots_json FROM helper_day_stations WHERE day_id=? AND station_id=?');$q->execute([$d['id'],$s['id']]);$active=json_decode((string)$q->fetchColumn(),true)?:[];$o=$occ[$k]??['used'=>0,'names'=>''];$full=(int)$o['used']>=(int)$s['capacity'];$checked=isset($current[$k]);?>
            <td><?php if(in_array($sl['key'],$active,true)):?><button type="button" class="slot <?=$full?'full':((int)$o['used']?'partial':'')?> <?=$checked?'selected':''?>" data-key="<?=h($k)?>" <?=$full&&!$checked?'disabled':''?>><b><?=(int)$o['used']?>/<?=$s['capacity']?></b><small><?=h($o['names'])?></small><span><?=$checked?'✓':''?></span></button><?php else:?>–<?php endif;?></td>
            <?php endforeach;?></tr><?php endforeach;?>
            </tbody></table></div>
        </details>
        <?php endforeach;?>
        <div id="hidden"></div>
        <div class="helper-submit-row"><button class="helper-submit" id="submit" disabled>Für diese Schichten eintragen</button></div>
    </form>
</section>
<?php endif;?>
</div>
<script>
const f=document.getElementById('form');
if(f){
 const initialSelected=new Set(<?=json_encode(array_keys($current))?>);
 const canWithdraw=<?= $reg ? 'true' : 'false' ?>;
 const selected=new Set(initialSelected),box=document.getElementById('hidden'),btn=document.getElementById('submit');
 const firstNameInput=document.getElementById('helper-first-name');
 const lastNameInput=document.getElementById('helper-last-name');
 const emailInput=document.getElementById('helper-email');
 function updateButton(){
  const validName=firstNameInput.value.trim().length>0 && lastNameInput.value.trim().length>0;
  const validEmail=emailInput.value.trim().length>0 && emailInput.validity.valid;
  btn.disabled=!(validName && validEmail);
 }
 function draw(){box.innerHTML='';selected.forEach(k=>{let i=document.createElement('input');i.type='hidden';i.name='sel[]';i.value=k;box.appendChild(i)});btn.textContent='Für diese Schichten eintragen';document.querySelectorAll('.slot').forEach(b=>{b.classList.toggle('selected',selected.has(b.dataset.key));b.querySelector('span').textContent=selected.has(b.dataset.key)?'✓':''});updateButton()}
 firstNameInput.addEventListener('input',updateButton);
 lastNameInput.addEventListener('input',updateButton);
 emailInput.addEventListener('input',updateButton);
 document.querySelectorAll('.slot:not(:disabled)').forEach(b=>b.onclick=()=>{selected.has(b.dataset.key)?selected.delete(b.dataset.key):selected.add(b.dataset.key);draw()});
 draw();
 f.addEventListener('submit',function(event){
  if(!firstNameInput.value.trim() || !lastNameInput.value.trim() || !emailInput.value.trim() || !emailInput.validity.valid || (!selected.size && !canWithdraw)){
   event.preventDefault();
   updateButton();
   if(!firstNameInput.value.trim()) firstNameInput.focus();
   else if(!lastNameInput.value.trim()) lastNameInput.focus();
   else if(!emailInput.value.trim() || !emailInput.validity.valid) emailInput.focus();
  }
 });
}
</script>
</body>
</html>
