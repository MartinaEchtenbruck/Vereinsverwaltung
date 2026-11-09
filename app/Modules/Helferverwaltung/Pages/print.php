<?php
if (!defined('APP_ENTRY_POINT')) { http_response_code(404); exit; }
$root = dirname(__DIR__, 4);
require_once $root . '/auth.php';
checkPermission('manage_helpers');
require_once $root . '/db_connection.php';
$db = (new Database())->getConnection();
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function hp($value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }

$actionId = (int)($_GET['aktion'] ?? 0);
$q = $db->prepare('SELECT * FROM helper_actions WHERE id = ?');
$q->execute([$actionId]);
$action = $q->fetch(PDO::FETCH_ASSOC);
$stations = [];
$days = [];
$openTotal = 0;
$requiredTotal = 0;

if ($action) {
    $q = $db->prepare('SELECT * FROM helper_stations WHERE action_id = ? ORDER BY sort_order, id');
    $q->execute([$actionId]);
    $stations = $q->fetchAll(PDO::FETCH_ASSOC);

    $q = $db->prepare('SELECT * FROM helper_days WHERE action_id = ? ORDER BY event_date');
    $q->execute([$actionId]);
    $days = $q->fetchAll(PDO::FETCH_ASSOC);

    $q = $db->prepare("SELECT a.day_id, a.station_id, a.slot_key, r.full_name FROM helper_assignments a JOIN helper_registrations r ON r.id = a.registration_id AND r.status = 'active' WHERE r.action_id = ? ORDER BY r.full_name");
    $q->execute([$actionId]);
    $filled = [];
    foreach ($q->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $key = $row['day_id'] . '|' . $row['station_id'] . '|' . $row['slot_key'];
        if (!isset($filled[$key])) $filled[$key] = ['count' => 0, 'people' => []];
        $filled[$key]['count']++;
        $filled[$key]['people'][] = $row['full_name'];
    }

    $q = $db->prepare('SELECT day_id, station_id, active_slots_json FROM helper_day_stations WHERE station_id IN (SELECT id FROM helper_stations WHERE action_id = ?)');
    $q->execute([$actionId]);
    $active = [];
    foreach ($q->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $active[$row['day_id'] . '|' . $row['station_id']] = json_decode((string)$row['active_slots_json'], true) ?: [];
    }

    foreach ($days as &$day) {
        $day['slots'] = json_decode((string)$day['slots_json'], true) ?: [];
        $day['matrix'] = [];
        $day['open'] = 0;
        $day['required'] = 0;
        foreach ($stations as $station) {
            $activeKeys = $active[$day['id'] . '|' . $station['id']] ?? [];
            foreach ($day['slots'] as $slot) {
                $slotKey = (string)($slot['key'] ?? '');
                $isActive = in_array($slotKey, $activeKeys, true);
                $cellKey = $day['id'] . '|' . $station['id'] . '|' . $slotKey;
                $count = $filled[$cellKey]['count'] ?? 0;
                $capacity = (int)$station['capacity'];
                $shownCount = min($capacity, $count);
                if ($isActive) {
                    $requiredTotal += $capacity;
                    $openTotal += max(0, $capacity - $shownCount);
                    $day['required'] += $capacity;
                    $day['open'] += max(0, $capacity - $shownCount);
                }
                $day['matrix'][$station['id']][$slotKey] = [
                    'active' => $isActive,
                    'filled' => $shownCount,
                    'actual' => $count,
                    'capacity' => $capacity,
                    'people' => $filled[$cellKey]['people'] ?? [],
                    'state' => !$isActive ? 'inactive' : ($shownCount === 0 ? 'empty' : ($shownCount < $capacity ? 'partial' : 'full')),
                ];
            }
        }
    }
    unset($day);
}

$weekday = [
    'Monday' => 'Montag', 'Tuesday' => 'Dienstag', 'Wednesday' => 'Mittwoch',
    'Thursday' => 'Donnerstag', 'Friday' => 'Freitag', 'Saturday' => 'Samstag', 'Sunday' => 'Sonntag'
];
?><!doctype html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Druckansicht – <?= hp($action['title'] ?? 'Helferaktion') ?></title>
<style>
    @page { size: landscape; margin: 12mm; }
    * { box-sizing: border-box; }
    body { margin: 0; color: #202833; background: #fff; font-family: Arial, Helvetica, sans-serif; font-size: 10pt; text-align: center; }
    .print-toolbar { display: flex; justify-content: flex-end; margin: 0 0 12px; }
    .print-button { border: 1px solid #9aa4b2; background: #f4f6f8; color: #202833; border-radius: 5px; padding: 8px 13px; font-size: 10pt; cursor: pointer; }
    .action-header { border-bottom: 2px solid #35465a; padding: 0 0 10px; margin: 0 0 16px; }
    .print-title { font-size: 26pt; font-weight: 800; margin: 0 0 14px; text-align: center; }
    h1 { font-size: 22pt; margin: 0 0 7px; }
    .action-meta { display: flex; flex-wrap: wrap; justify-content: center; gap: 8px 24px; color: #465364; font-size: 10pt; }
    .action-description { margin: 8px 0 0; white-space: normal; line-height: 1.35; }
    .summary { margin-top: 7px; font-weight: bold; }
    .day-section { margin: 0 0 20px; break-inside: avoid-page; page-break-inside: avoid; }
    .day-heading { display: flex; align-items: baseline; justify-content: center; flex-wrap: wrap; gap: 8px 18px; border-bottom: 1px solid #aab3bd; padding: 0 0 5px; margin: 0 0 7px; text-align: center; }
    h2 { font-size: 14pt; margin: 0; }
    .day-open { font-size: 9pt; font-weight: bold; white-space: nowrap; }
    .matrix-wrap { width: 100%; overflow-x: auto; }
    table { width: max-content; border-collapse: collapse; table-layout: fixed; }
    th, td { border: 1px solid #aeb7c2; padding: 0; vertical-align: middle; }
    thead th { background: #e9edf2; font-size: 9pt; text-align: center; font-weight: bold; }
    thead th:first-child { text-align: center; width: 105px; min-width: 105px; max-width: 105px; padding: 6px 7px; }
    tbody th { background: #f3f5f7; text-align: center; white-space: normal; width: 105px; min-width: 105px; max-width: 105px; padding: 6px 7px; font-weight: bold; overflow-wrap: anywhere; }
    tbody tr { height: 80px; }
    tbody td { height: 80px; text-align: center; }
    /* Die Hintergrundfarbe liegt auf der Tabellenzelle selbst. So füllt sie auch dann
       die komplette Zeilenhöhe, wenn eine Nachbarzelle durch viele Namen höher wird. */
    tbody td.full, tbody td.inactive { background: #e5e7eb; }
    tbody td.partial, tbody td.empty { background: #f8d7da; }
    .cell { min-height: 80px; height: 100%; border-radius: 0; padding: 6px; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; overflow-wrap: anywhere; }
    .cell-count { font-size: 9pt; font-weight: bold; white-space: nowrap; margin-bottom: 3px; }
    .cell-people { font-size: 11pt; line-height: 1.3; font-weight: 600; overflow-wrap: anywhere; }
    .cell-people span { display: block; }
    .cell.full, .cell.partial, .cell.empty { background: transparent; }
    .cell.inactive { background: transparent; color: #6b7280; text-align: center; font-weight: bold; padding: 6px; justify-content: center; }
    .no-data { border: 1px solid #c8ced6; padding: 12px; color: #5b6572; }
    @media screen { body { padding: 18px; } .page { max-width: 1500px; margin: 0 auto; } }
    @media print {
        .print-toolbar { display: none !important; }
        body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .day-section { break-inside: auto; page-break-inside: auto; }
        tr { break-inside: avoid; page-break-inside: avoid; }
        thead { display: table-header-group; }
    }
</style>
</head>
<body>
<div class="page">
    <div class="print-toolbar"><button class="print-button" type="button" onclick="window.print()">Drucken</button></div>
    <?php if (!$action): ?>
        <div class="no-data">Die angegebene Helferaktion wurde nicht gefunden.</div>
    <?php else: ?>
        <div class="print-title">Helferliste – Stand vom <?= date('d.m.Y, H:i') ?> Uhr</div>
        <header class="action-header">
            <h1><?= hp($action['title']) ?></h1>
            <div class="action-meta"><span><?= date('d.m.Y', strtotime($action['start_date'])) ?> – <?= date('d.m.Y', strtotime($action['end_date'])) ?></span></div>
            <?php if (!empty($action['description'])): ?><p class="action-description"><?= nl2br(hp($action['description'])) ?></p><?php endif; ?>
            <div class="summary">Noch <?= $openTotal ?> von <?= $requiredTotal ?> Schichten zu besetzen</div>
        </header>

        <?php if (!$days): ?>
            <div class="no-data">Für diese Aktion sind keine Veranstaltungstage angelegt.</div>
        <?php else: ?>
            <?php foreach ($days as $day): ?>
                <section class="day-section">
                    <div class="day-heading">
                        <h2><?= hp($weekday[date('l', strtotime($day['event_date']))] ?? '') ?> · <?= date('d.m.Y', strtotime($day['event_date'])) ?></h2>
                        <span class="day-open">Noch <?= (int)$day['open'] ?>/<?= (int)$day['required'] ?> Schichten zu besetzen</span>
                    </div>
                    <?php if (!$stations): ?>
                        <div class="no-data">Für diese Aktion sind noch keine Stationen angelegt.</div>
                    <?php else: ?>
                        <div class="matrix-wrap">
                            <table>
                                <colgroup><col style="width:105px"><?php foreach ($stations as $station): ?><col style="width:320px"><?php endforeach; ?></colgroup>
                                <thead><tr><th>Zeitfenster</th><?php foreach ($stations as $station): ?><th><?= hp($station['name']) ?></th><?php endforeach; ?></tr></thead>
                                <tbody>
                                <?php foreach ($day['slots'] as $slot): ?>
                                    <tr>
                                        <th><?= hp($slot['label'] ?? $slot['key'] ?? '') ?></th>
                                        <?php foreach ($stations as $station):
                                            $cell = $day['matrix'][$station['id']][$slot['key']] ?? ['active'=>false,'state'=>'inactive','filled'=>0,'capacity'=>(int)$station['capacity'],'people'=>[]];
                                        ?>
                                            <td class="<?= hp($cell['state']) ?>">
                                                <?php if (!$cell['active']): ?>
                                                    <div class="cell inactive">Nicht erforderlich</div>
                                                <?php else: ?>
                                                    <div class="cell <?= hp($cell['state']) ?>">
                                                        <div class="cell-count"><?= (int)$cell['filled'] ?>/<?= (int)$cell['capacity'] ?></div>
                                                        <div class="cell-people"><?php foreach ($cell['people'] as $person): ?><span><?= hp($person) ?></span><?php endforeach; ?></div>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </section>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endif; ?>
</div>
</body>
</html>
