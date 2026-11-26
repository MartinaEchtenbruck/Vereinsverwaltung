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
// Zahlungshinweise vor der POST-Verarbeitung laden, damit sie bereits beim
// Erstellen der Bestellbestätigungs-Mail verfügbar sind.
$paymentTextQuery=$conn->prepare('SELECT setting_value FROM sales_settings WHERE setting_key=?');
$paymentTextQuery->execute(['order_confirmation_payment_text']);
$paymentConfirmationText=$paymentTextQuery->fetchColumn();
if($paymentConfirmationText===false){
    $paymentConfirmationText='Bitte bezahle den Betrag entsprechend den Zahlungsinformationen, die dir der Förderverein mitgeteilt hat.';
}

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
  $q=$conn->prepare("SELECT id,name,description,sale_price_cents FROM sales_items WHERE action_id=? AND sold_to_parents=1 ORDER BY sort_order,id");$q->execute([$action['id']]);$items=$q->fetchAll(PDO::FETCH_ASSOC);
  $submittedCart=json_decode((string)($_POST['cart_payload']??'[]'),true);
  if(!is_array($submittedCart))throw new RuntimeException('Der Warenkorb konnte nicht gelesen werden. Bitte prüfe deine Auswahl.');
  $lines=[];$total=0;
  $itemById=[];foreach($items as $it)$itemById[(int)$it['id']]=$it;
  foreach($submittedCart as $entry){
   if(!is_array($entry))continue;
   $itemId=(int)($entry['item_id']??0);$qty=(int)($entry['quantity']??0);
   if(!isset($itemById[$itemId]))throw new RuntimeException('Ein ausgewählter Artikel ist nicht mehr verfügbar.');
   if($qty<1||$qty>100)throw new RuntimeException('Bitte prüfe die Mengen im Warenkorb (1 bis 100 je Position).');
   $it=$itemById[$itemId];$selectedVariants=(array)($entry['variants']??[]);
   $oq=$conn->prepare('SELECT o.id,o.property_name,v.value_text FROM sales_item_options o JOIN sales_item_option_values v ON v.option_id=o.id WHERE o.item_id=? ORDER BY o.property_no,v.sort_order,v.id');$oq->execute([$itemId]);
   $valid=[];$names=[];foreach($oq->fetchAll(PDO::FETCH_ASSOC) as $r){$valid[(int)$r['id']][]=$r['value_text'];$names[(int)$r['id']]=$r['property_name'];}
   $chosen=[];foreach($valid as $optionId=>$values){$value=trim((string)($selectedVariants[$optionId]??''));if($value===''||!in_array($value,$values,true))throw new RuntimeException('Bitte wähle für „'.$it['name'].'“ alle erforderlichen Ausprägungen aus.');$chosen[]=$names[$optionId].': '.$value;}
   $variantText=implode(' · ',$chosen);$unit=(int)$it['sale_price_cents'];$line=$qty*$unit;$total+=$line;
   $lines[]=['item'=>$it,'qty'=>$qty,'variant_text'=>$variantText,'unit_cents'=>$unit,'line_cents'=>$line];
  }
  if(!$lines)throw new RuntimeException('Bitte wähle mindestens einen Artikel aus.');
  // Gemeinsame Sperre verhindert, dass parallele Bestellungen den Nummernstand gleichzeitig zurücksetzen.
  $numberingLock=(int)$conn->query("SELECT GET_LOCK('sales_orders_numbering',10)")->fetchColumn();
  if($numberingLock!==1)throw new RuntimeException('Die Bestellnummer konnte gerade nicht reserviert werden. Bitte versuche es erneut.');
  try {
   // Nur wenn wirklich keinerlei Bestellung mehr existiert, darf die AUTO_INCREMENT-Folge neu beginnen.
   $existingOrders=(int)$conn->query('SELECT COUNT(*) FROM sales_orders')->fetchColumn();
   if($existingOrders===0)$conn->exec('ALTER TABLE sales_orders AUTO_INCREMENT=1');
   $conn->beginTransaction();
   $q=$conn->prepare("INSERT INTO sales_orders(action_id,order_number,customer_name,customer_email,group_id,total_cents,created_at) VALUES(?,?,?,?,?,?,NOW())");
   $number=''; // Eindeutige, gut lesbare Bestellnummer wird aus ID und Jahr gebildet.
   $q->execute([$action['id'],'PENDING',$name,$email,$groupId,$total]);$orderId=(int)$conn->lastInsertId();
   $number='V'.date('y').'-'.str_pad((string)$orderId,5,'0',STR_PAD_LEFT);
   $conn->prepare('UPDATE sales_orders SET order_number=? WHERE id=?')->execute([$number,$orderId]);
   $ins=$conn->prepare('INSERT INTO sales_order_items(order_id,item_id,item_name,variant_text,quantity,unit_price_cents,line_total_cents) VALUES(?,?,?,?,?,?,?)');
   foreach($lines as $line)$ins->execute([$orderId,$line['item']['id'],$line['item']['name'],$line['variant_text'],$line['qty'],$line['unit_cents'],$line['line_cents']]);
   $conn->commit(); file_put_contents($rateFile,(string)$now,LOCK_EX);
  } finally {
   $conn->query("SELECT RELEASE_LOCK('sales_orders_numbering')");
  }
  // Neue Bestellung an alle aktiven Benutzer mit Verkaufsberechtigung melden.
  // Fehler bei Benachrichtigung oder Push dürfen die bereits gespeicherte Bestellung nicht beeinträchtigen.
  try {
   require_once $appRoot.'/app/Core/Notifications.php';
   $notificationUsers=fvNotificationRecipients($conn,['Verkauf']);
   $notificationTitle='Neue Bestellung eingegangen';
   $notificationMessage='Bestellung '.$number.' von '.$name.' ('.$group['name'].') – '.salesMoney($total).'.';
   $notificationUrl='verkauf_uebersicht.php?'.http_build_query(['aktion'=>(int)$action['id'],'open_order'=>$orderId]);
   foreach($notificationUsers as $notificationUserId){
    fvCreateNotification($conn,$notificationUserId,'new_sales_order','sales_order',$orderId,'new',$notificationTitle,$notificationMessage,$notificationUrl);
   }
  } catch(Throwable $notificationError) {
   error_log('Neue Bestellung: Benachrichtigung konnte nicht erstellt werden (Bestellung '.$number.'): '.$notificationError->getMessage());
  }
  $mailLines=["Hallo {$name},","","vielen Dank für deine Bestellung zur Verkaufsaktion „{$action['title']}“.","Bestellnummer: {$number}","Gruppe: {$group['name']}","","Deine Bestellung:"];
  foreach($lines as $line)$mailLines[]=$line['item']['name'].($line['variant_text']!==''?' ('.$line['variant_text'].')':'').' – '.$line['qty'].' × '.salesMoney($line['unit_cents']).' = '.salesMoney($line['line_cents']);
  $mailLines[]='';$mailLines[]='Gesamtbetrag: '.salesMoney($total);if($paymentConfirmationText!==''){$mailLines[]='';foreach(preg_split('/\r\n|\r|\n/',$paymentConfirmationText) as $paymentLine)$mailLines[]=$paymentLine;}$mailLines[]='';$mailLines[]='Viele Grüße';$mailLines[]='Förderverein des Ev. Kindergartens Duisburg-Rumeln';
  $headers="From: foerderverein@evkigarumeln.de\r\nReply-To: foerderverein@evkigarumeln.de\r\nBcc: foerderverein@evkigarumeln.de\r\nContent-Type: text/plain; charset=UTF-8\r\n";
  $subject='=?UTF-8?B?'.base64_encode('Bestellbestätigung '.$number.' – '.$action['title']).'?=';
  $mailSent = @mail($email,$subject,implode("\r\n",$mailLines),$headers);
  if(!$mailSent) error_log('Bestellbestätigung konnte nicht versendet werden; Bestellung '.$number.' wurde gespeichert.');
  $notice='Vielen Dank! Deine Bestellung wurde aufgenommen. Deine Bestellnummer lautet '.$number.'. '.($mailSent?'Eine Bestätigung wurde an deine E-Mail-Adresse versendet.':'Der E-Mail-Versand hat leider nicht funktioniert. Bitte notiere deine Bestellnummer und kontaktiere den Förderverein.');
  $_POST=[]; $selected=(int)$action['id'];
 }catch(Throwable $e){if($conn->inTransaction())$conn->rollBack();$error=$e instanceof RuntimeException?$e->getMessage():'Die Bestellung konnte nicht gespeichert werden. Bitte versuche es später erneut.';if(!($e instanceof RuntimeException))error_log('Öffentliche Bestellung: '.$e->getMessage());}
}
if(!$action && $selected && $_SERVER['REQUEST_METHOD']!=='POST')$error='Diese Verkaufsaktion ist nicht verfügbar oder der Bestellzeitraum ist beendet.';
if($action){$q=$conn->prepare("SELECT * FROM sales_items WHERE action_id=? AND sold_to_parents=1 ORDER BY sort_order,id");$q->execute([$action['id']]);$items=$q->fetchAll(PDO::FETCH_ASSOC);$itemOptions=[];foreach($items as $it){$oq=$conn->prepare('SELECT o.id,o.property_name,v.value_text FROM sales_item_options o LEFT JOIN sales_item_option_values v ON v.option_id=o.id WHERE o.item_id=? ORDER BY o.property_no,v.sort_order,v.id');$oq->execute([$it['id']]);foreach($oq->fetchAll(PDO::FETCH_ASSOC) as $r){$oid=(int)$r['id'];if(!isset($itemOptions[$it['id']][$oid]))$itemOptions[$it['id']][$oid]=['name'=>$r['property_name'],'values'=>[]];if($r['value_text']!==null)$itemOptions[$it['id']][$oid]['values'][]=$r['value_text'];}}}
$groups=$conn->query('SELECT id,name FROM sales_groups WHERE is_active=1 ORDER BY sort_order,id')->fetchAll(PDO::FETCH_ASSOC);

function esc($v){return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');}
?><!doctype html><html lang="de"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?= $action ? esc($action['title']) : 'Bestellen – Förderverein' ?></title><link rel="stylesheet" href="<?=esc($assetPrefix)?>styles.css"><style>
body{padding:16px}.public-wrap{max-width:850px;margin:20px auto;background:white;padding:clamp(18px,4vw,36px);border-radius:12px;box-shadow:0 3px 18px #0001}.muted{color:#65717b}.field{display:grid;gap:6px;margin:14px 0}.field input,.field select{width:100%;padding:11px;border:1px solid #ccd3d8;border-radius:6px;font:inherit}.product-list{display:grid;gap:14px}.product{border:1px solid #e1e6ea;border-radius:9px;padding:15px;display:grid;grid-template-columns:minmax(0,1fr) auto;gap:12px;align-items:start}.product-info{grid-column:1/-1}.product-info p{margin:5px 0}.price{font-weight:600}.variant-group{border:0;padding:0;margin:0;min-width:0}.variant-group legend{font-weight:600;margin-bottom:7px}.variant-choices{display:flex;gap:7px;flex-wrap:wrap}.variant-choice input{position:absolute;opacity:0;pointer-events:none}.variant-choice span{display:inline-block;border:1px solid #cbd3d9;border-radius:7px;padding:8px 12px;cursor:pointer;background:#fff}.variant-choice input:checked+span{border-color:#2679a8;background:#e8f3f9;box-shadow:inset 0 0 0 1px #2679a8}.variant-choice input:focus-visible+span{outline:2px solid #2679a8;outline-offset:2px}.quantity{display:flex;align-items:center;gap:8px;justify-self:end}.quantity input{width:76px;padding:8px;border:1px solid #ccd3d8;border-radius:6px}.line-total{font-weight:600;text-align:right;align-self:center}.total{font-size:1.25rem;font-weight:700;text-align:right;margin:20px 0}.alert{padding:14px;border-radius:7px;margin:14px 0;background:#e9f7ed}.alert.error{background:#fff0ee}.submit{padding:12px 22px;background:#2679a8;color:white;border:0;border-radius:7px;font-size:1rem;cursor:pointer}.hp{position:absolute;left:-10000px;width:1px;height:1px;overflow:hidden}@media(max-width:600px){.product{grid-template-columns:1fr auto;padding:12px}.variant-group{grid-column:1/-1}.quantity{grid-column:1}.line-total{grid-column:2;grid-row:3}}

.action-intro{margin-top:0;font-weight:600}.action-description{margin:8px 0 18px}
.product{grid-template-columns:minmax(0,1fr);gap:12px}
.product-info{grid-column:auto}
.variant-group{display:block;width:100%;margin:0}
.variant-choices{justify-content:flex-start}
.product-actions{display:flex;align-items:center;justify-content:flex-end;gap:12px;width:100%}
.quantity{justify-self:auto;margin:0}
.quantity input{width:76px}
.add-to-cart{padding:9px 14px;cursor:pointer;transition:background-color .15s ease,box-shadow .15s ease,transform .08s ease}
.add-to-cart:hover{filter:none;transform:none;box-shadow:0 2px 6px #0002}
.add-to-cart:active{transform:translateY(1px) scale(.98);box-shadow:none}
.add-to-cart.is-added{background:#287a48!important;border-color:#287a48!important;box-shadow:0 0 0 3px #287a4826}
.add-to-cart:focus-visible{outline:3px solid #2679a866;outline-offset:2px}
.variant-group.missing-choice legend{color:#b42318}
.cart-section{margin-top:24px;border-top:1px solid #e1e6ea;padding-top:18px}
.cart-row{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:12px 0;border-bottom:1px solid #e8ecef}
.cart-product{min-width:0}.cart-controls{display:flex;align-items:center;gap:12px}
.cart-controls input{width:68px;padding:7px;border:1px solid #ccd3d8;border-radius:6px;font:inherit}
.remove-cart{border:0;background:transparent;color:#a33;cursor:pointer;text-decoration:underline;font:inherit}
.total{padding-top:10px;border-top:1px solid #e1e6ea}
.submit-row{display:flex;justify-content:flex-end}
.submit{padding:12px 22px;font-weight:600;cursor:pointer}
.submit:disabled{opacity:.65;cursor:wait}
.order-note{margin-top:12px;font-size:.92rem}
@media(max-width:600px){.product{grid-template-columns:1fr}.product-actions{justify-content:space-between}.cart-row{align-items:flex-start;flex-direction:column}.cart-controls{width:100%;justify-content:space-between}}
</style></head><body><main class="public-wrap"><h1><?= $action ? esc($action['title']) : 'Bestellen' ?></h1><?php if(!$action):?><p class="muted">Förderverein des Ev. Kindergartens Duisburg-Rumeln</p><?php endif;?>
<?php if($notice):?><div class="alert"><?=esc($notice)?></div><?php endif;?>
<?php if($error):?><div class="alert error"><?=esc($error)?></div><?php endif;?>
<?php if(!$actions):?><p>Zurzeit ist keine Verkaufsaktion für Bestellungen geöffnet.</p>
<?php elseif(!$action):?><p>Bitte wähle die Verkaufsaktion aus, für die du bestellen möchtest.</p><ul><?php foreach($actions as $a):?><li><a href="?aktion=<?=(int)$a['id']?>"><?=esc($a['title'])?></a> (<?=date('d.m.Y',strtotime($a['start_date']))?>–<?=date('d.m.Y',strtotime($a['end_date']))?>)</li><?php endforeach;?></ul>
<?php else:?><p class="muted action-intro">Ein Verkaufsaktion des Förderverein des Ev. Kindergartens Duisburg-Rumeln:</p><?php if($action['description']):?><p class="action-description"><?=nl2br(esc($action['description']))?></p><?php endif;?><p class="muted">Bestellzeitraum: <?=date('d.m.Y',strtotime($action['start_date']))?> bis <?=date('d.m.Y',strtotime($action['end_date']))?></p>
<form method="post" id="order-form"><input type="hidden" name="csrf_token" value="<?=esc($csrf)?>"><input type="hidden" name="action_id" value="<?=(int)$action['id']?>"><input type="hidden" name="cart_payload" id="cart-payload" value="[]"><label class="hp">Webseite<input name="website" tabindex="-1" autocomplete="off"></label>
<div class="field"><label for="customer_name">Name *</label><input id="customer_name" name="customer_name" maxlength="160" required value="<?=esc($_POST['customer_name']??'')?>"></div>
<div class="field"><label for="customer_email">E-Mail-Adresse *</label><input type="email" id="customer_email" name="customer_email" maxlength="190" required value="<?=esc($_POST['customer_email']??'')?>"></div>
<div class="field"><label for="group_id">Meine Bestellung soll hinterlegt werden in *</label><select id="group_id" name="group_id" required><option value="">Bitte auswählen</option><?php foreach($groups as $g):?><option value="<?=(int)$g['id']?>" <?=((int)($_POST['group_id']??0)===(int)$g['id'])?'selected':''?>><?=esc($g['name'])?></option><?php endforeach;?></select></div>
<h2>Artikel</h2><div class="product-list"><?php foreach($items as $it):$opts=$itemOptions[$it['id']]??[];?><section class="product" data-item-id="<?=(int)$it['id']?>" data-item-name="<?=esc($it['name'])?>" data-price="<?=(int)$it['sale_price_cents']?>"><div class="product-info"><strong><?=esc($it['name'])?></strong><?php if(!empty($it['description'])):?><p class="muted"><?=nl2br(esc($it['description']))?></p><?php endif;?><span class="price"><?=salesMoney((int)$it['sale_price_cents'])?></span></div><?php foreach($opts as $oid=>$op):?><fieldset class="variant-group"><legend><?=esc($op['name'])?> *</legend><div class="variant-choices"><?php foreach($op['values'] as $idx=>$value):?><label class="variant-choice"><input type="radio" name="variant[<?=(int)$it['id']?>][<?=$oid?>]" value="<?=esc($value)?>"><span><?=esc($value)?></span></label><?php endforeach;?></div></fieldset><?php endforeach;?><div class="product-actions"><label class="quantity">Menge <input type="number" class="pick-quantity" min="1" max="100" step="1" value="1" aria-label="Menge <?=esc($it['name'])?>"></label><button type="button" class="btn btn-primary add-to-cart">In den Warenkorb</button></div></section><?php endforeach;?></div>
<section class="cart-section"><h2>Mein Warenkorb</h2><div id="cart-empty" class="muted">Du hast noch keine Artikel ausgewählt.</div><div id="cart-items"></div><div class="total">Gesamtbetrag: <span id="grand-total">0,00 €</span></div></section>
<div class="submit-row"><button class="submit btn btn-primary" type="submit">Bestellung verbindlich absenden</button></div><p class="muted order-note">Nach dem Absenden der Bestellung bekommst Du eine Bestellbestätigung mit den Zahlungshinweisen per E-Mail. Deine Bestellung kann nach dem Absenden nicht selbst geändert werden. Wende dich bei Korrekturen bitte an den Förderverein.</p></form>
<script>(()=>{const fmt=n=>(n/100).toLocaleString('de-DE',{style:'currency',currency:'EUR'});const cart=[];const list=document.getElementById('cart-items'),empty=document.getElementById('cart-empty'),total=document.getElementById('grand-total'),payload=document.getElementById('cart-payload');function render(){list.innerHTML='';let sum=0;cart.forEach((row,index)=>{const line=row.price*row.quantity;sum+=line;const el=document.createElement('div');el.className='cart-row';const details=Object.entries(row.variants).map(([k,v])=>v.name+': '+v.value).join(' · ');const info=document.createElement('div');info.className='cart-product';const title=document.createElement('strong');title.textContent=row.name;info.appendChild(title);if(details){const small=document.createElement('div');small.className='muted';small.textContent=details;info.appendChild(small)}const controls=document.createElement('div');controls.className='cart-controls';const qty=document.createElement('input');qty.type='number';qty.min='1';qty.max='100';qty.value=row.quantity;qty.setAttribute('aria-label','Menge für '+row.name);qty.addEventListener('change',()=>{row.quantity=Math.max(1,Math.min(100,parseInt(qty.value||'1',10)||1));render()});const remove=document.createElement('button');remove.type='button';remove.className='remove-cart';remove.textContent='Entfernen';remove.addEventListener('click',()=>{cart.splice(index,1);render()});const price=document.createElement('strong');price.textContent=fmt(line);controls.append(qty,price,remove);el.append(info,controls);list.appendChild(el)});empty.hidden=cart.length>0;total.textContent=fmt(sum);payload.value=JSON.stringify(cart.map(r=>({item_id:r.itemId,quantity:r.quantity,variants:Object.fromEntries(Object.entries(r.variants).map(([id,v])=>[id,v.value]))})));}document.querySelectorAll('.product').forEach(card=>{card.querySelector('.add-to-cart').addEventListener('click',()=>{const variants={};let missing=false;card.querySelectorAll('.variant-group').forEach(group=>{const checked=group.querySelector('input:checked');if(!checked){missing=true;group.classList.add('missing-choice')}else{group.classList.remove('missing-choice');const id=checked.name.match(/\[(\d+)\]\[(\d+)\]/)[2];variants[id]={name:group.querySelector('legend').textContent.replace(/\s*\*$/,''),value:checked.value}}});if(missing){card.querySelector('.missing-choice').scrollIntoView({behavior:'smooth',block:'center'});return}const q=Math.max(1,Math.min(100,parseInt(card.querySelector('.pick-quantity').value||'1',10)||1));const row={itemId:Number(card.dataset.itemId),name:card.dataset.itemName,price:Number(card.dataset.price),quantity:q,variants};const signature=JSON.stringify([row.itemId,Object.entries(variants).map(([id,v])=>[id,v.value])]);const existing=cart.find(r=>JSON.stringify([r.itemId,Object.entries(r.variants).map(([id,v])=>[id,v.value])])===signature);if(existing)existing.quantity=Math.min(100,existing.quantity+q);else cart.push(row);render();card.querySelectorAll('.variant-group').forEach(group=>{group.querySelectorAll('input[type=radio]').forEach(input=>{input.checked=false});group.classList.remove('missing-choice')});const button=card.querySelector('.add-to-cart');if(button._feedbackTimer)clearTimeout(button._feedbackTimer);button.textContent='Hinzugefügt ✓';button.classList.add('is-added');button.setAttribute('aria-label','Artikel wurde zum Warenkorb hinzugefügt');button._feedbackTimer=setTimeout(()=>{button.textContent='In den Warenkorb';button.classList.remove('is-added');button.removeAttribute('aria-label');button._feedbackTimer=null},1400)})});document.getElementById('order-form').addEventListener('submit',e=>{if(!cart.length){e.preventDefault();alert('Bitte lege mindestens einen Artikel in den Warenkorb.');return}const b=document.querySelector('.submit');b.disabled=true;b.textContent='Wird gesendet …'});render()})();</script><?php endif;?></main></body></html>
