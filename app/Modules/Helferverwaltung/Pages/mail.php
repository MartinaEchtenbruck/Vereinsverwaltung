<?php
if (!defined('APP_ENTRY_POINT')) { http_response_code(404); exit; }
$root = dirname(__DIR__, 4);
if (session_status() === PHP_SESSION_NONE) session_start();
require_once $root . '/auth.php';
checkPermission('manage_helpers');
require_once $root . '/db_connection.php';
require_once $root . '/app/Core/Mail.php';
require_once $root . '/app/Modules/Mitgliederverwaltung/Includes/mail_placeholders.php';

$db = (new Database())->getConnection();
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
if (empty($_SESSION['helper_mail_csrf'])) $_SESSION['helper_mail_csrf'] = bin2hex(random_bytes(32));
$csrf = $_SESSION['helper_mail_csrf'];
$actionId = (int)($_GET['aktion'] ?? $_POST['action_id'] ?? 0);
$error = '';
$notice = trim((string)($_GET['meldung'] ?? ''));

function hm_h($value): string { return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function hm_json(array $data, int $status = 200): void {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}
function hm_date(string $date): string {
    $dt = DateTimeImmutable::createFromFormat('!Y-m-d', $date);
    return $dt ? $dt->format('d.m.Y') : $date;
}
function hm_weekday(string $date): string {
    $names = [1=>'Montag',2=>'Dienstag',3=>'Mittwoch',4=>'Donnerstag',5=>'Freitag',6=>'Samstag',7=>'Sonntag'];
    return $names[(int)date('N', strtotime($date))] ?? '';
}
function hm_split_name(string $fullName): array {
    $parts = preg_split('/\s+/u', trim($fullName), 2, PREG_SPLIT_NO_EMPTY) ?: [];
    return [$parts[0] ?? '', $parts[1] ?? ''];
}
function hm_load_recipient(PDO $db, int $actionId, int $registrationId): ?array {
    $q = $db->prepare("SELECT id, full_name, email FROM helper_registrations WHERE id=? AND action_id=? AND status='active'");
    $q->execute([$registrationId, $actionId]);
    $person = $q->fetch(PDO::FETCH_ASSOC);
    if (!$person) return null;

    // Eine E-Mail-Adresse kann in der Aktion mehrere Registrierungsdatensätze haben.
    // Diese werden für den Versand zu genau einem Empfänger zusammengefasst.
    $q = $db->prepare("SELECT id FROM helper_registrations WHERE action_id=? AND status='active' AND LOWER(TRIM(email))=LOWER(TRIM(?)) ORDER BY id");
    $q->execute([$actionId, $person['email']]);
    $registrationIds = array_map('intval', $q->fetchAll(PDO::FETCH_COLUMN));
    if (!$registrationIds) return null;
    $marks = implode(',', array_fill(0, count($registrationIds), '?'));
    $sql = "SELECT a.day_id,a.station_id,a.slot_key,d.event_date,d.slots_json,s.name AS station_name,n.note
        FROM helper_assignments a
        JOIN helper_registrations r ON r.id=a.registration_id AND r.action_id=? AND r.status='active'
        JOIN helper_days d ON d.id=a.day_id AND d.action_id=?
        JOIN helper_stations s ON s.id=a.station_id AND s.action_id=?
        LEFT JOIN helper_shift_notes n ON n.day_id=a.day_id AND n.station_id=a.station_id AND n.slot_key=a.slot_key
        WHERE a.registration_id IN ($marks) ORDER BY d.event_date,a.slot_key,s.sort_order,s.name";
    $q = $db->prepare($sql);
    $q->execute(array_merge([$actionId, $actionId, $actionId], $registrationIds));
    $shiftMap = [];
    foreach ($q->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $shiftKey = $row['day_id'].'|'.$row['station_id'].'|'.$row['slot_key'];
        if (isset($shiftMap[$shiftKey])) continue;
        $slots = json_decode((string)$row['slots_json'], true) ?: [];
        $slotLabel = (string)$row['slot_key'];
        foreach ($slots as $slot) {
            if ((string)($slot['key'] ?? '') === (string)$row['slot_key']) {
                $slotLabel = (string)($slot['label'] ?? $slot['key']);
                break;
            }
        }
        $shiftMap[$shiftKey] = [
            'date' => (string)$row['event_date'],
            'station' => (string)$row['station_name'],
            'slot' => $slotLabel,
            'note' => trim((string)($row['note'] ?? '')),
        ];
    }
    $shifts = array_values($shiftMap);
    if (!$shifts) return null;
    [$firstName, $lastName] = hm_split_name((string)$person['full_name']);
    $shiftLines = [];
    $hintLines = [];
    foreach ($shifts as $shift) {
        $shiftLines[] = hm_weekday($shift['date']) . ', ' . hm_date($shift['date']) . ' – ' . $shift['station'] . ', ' . $shift['slot'];
        if ($shift['note'] !== '') {
            $hintLines[] = $shift['station'] . ' – ' . hm_weekday($shift['date']) . ', ' . hm_date($shift['date']) . ' (' . $shift['slot'] . ')' . "\n" . $shift['note'];
        }
    }
    return [
        'id' => (int)$person['id'], 'registration_ids' => $registrationIds, 'full_name' => (string)$person['full_name'],
        'vorname' => $firstName, 'nachname' => $lastName, 'email' => trim((string)$person['email']),
        'shifts' => $shifts, 'shift_text' => implode("\n", $shiftLines),
        'hint_text' => implode("\n\n", $hintLines),
    ];
}
function hm_render_text(string $text, array $values): string {
    return preg_replace_callback('/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/u', static fn($m) => array_key_exists($m[1], $values) ? (string)$values[$m[1]] : $m[0], $text) ?? $text;
}
function hm_render_html(string $html, array $values): string {
    $html = sanitizeMemberMailHtml($html);
    if (trim((string)($values['schicht_hinweise'] ?? '')) === '') {
        $html = preg_replace('~<(p|div)>\s*(?:<br>)?\s*\{\{\s*schicht_hinweise\s*\}\}\s*</\1>~i', '', $html) ?? $html;
    }
    return preg_replace_callback('/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/u', static function ($m) use ($values) {
        if (!array_key_exists($m[1], $values)) return $m[0];
        $value = htmlspecialchars((string)$values[$m[1]], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        return nl2br($value, false);
    }, $html) ?? $html;
}
function hm_values(array $person, array $action): array {
    return [
        'vorname' => $person['vorname'], 'nachname' => $person['nachname'], 'email' => $person['email'],
        'mitglied_seit' => '', 'mitglied_bis' => '', 'beitrag' => '',
        'aktion_name' => (string)$action['title'],
        'aktion_start' => hm_date((string)$action['start_date']),
        'aktion_ende' => hm_date((string)$action['end_date']),
        'helfer_schichten' => (string)$person['shift_text'],
        'schicht_hinweise' => (string)$person['hint_text'],
    ];
}

try {
    $q = $db->prepare('SELECT * FROM helper_actions WHERE id=?');
    $q->execute([$actionId]);
    $action = $q->fetch(PDO::FETCH_ASSOC);
    if (!$action) throw new RuntimeException('Die Helferaktion wurde nicht gefunden.');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!hash_equals($csrf, (string)($_POST['csrf_token'] ?? ''))) {
            if (isset($_POST['ajax_action'])) hm_json(['success'=>false,'error'=>'Sitzung abgelaufen. Bitte Seite neu laden.'], 403);
            throw new RuntimeException('Sitzung abgelaufen. Bitte lade die Seite neu.');
        }

        if (($_POST['ajax_action'] ?? '') === 'send_one') {
            $registrationId = (int)($_POST['registration_id'] ?? 0);
            $templateId = (int)($_POST['template_id'] ?? 0);
            $person = hm_load_recipient($db, $actionId, $registrationId);
            if (!$person) hm_json(['success'=>false,'error'=>'Helfer oder Schichtanmeldungen wurden nicht gefunden.'], 404);
            if (!filter_var($person['email'], FILTER_VALIDATE_EMAIL)) hm_json(['success'=>false,'error'=>'Keine gültige E-Mail-Adresse für '.$person['full_name'].'.'], 422);
            $q = $db->prepare('SELECT template_name,betreff,mailtext FROM email_templates WHERE id=?');
            $q->execute([$templateId]);
            $template = $q->fetch(PDO::FETCH_ASSOC);
            if (!$template) hm_json(['success'=>false,'error'=>'Bitte eine gültige E-Mail-Vorlage auswählen.'], 422);
            $values = hm_values($person, $action);
            $subject = trim(hm_render_text((string)$template['betreff'], $values));
            $body = hm_render_html((string)$template['mailtext'], $values);
            if ($subject === '' || mb_strlen($subject) > 255 || preg_match('/[\r\n]/', $subject)) hm_json(['success'=>false,'error'=>'Der Betreff der Vorlage ist ungültig.'], 422);
            if (preg_match('/\{\{\s*[a-zA-Z0-9_]+\s*\}\}/', $subject.' '.$body, $unknown)) hm_json(['success'=>false,'error'=>'Nicht aufgelöster Platzhalter in der Vorlage: '.$unknown[0]], 422);

            $limitFile = $root . '/bulk_mail_timestamps.log';
            $now = time(); $recent = [];
            if (is_file($limitFile)) {
                foreach (file($limitFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                    $ts = (int)trim($line);
                    if ($ts > $now - 3600) $recent[] = $ts;
                }
            }
            if (count($recent) >= 240) hm_json(['success'=>false,'rate_limit'=>true,'error'=>'Stundenlimit erreicht (maximal 240 E-Mails innerhalb von 60 Minuten). Bitte später fortsetzen.'], 429);

            $ok = false; $errorText = '';
            try {
                $ok = sendEmailWithAttachments($person['email'], $subject, $body, [], true);
                if (!$ok) $errorText = 'Der Mailserver hat den Versand nicht angenommen.';
            } catch (Throwable $e) {
                $errorText = 'Technischer Fehler beim Versand.';
                error_log('Helfer-Mailversand: ' . $e->getMessage());
            }
            $status = $ok ? 'sent' : 'failed';
            $ins = $db->prepare('INSERT INTO helper_mail_log(action_id,registration_id,recipient_name,recipient_email,template_name,subject,send_status,error_message,sent_at) VALUES(?,?,?,?,?,?,?,?,?)');
            $ins->execute([$actionId, $registrationId, $person['full_name'], $person['email'], $template['template_name'], $subject, $status, $ok ? null : $errorText, $ok ? date('Y-m-d H:i:s') : null]);
            if ($ok) file_put_contents($limitFile, $now . "\n", FILE_APPEND | LOCK_EX);
            hm_json(['success'=>$ok,'status'=>$status,'registration_id'=>$registrationId,'message'=>$ok?'E-Mail an '.$person['email'].' an den Mailserver übergeben.':$errorText]);
        }

        if (($_POST['do'] ?? '') === 'save_notes') {
            $q = $db->prepare('SELECT d.id AS day_id,ds.station_id,ds.active_slots_json,d.slots_json FROM helper_days d JOIN helper_day_stations ds ON ds.day_id=d.id JOIN helper_stations s ON s.id=ds.station_id AND s.action_id=d.action_id WHERE d.action_id=?');
            $q->execute([$actionId]);
            $valid = [];
            foreach ($q->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $activeSlots = json_decode((string)$row['active_slots_json'], true) ?: [];
                foreach ($activeSlots as $slotKey) $valid[$row['day_id'].'|'.$row['station_id'].'|'.$slotKey] = true;
            }
            $db->beginTransaction();
            $save = $db->prepare('INSERT INTO helper_shift_notes(day_id,station_id,slot_key,note) VALUES(?,?,?,?) ON DUPLICATE KEY UPDATE note=VALUES(note)');
            $delete = $db->prepare('DELETE FROM helper_shift_notes WHERE day_id=? AND station_id=? AND slot_key=?');
            foreach ((array)($_POST['notes'] ?? []) as $dayId => $stations) {
                foreach ((array)$stations as $stationId => $slots) {
                    foreach ((array)$slots as $slotKey => $note) {
                        $key = (int)$dayId.'|'.(int)$stationId.'|'.(string)$slotKey;
                        if (!isset($valid[$key])) throw new RuntimeException('Eine Schichtzuordnung ist ungültig. Bitte Seite neu laden.');
                        $note = trim((string)$note);
                        if (mb_strlen($note) > 5000) throw new RuntimeException('Ein Schichthinweis darf maximal 5.000 Zeichen lang sein.');
                        if ($note === '') $delete->execute([(int)$dayId,(int)$stationId,(string)$slotKey]);
                        else $save->execute([(int)$dayId,(int)$stationId,(string)$slotKey,$note]);
                    }
                }
            }
            $db->commit();
            header('Location: helpermail.php?aktion='.$actionId.'&meldung='.rawurlencode('Schichthinweise wurden gespeichert.'));
            exit;
        }
        throw new RuntimeException('Unbekannte Aktion.');
    }

    $q = $db->prepare("SELECT d.id AS day_id,d.event_date,d.slots_json,s.id AS station_id,s.name AS station_name,s.sort_order,ds.active_slots_json
        FROM helper_days d JOIN helper_day_stations ds ON ds.day_id=d.id
        JOIN helper_stations s ON s.id=ds.station_id AND s.action_id=d.action_id
        WHERE d.action_id=? ORDER BY d.event_date,s.sort_order,s.name");
    $q->execute([$actionId]);
    $shiftRows = [];
    foreach ($q->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $slots = json_decode((string)$row['slots_json'], true) ?: [];
        $labels = [];
        foreach ($slots as $slot) $labels[(string)($slot['key'] ?? '')] = (string)($slot['label'] ?? $slot['key'] ?? '');
        $activeSlots = json_decode((string)$row['active_slots_json'], true) ?: [];
        foreach ($activeSlots as $slotKey) {
            $shiftRows[] = ['day_id'=>(int)$row['day_id'],'date'=>$row['event_date'],'station_id'=>(int)$row['station_id'],'station'=>$row['station_name'],'slot_key'=>(string)$slotKey,'slot_label'=>$labels[(string)$slotKey] ?? (string)$slotKey];
        }
    }
    $q = $db->prepare('SELECT day_id,station_id,slot_key,note FROM helper_shift_notes WHERE day_id IN (SELECT id FROM helper_days WHERE action_id=?)');
    $q->execute([$actionId]);
    $notes = [];
    foreach ($q->fetchAll(PDO::FETCH_ASSOC) as $row) $notes[$row['day_id'].'|'.$row['station_id'].'|'.$row['slot_key']] = $row['note'];
    foreach ($shiftRows as &$shift) $shift['note'] = $notes[$shift['day_id'].'|'.$shift['station_id'].'|'.$shift['slot_key']] ?? '';
    unset($shift);

    $q = $db->prepare("SELECT MIN(r.id) AS id,MIN(r.full_name) AS full_name,MIN(r.email) AS email FROM helper_registrations r JOIN helper_assignments a ON a.registration_id=r.id WHERE r.action_id=? AND r.status='active' GROUP BY LOWER(TRIM(r.email)) ORDER BY MIN(r.full_name)");
    $q->execute([$actionId]);
    $recipients = [];
    foreach ($q->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $person = hm_load_recipient($db, $actionId, (int)$row['id']);
        if (!$person) continue;
        $person['valid_email'] = (bool)filter_var($person['email'], FILTER_VALIDATE_EMAIL);
        $recipients[] = $person;
    }
    $q = $db->prepare("SELECT LOWER(TRIM(recipient_email)) AS email_key,MAX(sent_at) AS last_sent FROM helper_mail_log WHERE action_id=? AND send_status='sent' GROUP BY LOWER(TRIM(recipient_email))");
    $q->execute([$actionId]);
    $lastSent = [];
    foreach ($q->fetchAll(PDO::FETCH_ASSOC) as $row) $lastSent[(string)$row['email_key']] = $row['last_sent'];
    foreach ($recipients as &$recipient) $recipient['last_sent'] = $lastSent[strtolower(trim($recipient['email']))] ?? null;
    unset($recipient);

    $templates = $db->query('SELECT id,template_name,betreff,mailtext FROM email_templates ORDER BY template_name,id')->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    if ($db->inTransaction()) $db->rollBack();
    if (isset($_POST['ajax_action'])) {
        error_log('Helfer-Mailversand AJAX: '.$e->getMessage());
        hm_json(['success'=>false,'error'=>'Die Aktion konnte nicht ausgeführt werden. Bitte Server-Fehlerprotokoll prüfen.'], 500);
    }
    $error = $e instanceof RuntimeException ? $e->getMessage() : 'Der Helfer-Mailbereich konnte nicht geladen werden. Bitte Datenbankmigration prüfen.';
    if (!($e instanceof RuntimeException)) error_log('Helfer-Mailbereich: '.$e->getMessage());
    $shiftRows = $shiftRows ?? []; $recipients = $recipients ?? []; $templates = $templates ?? []; $lastSent = $lastSent ?? [];
}

$recipientJs = [];
foreach (($recipients ?? []) as $person) {
    $actionValues = hm_values($person, $action);
    $recipientJs[] = [
        'id'=>$person['id'],'name'=>$person['full_name'],'email'=>$person['email'],'valid_email'=>$person['valid_email'],
        'last_sent'=>$person['last_sent'] ? date('d.m.Y H:i', strtotime($person['last_sent'])) : '',
        'values'=>$actionValues,
    ];
}
$templateJs = array_map(static fn($t)=>['id'=>(int)$t['id'],'name'=>$t['template_name'],'subject'=>$t['betreff'],'body'=>sanitizeMemberMailHtml((string)$t['mailtext'])], $templates ?? []);
$selectedTemplate = (int)($_GET['vorlage'] ?? ($templates[0]['id'] ?? 0));
?>
<!doctype html><html lang="de"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Helfer anschreiben – <?=hm_h($action['title'] ?? '')?></title><link rel="icon" type="image/png" sizes="32x32" href="/intern/assets/icons/favicon.png"><link rel="apple-touch-icon" sizes="180x180" href="/intern/assets/icons/apple-touch-icon.png"><link rel="manifest" href="/intern/manifest.webmanifest"><meta name="theme-color" content="#FFF500"><link rel="stylesheet" href="styles.css"><link rel="stylesheet" href="includes/navigation.css"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
.hm-wrap{max-width:1450px;margin:24px auto}.hm-card{background:#fff;border-radius:12px;padding:20px;margin:18px 0;box-shadow:0 2px 12px #0000000d}.hm-head{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap}.hm-head h2{margin:0;font-size:1.15rem}.hm-meta{display:flex;flex-wrap:wrap;gap:10px 22px;color:#667085;margin-top:8px}.hm-btn{border:0;border-radius:7px;padding:9px 13px;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:7px;background:#e9edf2;color:#202938;font:inherit}.hm-btn.primary{background:#244e79;color:#fff}.hm-btn:disabled{opacity:.5;cursor:not-allowed}.hm-muted{color:#667085;font-size:.9rem}.hm-alert{padding:12px 15px;border-radius:8px;margin:12px 0;background:#e8f5e9}.hm-alert.error{background:#fdeaea;color:#8b1e1e}.hm-field{display:flex;flex-direction:column;gap:6px;font-weight:600;margin:9px 0}.hm-field input,.hm-field select,.hm-field textarea{box-sizing:border-box;width:100%;padding:9px 10px;border:1px solid #cbd0d8;border-radius:7px;font:inherit;background:#fff}.hm-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:14px}.hm-table-wrap{overflow-x:auto}.hm-table{width:100%;border-collapse:collapse}.hm-table th,.hm-table td{padding:10px 12px;border-bottom:1px solid #e8e8e8;text-align:left;vertical-align:top}.hm-table th{background:#f7f9fb;color:#34445a;font-weight:650}.hm-table textarea{width:100%;min-width:260px;min-height:62px;box-sizing:border-box;padding:8px;border:1px solid #d5dbe2;border-radius:7px;font:inherit;resize:vertical}.hm-recipient-table td{vertical-align:middle}.hm-status{display:inline-block;padding:4px 8px;border-radius:20px;background:#edf0f2;color:#59636e;font-size:.82rem;white-space:nowrap}.hm-status.sent{background:#d9f2df;color:#176b35}.hm-status.failed{background:#fde8e7;color:#a12622}.hm-preview{border:1px solid #e5e7eb;background:#fafafa;border-radius:8px;padding:15px;min-height:100px;line-height:1.5;overflow-wrap:anywhere}.hm-preview-body{margin-top:10px}.hm-toolbar{display:flex;gap:9px;align-items:center;flex-wrap:wrap;margin:12px 0}.hm-count{font-weight:700}.hm-progress{height:9px;background:#e9edf2;border-radius:20px;overflow:hidden}.hm-progress>span{display:block;height:100%;width:0;background:var(--primary-color,#3498db);transition:width .2s}.hm-log{white-space:pre-line;color:#475467;font-size:.9rem;margin-top:10px}.hm-back{display:inline-flex;gap:7px;align-items:center;text-decoration:none;margin:0 0 8px;color:var(--primary-color,#244e79)}.hm-collapsible{padding:0;overflow:hidden}.hm-collapsible>summary{list-style:none;cursor:pointer;padding:20px;user-select:none}.hm-collapsible>summary::-webkit-details-marker{display:none}.hm-collapsible>summary:after{content:"\f078";font-family:"Font Awesome 6 Free";font-weight:900;margin-left:auto;color:#667085;transition:transform .15s}.hm-collapsible[open]>summary:after{transform:rotate(180deg)}.hm-collapsible>summary h2{display:inline}.hm-collapsible-content{padding:0 20px 20px}.hm-collapsible>summary:hover{background:#fafbfc}@media(max-width:700px){.hm-collapsible>summary{padding:13px}.hm-collapsible-content{padding:0 13px 13px}}
@media(max-width:700px){.hm-card{padding:13px}.hm-wrap{margin:14px auto}.hm-table th,.hm-table td{padding:8px}.hm-table textarea{min-width:210px}}
</style></head><body><div class="container"><header class="app-page-header"><h1>Helfer anschreiben</h1><?php require $root.'/includes/navigation.php';?></header><main class="hm-wrap">
<a class="hm-back" href="helferlisten.php"><i class="fa-solid fa-arrow-left"></i> Zurück zu den Helferlisten</a>
<?php if($error):?><div class="hm-alert error"><?=hm_h($error)?></div><?php endif;?><?php if($notice):?><div class="hm-alert"><?=hm_h($notice)?></div><?php endif;?>
<section class="hm-card"><div class="hm-head"><h2><?=hm_h($action['title'] ?? 'Helferaktion')?></h2><span class="hm-count"><?=count($recipients ?? [])?> unterschiedliche Helfer</span></div><div class="hm-meta"><span><i class="fa-regular fa-calendar"></i> <?=hm_date((string)($action['start_date'] ?? ''))?> – <?=hm_date((string)($action['end_date'] ?? ''))?></span><span><i class="fa-regular fa-envelope"></i> Jede Person erhält eine individuelle E-Mail – unabhängig von der Anzahl ihrer Schichten.</span></div></section>
<details class="hm-card hm-collapsible"><summary class="hm-head"><h2>1. Schichtbezogene Hinweise</h2><span class="hm-muted">Die Hinweise werden dauerhaft an der jeweiligen Schicht gespeichert.</span></summary><div class="hm-collapsible-content"><?php if(!$shiftRows):?><p class="hm-muted">Für diese Aktion sind keine aktiven Schichten angelegt.</p><?php else:?><form method="post"><input type="hidden" name="csrf_token" value="<?=hm_h($csrf)?>"><input type="hidden" name="action_id" value="<?=$actionId?>"><input type="hidden" name="do" value="save_notes"><div class="hm-table-wrap"><table class="hm-table"><thead><tr><th>Schicht</th><th>Zusätzlicher Hinweis für die Helfer dieser Schicht</th></tr></thead><tbody><?php foreach($shiftRows as $shift):?><tr><td><strong><?=hm_h(hm_weekday($shift['date']))?>, <?=hm_date($shift['date'])?></strong><br><?=hm_h($shift['station'])?><br><span class="hm-muted"><?=hm_h($shift['slot_label'])?></span></td><td><textarea name="notes[<?=$shift['day_id']?>][<?=$shift['station_id']?>][<?=hm_h($shift['slot_key'])?>]" maxlength="5000" placeholder="Optionaler Hinweis für diese Schicht …"><?=hm_h($shift['note'])?></textarea></td></tr><?php endforeach;?></tbody></table></div><div class="hm-toolbar"><button class="hm-btn primary" type="submit"><i class="fa-solid fa-floppy-disk"></i> Hinweise speichern</button><span class="hm-muted">Ein leer gelassenes Feld entfernt einen zuvor gespeicherten Hinweis.</span></div></form><?php endif;?></div></details>
<details class="hm-card hm-collapsible"><summary class="hm-head"><h2>2. E-Mail-Vorlage</h2></summary><div class="hm-collapsible-content"><div class="hm-toolbar"><a class="hm-btn" href="mitglieder_email_vorlagen.php" target="_blank" rel="noopener"><i class="fa-solid fa-pen-to-square"></i> Mailvorlagen bearbeiten</a></div><?php if(!$templates):?><div class="hm-alert error">Es sind noch keine Mailvorlagen vorhanden. Lege zunächst unter „Mailvorlagen bearbeiten“ eine Vorlage für Helfer-Schichterinnerungen an.</div><?php else:?><label class="hm-field">Vorlage auswählen<select id="templateSelect"><?php foreach($templates as $template):?><option value="<?=(int)$template['id']?>" <?=((int)$template['id']===$selectedTemplate?'selected':'')?>><?=hm_h($template['template_name'])?></option><?php endforeach;?></select></label><p class="hm-muted">Die Vorlage wird aus der zentralen Mailvorlagen-Verwaltung geladen. Die Platzhalter werden für jeden Helfer individuell befüllt.</p><?php endif;?></div></details>
<details class="hm-card hm-collapsible"><summary class="hm-head"><h2>3. Empfänger auswählen</h2><strong id="selectedCount">0 ausgewählt</strong></summary><div class="hm-collapsible-content"><?php if(!$recipients):?><p class="hm-muted">Es gibt noch keine aktiven Helfer mit eingetragenen Schichten.</p><?php else:?><div class="hm-toolbar"><button class="hm-btn" type="button" id="selectAll">Alle auswählen</button><button class="hm-btn" type="button" id="selectNone">Auswahl aufheben</button><span class="hm-muted">Bereits angeschriebene Personen können erneut ausgewählt und angeschrieben werden.</span></div><div class="hm-table-wrap"><table class="hm-table hm-recipient-table"><thead><tr><th><input type="checkbox" id="headerCheck" aria-label="Alle auswählen"></th><th>Name</th><th>E-Mail</th><th>Schichten</th><th>Letzter Versand</th></tr></thead><tbody><?php foreach($recipients as $person):?><tr><td><input type="checkbox" class="recipient-check" data-id="<?=$person['id']?>" <?=$person['valid_email']?'':'disabled'?> <?=$person['valid_email']?'checked':''?>></td><td><?=hm_h($person['full_name'])?></td><td><?=hm_h($person['email'])?><?php if(!$person['valid_email']):?><br><span class="hm-status failed">Keine gültige E-Mail-Adresse</span><?php endif;?></td><td><?=count($person['shifts'])?></td><td><?php if($person['last_sent']):?><span class="hm-status sent">Versendet: <?=date('d.m.Y H:i',strtotime($person['last_sent']))?></span><?php else:?><span class="hm-status">Noch nicht versendet</span><?php endif;?></td></tr><?php endforeach;?></tbody></table></div><?php endif;?></div></details>
<details class="hm-card hm-collapsible"><summary class="hm-head"><h2>4. Vorschau und Versand</h2></summary><div class="hm-collapsible-content"><?php if($templates && $recipients):?><label class="hm-field">Vorschau für<select id="previewRecipient"><?php foreach($recipients as $person):?><option value="<?=$person['id']?>"><?=hm_h($person['full_name'])?><?= $person['valid_email']?'':' (keine gültige E-Mail)'?></option><?php endforeach;?></select></label><div class="hm-preview"><div><strong>An:</strong> <span id="previewTo"></span></div><div><strong>Betreff:</strong> <span id="previewSubject"></span></div><hr><div class="hm-preview-body" id="previewBody"></div></div><div class="hm-toolbar"><button class="hm-btn" type="button" id="refreshPreview">Vorschau aktualisieren</button><button class="hm-btn primary" type="button" id="sendButton"><i class="fa-solid fa-paper-plane"></i> Ausgewählte Helfer anschreiben</button></div><div id="sendStatus" class="hm-muted" aria-live="polite"></div><div class="hm-progress"><span id="progressBar"></span></div><div id="sendLog" class="hm-log"></div><?php elseif(!$templates):?><p class="hm-muted">Der Versand ist erst möglich, wenn eine Mailvorlage angelegt wurde.</p><?php else:?><p class="hm-muted">Es gibt keine versandfähigen Empfänger.</p><?php endif;?></div></details>
</main></div>
<script>
const RECIPIENTS=<?=json_encode($recipientJs,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT)?>;
const TEMPLATES=<?=json_encode($templateJs,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT)?>;
const CSRF=<?=json_encode($csrf)?>;
const $=id=>document.getElementById(id);const esc=s=>String(s??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
let selected=new Set([...document.querySelectorAll('.recipient-check:checked')].map(c=>Number(c.dataset.id)));
function currentTemplate(){return TEMPLATES.find(t=>t.id===Number($('templateSelect')?.value))||null;}
function renderPreview(){const p=RECIPIENTS.find(x=>x.id===Number($('previewRecipient')?.value));const t=currentTemplate();if(!p||!t)return;$('previewTo').textContent=p.name+' <'+p.email+'>';
 const render=(s,html=false)=>String(s??'').replace(/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/g,(_,k)=>{const v=p.values[k];if(v===undefined)return '{{'+k+'}}';if(!html)return String(v);return esc(v).replace(/\r?\n/g,'<br>');});
 let body=t.body;if(!String(p.values.schicht_hinweise||'').trim())body=body.replace(/<(p|div)>\s*(?:<br>)?\s*\{\{\s*schicht_hinweise\s*\}\}\s*<\/\1>/gi,'');
 $('previewSubject').textContent=render(t.subject);$('previewBody').innerHTML=render(body,true);
}
function updateCount(){if($('selectedCount'))$('selectedCount').textContent=selected.size+' ausgewählt';if($('headerCheck')){$('headerCheck').checked=RECIPIENTS.filter(p=>p.valid_email).length>0&&RECIPIENTS.filter(p=>p.valid_email).every(p=>selected.has(p.id));$('headerCheck').indeterminate=RECIPIENTS.some(p=>p.valid_email&&selected.has(p.id))&&!$('headerCheck').checked;}}
document.querySelectorAll('.recipient-check').forEach(c=>c.addEventListener('change',()=>{const id=Number(c.dataset.id);c.checked?selected.add(id):selected.delete(id);updateCount();}));
$('selectAll')?.addEventListener('click',()=>{RECIPIENTS.filter(p=>p.valid_email).forEach(p=>selected.add(p.id));document.querySelectorAll('.recipient-check:not(:disabled)').forEach(c=>c.checked=true);updateCount();});
$('selectNone')?.addEventListener('click',()=>{selected.clear();document.querySelectorAll('.recipient-check').forEach(c=>c.checked=false);updateCount();});
$('headerCheck')?.addEventListener('change',e=>{RECIPIENTS.filter(p=>p.valid_email).forEach(p=>e.target.checked?selected.add(p.id):selected.delete(p.id));document.querySelectorAll('.recipient-check:not(:disabled)').forEach(c=>c.checked=e.target.checked);updateCount();});
$('templateSelect')?.addEventListener('change',renderPreview);$('previewRecipient')?.addEventListener('change',renderPreview);$('refreshPreview')?.addEventListener('click',renderPreview);updateCount();renderPreview();
async function sendOne(registrationId,templateId){const fd=new FormData();fd.append('ajax_action','send_one');fd.append('csrf_token',CSRF);fd.append('action_id','<?=$actionId?>');fd.append('registration_id',registrationId);fd.append('template_id',templateId);const response=await fetch(location.href,{method:'POST',body:fd,credentials:'same-origin'});return await response.json();}
$('sendButton')?.addEventListener('click',async()=>{const ids=[...selected];const t=currentTemplate();if(!t){alert('Bitte eine Mailvorlage auswählen.');return;}if(!ids.length){alert('Bitte mindestens einen Empfänger auswählen.');return;}if(!confirm(`Es werden ${ids.length} individuelle E-Mails versendet. Möchtest du fortfahren?`))return;const btn=$('sendButton');btn.disabled=true;$('sendStatus').textContent='Versand wird vorbereitet …';$('sendLog').textContent='';$('progressBar').style.width='0%';let sent=0,failed=0,processed=0;try{for(let i=0;i<ids.length;i++){const result=await sendOne(ids[i],t.id);processed++;if(result.success)sent++;else failed++;$('progressBar').style.width=Math.round(processed/ids.length*100)+'%';$('sendStatus').textContent=`Bearbeitet: ${processed} von ${ids.length} · Erfolgreich: ${sent} · Fehlgeschlagen: ${failed}`;$('sendLog').textContent+=(result.success?'✓ ':'✕ ')+(result.message||result.error||'Unbekannter Fehler')+'\n';if(result.rate_limit){$('sendStatus').textContent=result.error+' Noch nicht versendete Empfänger bleiben unbearbeitet.';break;}if(i<ids.length-1)await new Promise(resolve=>setTimeout(resolve,16000));}if(processed===ids.length)$('sendStatus').textContent=`Versand abgeschlossen: ${sent} erfolgreich, ${failed} fehlgeschlagen. Die Versandzeit wird nach dem Neuladen angezeigt.`;}catch(e){$('sendStatus').textContent='Versand unterbrochen: '+e.message;}finally{btn.disabled=false;}});
</script></body></html>
