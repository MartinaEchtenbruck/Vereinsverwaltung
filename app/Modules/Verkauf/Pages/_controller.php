<?php
if (!defined('APP_ENTRY_POINT')) { http_response_code(404); exit; }
$appRoot=dirname(__DIR__,4);require_once $appRoot.'/auth.php';checkPermission('Verkauf');
require_once $appRoot.'/db_connection.php';require_once dirname(__DIR__).'/Support.php';
$db=new Database();$conn=$db->getConnection();$conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
if(empty($_SESSION['sales_csrf']))$_SESSION['sales_csrf']=bin2hex(random_bytes(32));$csrf=$_SESSION['sales_csrf'];
function sh($v){return salesH($v);} function parseSalesOptions($names,$values){$out=[];foreach([1,2] as $n){$label=trim((string)($names[$n]??''));$raw=(string)($values[$n]??'');$vals=array_values(array_unique(array_filter(array_map('trim',preg_split('/[,\r\n]+/',$raw)),fn($v)=>$v!=='')));if($label===''&&$vals)throw new RuntimeException('Bitte gib für jede Ausprägung eine Bezeichnung an.');if($label!==''&&!$vals)throw new RuntimeException('Bitte gib mindestens einen Wert für „'.$label.'“ an.');if($label!=='' )$out[]=['name'=>$label,'values'=>$vals];}return $out;} function saveSalesOptions(PDO $conn,int $itemId,array $options){$conn->prepare('DELETE FROM sales_item_options WHERE item_id=?')->execute([$itemId]);$ins=$conn->prepare('INSERT INTO sales_item_options(item_id,property_no,property_name) VALUES(?,?,?)');$val=$conn->prepare('INSERT INTO sales_item_option_values(option_id,value_text,sort_order) VALUES(?,?,?)');foreach($options as $i=>$option){$ins->execute([$itemId,$i+1,$option['name']]);$oid=(int)$conn->lastInsertId();foreach($option['values'] as $j=>$value)$val->execute([$oid,$value,$j+1]);}} function loadSalesOptions(PDO $conn,int $itemId){$q=$conn->prepare('SELECT o.id,o.property_no,o.property_name,v.id value_id,v.value_text FROM sales_item_options o LEFT JOIN sales_item_option_values v ON v.option_id=o.id WHERE o.item_id=? ORDER BY o.property_no,v.sort_order,v.id');$q->execute([$itemId]);$result=[];foreach($q->fetchAll(PDO::FETCH_ASSOC) as $r){$n=(int)$r['property_no'];if(!isset($result[$n]))$result[$n]=['id'=>(int)$r['id'],'name'=>$r['property_name'],'values'=>[]];if($r['value_id']!==null)$result[$n]['values'][]=$r['value_text'];}return $result;} function goSales($msg='', $backToAction=true){global $salesPage;$aid=$backToAction?(int)($_POST['action_id']??0):0;$params=[];if($aid)$params['aktion']=$aid;if($msg)$params['meldung']=$msg;$targetPage=$backToAction?$salesPage:'list';$target=$targetPage==='overview'?'verkauf_uebersicht.php':'verkauf.php';header('Location: '.$target.($params?'?'.http_build_query($params):''));exit;}
$notice=(string)($_GET['meldung']??'');$error='';
$conn->exec("UPDATE sales_actions SET status='closed' WHERE status='active' AND end_date < CURDATE()");
try{
 if($_SERVER['REQUEST_METHOD']==='POST'){
  if(!hash_equals($csrf,(string)($_POST['csrf_token']??'')))throw new RuntimeException('Sitzung abgelaufen. Bitte Seite neu laden.');
  $act=(string)($_POST['do']??'');$aid=(int)($_POST['action_id']??0);
  if($act==='create_action'){
   $title=trim((string)$_POST['title']);$start=(string)$_POST['start_date'];$end=(string)$_POST['end_date'];$desc=trim((string)($_POST['description']??''));
   if($title===''||!preg_match('/^\d{4}-\d{2}-\d{2}$/',$start)||!preg_match('/^\d{4}-\d{2}-\d{2}$/',$end)||$end<$start)throw new RuntimeException('Bitte Titel und einen gültigen Zeitraum eingeben.');
   $q=$conn->prepare("INSERT INTO sales_actions(title,description,start_date,end_date,status,shipping_cents,created_by) VALUES(?,?,?,?, 'draft',0,?)");$q->execute([$title,$desc,$start,$end,(int)$_SESSION['user_id']]);goSales('Verkaufsaktion wurde angelegt.');
  }
  if($act==='save_action'){
   $q=$conn->prepare('SELECT * FROM sales_actions WHERE id=?');$q->execute([$aid]);$a=$q->fetch(PDO::FETCH_ASSOC);if(!$a)throw new RuntimeException('Verkaufsaktion nicht gefunden.');
   if($a['status']==='completed')throw new RuntimeException('Abgeschlossene Aktionen sind eingefroren.');
   $title=trim((string)$_POST['title']);$desc=trim((string)($_POST['description']??''));$start=(string)$_POST['start_date'];$end=(string)$_POST['end_date'];$status=(string)($_POST['status']??$a['status']);
   if($title===''||!preg_match('/^\d{4}-\d{2}-\d{2}$/',$start)||!preg_match('/^\d{4}-\d{2}-\d{2}$/',$end)||!in_array($status,['draft','active','closed'],true)||$end<$start)throw new RuntimeException('Bitte prüfe Titel, Zeitraum und Status.');
   if($end<date('Y-m-d')&&$status==='active')$status='closed';
   $q=$conn->prepare('UPDATE sales_actions SET title=?,description=?,start_date=?,end_date=?,status=? WHERE id=?');$q->execute([$title,$desc,$start,$end,$status,$aid]);goSales('Aktion wurde gespeichert.');
  }
  if($act==='add_item'){
   $q=$conn->prepare('SELECT status FROM sales_actions WHERE id=?');$q->execute([$aid]);$st=$q->fetchColumn();if(!$st||$st==='completed')throw new RuntimeException('Artikel können nicht hinzugefügt werden.');
   $name=trim((string)$_POST['item_name']);$description=trim((string)($_POST['item_description']??''));$purchase=(int)round((float)$_POST['purchase_price']*100);$sale=(int)round((float)$_POST['sale_price']*100);$parent=isset($_POST['sold_to_parents'])?1:0;$options=parseSalesOptions((array)($_POST['property_name']??[]),(array)($_POST['property_values']??[]));
   if($name===''||$purchase<0||$sale<0)throw new RuntimeException('Bitte Artikel und gültige Preise angeben.');
   $conn->beginTransaction();$q=$conn->prepare('INSERT INTO sales_items(action_id,name,description,purchase_price_cents,sale_price_cents,sold_to_parents) VALUES(?,?,?,?,?,?)');$q->execute([$aid,$name,$description,$purchase,$sale,$parent]);saveSalesOptions($conn,(int)$conn->lastInsertId(),$options);$conn->commit();goSales('Artikel wurde hinzugefügt.');
  }
  if($act==='update_item'){
   $iid=(int)$_POST['item_id'];$q=$conn->prepare('SELECT a.status FROM sales_items i JOIN sales_actions a ON a.id=i.action_id WHERE i.id=? AND a.id=?');$q->execute([$iid,$aid]);$st=$q->fetchColumn();if(!$st||$st==='completed')throw new RuntimeException('Artikel kann nicht bearbeitet werden.');
   $name=trim((string)$_POST['item_name']);$description=trim((string)($_POST['item_description']??''));$purchase=(int)round((float)$_POST['purchase_price']*100);$sale=(int)round((float)$_POST['sale_price']*100);$parent=isset($_POST['sold_to_parents'])?1:0;$options=parseSalesOptions((array)($_POST['property_name']??[]),(array)($_POST['property_values']??[]));
   if($name===''||$purchase<0||$sale<0)throw new RuntimeException('Bitte gültige Artikelangaben eingeben.');
   $conn->beginTransaction();$conn->prepare('UPDATE sales_items SET name=?,description=?,purchase_price_cents=?,sale_price_cents=?,sold_to_parents=? WHERE id=?')->execute([$name,$description,$purchase,$sale,$parent,$iid]);saveSalesOptions($conn,$iid,$options);$conn->commit();goSales('Artikel wurde aktualisiert.');
  }
  if($act==='delete_item'){
   $q=$conn->prepare('SELECT a.status FROM sales_items i JOIN sales_actions a ON a.id=i.action_id WHERE i.id=? AND a.id=?');$q->execute([(int)$_POST['item_id'],$aid]);$st=$q->fetchColumn();if(!$st||$st==='completed')throw new RuntimeException('Artikel kann nicht gelöscht werden.');
   $used=$conn->prepare('SELECT COUNT(*) FROM sales_order_items WHERE item_id=?');$used->execute([(int)$_POST['item_id']]);if((int)$used->fetchColumn()>0)throw new RuntimeException('Der Artikel ist bereits bestellt worden und kann deshalb nicht gelöscht werden. Du kannst den Verkauf an Eltern stattdessen deaktivieren.');
   $conn->prepare('DELETE FROM sales_items WHERE id=?')->execute([(int)$_POST['item_id']]);goSales('Artikel wurde gelöscht.');
  }
  if($act==='save_internal'){
   $q=$conn->prepare('SELECT status FROM sales_actions WHERE id=?');$q->execute([$aid]);$st=$q->fetchColumn();if(!$st||$st==='completed')throw new RuntimeException('Interne Bestellung ist eingefroren.');
   $conn->beginTransaction();$conn->prepare('DELETE FROM sales_internal_order_items WHERE action_id=?')->execute([$aid]);$ins=$conn->prepare('INSERT INTO sales_internal_order_items(action_id,item_id,quantity) VALUES(?,?,?)');
   foreach((array)($_POST['internal_qty']??[]) as $iid=>$qty){$iid=(int)$iid;$qty=(int)$qty;if($qty<0||$qty>100000)throw new RuntimeException('Ungültige interne Menge.');if($qty){$q=$conn->prepare('SELECT id FROM sales_items WHERE id=? AND action_id=?');$q->execute([$iid,$aid]);if($q->fetchColumn())$ins->execute([$aid,$iid,$qty]);}}
   $conn->commit();goSales('Interne Bestellung wurde gespeichert.');
  }
  if($act==='order_status'){
   $field=(string)($_POST['field']??'');if(!in_array($field,['is_paid','is_packed','is_issued'],true))throw new RuntimeException('Ungültiger Status.');
   $conn->prepare("UPDATE sales_orders SET {$field}=? WHERE id=? AND action_id=?")->execute([isset($_POST['value'])?1:0,(int)$_POST['order_id'],$aid]);goSales('Bestellstatus aktualisiert.');
  }
  if($act==='delete_order'){$conn->prepare('DELETE FROM sales_orders WHERE id=? AND action_id=?')->execute([(int)$_POST['order_id'],$aid]);goSales('Bestellung wurde gelöscht und aus allen Berechnungen entfernt.');}
  if($act==='save_group'){$name=trim((string)$_POST['group_name']);$gid=(int)($_POST['group_id']??0);if($name==='')throw new RuntimeException('Gruppenname darf nicht leer sein.');if($gid)$conn->prepare('UPDATE sales_groups SET name=? WHERE id=?')->execute([$name,$gid]);else{$conn->prepare('INSERT INTO sales_groups(name,sort_order) VALUES(?,?)')->execute([$name,(int)$_POST['sort_order']]);}goSales('Gruppe gespeichert.');}
  if($act==='save_general_settings'){$paymentText=trim((string)($_POST['order_confirmation_payment_text']??''));if(mb_strlen($paymentText)>5000)throw new RuntimeException('Der Zahlungshinweis darf höchstens 5.000 Zeichen lang sein.');$conn->prepare('INSERT INTO sales_settings(setting_key,setting_value) VALUES(?,?) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)')->execute(['order_confirmation_payment_text',$paymentText]);goSales('Allgemeine Einstellungen wurden gespeichert.',false);}
  if($act==='complete_action'){
   $q=$conn->prepare('SELECT status FROM sales_actions WHERE id=?');$q->execute([$aid]);$currentStatus=$q->fetchColumn();if(!$currentStatus)throw new RuntimeException('Aktion nicht gefunden.');if(!in_array($currentStatus,['active','closed'],true))throw new RuntimeException('Nur aktive Aktionen oder Aktionen mit erreichtem Bestellschluss können abgeschlossen werden.');
   $conn->prepare("UPDATE sales_actions SET status='completed',completed_at=NOW(),completed_by=? WHERE id=?")->execute([(int)$_SESSION['user_id'],$aid]);goSales('Aktion abgeschlossen. Die Artikelpreise sind nun eingefroren.');
  }
  if($act==='delete_action'){
   $q=$conn->prepare('SELECT id FROM sales_actions WHERE id=?');$q->execute([$aid]);if(!$q->fetchColumn())throw new RuntimeException('Verkaufsaktion nicht gefunden.');
   $conn->prepare('DELETE FROM sales_actions WHERE id=?')->execute([$aid]);goSales('Verkaufsaktion und zugehörige Verkaufsdaten gelöscht.',false);
  }
 }
}catch(Throwable $e){if($conn->inTransaction())$conn->rollBack();$error=$e instanceof RuntimeException?$e->getMessage():'Die Änderung konnte nicht gespeichert werden.';if(!($e instanceof RuntimeException))error_log('Verkaufsmodul: '.$e->getMessage());}
$actions=$conn->query("SELECT * FROM sales_actions ORDER BY FIELD(status,'active','draft','closed','completed'),end_date DESC,id DESC")->fetchAll(PDO::FETCH_ASSOC);
$groups=$conn->query('SELECT * FROM sales_groups ORDER BY sort_order,id')->fetchAll(PDO::FETCH_ASSOC);
$paymentTextQuery=$conn->prepare('SELECT setting_value FROM sales_settings WHERE setting_key=?');$paymentTextQuery->execute(['order_confirmation_payment_text']);$paymentConfirmationText=$paymentTextQuery->fetchColumn();if($paymentConfirmationText===false)$paymentConfirmationText='Bitte bezahle den Betrag entsprechend den Zahlungsinformationen, die dir der Förderverein mitgeteilt hat.';
$selected=(int)($_GET['aktion']??0);$action=null;
if($salesPage!=='list'){
 $q=$conn->prepare('SELECT * FROM sales_actions WHERE id=?');$q->execute([$selected]);$action=$q->fetch(PDO::FETCH_ASSOC);
 if(!$action){header('Location: verkauf.php');exit;}
}
$items=[];$itemOptions=[];$orders=[];$internal=[];$stats=['revenue'=>0,'paid'=>0,'purchase'=>0,'qty'=>0];
if($action){
 $q=$conn->prepare('SELECT * FROM sales_items WHERE action_id=? ORDER BY sort_order,id');$q->execute([$selected]);$items=$q->fetchAll(PDO::FETCH_ASSOC);
 if(in_array($salesPage,['edit','overview'],true)){foreach($items as $it)$itemOptions[(int)$it['id']]=loadSalesOptions($conn,(int)$it['id']);}
 if($salesPage==='overview'){
  $q=$conn->prepare('SELECT * FROM sales_orders WHERE action_id=? ORDER BY created_at DESC,id DESC');$q->execute([$selected]);$orders=$q->fetchAll(PDO::FETCH_ASSOC);
  $q=$conn->prepare('SELECT item_id,quantity FROM sales_internal_order_items WHERE action_id=?');$q->execute([$selected]);foreach($q->fetchAll(PDO::FETCH_ASSOC) as $r)$internal[(int)$r['item_id']]=(int)$r['quantity'];
  // Bestellte Mengen je Eigenschaftskombination für die aufklappbare Artikelaufschlüsselung.
  $variantQuantities=[];$vq=$conn->prepare('SELECT soi.item_id,soi.variant_text,SUM(soi.quantity) quantity FROM sales_order_items soi JOIN sales_orders so ON so.id=soi.order_id WHERE so.action_id=? GROUP BY soi.item_id,soi.variant_text');$vq->execute([$selected]);
  foreach($vq->fetchAll(PDO::FETCH_ASSOC) as $vr){$iid=(int)$vr['item_id'];$variantQuantities[$iid][(string)$vr['variant_text']]=(int)$vr['quantity'];}
  $variantBreakdowns=[];
  foreach($items as $it){$iid=(int)$it['id'];$opts=$itemOptions[$iid]??[];if(!$opts)continue;$combinations=[[]];foreach($opts as $op){$next=[];foreach($combinations as $combination){foreach($op['values'] as $value){$copy=$combination;$copy[]=$op['name'].': '.$value;$next[]=$copy;}}$combinations=$next;}
   foreach($combinations as $combination){$variantText=implode(' · ',$combination);$variantBreakdowns[$iid][]=['label'=>$variantText,'quantity'=>$variantQuantities[$iid][$variantText]??0];}
  }
  foreach($items as $it){$parentQty=0;foreach($orders as $o){$q=$conn->prepare('SELECT COALESCE(SUM(quantity),0) FROM sales_order_items WHERE order_id=? AND item_id=?');$q->execute([$o['id'],$it['id']]);$parentQty+=(int)$q->fetchColumn();}$all=$parentQty+($internal[$it['id']]??0);$stats['qty']+=$all;$stats['purchase']+=$all*(int)$it['purchase_price_cents'];}
  foreach($orders as $o){$stats['revenue']+=(int)$o['total_cents'];if($o['is_paid'])$stats['paid']+=(int)$o['total_cents'];}
  $stats['cost_with_shipping']=$stats['purchase'];$stats['surplus']=$stats['revenue']-$stats['purchase'];
 }
}
