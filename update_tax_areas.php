<?php
/**
 * Einmaliger Backfill der steuerlichen Tätigkeitsbereiche.
 * Muss im Stammverzeichnis /intern/ neben tax_area_mapping.csv liegen.
 * Verwendet ausschließlich bestehende Auth-, DB- und Verschlüsselungsroutinen.
 */
require_once __DIR__ . '/auth.php';
checkPermission('manage_accounting');
require_once __DIR__ . '/db_connection.php';
require_once __DIR__ . '/app/Core/MemberEncryption.php';

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('X-Robots-Tag: noindex, nofollow, noarchive');

$appRoot = __DIR__;
$db = new Database();
$conn = $db->getConnection();
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$crypto = new MemberEncryption($appRoot);

if (empty($_SESSION['tax_area_backfill_csrf'])) {
    $_SESSION['tax_area_backfill_csrf'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['tax_area_backfill_csrf'];
$confirmPhrase = 'ZUORDNUNG ÜBERNEHMEN';
$allowedAreas = ['ideell', 'asset_management', 'purpose_operation', 'commercial_business'];
$areaLabels = [
    'ideell' => 'Ideeller Bereich',
    'asset_management' => 'Vermögensverwaltung',
    'purpose_operation' => 'Zweckbetrieb',
    'commercial_business' => 'Steuerpflichtiger wirtschaftlicher Geschäftsbetrieb',
];

function h($value): string { return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function normText(?string $value): string {
    $value = trim((string)$value);
    $value = preg_replace('/\s+/u', ' ', $value) ?? $value;
    return function_exists('mb_strtolower') ? mb_strtolower($value, 'UTF-8') : strtolower($value);
}
function loadMapping(string $path, array $allowedAreas): array {
    if (!is_file($path) || !is_readable($path)) throw new RuntimeException('Die Datei tax_area_mapping.csv fehlt oder ist nicht lesbar.');
    $fh = fopen($path, 'rb');
    if (!$fh) throw new RuntimeException('Die Zuordnungsdatei kann nicht geöffnet werden.');
    $header = fgetcsv($fh, 0, ';', '"', '\\');
    if (!$header) throw new RuntimeException('Die Zuordnungsdatei ist leer.');
    $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string)$header[0]);
    $required = ['booking_date','description','category','tax_area','expected_count','source_file','source_rows'];
    if ($header !== $required) throw new RuntimeException('Unerwartetes Format der Zuordnungsdatei. Bitte das mitgelieferte Original verwenden.');
    $mapping = [];
    while (($values = fgetcsv($fh, 0, ';', '"', '\\')) !== false) {
        if (count($values) !== count($required)) throw new RuntimeException('Eine Zeile der Zuordnungsdatei hat eine falsche Spaltenzahl.');
        $r = array_combine($required, $values);
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $r['booking_date']);
        if (!$date || $date->format('Y-m-d') !== $r['booking_date']) throw new RuntimeException('Ungültiges Datum in der Zuordnungsdatei.');
        if (!in_array($r['tax_area'], $allowedAreas, true)) throw new RuntimeException('Ungültiger Tätigkeitsbereich in der Zuordnungsdatei.');
        $count = filter_var($r['expected_count'], FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
        if ($count === false) throw new RuntimeException('Ungültige erwartete Anzahl in der Zuordnungsdatei.');
        $key = $r['booking_date'] . "\x1F" . normText($r['description']) . "\x1F" . normText($r['category']);
        if (isset($mapping[$key])) throw new RuntimeException('Doppelter Identitätsschlüssel in der Zuordnungsdatei.');
        $mapping[$key] = $r;
        $mapping[$key]['expected_count'] = (int)$count;
    }
    fclose($fh);
    if (count($mapping) !== 284) throw new RuntimeException('Die Zuordnungsdatei enthält nicht die erwarteten 284 eindeutigen Buchungsschlüssel.');
    return $mapping;
}
function columnSet(PDO $conn, string $table): array {
    $set = [];
    foreach ($conn->query('SHOW COLUMNS FROM `' . str_replace('`', '', $table) . '`')->fetchAll(PDO::FETCH_ASSOC) as $row) $set[$row['Field']] = true;
    return $set;
}

$errors = [];
$plan = [];
$stats = ['mapping_keys'=>0,'expected_bookings'=>0,'matched'=>0,'to_update'=>0,'already_correct'=>0,'unmatched'=>0,'ambiguous_or_mismatched'=>0];
$applied = false;
$appliedCount = 0;
try {
    $columns = columnSet($conn, 'accounting_bookings');
    if (!isset($columns['tax_area'])) throw new RuntimeException('In der Tabelle accounting_bookings fehlt tax_area. Bitte zuerst die Migration 2026-11-10_02_accounting_tax_areas.sql einspielen.');
    foreach (['booking_date','booking_type','description_enc','amount_enc','fiscal_year_id'] as $requiredColumn) {
        if (!isset($columns[$requiredColumn])) throw new RuntimeException('Unerwartete Datenbankstruktur: accounting_bookings.' . $requiredColumn . ' fehlt.');
    }
    $hasInlineCategory = isset($columns['category_enc']);
    $hasLegacyCategory = isset($columns['category_id']);
    if (!$hasInlineCategory && !$hasLegacyCategory) throw new RuntimeException('Keine unterstützte Kategorie-Spalte in accounting_bookings gefunden.');

    if ($hasInlineCategory && $hasLegacyCategory) {
        $sql = 'SELECT b.*, b.category_enc AS _category_enc, c.name_enc AS _legacy_category_enc, y.status AS _year_status FROM accounting_bookings b LEFT JOIN accounting_categories c ON c.id=b.category_id LEFT JOIN accounting_fiscal_years y ON y.id=b.fiscal_year_id ORDER BY b.booking_date,b.id';
    } elseif ($hasInlineCategory) {
        $sql = 'SELECT b.*, b.category_enc AS _category_enc, NULL AS _legacy_category_enc, y.status AS _year_status FROM accounting_bookings b LEFT JOIN accounting_fiscal_years y ON y.id=b.fiscal_year_id ORDER BY b.booking_date,b.id';
    } else {
        $sql = 'SELECT b.*, NULL AS _category_enc, c.name_enc AS _legacy_category_enc, y.status AS _year_status FROM accounting_bookings b LEFT JOIN accounting_categories c ON c.id=b.category_id LEFT JOIN accounting_fiscal_years y ON y.id=b.fiscal_year_id ORDER BY b.booking_date,b.id';
    }

    $mapping = loadMapping(__DIR__ . '/tax_area_mapping.csv', $allowedAreas);
    $stats['mapping_keys'] = count($mapping);
    foreach ($mapping as $m) $stats['expected_bookings'] += $m['expected_count'];

    $dbRows = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    $indexed = [];
    foreach ($dbRows as $b) {
        $description = $crypto->decrypt($b['description_enc'] ?? null) ?? '';
        if (!empty($b['_category_enc'])) $category = $crypto->decrypt($b['_category_enc']);
        elseif (!empty($b['_legacy_category_enc'])) $category = $crypto->decrypt($b['_legacy_category_enc']);
        else $category = '';
        $key = (string)$b['booking_date'] . "\x1F" . normText($description) . "\x1F" . normText($category);
        $indexed[$key][] = $b;
    }

    foreach ($mapping as $key => $m) {
        $matches = $indexed[$key] ?? [];
        if (count($matches) === 0) {
            $stats['unmatched']++;
            $errors[] = 'Nicht gefunden: ' . $m['booking_date'] . ' · ' . $m['description'] . ' · ' . $m['category'] . ' (erwartet ' . $m['expected_count'] . ', gefunden 0).';
            continue;
        }
        if (count($matches) !== $m['expected_count']) {
            $stats['ambiguous_or_mismatched']++;
            $errors[] = 'Anzahl weicht ab: ' . $m['booking_date'] . ' · ' . $m['description'] . ' · ' . $m['category'] . ' (erwartet ' . $m['expected_count'] . ', gefunden ' . count($matches) . ').';
            continue;
        }
        $keyPlan = ['mapping'=>$m,'bookings'=>[]];
        foreach ($matches as $b) {
            if (!in_array($b['booking_type'], ['income','expense'], true)) {
                $stats['ambiguous_or_mismatched']++;
                $errors[] = 'Keine Einnahme/Ausgabe: Buchungs-ID ' . (int)$b['id'] . ' (' . $m['booking_date'] . ' · ' . $m['description'] . ').';
                continue 2;
            }
            if (($b['_year_status'] ?? null) === 'closed') {
                $stats['ambiguous_or_mismatched']++;
                $errors[] = 'Geschäftsjahr bereits festgeschrieben: Buchungs-ID ' . (int)$b['id'] . ' (' . $m['booking_date'] . ').';
                continue 2;
            }
            $current = $b['tax_area'] ?? null;
            if ($current !== null && $current !== '' && $current !== $m['tax_area']) {
                $stats['ambiguous_or_mismatched']++;
                $errors[] = 'Abweichende bestehende Zuordnung: Buchungs-ID ' . (int)$b['id'] . ' hat bereits ' . ($areaLabels[$current] ?? $current) . ', vorgesehen ist ' . $areaLabels[$m['tax_area']] . '.';
                continue 2;
            }
            $keyPlan['bookings'][] = ['id'=>(int)$b['id'],'current'=>$current,'tax_area'=>$m['tax_area']];
        }
        $stats['matched'] += count($matches);
        foreach ($keyPlan['bookings'] as $item) {
            if ($item['current'] === null || $item['current'] === '') $stats['to_update']++;
            else $stats['already_correct']++;
        }
        $plan[] = $keyPlan;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!hash_equals($csrf, (string)($_POST['csrf_token'] ?? ''))) throw new RuntimeException('Sitzung abgelaufen. Bitte Seite neu laden.');
        if (($_POST['action'] ?? '') !== 'apply') throw new RuntimeException('Ungültige Aktion.');
        if (!hash_equals($confirmPhrase, trim((string)($_POST['confirm_phrase'] ?? '')))) throw new RuntimeException('Bitte die Bestätigungsphrase exakt eingeben.');
        if ($errors) throw new RuntimeException('Es gibt Zuordnungsprobleme. Es wurde nichts geändert. Bitte zuerst die angezeigten Konflikte klären.');
        $conn->beginTransaction();
        try {
            $update = $conn->prepare('UPDATE accounting_bookings SET tax_area=? WHERE id=? AND (tax_area IS NULL OR tax_area=\'\')');
            foreach ($plan as $entry) {
                foreach ($entry['bookings'] as $item) {
                    if ($item['current'] === null || $item['current'] === '') {
                        $update->execute([$item['tax_area'], $item['id']]);
                        if ($update->rowCount() !== 1) throw new RuntimeException('Buchungs-ID ' . $item['id'] . ' wurde während der Ausführung verändert. Transaktion wird zurückgerollt.');
                        $appliedCount++;
                    }
                }
            }
            $conn->commit();
            $applied = true;
            $_SESSION['tax_area_backfill_done_at'] = date('c');
        } catch (Throwable $e) {
            if ($conn->inTransaction()) $conn->rollBack();
            throw $e;
        }
    }
} catch (Throwable $e) {
    if ($conn instanceof PDO && $conn->inTransaction()) $conn->rollBack();
    $errors[] = $e->getMessage();
}

$canApply = !$applied && !$errors && $stats['matched'] === $stats['expected_bookings'] && $stats['to_update'] > 0;
?>
<!doctype html>
<html lang="de"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Einmalige Zuordnung steuerlicher Tätigkeitsbereiche</title>
<style>
body{font-family:Arial,sans-serif;background:#f4f6f8;color:#202733;margin:0;padding:24px}.wrap{max-width:1100px;margin:0 auto}.card{background:#fff;border:1px solid #dfe3e8;border-radius:10px;padding:22px;margin:0 0 18px;box-shadow:0 2px 8px #00000008}h1{font-size:24px;margin:0 0 10px}.muted{color:#667085}.stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:10px}.stat{background:#f7f8fa;padding:14px;border-radius:8px}.stat strong{display:block;font-size:24px;margin-top:4px}.error{background:#fff1f0;border:1px solid #f3b6b2;color:#8a1c13;padding:10px 12px;border-radius:6px;margin:8px 0}.success{background:#ecfdf3;border:1px solid #a6e3bc;color:#176b3a;padding:14px;border-radius:7px}.warning{background:#fff8e6;border:1px solid #f2d38b;padding:12px;border-radius:7px}.btn{background:#174ea6;color:#fff;border:0;border-radius:6px;padding:11px 16px;font-weight:700;cursor:pointer}.btn:disabled{opacity:.45;cursor:not-allowed}input[type=text]{padding:10px;border:1px solid #cbd2da;border-radius:6px;width:min(100%,360px);box-sizing:border-box}.errors{max-height:420px;overflow:auto}.errors .error{font-size:14px}.actions{display:flex;gap:12px;align-items:center;flex-wrap:wrap;margin-top:14px}@media(max-width:600px){body{padding:10px}.card{padding:15px}}
</style></head><body><div class="wrap">
<section class="card"><h1>Einmalige Zuordnung der Tätigkeitsbereiche</h1><p class="muted">Die Seite verwendet die bestehende Anmeldung, Berechtigung <code>manage_accounting</code>, Datenbankverbindung und <code>MemberEncryption</code> der Anwendung. Standardmäßig erfolgt nur eine Prüfung; Daten werden erst nach ausdrücklicher Bestätigung geändert.</p>
<?php if($applied): ?><div class="success"><strong>Zuordnung erfolgreich abgeschlossen.</strong><br><?= (int)$appliedCount ?> Buchungen wurden aktualisiert. Es wurde ausschließlich die Spalte <code>tax_area</code> geändert.</div><?php endif; ?>
<?php if($errors): ?><h2>Prüfhinweise / Konflikte</h2><div class="errors"><?php foreach(array_unique($errors) as $msg): ?><div class="error"><?=h($msg)?></div><?php endforeach; ?></div><?php endif; ?>
</section>
<section class="card"><h2>Prüfergebnis</h2><div class="stats">
<div class="stat">Eindeutige Zuordnungsschlüssel<strong><?= (int)$stats['mapping_keys'] ?></strong></div>
<div class="stat">Erwartete Buchungen<strong><?= (int)$stats['expected_bookings'] ?></strong></div>
<div class="stat">Eindeutig gefunden<strong><?= (int)$stats['matched'] ?></strong></div>
<div class="stat">Noch zu aktualisieren<strong><?= (int)$stats['to_update'] ?></strong></div>
<div class="stat">Bereits korrekt<strong><?= (int)$stats['already_correct'] ?></strong></div>
<div class="stat">Nicht gefunden<strong><?= (int)$stats['unmatched'] ?></strong></div>
<div class="stat">Anzahl-/Datenkonflikte<strong><?= (int)$stats['ambiguous_or_mismatched'] ?></strong></div>
</div>
<p class="warning" style="margin-top:16px">Nicht zugeordnet werden die 12 ausdrücklich ausgenommenen Buchungen und die 29 internen Transfers/Bestandsnachweise ohne steuerlichen Vorgang. Die Zuordnung wird nur ausgeführt, wenn jeder vorgesehene Datensatz exakt und eindeutig gefunden wurde und kein Geschäftsjahr bereits festgeschrieben ist.</p>
<?php if($canApply): ?><form method="post" class="actions" onsubmit="return confirm('Die steuerlichen Tätigkeitsbereiche jetzt endgültig übernehmen?')"><input type="hidden" name="csrf_token" value="<?=h($csrf)?>"><input type="hidden" name="action" value="apply"><label>Zur Bestätigung <input type="text" name="confirm_phrase" required autocomplete="off" placeholder="ZUORDNUNG ÜBERNEHMEN"></label><button class="btn" type="submit">Zuordnung jetzt übernehmen</button></form>
<?php elseif(!$applied && !$errors): ?><p class="muted">Es gibt nichts zu aktualisieren. Bereits korrekt zugeordnete Buchungen bleiben unverändert.</p><?php endif; ?>
</section>
<section class="card"><h2>Nach erfolgreicher Ausführung</h2><p>Bitte diese PHP-Datei und <code>tax_area_mapping.csv</code> unmittelbar nach erfolgreicher Ausführung wieder vom Webserver löschen. Die Zuordnung ist dann in der Buchführung gespeichert; ein erneuter Aufruf ist nicht erforderlich.</p></section>
</div></body></html>
