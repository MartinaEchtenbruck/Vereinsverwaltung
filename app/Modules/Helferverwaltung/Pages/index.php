<?php
if(!defined('APP_ENTRY_POINT')){http_response_code(404);exit;}
$root=dirname(__DIR__,4);require_once $root.'/auth.php';checkPermission('manage_helpers');require_once $root.'/db_connection.php';require_once $root.'/app/Core/Mail.php';
$db=(new Database())->getConnection();$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
if(empty($_SESSION['helpers_csrf']))$_SESSION['helpers_csrf']=bin2hex(random_bytes(32));$csrf=$_SESSION['helpers_csrf'];$error='';$notice='';
function hh($v){return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');}
try{if($_SERVER['REQUEST_METHOD']==='POST'){
 if(!hash_equals($csrf,(string)($_POST['csrf']??'')))throw new RuntimeException('Sitzung abgelaufen. Bitte neu laden.');$do=$_POST['do']??'';
 if($do==='create'){$title=trim($_POST['title']??'');$desc=trim($_POST['description']??'');$start=$_POST['start']??'';$end=$_POST['end']??'';$slots=[];foreach(preg_split('/\R+/',trim($_POST['slots']??'')) as $line){if(preg_match('/^(\d{2}:\d{2})\s*-\s*(\d{2}:\d{2})$/',$line,$m)&&$m[1]<$m[2])$slots[]=['key'=>$m[1].'-'.$m[2],'label'=>$m[1].'–'.$m[2]];}if(!$title||!preg_match('/^\d{4}-\d{2}-\d{2}$/',$start)||!preg_match('/^\d{4}-\d{2}-\d{2}$/',$end)||$end<$start||!$slots)throw new RuntimeException('Bitte Titel, gültigen Zeitraum und mindestens ein Zeitfenster angeben.');$db->beginTransaction();$q=$db->prepare("INSERT INTO helper_actions(title,description,start_date,end_date,status,default_slots,created_by) VALUES(?,?,?,?,'draft',?,?)");$q->execute([$title,$desc,$start,$end,json_encode($slots),$_SESSION['user_id']]);$aid=(int)$db->lastInsertId();$day=new DateTime($start);$last=new DateTime($end);$ins=$db->prepare('INSERT INTO helper_days(action_id,event_date,slots_json) VALUES(?,?,?)');while($day<=$last){$ins->execute([$aid,$day->format('Y-m-d'),json_encode($slots)]);$day->modify('+1 day');}$db->commit();$notice='Helferaktion angelegt. Jetzt Stationen hinzufügen.';}
 if($do==='update_action'){$aid=(int)$_POST['action_id'];$title=trim($_POST['title']??'');$desc=trim($_POST['description']??'');$start=$_POST['start']??'';$end=$_POST['end']??'';if(!$title||!preg_match('/^\d{4}-\d{2}-\d{2}$/',$start)||!preg_match('/^\d{4}-\d{2}-\d{2}$/',$end)||$end<$start)throw new RuntimeException('Bitte Titel und einen gültigen Zeitraum angeben.');$db->prepare('UPDATE helper_actions SET title=?,description=?,start_date=?,end_date=? WHERE id=?')->execute([$title,$desc,$start,$end,$aid]);$notice='Stammdaten gespeichert.';}
 if($do==='status'){$st=$_POST['status']??'';if(!in_array($st,['draft','active','closed'],true))throw new RuntimeException('Ungültiger Status.');$db->prepare('UPDATE helper_actions SET status=? WHERE id=?')->execute([$st,(int)$_POST['id']]);$notice='Status gespeichert.';}
 if($do==='station'){$aid=(int)$_POST['action_id'];$name=trim($_POST['name']??'');$cap=(int)($_POST['capacity']??1);if(!$name||$cap<1||$cap>100)throw new RuntimeException('Bitte Stationsname und Kapazität (1–100) angeben.');$db->prepare('INSERT INTO helper_stations(action_id,name,description,capacity,sort_order) VALUES(?,?,?,?,?)')->execute([$aid,$name,trim($_POST['description']??''),$cap,(int)$_POST['sort_order']]);$sid=(int)$db->lastInsertId();$days=$db->prepare('SELECT id,slots_json FROM helper_days WHERE action_id=?');$days->execute([$aid]);$ins=$db->prepare('INSERT INTO helper_day_stations(day_id,station_id,active_slots_json) VALUES(?,?,?)');foreach($days as $d){$keys=array_column(json_decode($d['slots_json'],true)?:[],'key');$ins->execute([$d['id'],$sid,json_encode($keys)]);}$notice='Station hinzugefügt (standardmäßig für alle Zeitfenster aktiv).';}
 if($do==='station_delete'){$db->prepare('DELETE FROM helper_stations WHERE id=? AND action_id=?')->execute([(int)$_POST['station_id'],(int)$_POST['action_id']]);$notice='Station gelöscht.';}
 if($do==='day_grid'){$dayid=(int)$_POST['day_id'];$lines=preg_split('/\R+/',trim($_POST['slots']??''));$slots=[];foreach($lines as $line)if(preg_match('/^(\d{2}:\d{2})\s*-\s*(\d{2}:\d{2})$/',$line,$m)&&$m[1]<$m[2])$slots[]=['key'=>$m[1].'-'.$m[2],'label'=>$m[1].'–'.$m[2]];if(!$slots)throw new RuntimeException('Mindestens ein gültiges Zeitfenster erforderlich.');$db->beginTransaction();$db->prepare('UPDATE helper_days SET slots_json=?,custom_grid=1 WHERE id=?')->execute([json_encode($slots),$dayid]);$ds=$db->prepare('SELECT station_id,active_slots_json FROM helper_day_stations WHERE day_id=?');$ds->execute([$dayid]);$valid=array_column($slots,'key');foreach($ds as $r){$old=json_decode($r['active_slots_json'],true)?:[];$keep=array_values(array_intersect($old,$valid));$db->prepare('UPDATE helper_day_stations SET active_slots_json=? WHERE day_id=? AND station_id=?')->execute([json_encode($keep),$dayid,$r['station_id']]);}$db->commit();$notice='Zeitfenster dieses Tages aktualisiert. Stationsbelegungen bitte prüfen.';}
 if($do==='station_slots'){$dayid=(int)$_POST['day_id'];$sid=(int)$_POST['station_id'];$keys=array_values(array_unique(array_map('strval',(array)($_POST['slots']??[]))));$q=$db->prepare('SELECT slots_json FROM helper_days WHERE id=?');$q->execute([$dayid]);$valid=array_column(json_decode((string)$q->fetchColumn(),true)?:[],'key');if(array_diff($keys,$valid))throw new RuntimeException('Ungültige Zeitfenster.');$db->prepare('INSERT INTO helper_day_stations(day_id,station_id,active_slots_json) VALUES(?,?,?) ON DUPLICATE KEY UPDATE active_slots_json=VALUES(active_slots_json)')->execute([$dayid,$sid,json_encode($keys)]);$notice='Stationszeiten gespeichert.';}
 }}catch(Throwable $e){if($db->inTransaction())$db->rollBack();$error=$e instanceof RuntimeException?$e->getMessage():'Speichern fehlgeschlagen.';if(!($e instanceof RuntimeException))error_log($e->getMessage());}
$actions=$db->query("SELECT a.*,(SELECT COUNT(*) FROM helper_stations s WHERE s.action_id=a.id) station_count FROM helper_actions a ORDER BY a.start_date DESC,a.id DESC")->fetchAll(PDO::FETCH_ASSOC);
// Schichtbedarf und tatsächliche Besetzung je Aktion ermitteln. Eine Schicht
// entspricht einem aktiven Stations-Zeitfenster; die Stationskapazität zählt
// als Anzahl benötigter Helfer.
$shiftTotals=[];
$shiftRows=$db->query("SELECT d.action_id,d.id day_id,s.id station_id,s.capacity,ds.active_slots_json
 FROM helper_days d
 JOIN helper_stations s ON s.action_id=d.action_id
 JOIN helper_day_stations ds ON ds.day_id=d.id AND ds.station_id=s.id")->fetchAll(PDO::FETCH_ASSOC);
$filledRows=$db->query("SELECT a.day_id,a.station_id,a.slot_key,COUNT(*) filled
 FROM helper_assignments a
 JOIN helper_registrations r ON r.id=a.registration_id AND r.status='active'
 GROUP BY a.day_id,a.station_id,a.slot_key")->fetchAll(PDO::FETCH_ASSOC);
$filledByShift=[];
foreach($filledRows as $row){$filledByShift[$row['day_id'].'|'.$row['station_id'].'|'.$row['slot_key']]=(int)$row['filled'];}
foreach($shiftRows as $row){
 $keys=json_decode((string)$row['active_slots_json'],true)?:[];
 foreach($keys as $slotKey){
  $key=$row['day_id'].'|'.$row['station_id'].'|'.$slotKey;
  $capacity=max(0,(int)$row['capacity']);
  $filled=min($capacity,$filledByShift[$key]??0);
  $aid=(int)$row['action_id'];
  if(!isset($shiftTotals[$aid]))$shiftTotals[$aid]=['open'=>0,'total'=>0];
  $shiftTotals[$aid]['total']+=$capacity;
  $shiftTotals[$aid]['open']+=max(0,$capacity-$filled);
 }
}
foreach($actions as &$actionRow){$totals=$shiftTotals[(int)$actionRow['id']]??['open'=>0,'total'=>0];$actionRow['open_shifts']=$totals['open'];$actionRow['total_shifts']=$totals['total'];}unset($actionRow);
$selected=(int)($_GET['aktion']??($actions[0]['id']??0));$q=$db->prepare('SELECT * FROM helper_actions WHERE id=?');$q->execute([$selected]);$action=$q->fetch(PDO::FETCH_ASSOC);$stations=[];$days=[];if($action){$q=$db->prepare('SELECT * FROM helper_stations WHERE action_id=? ORDER BY sort_order,id');$q->execute([$selected]);$stations=$q->fetchAll(PDO::FETCH_ASSOC);$q=$db->prepare('SELECT * FROM helper_days WHERE action_id=? ORDER BY event_date');$q->execute([$selected]);$days=$q->fetchAll(PDO::FETCH_ASSOC);}
?><!doctype html><html lang="de"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Helferlisten</title><link rel="icon" type="image/png" sizes="32x32" href="/intern/assets/icons/favicon.png"><link rel="manifest" href="/intern/manifest.webmanifest"><link rel="stylesheet" href="styles.css"><link rel="stylesheet" href="includes/navigation.css"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"><style>
.helper-wrap{max-width:1250px;margin:24px auto}
.helper-card{background:#fff;border-radius:12px;padding:20px;margin:18px 0;box-shadow:0 2px 12px #0000000d}
.helper-table-header{display:flex;align-items:center;justify-content:space-between;gap:12px;margin:0 0 10px}
.helper-table-header h2{margin:0;font-size:1.15rem}
.helper-btn{border:0;border-radius:7px;padding:9px 13px;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:7px;background:#e9edf2;color:#202938}
.helper-btn.primary{background:#3498db;color:#fff}
.helper-icon-btn{width:34px;height:34px;justify-content:center;padding:0;border-radius:7px;background:#e9edf2;color:#3498db}
.helper-table{width:100%;border-collapse:collapse}
.helper-table th,.helper-table td{padding:11px 12px;border-bottom:1px solid #e8e8e8;text-align:left;vertical-align:middle}
.helper-table th{font-weight:650;color:#34445a}
.helper-table tbody tr:hover{background:#f5f7fa}
.helper-form-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:14px}
.helper-field{display:flex;flex-direction:column;gap:5px;font-weight:600;margin:9px 0}
.helper-field input,.helper-field textarea{box-sizing:border-box;width:100%;padding:9px 10px;border:1px solid #cbd0d8;border-radius:7px;font:inherit}
.helper-alert{padding:12px 15px;border-radius:8px;margin:12px 0;background:#e8f5e9}
.helper-alert.error{background:#fdeaea;color:#8b1e1e}
.helper-muted{color:#667085;font-size:.9rem}
.helper-tag{display:inline-block;border-radius:20px;padding:4px 9px;background:#e8edf1;font-size:.85rem;white-space:nowrap}
.helper-tag.active{background:#d9f2df;color:#176b35}
.helper-tag.closed{background:#ffedcf;color:#805400}
.helper-tag.draft{background:#e8edf1;color:#475467}
.helper-tag.shifts-red{background:#fde8e7;color:#a12622}
.helper-tag.shifts-yellow{background:#fff3cd;color:#805b00}
.helper-tag.shifts-green{background:#d9f2df;color:#176b35}
.helper-new-form[hidden]{display:none}
.helper-actions-cell{white-space:nowrap}\n.helper-mobile-info{display:none}
.helper-actions-cell .helper-btn{margin-right:4px}
.helper-desktop-date,.helper-desktop-status,.helper-desktop-shifts{display:table-cell}
@media(max-width:650px){
  .helper-card{padding:12px}
  .helper-table{border-collapse:separate;border-spacing:0 10px}
  .helper-table thead{display:none}
  .helper-table tbody{display:block}
  .helper-table tbody tr{display:grid;grid-template-columns:minmax(0,1fr) 38px;grid-template-areas:"info actions";column-gap:10px;align-items:center;padding:13px;border:1px solid #e5e9ee;border-radius:10px;background:#fff;box-shadow:0 1px 4px #00000008}
  .helper-table tbody tr:hover{background:#fff}
  .helper-table td{display:block;border:0;padding:0;font-size:.9rem;min-width:0}
  .helper-table td.helper-info-cell{grid-area:info}
  .helper-mobile-info{display:flex;flex-direction:column;align-items:flex-start;gap:5px}
  .helper-mobile-title{font-weight:650;font-size:1rem;color:#253247}
  .helper-mobile-date{color:#667085;font-size:.85rem}
  .helper-mobile-tags{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
  .helper-mobile-tags .helper-tag{margin:0}
  
  .helper-table td.helper-actions-cell{grid-area:actions;display:flex;flex-direction:column;align-items:center;justify-self:end;gap:6px;width:38px}
  .helper-table td.helper-actions-cell .helper-btn{margin:0;flex:0 0 32px}
  .helper-table-header{gap:8px}
  #toggleNewAction{width:32px;height:32px;min-width:32px;padding:0;justify-content:center;gap:0;font-size:0;border-radius:7px;flex:0 0 32px}
  #toggleNewAction i{font-size:1rem;margin:0}
 }
</style></head><body><div class="container"><header class="app-page-header"><h1>Helferlisten</h1><?php require $root.'/includes/navigation.php';?></header><main class="helper-wrap">
<?php if($error):?><div class="helper-alert error"><?=hh($error)?></div><?php endif;?><?php if($notice):?><div class="helper-alert"><?=hh($notice)?></div><?php endif;?>
<section class="helper-card"><div class="helper-table-header"><h2>Vorhandene Helferaktionen</h2><button type="button" class="helper-btn primary" id="toggleNewAction"><i class="fa-solid fa-plus"></i> Neue Helferaktion anlegen</button></div>
<div id="newActionForm" class="helper-new-form" hidden><form method="post"><input type="hidden" name="csrf" value="<?=hh($csrf)?>"><input type="hidden" name="do" value="create"><div class="helper-form-grid"><label class="helper-field">Titel<input name="title" required></label><label class="helper-field">Beginn<input type="date" name="start" required></label><label class="helper-field">Ende<input type="date" name="end" required></label></div><label class="helper-field">Beschreibung<textarea name="description" rows="2"></textarea></label><label class="helper-field">Standard-Zeitfenster (je Zeile von–bis)<textarea name="slots" rows="4" required>09:00-12:00
12:00-14:00
14:00-16:00
16:00-18:00
18:00-19:00</textarea></label><button class="helper-btn primary" type="submit">Aktion anlegen</button><button class="helper-btn" type="button" id="cancelNewAction">Abbrechen</button></form></div>
<?php if(!$actions):?><p class="helper-muted">Noch keine Helferaktionen angelegt.</p><?php else:?><div style="overflow-x:auto"><table class="helper-table"><thead><tr><th>Aktion</th><th>Zeitraum</th><th>Status</th><th>Besetzte Schichten</th><th aria-label="Aktionen"></th></tr></thead><tbody><?php foreach($actions as $a):$open=(int)$a['open_shifts'];$total=(int)$a['total_shifts'];$filled=max(0,$total-$open);$shiftClass=$open===0?'shifts-green':($open<=3?'shifts-yellow':'shifts-red');?><tr><td class="helper-info-cell"><div class="helper-mobile-info"><div class="helper-mobile-title"><?=hh($a['title'])?></div><div class="helper-mobile-date"><?=date('d.m.Y',strtotime($a['start_date']))?> – <?=date('d.m.Y',strtotime($a['end_date']))?></div><div class="helper-mobile-tags"><span class="helper-tag <?=$a['status']?>"><?=['draft'=>'Entwurf','active'=>'Öffentlich aktiv','closed'=>'Geschlossen'][$a['status']]??hh($a['status'])?></span><span class="helper-tag <?=$shiftClass?>"><?=$filled?>/<?=$total?> besetzt</span></div></div></td><td class="helper-actions-cell"><a class="helper-btn helper-icon-btn" href="helferaktion.php?aktion=<?=(int)$a['id']?>" title="Helferaktion bearbeiten" aria-label="Helferaktion bearbeiten"><i class="fa-solid fa-pen"></i></a><a class="helper-btn helper-icon-btn" href="helferbelegung.php?aktion=<?=(int)$a['id']?>" title="Belegung anzeigen" aria-label="Belegung anzeigen"><i class="fa-solid fa-table-cells-large"></i></a></td></tr><?php endforeach;?></tbody></table></div><?php endif;?></section></main></div><script>
const toggle=document.getElementById('toggleNewAction'),form=document.getElementById('newActionForm');toggle.addEventListener('click',()=>{form.hidden=!form.hidden;toggle.innerHTML=form.hidden?'<i class="fa-solid fa-plus"></i> Neue Helferaktion anlegen':'<i class="fa-solid fa-minus"></i> Formular schließen';});document.getElementById('cancelNewAction').addEventListener('click',()=>{form.hidden=true;toggle.innerHTML='<i class="fa-solid fa-plus"></i> Neue Helferaktion anlegen';});
</script></body></html>