<?php
if (!defined('APP_ENTRY_POINT')) { http_response_code(404); exit; }
$salesPage='list';
require __DIR__.'/_controller.php';
require __DIR__.'/_layout_start.php';
if(isset($_GET['neu'])):
?>
<section class="card"><h2>Neue Verkaufsaktion anlegen</h2><form method="post"><input type="hidden" name="csrf_token" value="<?=sh($csrf)?>"><input type="hidden" name="do" value="create_action"><div class="grid"><label class="field">Titel<input name="title" maxlength="180" required></label><label class="field">Bestellstart<input type="date" name="start_date" required></label><label class="field">Bestellende<input type="date" name="end_date" required></label><label class="field">Beschreibung<textarea name="description" rows="2"></textarea></label></div><button class="btn btn-primary btnwide">Aktion anlegen</button> <a class="btn btnwide" href="verkauf.php">Abbrechen</a></form></section>
<?php else: ?>
<section class="card">
 <div class="list-heading"><h2>Verkaufsaktionen</h2><a class="btn btn-primary btnwide" href="verkauf.php?neu=1"><i class="fas fa-plus"></i> Neue Verkaufsaktion anlegen</a></div>
 <?php if(!$actions): ?><p class="muted">Es sind noch keine Verkaufsaktionen vorhanden.</p><?php else: ?>
 <div class="tablewrap"><table class="action-list"><thead><tr><th>Name</th><th>Zeitraum</th><th>Status</th><th class="action-tools">Aktionen</th></tr></thead><tbody>
 <?php foreach($actions as $a): $listStatus=$a['status']==='draft'?'Entwurf':($a['status']==='active'?'Aktiv':'Inaktiv'); ?>
 <tr><td><strong><?=sh($a['title'])?></strong></td><td><?=date('d.m.Y',strtotime($a['start_date']))?> – <?=date('d.m.Y',strtotime($a['end_date']))?></td><td><span class="tag <?=sh($a['status']==='draft'?'draft':($a['status']==='active'?'active':'inactive'))?>"><?=$listStatus?></span></td><td class="action-tools"><a class="iconbtn" href="verkauf.php?aktion=<?=(int)$a['id']?>" title="Verkaufsaktion bearbeiten" aria-label="Verkaufsaktion bearbeiten"><i class="fas fa-pen"></i></a><a class="iconbtn" href="verkauf_uebersicht.php?aktion=<?=(int)$a['id']?>" title="Verkaufsübersicht öffnen" aria-label="Verkaufsübersicht öffnen"><i class="fas fa-chart-column"></i></a><form method="post" class="inline" onsubmit="return confirm('Diese Verkaufsaktion einschließlich aller Artikel und Bestellungen endgültig löschen?');"><input type="hidden" name="csrf_token" value="<?=sh($csrf)?>"><input type="hidden" name="do" value="delete_action"><input type="hidden" name="action_id" value="<?=(int)$a['id']?>"><button class="iconbtn danger" type="submit" title="Verkaufsaktion löschen" aria-label="Verkaufsaktion löschen"><i class="fas fa-trash"></i></button></form></td></tr>
 <?php endforeach; ?></tbody></table></div><?php endif; ?>
</section>
<section class="card general-settings">
 <details>
  <summary><strong>Allgemeine Einstellungen</strong><span class="settings-hint">Einstellungen für alle Verkaufsaktionen</span></summary>
  <div class="settings-content">
   <section class="settings-panel"><h3>Zahlungshinweise für die Mail zur Bestellbestätigung</h3><p>Dieser Text wird in die Bestellbestätigung per E-Mail aufgenommen und informiert über die Zahlung.</p><form method="post"><input type="hidden" name="csrf_token" value="<?=sh($csrf)?>"><input type="hidden" name="do" value="save_general_settings"><label class="field">Zahlungshinweis<textarea name="order_confirmation_payment_text" rows="4" maxlength="5000"><?=sh($paymentConfirmationText)?></textarea></label><button class="btn btn-primary btnwide">Zahlungshinweis speichern</button></form></section>
   <section class="settings-panel"><h3>Bestellgruppen</h3><p>In der Bestellung kann angegeben werden, in welcher Gruppe die Bestellung hinterlegt werden soll. Diese Gruppen können hier benannt werden:</p><div class="settings-groups">
   <?php foreach($groups as $g): ?><form method="post" class="group-setting-form"><input type="hidden" name="csrf_token" value="<?=sh($csrf)?>"><input type="hidden" name="do" value="save_group"><input type="hidden" name="group_id" value="<?=(int)$g['id']?>"><label class="field">Gruppe<input name="group_name" value="<?=sh($g['name'])?>" required></label><button class="btn btn-primary btnwide">Speichern</button></form><?php endforeach; ?>
   <form method="post" class="group-setting-form"><input type="hidden" name="csrf_token" value="<?=sh($csrf)?>"><input type="hidden" name="do" value="save_group"><label class="field">Neue Gruppe<input name="group_name" required></label><input type="hidden" name="sort_order" value="<?=count($groups)+1?>"><button class="btn btn-primary btnwide"><i class="fas fa-plus"></i> Gruppe hinzufügen</button></form>
   </div></section>
  </div>
 </details>
</section>
<?php endif; require __DIR__.'/_layout_end.php'; ?>
