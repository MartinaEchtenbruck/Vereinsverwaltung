<?php
if (!defined('APP_ENTRY_POINT')) { http_response_code(404); exit; }
$salesPage='overview';
require __DIR__.'/_controller.php';
$openVariantIds = [];
$openVariantParam = $_GET['open_variant'] ?? '';
if (is_array($openVariantParam)) {
    $openVariantIds = array_values(array_unique(array_filter(array_map('intval', $openVariantParam), static fn($id) => $id > 0)));
} elseif (is_string($openVariantParam) && $openVariantParam !== '') {
    $openVariantIds = array_values(array_unique(array_filter(array_map('intval', explode(',', $openVariantParam)), static fn($id) => $id > 0)));
} elseif (is_numeric($openVariantParam) && (int)$openVariantParam > 0) {
    $openVariantIds = [(int)$openVariantParam];
}
$openOrderId = (int)($_GET['open_order'] ?? 0);
if ($openOrderId > 0) {
    $knownOrderIds = array_map(static fn($row) => (int)$row['id'], $orders);
    if (!in_array($openOrderId, $knownOrderIds, true)) $openOrderId = 0;
}
require __DIR__.'/_layout_start.php';
?>
<style>
.sales-collapsible > summary {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    cursor: pointer;
    list-style: none;
    font-size: 1.2rem;
    font-weight: 700;
    line-height: 1.3;
}
.sales-collapsible > summary::-webkit-details-marker { display: none; }
.sales-collapsible > summary::after {
    content: '+';
    flex: 0 0 auto;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border: 1px solid #d5dee5;
    border-radius: 6px;
    color: #52616b;
    font-size: 1.15rem;
    font-weight: 500;
}
.sales-collapsible[open] > summary::after { content: '−'; }
.sales-collapsible-content { margin-top: 16px; }

/* Parent- und Child-Zeilen als zusammengehörige Gruppe darstellen */
.finance-table { border-collapse: separate; border-spacing: 0; }
.finance-table tr.finance-variant-group > td { border-bottom: 0; }
.finance-table tr.finance-item-row.finance-variant-group > td {
    background: #f6f8fa;
    border-top: 1px solid #dce3e8;
}
.finance-table tr.finance-item-row.finance-variant-group > td:first-child {
    border-left: 1px solid #dce3e8;
    border-top-left-radius: 7px;
}
.finance-table tr.finance-item-row.finance-variant-group > td:last-child {
    border-right: 1px solid #dce3e8;
    border-top-right-radius: 7px;
}
.finance-table tr.finance-item-row.finance-variant-group.is-open > td { border-bottom: 0; }
.finance-table tr.finance-item-row.finance-variant-group:not(.is-open) > td {
    border-bottom: 1px solid #dce3e8;
}
.finance-table tr.finance-item-row.finance-variant-group:not(.is-open) > td:first-child {
    border-bottom-left-radius: 7px;
}
.finance-table tr.finance-item-row.finance-variant-group:not(.is-open) > td:last-child {
    border-bottom-right-radius: 7px;
}
.finance-table tr.variant-detail-row.finance-variant-group > td {
    background: #fafbfc;
    border-top: 0;
    border-bottom: 0;
}
.finance-table tr.variant-detail-row.finance-variant-group > td:first-child {
    border-left: 1px solid #dce3e8;
    padding-left: 30px;
}
.finance-table tr.variant-detail-row.finance-variant-group > td:last-child {
    border-right: 1px solid #dce3e8;
}
.finance-table tr.variant-detail-row.finance-variant-group.is-last > td {
    border-bottom: 1px solid #dce3e8;
}
.finance-table tr.variant-detail-row.finance-variant-group.is-last > td:first-child {
    border-bottom-left-radius: 7px;
}
.finance-table tr.variant-detail-row.finance-variant-group.is-last > td:last-child {
    border-bottom-right-radius: 7px;
}
.finance-table tr.finance-item-row.finance-variant-group.is-open:hover > td,
.finance-table tr.variant-detail-row.finance-variant-group:hover > td {
    background: #f3f6f8;
}
</style>
<section class="card action-header"><a href="verkauf.php?aktion=<?=$selected?>" class="back-link"><i class="fas fa-arrow-left"></i> Zurück zur Aktionsbearbeitung</a><div class="action-title-row"><div><h2><?=sh($action['title'])?> <span class="tag <?=sh($action['status'])?>"><?=['draft'=>'Entwurf','active'=>'Aktiv','closed'=>'Bestellschluss erreicht','completed'=>'Abgeschlossen'][$action['status']]?></span></h2><p class="action-period">Zeitraum: <?=date('d.m.Y',strtotime($action['start_date']))?> – <?=date('d.m.Y',strtotime($action['end_date']))?></p><p class="action-description"><?=nl2br(sh($action['description']??''))?></p></div></div><p>Öffentliche Bestellseite: <a href="../bestellen/" target="_blank" rel="noopener">/bestellen/</a></p>
<?php if(in_array($action['status'],['active','closed'],true)): ?><form method="post" onsubmit="return confirm('Aktion abschließen? Danach sind die Werte eingefroren.');"><input type="hidden" name="csrf_token" value="<?=sh($csrf)?>"><input type="hidden" name="do" value="complete_action"><input type="hidden" name="action_id" value="<?=$selected?>"><button class="btn btn-success btnwide">Aktion abschließen und Werte einfrieren</button></form><?php endif; ?></section>
<details class="card sales-collapsible"<?= $openVariantIds ? ' open' : '' ?>><summary>Finanzübersicht</summary><div class="sales-collapsible-content"><div class="stats"><div class="stat">Erwarteter Verkaufserlös<strong><?=salesMoney($stats['revenue'])?></strong></div><div class="stat">Zahlungseingänge<br>(bezahlt markiert)<strong><?=salesMoney($stats['paid'])?></strong></div><div class="stat">Einkaufskosten<strong><?=salesMoney($stats['purchase'])?></strong></div><div class="stat">Gesamtkosten<strong><?=salesMoney($stats['purchase'])?></strong></div><div class="stat">Voraussichtlicher Überschuss<strong><?=salesMoney($stats['revenue']-$stats['purchase'])?></strong></div></div><?php if($variantBreakdowns): ?><p class="muted">Artikel mit Ausprägungen lassen sich aufklappen. Die Aufschlüsselung zeigt die Elternbestellungen je Eigenschaftskombination; interne Mengen werden nur auf Artikelebene erfasst.</p><?php endif; ?><div class="tablewrap"><table class="finance-table"><thead><tr><th>Artikel</th><th>Elternmenge</th><th>Interne Menge</th><th>Gesamt zu bestellen</th><th>Einkaufspreis</th><th>Gesamtkosten</th></tr></thead><tbody><?php foreach($items as $it): $pq=0;foreach($orders as $o){if(!empty($o['is_cancelled']))continue;$q=$conn->prepare('SELECT COALESCE(SUM(quantity),0) FROM sales_order_items WHERE order_id=? AND item_id=?');$q->execute([$o['id'],$it['id']]);$pq+=(int)$q->fetchColumn();}$iq=$internal[$it['id']]??0;$totalQty=$pq+$iq;$iid=(int)$it['id'];$hasVariants=!empty($variantBreakdowns[$iid]);$isVariantOpen=$hasVariants && in_array($iid,$openVariantIds,true);$nextOpenVariantIds=$openVariantIds;if($isVariantOpen){$nextOpenVariantIds=array_values(array_diff($nextOpenVariantIds,[$iid]));}else{$nextOpenVariantIds[]=$iid;$nextOpenVariantIds=array_values(array_unique($nextOpenVariantIds));}$variantUrl='verkauf_uebersicht.php?aktion='.$selected.($nextOpenVariantIds?'&open_variant='.implode(',',$nextOpenVariantIds):'').'#finance-item-'.$iid; ?><tr id="finance-item-<?=$iid?>" class="finance-item-row<?=$hasVariants?' has-variants finance-variant-group':''?><?=$isVariantOpen?' is-open':''?>" <?php if($hasVariants): ?>tabindex="0" role="button" aria-expanded="<?=$isVariantOpen?'true':'false'?>" aria-label="Details zu <?=sh($it['name'])?> <?=$isVariantOpen?'ausblenden':'anzeigen'?>" onclick="window.location.href='<?=sh($variantUrl)?>'" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();window.location.href='<?=sh($variantUrl)?>'}"<?php endif; ?>><td><span class="finance-item-name"><?=sh($it['name'])?></span></td><td><?=$pq?></td><td><?=$iq?></td><td><strong><?=$totalQty?></strong></td><td><?=salesMoney((int)$it['purchase_price_cents'])?></td><td><?=salesMoney($totalQty*(int)$it['purchase_price_cents'])?><?php if($hasVariants): ?> <button type="button" class="variant-toggle-icon" aria-expanded="<?=$isVariantOpen?'true':'false'?>" title="<?=$isVariantOpen?'Eigenschaften ausblenden':'Eigenschaften anzeigen'?>" aria-label="Eigenschaften von <?=sh($it['name'])?> <?=$isVariantOpen?'ausblenden':'anzeigen'?>" onclick="event.stopPropagation();window.location.href='<?=sh($variantUrl)?>'"><i class="fas fa-search" aria-hidden="true"></i></button><?php endif; ?></td></tr><?php if($isVariantOpen): $variantCount=count($variantBreakdowns[$iid]); foreach($variantBreakdowns[$iid] as $variantIndex=>$variant): $variantQty=(int)$variant['quantity']; ?><tr class="variant-detail-row finance-variant-group variant-detail-parent-<?=$iid?><?=$variantIndex===$variantCount-1?' is-last':''?>"><td><span class="variant-detail-label">↳ <?=sh($variant['label'])?></span></td><td><?=$variantQty?></td><td>–</td><td><?=$variantQty?></td><td><?=salesMoney((int)$it['purchase_price_cents'])?></td><td><?=salesMoney($variantQty*(int)$it['purchase_price_cents'])?></td></tr><?php endforeach; endif; ?><?php endforeach; ?></tbody></table></div></div></details>
<details class="card sales-collapsible"><summary>Interne Bestellung (Kindergarten)</summary><div class="sales-collapsible-content"><p>Hier die zusätzlichen Mengen für den Kindergarten eintragen. Alle Artikel stehen zur Verfügung, auch solche, die nicht an Eltern verkauft werden.</p><form method="post"><input type="hidden" name="csrf_token" value="<?=sh($csrf)?>"><input type="hidden" name="do" value="save_internal"><input type="hidden" name="action_id" value="<?=$selected?>"><div class="tablewrap"><table><thead><tr><th>Artikel</th><th>Einkaufspreis</th><th>Interne Menge</th></tr></thead><tbody><?php foreach($items as $it): ?><tr><td><?=sh($it['name'])?></td><td><?=salesMoney((int)$it['purchase_price_cents'])?></td><td><input type="number" name="internal_qty[<?=$it['id']?>]" min="0" max="100000" value="<?=$internal[$it['id']]??0?>" <?=$action['status']==='completed'?'disabled':''?>></td></tr><?php endforeach; ?></tbody></table></div><?php if($action['status']!=='completed'): ?><button class="btn btn-primary btnwide">Interne Bestellung speichern</button><?php endif; ?></form></div></details>
<section class="card parent-orders-card" id="parent-orders">
  <h2>Elternbestellungen (<?=count($orders)?>)</h2>
  <div class="filters order-filters">
    <input id="orderSearch" type="search" placeholder="Name, E-Mail oder Bestellnummer suchen" aria-label="Bestellungen suchen">
    <select id="groupFilter" aria-label="Gruppe filtern"><option value="">Alle Gruppen</option><?php foreach($groups as $groupRow): ?><option value="<?=sh($groupRow['name'])?>"><?=sh($groupRow['name'])?></option><?php endforeach; ?></select>
    <select id="statusFilter" aria-label="Bestellstatus filtern"><option value="">Alle Bestellungen</option><option value="unpaid">Nicht bezahlt</option><option value="unpacked">Nicht gepackt</option><option value="unissued">Nicht ausgegeben</option></select>
  </div>
  <div class="tablewrap">
    <table id="ordersTable" class="orders-table">
      <thead><tr><th>Bestellnr.</th><th>Name</th><th>E-Mail</th><th>Gruppe</th><th>Gesamt</th><th>Bezahlt</th><th>Gepackt</th><th>Ausgegeben</th></tr></thead>
      <tbody>
      <?php if(!$orders): ?><tr><td colspan="8" class="orders-empty">Für diese Verkaufsaktion sind noch keine Elternbestellungen eingegangen.</td></tr><?php endif; ?>
      <?php foreach($orders as $orderRow):
        $orderId=(int)$orderRow['id'];
        $isOrderOpen=($openOrderId===$orderId);
        $orderUrlParams=['aktion'=>$selected];
        if($openVariantIds)$orderUrlParams['open_variant']=implode(',',$openVariantIds);
        if(!$isOrderOpen)$orderUrlParams['open_order']=$orderId;
        $orderUrl='verkauf_uebersicht.php?'.http_build_query($orderUrlParams).'#order-'.$orderId;
        $groupName=(string)($orderRow['group_name']??'');
        $orderLines=$orderItemsByOrder[$orderId]??[];
      ?>
        <tr id="order-<?=$orderId?>" class="order-summary-row<?=$isOrderOpen?' is-open':''?><?=!empty($orderRow['is_cancelled'])?' is-cancelled':''?>"
            data-name="<?=sh(mb_strtolower($orderRow['customer_name'].' '.$orderRow['customer_email'].' '.$orderRow['order_number']))?>"
            data-group="<?=sh($groupName)?>" data-paid="<?=$orderRow['is_paid']?'1':'0'?>" data-packed="<?=$orderRow['is_packed']?'1':'0'?>" data-issued="<?=$orderRow['is_issued']?'1':'0'?>"
            tabindex="0" role="button" aria-expanded="<?=$isOrderOpen?'true':'false'?>"
            aria-label="Bestellung <?=sh($orderRow['order_number'])?> von <?=sh($orderRow['customer_name'])?> <?=$isOrderOpen?'Details schließen':'Details öffnen'?>"
            onclick="window.location.href='<?=sh($orderUrl)?>'"
            onkeydown="if((event.key==='Enter'||event.key===' ') && event.target===this){event.preventDefault();window.location.href='<?=sh($orderUrl)?>'}">
          <td><strong><?=sh($orderRow['order_number'])?></strong><?php if(!empty($orderRow['is_cancelled'])): ?><span class="order-cancelled-badge">Storniert</span><?php endif; ?><small class="order-date"><?=date('d.m.Y H:i',strtotime($orderRow['created_at']))?></small></td>
          <td><?=sh($orderRow['customer_name'])?></td>
          <td><a href="mailto:<?=sh($orderRow['customer_email'])?>" onclick="event.stopPropagation()" class="order-email"><?=sh($orderRow['customer_email'])?></a></td>
          <td><?=sh($groupName)?></td>
          <td class="order-total"><?=salesMoney((int)$orderRow['total_cents'])?></td>
          <?php foreach(['is_paid'=>'Bezahlt','is_packed'=>'Gepackt','is_issued'=>'Ausgegeben'] as $statusField=>$statusLabel): ?>
            <td class="order-status-cell"><form method="post" class="order-status-form" onclick="event.stopPropagation()">
              <input type="hidden" name="csrf_token" value="<?=sh($csrf)?>"><input type="hidden" name="do" value="order_status"><input type="hidden" name="action_id" value="<?=$selected?>"><input type="hidden" name="order_id" value="<?=$orderId?>"><input type="hidden" name="field" value="<?=$statusField?>"><input type="hidden" name="open_order" value="<?=$isOrderOpen?$orderId:0?>">
              <input type="checkbox" name="value" value="1" <?=$orderRow[$statusField]?'checked':''?> onchange="this.form.submit()" aria-label="<?=$statusLabel?> für <?=sh($orderRow['order_number'])?>" <?=!empty($orderRow['is_cancelled']) ? 'disabled' : ''?>>
            </form></td>
          <?php endforeach; ?>
        </tr>
        <?php if($isOrderOpen): ?>
          <tr class="order-detail-row"><td colspan="8">
            <div class="order-detail-panel">
              <div class="order-detail-heading"><strong>Bestelldetails – <?=sh($orderRow['order_number'])?><?=!empty($orderRow['is_cancelled'])?' · STORNIERT':''?></strong>
                <?php if(!empty($orderRow['is_cancelled'])): ?>
                  <form method="post" class="order-delete-form" onclick="event.stopPropagation()" onsubmit="return confirm('Stornierung dieser Bestellung aufheben? Sie wird danach wieder in allen Berechnungen berücksichtigt.');">
                    <input type="hidden" name="csrf_token" value="<?=sh($csrf)?>"><input type="hidden" name="do" value="restore_order"><input type="hidden" name="action_id" value="<?=$selected?>"><input type="hidden" name="order_id" value="<?=$orderId?>"><input type="hidden" name="open_order" value="<?=$orderId?>"><button type="submit" class="smallbtn" title="Stornierung aufheben"><i class="fas fa-undo" aria-hidden="true"></i> Stornierung aufheben</button>
                  </form>
                <?php else: ?>
                  <form method="post" class="order-delete-form" onclick="event.stopPropagation()" onsubmit="return confirm('Bestellung wirklich stornieren? Sie bleibt erhalten, wird aber aus allen Berechnungen und Bestellmengen herausgenommen.');">
                    <input type="hidden" name="csrf_token" value="<?=sh($csrf)?>"><input type="hidden" name="do" value="cancel_order"><input type="hidden" name="action_id" value="<?=$selected?>"><input type="hidden" name="order_id" value="<?=$orderId?>"><input type="hidden" name="open_order" value="<?=$orderId?>"><button type="submit" class="smallbtn" title="Bestellung stornieren"><i class="fas fa-ban" aria-hidden="true"></i> Bestellung stornieren</button>
                  </form>
                <?php endif; ?>
              </div>
              <?php if($orderLines): ?><div class="order-cart-lines">
                <?php foreach($orderLines as $lineRow): ?>
                  <div class="order-cart-line">
                    <div class="order-cart-product"><strong><?=sh($lineRow['item_name'])?></strong><?php if(trim((string)($lineRow['variant_text']??''))!==''): ?><div class="order-cart-variant"><?=sh($lineRow['variant_text'])?></div><?php endif; ?></div>
                    <div class="order-cart-calculation"><span><?= (int)$lineRow['quantity'] ?> × <?=salesMoney((int)$lineRow['unit_price_cents'])?></span><strong><?=salesMoney((int)$lineRow['line_total_cents'])?></strong></div>
                  </div>
                <?php endforeach; ?>
              </div><?php else: ?><p class="muted">Zu dieser Bestellung sind keine Positionen hinterlegt.</p><?php endif; ?>
              <div class="order-cart-grand-total"><span>Gesamtbetrag</span><strong><?=salesMoney((int)$orderRow['total_cents'])?></strong></div>
            </div>
          </td></tr>
        <?php endif; ?>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>
<script>
(()=>{
  const table=document.getElementById('ordersTable');
  if(!table)return;
  const search=document.getElementById('orderSearch');
  const group=document.getElementById('groupFilter');
  const status=document.getElementById('statusFilter');
  const summaries=Array.from(table.querySelectorAll('tr.order-summary-row'));
  function applyOrderFilters(){
    const needle=(search?.value||'').trim().toLocaleLowerCase('de-DE');
    const groupValue=group?.value||'';
    const statusValue=status?.value||'';
    summaries.forEach(row=>{
      const matchesText=!needle||(row.dataset.name||'').toLocaleLowerCase('de-DE').includes(needle);
      const matchesGroup=!groupValue||row.dataset.group===groupValue;
      const matchesStatus=!statusValue||(statusValue==='unpaid'&&row.dataset.paid!=='1')||(statusValue==='unpacked'&&row.dataset.packed!=='1')||(statusValue==='unissued'&&row.dataset.issued!=='1');
      const visible=matchesText&&matchesGroup&&matchesStatus;
      row.hidden=!visible;
      const detail=row.nextElementSibling?.classList.contains('order-detail-row')?row.nextElementSibling:null;
      if(detail)detail.hidden=!visible;
    });
  }
  [search,group,status].forEach(control=>control?.addEventListener(control===search?'input':'change',applyOrderFilters));
})();
</script>
<style>
.parent-orders-card .order-filters{margin:0 0 14px}
.orders-table{width:100%;min-width:980px;border-collapse:separate;border-spacing:0}
.orders-table th,.orders-table td{padding:11px 12px;text-align:left;vertical-align:middle;border-bottom:1px solid #e7ecef}
.orders-table thead th{font-size:.9rem;color:#596772;font-weight:650;white-space:nowrap;background:#f7f9fa}
.orders-table tbody tr.order-summary-row{cursor:pointer;transition:background .12s ease}
.orders-table tbody tr.order-summary-row:hover td{background:#f0f5f8}
.orders-table tbody tr.order-summary-row:focus-visible{outline:2px solid var(--primary,#3977a8);outline-offset:-2px}
.orders-table tbody tr.order-summary-row.is-open td{background:#eaf2f7;border-bottom-color:#d3e0e8}
.orders-table tbody tr.order-summary-row.is-cancelled td{background:#f7f3f3;color:#777}
.orders-table tbody tr.order-summary-row.is-cancelled:hover td{background:#f1eaea}
.order-cancelled-badge{display:inline-block;margin-left:7px;padding:2px 7px;border-radius:10px;background:#f8dddd;color:#9b3030;font-size:.72rem;font-weight:700;vertical-align:middle}
.order-status-cell input:disabled{opacity:.35;cursor:not-allowed}
.orders-table .order-date{display:block;margin-top:3px;color:#78858e;font-size:.78rem;white-space:nowrap}
.orders-table .order-email{color:inherit;text-decoration:none}
.orders-table .order-email:hover{text-decoration:underline}
.orders-table .order-total{font-weight:700;white-space:nowrap}
.orders-table .order-status-cell{text-align:center;width:76px}
.orders-table .order-status-form{display:flex;justify-content:center;margin:0}
.orders-table .order-status-form input{width:18px;height:18px;accent-color:var(--primary,#3977a8);cursor:pointer}
.orders-table .order-detail-row>td{padding:0 12px 14px;background:#eaf2f7;border-bottom:1px solid #d3e0e8}
.order-detail-panel{padding:14px 16px;background:#fff;border:1px solid #dbe5eb;border-radius:8px;box-shadow:0 1px 3px #00000008}
.order-detail-heading{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:8px;color:#344854}
.order-delete-form{margin:0}
.order-delete-form .smallbtn{font-size:.82rem}
.order-cart-lines{display:grid;gap:0}
.order-cart-line{display:flex;align-items:center;justify-content:space-between;gap:18px;padding:11px 0;border-bottom:1px solid #edf0f2}
.order-cart-product{min-width:0}
.order-cart-variant{margin-top:3px;color:#687781;font-size:.9rem}
.order-cart-calculation{display:flex;align-items:center;gap:22px;white-space:nowrap}
.order-cart-calculation>span{color:#56636c}
.order-cart-grand-total{display:flex;justify-content:flex-end;align-items:center;gap:26px;padding-top:13px;font-size:1.05rem}
.order-cart-grand-total strong{font-size:1.15rem}
.orders-empty{text-align:center!important;padding:24px!important;color:#6c7880}
@media(max-width:650px){.order-cart-line{align-items:flex-start;flex-direction:column;gap:7px}.order-cart-calculation{width:100%;justify-content:space-between}.order-detail-heading{align-items:flex-start;flex-direction:column}}
</style>

<style>
/* Parent- und Child-Zeilen bilden gemeinsam einen dezent umrahmten Block. */
.finance-table tr.finance-item-row.has-variants{cursor:pointer}
.finance-table tr.finance-item-row.has-variants:hover td{background:#eaf1f7}
.finance-table tr.finance-item-row.has-variants:focus-visible{outline:2px solid var(--primary,#3977a8);outline-offset:-2px}
.finance-table .finance-item-name{font:inherit;font-weight:600;color:inherit}
.finance-table .variant-toggle-icon{display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;margin-left:8px;padding:0;border:1px solid #cbd8e0;border-radius:6px;background:#fff;color:#52616b;cursor:pointer;vertical-align:middle}
.finance-table .variant-toggle-icon:hover{background:#edf3f6;color:#243746}
.finance-table .finance-item-row.is-open .variant-toggle-icon{background:#dce9f1;border-color:#a9c0cf;color:#29485e}
.finance-table tr.finance-item-row.finance-variant-group.is-open > td{background:#eaf1f6;border-top:1px solid #cbd9e2;border-bottom:0}
.finance-table tr.finance-item-row.finance-variant-group.is-open > td:first-child{border-left:1px solid #cbd9e2;border-top-left-radius:8px}
.finance-table tr.finance-item-row.finance-variant-group.is-open > td:last-child{border-right:1px solid #cbd9e2;border-top-right-radius:8px}
.finance-table tr.variant-detail-row.finance-variant-group > td{background:#f7f9fb;border-top:0;border-bottom:0}
.finance-table tr.variant-detail-row.finance-variant-group > td:first-child{border-left:1px solid #cbd9e2;padding-left:30px}
.finance-table tr.variant-detail-row.finance-variant-group > td:last-child{border-right:1px solid #cbd9e2}
.finance-table tr.variant-detail-row.finance-variant-group.is-last > td{border-bottom:1px solid #cbd9e2}
.finance-table tr.variant-detail-row.finance-variant-group.is-last > td:first-child{border-bottom-left-radius:8px}
.finance-table tr.variant-detail-row.finance-variant-group.is-last > td:last-child{border-bottom-right-radius:8px}
.finance-table tr.finance-item-row.finance-variant-group.is-open:hover > td,
.finance-table tr.variant-detail-row.finance-variant-group:hover > td{background:#f1f5f8}
.finance-table .variant-detail-label{display:inline-block;padding-left:28px;font-size:.94em}
.finance-table .variant-detail-row[hidden]{display:none!important}
</style>
<?php require __DIR__.'/_layout_end.php'; ?>
