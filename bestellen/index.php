<?php
$appRoot = dirname(__DIR__);
if (!is_file($appRoot . '/db_connection.php') && is_file($appRoot . '/intern/db_connection.php')) {
    $appRoot .= '/intern';
}
require_once $appRoot . '/db_connection.php';
require_once $appRoot . '/app/Modules/Verkauf/Support.php';
$assetPrefix = ($appRoot === dirname(__DIR__)) ? '../' : '../intern/';
$database = new Database(); $conn = $database->getConnection();
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$notice=''; $error='';
if (session_status()===PHP_SESSION_NONE) session_start();
if (empty($_SESSION['sales_order_csrf'])) $_SESSION['sales_order_csrf']=bin2hex(random_bytes(32));
$csrf=$_SESSION['sales_order_csrf'];
// Status anhand des Datums serverseitig schließen.
$conn->exec("UPDATE sales_actions SET status='closed' WHERE status='active' AND end_date < CURDATE()");
$actions=$conn->query("SELECT * FROM sales_actions WHERE status='active' AND start_date<=CURDATE() AND end_date>=CURDATE() ORDER BY start_date,id")->fetchAll(PDO::FETCH_ASSOC);
$selected=(int)($_GET['aktion']??$_POST['action_id']??0);
$action=null;
if($selected){foreach($actions as $a)if((int)$a['id']===$selected)$action=$a;}
elseif(count($actions)===1)$action=$actions[0];
if($_SERVER['REQUEST_METHOD']==='POST'){
 try{
  if(!hash_equals($csrf,(string)($_POST['csrf_token']??'')))throw new RuntimeException('Sitzung abgelaufen. Bitte lade die Seite neu.');
  if(trim((string)($_POST['website']??''))!=='')throw new RuntimeException('Die Bestellung konnte nicht verarbeitet werden.');
  $now=time(); $ip=(string)($_SERVER['REMOTE_ADDR']??'unknown'); $rateFile=sys_get_temp_dir().'/sales-order-rate-'.hash('sha256',$ip);
  $recent=is_file($rateFile)?(int)file_get_contents($rateFile):0;
  if($recent && $now-$recent<20)throw new RuntimeException('Bitte warte kurz, bevor du eine weitere Bestellung absendest.');
  if(!$action)throw new RuntimeException('Diese Verkaufsaktion ist nicht verfügbar.');
  $name=trim((string)($_POST['customer_name']??'')); $email=trim((string)($_POST['customer_email']??'')); $groupId=(int)($_POST['group_id']??0);
  if($name===''||mb_strlen($name)>160)throw new RuntimeException('Bitte gib deinen Namen ein.');
  if(!filter_var($email,FILTER_VALIDATE_EMAIL))throw new RuntimeException('Bitte gib eine gültige E-Mail-Adresse ein.');
  $q=$conn->prepare('SELECT id,name FROM sales_groups WHERE id=? AND is_active=1');$q->execute([$groupId]);$group=$q->fetch(PDO::FETCH_ASSOC);
  if(!$group)throw new RuntimeException('Bitte wähle eine gültige Gruppe.');
  $q=$conn->prepare("SELECT id,name,sale_price FROM sales_items WHERE action_id=? AND sold_to_parents=1 ORDER BY sort_order,id");$q->execute([$action['id']]);$items=$q->fetchAll(PDO::FETCH_ASSOC);
  $lines=[];$total=0;
  foreach($items as $it){$qty=(int)($_POST['qty'][$it['id']]??0);if($qty<0||$qty>100)throw new RuntimeException('Bitte prüfe die eingegebenen Mengen.');if($qty>0){$line=$qty*(int)round((float)$it['sale_price']*100);$total+=$line;$lines[]=['item'=>$it,'qty'=>$qty,'unit_cents'=>(int)round((float)$it['sale_price']*100),'line_cents'=>$line];}}
  if(!$lines)throw new RuntimeException('Bitte wähle mindestens einen Artikel aus.');
  $conn->beginTransaction();
  $q=$conn->prepare("INSERT INTO sales_orders(action_id,order_number,customer_name,customer_email,group_id,total_cents,created_at) VALUES(?,?,?,?,?,?,NOW())");
  $number=''; // Eindeutige, gut lesbare Bestellnummer wird aus ID und Jahr gebildet.
  $q->execute([$action['id'],'PENDING',$name,$email,$groupId,$total]);$orderId=(int)$conn->lastInsertId();
  $number='V'.date('y').'-'.str_pad((string)$orderId,5,'0',STR_PAD_LEFT);
  $conn->prepare('UPDATE sales_orders SET order_number=? WHERE id=?')->execute([$number,$orderId]);
  $ins=$conn->prepare('INSERT INTO sales_order_items(order_id,item_id,item_name,quantity,unit_price_cents,line_total_cents) VALUES(?,?,?,?,?,?)');
  foreach($lines as $line)$ins->execute([$orderId,$line['item']['id'],$line['item']['name'],$line['qty'],$line['unit_cents'],$line['line_cents']]);
  $conn->commit(); file_put_contents($rateFile,(string)$now,LOCK_EX);
  $mailLines=["Hallo {$name},","","vielen Dank für deine Bestellung zur Verkaufsaktion „{$action['title']}“.","Bestellnummer: {$number}","Gruppe: {$group['name']}","","Deine Bestellung:"];
  foreach($lines as $line)$mailLines[]=$line['item']['name'].' – '.$line['qty'].' × '.salesMoney($line['unit_cents']).' = '.salesMoney($line['line_cents']);
  $mailLines[]='';$mailLines[]='Gesamtbetrag: '.salesMoney($total);$mailLines[]='';$mailLines[]='Bitte bezahle den Betrag entsprechend den Zahlungsinformationen, die dir der Förderverein mitgeteilt hat.';$mailLines[]='';$mailLines[]='Viele Grüße';$mailLines[]='Förderverein des Ev. Kindergartens Duisburg-Rumeln';
  $headers="From: foerderverein@evkigarumeln.de\r\nReply-To: foerderverein@evkigarumeln.de\r\nBcc: foerderverein@evkigarumeln.de\r\nContent-Type: text/plain; charset=UTF-8\r\n";
  $subject='=?UTF-8?B?'.base64_encode('Bestellbestätigung '.$number.' – '.$action['title']).'?=';
  $mailSent = @mail($email,$subject,implode("\r\n",$mailLines),$headers);
  if(!$mailSent) error_log('Bestellbestätigung konnte nicht versendet werden; Bestellung '.$number.' wurde gespeichert.');
  $notice='Vielen Dank! Deine Bestellung wurde aufgenommen. Deine Bestellnummer lautet '.$number.'. '.($mailSent?'Eine Bestätigung wurde an deine E-Mail-Adresse versendet.':'Der E-Mail-Versand hat leider nicht funktioniert. Bitte notiere deine Bestellnummer und kontaktiere den Förderverein.');
  $_POST=[]; $selected=(int)$action['id'];
 }catch(Throwable $e){if($conn->inTransaction())$conn->rollBack();$error=$e instanceof RuntimeException?$e->getMessage():'Die Bestellung konnte nicht gespeichert werden. Bitte versuche es später erneut.';if(!($e instanceof RuntimeException))error_log('Öffentliche Bestellung: '.$e->getMessage());}
}
if(!$action && $selected && $_SERVER['REQUEST_METHOD']!=='POST')$error='Diese Verkaufsaktion ist nicht verfügbar oder der Bestellzeitraum ist beendet.';
if($action){$q=$conn->prepare("SELECT * FROM sales_items WHERE action_id=? AND sold_to_parents=1 ORDER BY sort_order,id");$q->execute([$action['id']]);$items=$q->fetchAll(PDO::FETCH_ASSOC);}
$groups=$conn->query('SELECT id,name FROM sales_groups WHERE is_active=1 ORDER BY sort_order,id')->fetchAll(PDO::FETCH_ASSOC);
function esc($v){return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');}
?><!doctype html><html lang="de"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Bestellen – Förderverein</title><link rel="stylesheet" href="<?=esc($assetPrefix)?>styles.css"><style>
body{padding:16px}.public-wrap{max-width:850px;margin:20px auto;background:white;padding:clamp(18px,4vw,36px);border-radius:12px;box-shadow:0 3px 18px #0001}.muted{color:#65717b}.field{display:grid;gap:6px;margin:14px 0}.field input,.field select{width:100%;padding:11px;border:1px solid #ccd3d8;border-radius:6px;font:inherit}.items{width:100%;border-collapse:collapse}.items th,.items td{padding:12px 8px;border-bottom:1px solid #e5e8eb}.items input{width:76px;padding:8px}.total{font-size:1.25rem;font-weight:700;text-align:right;margin:20px 0}.alert{padding:14px;border-radius:7px;margin:14px 0;background:#e9f7ed}.alert.error{background:#fff0ee}.submit{padding:12px 22px;background:#2679a8;color:white;border:0;border-radius:7px;font-size:1rem;cursor:pointer}.hp{position:absolute;left:-10000px;width:1px;height:1px;overflow:hidden}@media(max-width:600px){.items th,.items td{padding:8px 4px;font-size:.92rem}.items input{width:60px}}
</style></head><body><main class="public-wrap"><h1>Bestellen</h1><p class="muted">Förderverein des Ev. Kindergartens Duisburg-Rumeln</p>
<?php if($notice):?><div class="alert"><?=esc($notice)?></div><?php endif;?>
<?php if($error):?><div class="alert error"><?=esc($error)?></div><?php endif;?>
<?php if(!$actions):?><p>Zurzeit ist keine Verkaufsaktion für Bestellungen geöffnet.</p>
<?php elseif(!$action):?><p>Bitte wähle die Verkaufsaktion aus, für die du bestellen möchtest.</p><ul><?php foreach($actions as $a):?><li><a href="?aktion=<?=(int)$a['id']?>"><?=esc($a['title'])?></a> (<?=date('d.m.Y',strtotime($a['start_date']))?>–<?=date('d.m.Y',strtotime($a['end_date']))?>)</li><?php endforeach;?></ul>
<?php else:?><h2><?=esc($action['title'])?></h2><?php if($action['description']):?><p><?=nl2br(esc($action['description']))?></p><?php endif;?><p class="muted">Bestellzeitraum: <?=date('d.m.Y',strtotime($action['start_date']))?> bis <?=date('d.m.Y',strtotime($action['end_date']))?></p>
<form method="post" id="order-form"><input type="hidden" name="csrf_token" value="<?=esc($csrf)?>"><input type="hidden" name="action_id" value="<?=(int)$action['id']?>"><label class="hp">Webseite<input name="website" tabindex="-1" autocomplete="off"></label>
<div class="field"><label for="customer_name">Name *</label><input id="customer_name" name="customer_name" maxlength="160" required value="<?=esc($_POST['customer_name']??'')?>"></div>
<div class="field"><label for="customer_email">E-Mail-Adresse *</label><input type="email" id="customer_email" name="customer_email" maxlength="190" required value="<?=esc($_POST['customer_email']??'')?>"></div>
<div class="field"><label for="group_id">Gruppe *</label><select id="group_id" name="group_id" required><option value="">Bitte auswählen</option><?php foreach($groups as $g):?><option value="<?=(int)$g['id']?>" <?=((int)($_POST['group_id']??0)===(int)$g['id'])?'selected':''?>><?=esc($g['name'])?></option><?php endforeach;?></select></div>
<h3>Artikel</h3><table class="items"><thead><tr><th>Artikel</th><th>Einzelpreis</th><th>Menge</th><th>Summe</th></tr></thead><tbody><?php foreach($items as $it):?><tr data-price="<?=number_format((float)$it['sale_price'],2,'.','')?>"><td><?=esc($it['name'])?></td><td><?=salesMoney((int)round((float)$it['sale_price']*100))?></td><td><input type="number" name="qty[<?=(int)$it['id']?>]" min="0" max="100" step="1" value="<?=esc($_POST['qty'][$it['id']]??0)?>" aria-label="Menge <?=esc($it['name'])?>"></td><td class="line-total">0,00 €</td></tr><?php endforeach;?></tbody></table><div class="total">Gesamt: <span id="grand-total">0,00 €</span></div><button class="submit" type="submit">Bestellung verbindlich absenden</button><p class="muted">Deine Bestellung kann nach dem Absenden nicht selbst geändert werden. Wende dich bei Korrekturen bitte an den Förderverein.</p></form>
<script>(()=>{const fmt=n=>n.toLocaleString('de-DE',{style:'currency',currency:'EUR'});function calc(){let total=0;document.querySelectorAll('tbody tr[data-price]').forEach(row=>{const input=row.querySelector('input');let q=Math.max(0,Math.min(100,parseInt(input.value||'0',10)||0));const line=Math.round(Number(row.dataset.price)*100)*q;total+=line;row.querySelector('.line-total').textContent=fmt(line/100)});document.getElementById('grand-total').textContent=fmt(total/100)}document.querySelectorAll('tbody input').forEach(i=>i.addEventListener('input',calc));document.getElementById('order-form').addEventListener('submit',()=>{const b=document.querySelector('.submit');b.disabled=true;b.textContent='Wird gesendet …'});calc()})();</script><?php endif;?></main></body></html>
