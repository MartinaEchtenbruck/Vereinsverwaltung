<?php
if (!defined('APP_ENTRY_POINT')) { http_response_code(404); exit; }

$appRoot = dirname(__DIR__, 4);
require_once $appRoot . '/auth.php';
checkPermission('manage_accounting');
require_once $appRoot . '/db_connection.php';
require_once $appRoot . '/app/Core/MemberEncryption.php';

$db = new Database();
$conn = $db->getConnection();
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$crypto = new MemberEncryption($appRoot);

// Zentraler Betriebsmodus: Während der Altbestandsbereinigung werden Kontrollen
// nicht durch spätere Korrekturen an Buchungen automatisch entwertet.
$accountingSettings = $conn->query('SELECT mode, regular_activated_at FROM accounting_system_settings WHERE id=1')->fetch(PDO::FETCH_ASSOC);
$accountingMode = ($accountingSettings && $accountingSettings['mode'] === 'regular') ? 'regular' : 'migration';
$migrationMode = ($accountingMode === 'migration');

if (empty($_SESSION['accounting_csrf'])) $_SESSION['accounting_csrf'] = bin2hex(random_bytes(32));
$csrf = $_SESSION['accounting_csrf'];
$message = (string)($_GET['meldung'] ?? '');
$error = '';

function accH($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function accMoneyToCents(string $value): int {
    $value = trim(str_replace(['€', ' '], '', $value));
    if ($value === '') throw new RuntimeException('Bitte einen Betrag eingeben.');
    if (str_contains($value, ',') && str_contains($value, '.')) {
        $value = str_replace('.', '', $value);
        $value = str_replace(',', '.', $value);
    } else {
        $value = str_replace(',', '.', $value);
    }
    if (!preg_match('/^-?\d+(?:\.\d{1,2})?$/', $value)) throw new RuntimeException('Bitte einen gültigen Geldbetrag mit höchstens zwei Nachkommastellen eingeben.');
    return (int)round((float)$value * 100);
}
function accCentsToDisplay(int $cents): string { return number_format($cents / 100, 2, ',', '.') . ' €'; }
function accRedirect(string $msg, string $target = ''): never { header('Location: buchungen.php?meldung=' . rawurlencode($msg) . $target); exit; }
function accDateValid(string $date): bool {
    $d = DateTimeImmutable::createFromFormat('!Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

/** Fingerprint der für eine interne Kontrolle relevanten Buchungen.
 * updated_at sorgt dafür, dass auch eine nachträgliche Änderung erkannt wird.
 */
function accControlFingerprint(PDO $conn, int $yearId, string $kind, string $until, ?string $monthStart = null): string {
    $dateCondition = $monthStart !== null ? 'booking_date >= ? AND booking_date < ?' : 'booking_date <= ?';
    if ($kind === 'bank') {
        $accountCondition = "((booking_type IN ('income','expense') AND payment_channel='bank') OR (booking_type='transfer' AND (transfer_from='bank' OR transfer_to='bank')))";
    } else {
        $accountCondition = "((booking_type IN ('income','expense') AND payment_channel='cash') OR (booking_type='transfer' AND (transfer_from='cash' OR transfer_to='cash')))";
    }
    // Eine Kassenzählung prüft den Kassenbestand seit der vorherigen Zählung.
    // Deshalb müssen Fingerprints für die Barkasse auch Buchungen aus vorherigen
    // Geschäftsjahren bis zum Stichtag berücksichtigen (Übertrag des Kassenbestands).
    if ($kind === 'cash') {
        $sql = 'SELECT id,fiscal_year_id,booking_date,updated_at,booking_type,payment_channel,transfer_from,transfer_to FROM accounting_bookings WHERE '.$dateCondition.' AND '.$accountCondition.' ORDER BY booking_date,id,fiscal_year_id';
        $params = $monthStart !== null ? [$monthStart,$until] : [$until];
    } else {
        $sql = 'SELECT id,fiscal_year_id,booking_date,updated_at,booking_type,payment_channel,transfer_from,transfer_to FROM accounting_bookings WHERE fiscal_year_id=? AND '.$dateCondition.' AND '.$accountCondition.' ORDER BY booking_date,id';
        $params = $monthStart !== null ? [$yearId,$monthStart,$until] : [$yearId,$until];
    }
    $q=$conn->prepare($sql);
    $q->execute($params);
    return hash('sha256', json_encode($q->fetchAll(PDO::FETCH_ASSOC), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
}
/** Legacy-Fingerprint der früheren Implementierung: nur Buchungen des jeweiligen Geschäftsjahres.
 * Dient ausschließlich dazu, bestehende Kontrollen sicher auf den jahresübergreifenden
 * Fingerprint umzustellen, ohne bereits erkannte Änderungen zu übergehen.
 */
function accLegacyCashControlFingerprint(PDO $conn, int $yearId, string $until): string {
    $accountCondition = "((booking_type IN ('income','expense') AND payment_channel='cash') OR (booking_type='transfer' AND (transfer_from='cash' OR transfer_to='cash')) )";
    $sql = 'SELECT id,booking_date,updated_at,booking_type,payment_channel,transfer_from,transfer_to FROM accounting_bookings WHERE fiscal_year_id=? AND booking_date<=? AND '.$accountCondition.' ORDER BY booking_date,id';
    $q=$conn->prepare($sql);
    $q->execute([$yearId,$until]);
    return hash('sha256', json_encode($q->fetchAll(PDO::FETCH_ASSOC), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
}
function accCashBalanceAtDate(PDO $conn, MemberEncryption $crypto, array $year, string $date): int {
    $balance=accDecryptCents($crypto,$year['opening_cash_enc'] ?? null);
    $q=$conn->prepare('SELECT * FROM accounting_bookings WHERE fiscal_year_id=? AND booking_date<=? ORDER BY booking_date,id');
    $q->execute([(int)$year['id'],$date]);
    foreach($q->fetchAll(PDO::FETCH_ASSOC) as $b){
        $amount=accDecryptCents($crypto,$b['amount_enc']);
        if($b['booking_type']==='income' && $b['payment_channel']==='cash') $balance+=$amount;
        elseif($b['booking_type']==='expense' && $b['payment_channel']==='cash') $balance-=$amount;
        elseif($b['booking_type']==='transfer' && $b['transfer_to']==='cash') $balance+=$amount;
        elseif($b['booking_type']==='transfer' && $b['transfer_from']==='cash') $balance-=$amount;
    }
    return $balance;
}

// Steuerliche Tätigkeitsbereiche gemeinnütziger Körperschaften.
function accTaxAreaLabel(?string $area): string {
    return [
        'ideell' => 'Ideeller Bereich',
        'asset_management' => 'Vermögensverwaltung',
        'purpose_operation' => 'Zweckbetrieb',
        'commercial_business' => 'Steuerpflichtiger wirtschaftlicher Geschäftsbetrieb',
    ][$area ?? ''] ?? 'Noch nicht zugeordnet';
}

// Kategorien sind interne Freitextmerkmale. Die Vorschlagsliste wird je Geschäftsjahr
// aus den tatsächlich verwendeten Buchungskategorien aufgebaut.
function accCategoryKey(string $name): string {
    $name = preg_replace('/\s+/u', ' ', trim($name)) ?? trim($name);
    return function_exists('mb_strtolower') ? mb_strtolower($name, 'UTF-8') : strtolower($name);
}
function accNormalizeCategory(string $name): string {
    return preg_replace('/\s+/u', ' ', trim($name)) ?? trim($name);
}
function accLegacyCategoryMap(PDO $conn, MemberEncryption $crypto): array {
    $map = [];
    foreach ($conn->query('SELECT id,name_enc FROM accounting_categories') as $row) {
        $map[(int)$row['id']] = (string)$crypto->decrypt($row['name_enc']);
    }
    return $map;
}
function accYearCategories(PDO $conn, MemberEncryption $crypto, int $yearId, array $legacyMap): array {
    $q = $conn->prepare('SELECT category_enc,category_id FROM accounting_bookings WHERE fiscal_year_id=? ORDER BY booking_date,id');
    $q->execute([$yearId]);
    $unique = [];
    foreach ($q->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $name = !empty($row['category_enc']) ? $crypto->decrypt($row['category_enc']) : ($legacyMap[(int)($row['category_id'] ?? 0)] ?? '');
        $name = accNormalizeCategory((string)$name);
        if ($name === '') continue;
        $key = accCategoryKey($name);
        if (!array_key_exists($key, $unique)) $unique[$key] = $name;
    }
    natcasesort($unique);
    return array_values($unique);
}

function accDecryptCents(MemberEncryption $crypto, ?string $value): int {
    if ($value === null || $value === '') return 0;
    $plain = $crypto->decrypt($value);
    return (int)round((float)$plain * 100);
}
function accYearBalances(PDO $conn, MemberEncryption $crypto, array $year): array {
    $bank = accDecryptCents($crypto, $year['opening_bank_enc'] ?? null);
    $cash = accDecryptCents($crypto, $year['opening_cash_enc'] ?? null);
    $income = 0; $expense = 0; $rows = [];
    $q = $conn->prepare('SELECT * FROM accounting_bookings WHERE fiscal_year_id=? ORDER BY booking_date,id');
    $q->execute([(int)$year['id']]);
    foreach ($q->fetchAll(PDO::FETCH_ASSOC) as $b) {
        $amount = accDecryptCents($crypto, $b['amount_enc']);
        if ($b['booking_type'] === 'income') {
            if ($b['payment_channel'] === 'bank') $bank += $amount; else $cash += $amount;
            $income += $amount;
        } elseif ($b['booking_type'] === 'expense') {
            if ($b['payment_channel'] === 'bank') $bank -= $amount; else $cash -= $amount;
            $expense += $amount;
        } elseif ($b['transfer_from'] === 'bank') {
            $bank -= $amount; $cash += $amount;
        } else {
            $cash -= $amount; $bank += $amount;
        }
        $b['amount_cents'] = $amount; $b['bank_balance'] = $bank; $b['cash_balance'] = $cash;
        $b['description'] = $crypto->decrypt($b['description_enc']);
        $b['paper_reference'] = $crypto->decrypt($b['paper_reference_enc']);
        $rows[] = $b;
    }
    return ['opening_bank'=>accDecryptCents($crypto, $year['opening_bank_enc'] ?? null),
        'opening_cash'=>accDecryptCents($crypto, $year['opening_cash_enc'] ?? null),
        'closing_bank'=>$bank, 'closing_cash'=>$cash, 'income'=>$income, 'expense'=>$expense,
        'result'=>$income-$expense, 'bookings'=>$rows];
}

/* Geschäftsjahre für aktuelles und vorheriges Vereinsjahr sicherstellen.
   Das vorherige Jahr bleibt offen, bis es tatsächlich abgeschlossen wird. */
$today = new DateTimeImmutable('today');
$startYear = (int)$today->format('n') >= 8 ? (int)$today->format('Y') : (int)$today->format('Y') - 1;
foreach ([$startYear - 1, $startYear] as $year) {
    $label = substr((string)$year, -2) . '-' . substr((string)($year + 1), -2);
    $start = sprintf('%04d-08-01', $year);
    $end = sprintf('%04d-07-31', $year + 1);
    $stmt = $conn->prepare('INSERT IGNORE INTO accounting_fiscal_years (label,start_date,end_date,status,opening_bank_enc,opening_cash_enc) VALUES (?,?,?,?,?,?)');
    $stmt->execute([$label, $start, $end, 'open', $crypto->encrypt('0.00'), $crypto->encrypt('0.00')]);
}

// Beim ausdrücklich gesetzten Wechsel in den regulären Betrieb werden alle bis
// dahin vorhandenen Kontrollen einmalig auf den dann bereinigten Buchungsstand
// verankert. Erst spätere Änderungen können sie anschließend entwerten.
if ($accountingMode === 'regular' && empty($accountingSettings['regular_activated_at'])) {
    $conn->beginTransaction();
    try {
        $bankRows = $conn->query('SELECT id,fiscal_year_id,reconciled_until FROM accounting_bank_reconciliations')->fetchAll(PDO::FETCH_ASSOC);
        foreach ($bankRows as $row) {
            $monthStart = (new DateTimeImmutable($row['reconciled_until']))->modify('first day of this month')->format('Y-m-d');
            $monthEndExclusive = (new DateTimeImmutable($monthStart))->modify('+1 month')->format('Y-m-d');
            $fp = accControlFingerprint($conn, (int)$row['fiscal_year_id'], 'bank', $monthEndExclusive, $monthStart);
            $conn->prepare('UPDATE accounting_bank_reconciliations SET booking_fingerprint=? WHERE id=?')->execute([$fp, (int)$row['id']]);
        }
        $cashRows = $conn->query('SELECT id,fiscal_year_id,counted_on FROM accounting_cash_counts')->fetchAll(PDO::FETCH_ASSOC);
        foreach ($cashRows as $row) {
            $fp = accControlFingerprint($conn, (int)$row['fiscal_year_id'], 'cash', $row['counted_on']);
            $conn->prepare('UPDATE accounting_cash_counts SET booking_fingerprint=? WHERE id=?')->execute([$fp, (int)$row['id']]);
        }
        $conn->prepare("UPDATE accounting_system_settings SET regular_activated_at=NOW(),updated_at=NOW() WHERE id=1 AND mode='regular' AND regular_activated_at IS NULL")->execute();
        $conn->commit();
        $accountingSettings['regular_activated_at'] = date('Y-m-d H:i:s');
    } catch (Throwable $e) {
        if ($conn->inTransaction()) $conn->rollBack();
        throw $e;
    }
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!hash_equals($csrf, (string)($_POST['csrf_token'] ?? ''))) throw new RuntimeException('Sitzung abgelaufen. Bitte Seite neu laden.');
        $action = (string)($_POST['action'] ?? '');

        if ($action === 'save_booking') {
            $id = (int)($_POST['booking_id'] ?? 0);
            $yearId = (int)($_POST['fiscal_year_id'] ?? 0);
            $date = (string)($_POST['booking_date'] ?? '');
            $type = (string)($_POST['booking_type'] ?? '');
            $description = trim((string)($_POST['description'] ?? ''));
            $reference = trim((string)($_POST['paper_reference'] ?? ''));
            if (!accDateValid($date) || $description === '') throw new RuntimeException('Bitte Datum und Beschreibung korrekt ausfüllen.');
            if (!in_array($type, ['income','expense','transfer'], true)) throw new RuntimeException('Ungültige Buchungsart.');
            $q = $conn->prepare('SELECT * FROM accounting_fiscal_years WHERE id=?');
            $q->execute([$yearId]); $year = $q->fetch(PDO::FETCH_ASSOC);
            if (!$year || $date < $year['start_date'] || $date > $year['end_date']) throw new RuntimeException('Das Buchungsdatum liegt nicht im ausgewählten Geschäftsjahr.');
            if ($year['status'] === 'closed') throw new RuntimeException('Das Geschäftsjahr ist abgeschlossen und kann nicht mehr verändert werden.');
            if ($id) {
                $q=$conn->prepare('SELECT id FROM accounting_bookings WHERE id=? AND fiscal_year_id=?');$q->execute([$id,$yearId]);
                if(!$q->fetchColumn()) throw new RuntimeException('Buchung nicht gefunden.');
            }

            $category = null; $channel = null; $from = null; $to = null; $taxArea = null;
            if ($type === 'transfer') {
                $from = (string)($_POST['transfer_from'] ?? '');
                $to = (string)($_POST['transfer_to'] ?? '');
                if (!in_array($from,['bank','cash'],true) || !in_array($to,['bank','cash'],true) || $from === $to) throw new RuntimeException('Bitte unterschiedliche Quell- und Zielkonten für die Umbuchung auswählen.');
            } else {
                $channel = (string)($_POST['payment_channel'] ?? '');
                if (!in_array($channel,['bank','cash'],true)) throw new RuntimeException('Bitte Zahlungsweg auswählen.');
                $category = accNormalizeCategory((string)($_POST['category'] ?? ''));
                if ($category === '') throw new RuntimeException('Bitte eine Kategorie eingeben oder auswählen.');
                if ((function_exists('mb_strlen') ? mb_strlen($category, 'UTF-8') : strlen($category)) > 150) throw new RuntimeException('Die Kategorie darf höchstens 150 Zeichen enthalten.');
                $legacyMapForSave = accLegacyCategoryMap($conn, $crypto);
                foreach (accYearCategories($conn, $crypto, $yearId, $legacyMapForSave) as $existingCategory) {
                    if (accCategoryKey($existingCategory) === accCategoryKey($category)) { $category = $existingCategory; break; }
                }
                $taxArea = (string)($_POST['tax_area'] ?? '');
                $allowedTaxAreas = ['ideell','asset_management','purpose_operation','commercial_business'];
                if (!in_array($taxArea, $allowedTaxAreas, true)) throw new RuntimeException('Bitte einen steuerlichen Tätigkeitsbereich auswählen.');
            }
            $amount = abs(accMoneyToCents((string)($_POST['amount'] ?? '0')));
            if ($amount <= 0) throw new RuntimeException('Der Betrag muss größer als null sein.');
            $conn->beginTransaction();
            $categoryEnc = $category === null ? null : $crypto->encrypt($category);
            $descriptionEnc = $crypto->encrypt($description);
            $amountEnc = $crypto->encrypt(number_format($amount/100,2,'.',''));
            $referenceEnc = $crypto->encrypt($reference === '' ? null : $reference);
            if ($id) {
                $conn->prepare('UPDATE accounting_bookings SET fiscal_year_id=?,booking_date=?,booking_type=?,payment_channel=?,transfer_from=?,transfer_to=?,category_id=NULL,category_enc=?,tax_area=?,description_enc=?,amount_enc=?,paper_reference_enc=? WHERE id=?')
                    ->execute([$yearId,$date,$type,$channel,$from,$to,$categoryEnc,$taxArea,$descriptionEnc,$amountEnc,$referenceEnc,$id]);
            } else {
                $conn->prepare('INSERT INTO accounting_bookings (fiscal_year_id,booking_date,booking_type,payment_channel,transfer_from,transfer_to,category_id,category_enc,tax_area,description_enc,amount_enc,paper_reference_enc,created_by) VALUES (?,?,?,?,?,?,NULL,?,?,?,?,?,?)')
                    ->execute([$yearId,$date,$type,$channel,$from,$to,$categoryEnc,$taxArea,$descriptionEnc,$amountEnc,$referenceEnc,(int)$_SESSION['user_id']]);
            }
            $conn->commit();
            $savedId = $id ?: (int)$conn->lastInsertId();
            accRedirect($id ? 'Buchung wurde aktualisiert.' : 'Buchung wurde angelegt.', '&jahr=' . $yearId . '#buchung-row-' . $savedId);
        }

        if ($action === 'delete_booking') {
            $id=(int)($_POST['booking_id']??0);
            $yearId=(int)($_POST['fiscal_year_id']??0);
            $q=$conn->prepare('SELECT b.id,b.fiscal_year_id,y.status FROM accounting_bookings b JOIN accounting_fiscal_years y ON y.id=b.fiscal_year_id WHERE b.id=?');$q->execute([$id]);$row=$q->fetch(PDO::FETCH_ASSOC);
            if(!$row) throw new RuntimeException('Buchung nicht gefunden.');
            if($row['status']==='closed') throw new RuntimeException('Buchungen eines abgeschlossenen Geschäftsjahres können nicht gelöscht werden.');
            $conn->prepare('DELETE FROM accounting_bookings WHERE id=?')->execute([$id]);
            accRedirect('Buchung wurde gelöscht.', '&jahr=' . (int)$row['fiscal_year_id'] . '#buchungen-liste');
        }


        if ($action === 'reconcile_bank_month') {
            $yearId=(int)($_POST['fiscal_year_id']??0); $month=(string)($_POST['month_key']??'');
            if(!preg_match('/^\d{4}-\d{2}$/',$month)) throw new RuntimeException('Ungültiger Abrechnungsmonat.');
            $q=$conn->prepare('SELECT * FROM accounting_fiscal_years WHERE id=?');$q->execute([$yearId]);$year=$q->fetch(PDO::FETCH_ASSOC);
            if(!$year || $year['status']==='closed') throw new RuntimeException('Das Geschäftsjahr ist nicht bearbeitbar.');
            $monthStart=$month.'-01'; $monthEnd=(new DateTimeImmutable($monthStart))->modify('last day of this month')->format('Y-m-d');
            if($monthStart<$year['start_date'] || $monthStart>$year['end_date'] || $monthStart>$today->format('Y-m-d')) throw new RuntimeException('Der Monat liegt außerhalb des bereits begonnenen Geschäftsjahreszeitraums.');
            $fingerprint=$migrationMode ? null : accControlFingerprint($conn,$yearId,'bank',(new DateTimeImmutable($monthStart))->modify('+1 month')->format('Y-m-d'),$monthStart);
            $note=trim((string)($_POST['control_note']??''));
            $conn->prepare('INSERT INTO accounting_bank_reconciliations (fiscal_year_id,reconciled_until,performed_on,receipts_complete,transactions_complete,note_enc,created_by,booking_fingerprint) VALUES (?,?,CURDATE(),1,1,?,?,?)')
                ->execute([$yearId,$monthEnd,$note===''?null:$crypto->encrypt($note),(int)$_SESSION['user_id'],$fingerprint]);
            accRedirect('Kontoauszugabgleich für '.(new DateTimeImmutable($monthStart))->format('m/Y').' wurde gespeichert.','&jahr='.$yearId.'#buchungen-liste');
        }

        if ($action === 'save_cash_count') {
            $yearId=(int)($_POST['fiscal_year_id']??0); $date=(string)($_POST['counted_on']??'');
            if(!accDateValid($date)) throw new RuntimeException('Bitte ein gültiges Zähldatum auswählen.');
            $q=$conn->prepare('SELECT * FROM accounting_fiscal_years WHERE id=?');$q->execute([$yearId]);$year=$q->fetch(PDO::FETCH_ASSOC);
            if(!$year || $year['status']==='closed') throw new RuntimeException('Das Geschäftsjahr ist nicht bearbeitbar.');
            if($date<$year['start_date'] || $date>$year['end_date'] || $date>$today->format('Y-m-d')) throw new RuntimeException('Das Zähldatum muss innerhalb des Geschäftsjahres liegen und darf nicht in der Zukunft liegen.');
            $actual=accMoneyToCents((string)($_POST['actual_amount']??''));
            if($actual<0) throw new RuntimeException('Der gezählte Kassenbestand darf nicht negativ sein.');
            $expected=accCashBalanceAtDate($conn,$crypto,$year,$date); $difference=$actual-$expected;
            $fingerprint=$migrationMode ? null : accControlFingerprint($conn,$yearId,'cash',$date); $note=trim((string)($_POST['count_note']??''));
            $conn->prepare('INSERT INTO accounting_cash_counts (fiscal_year_id,counted_on,actual_amount_enc,calculated_amount_enc,difference_enc,note_enc,created_by,booking_fingerprint) VALUES (?,?,?,?,?,?,?,?)')
                ->execute([$yearId,$date,$crypto->encrypt(number_format($actual/100,2,'.','')),$crypto->encrypt(number_format($expected/100,2,'.','')),$crypto->encrypt(number_format($difference/100,2,'.','')),$note===''?null:$crypto->encrypt($note),(int)$_SESSION['user_id'],$fingerprint]);
            accRedirect($difference===0?'Kassenzählung stimmt mit dem rechnerischen Bestand überein.':'Kassenzählung gespeichert. Es besteht eine Differenz von '.accCentsToDisplay($difference).'.','&jahr='.$yearId.'#buchungen-liste');
        }

        if ($action === 'finalize_year') {
            $yearId = (int)($_POST['fiscal_year_id'] ?? 0);
            $q = $conn->prepare('SELECT * FROM accounting_fiscal_years WHERE id=? FOR UPDATE');
            $conn->beginTransaction();
            $q->execute([$yearId]); $year = $q->fetch(PDO::FETCH_ASSOC);
            if (!$year) throw new RuntimeException('Geschäftsjahr nicht gefunden.');
            if ($year['status'] === 'closed') throw new RuntimeException('Dieses Geschäftsjahr ist bereits festgeschrieben.');
            if ($today->format('Y-m-d') <= $year['end_date']) throw new RuntimeException('Eine Festschreibung ist erst nach dem Ende des Geschäftsjahres möglich.');
            $unassigned = $conn->prepare("SELECT COUNT(*) FROM accounting_bookings WHERE fiscal_year_id=? AND booking_type IN ('income','expense') AND tax_area IS NULL");
            $unassigned->execute([$yearId]);
            if ((int)$unassigned->fetchColumn() > 0) throw new RuntimeException('Festschreibung nicht möglich: Bitte ordne zunächst allen Einnahmen und Ausgaben einen steuerlichen Tätigkeitsbereich zu.');
            $stats = accYearBalances($conn, $crypto, $year);
            $conn->prepare("UPDATE accounting_fiscal_years SET status='closed', closing_bank_enc=?, closing_cash_enc=?, closed_at=NOW(), closed_by=? WHERE id=?")
                ->execute([$crypto->encrypt(number_format($stats['closing_bank']/100,2,'.','')),
                    $crypto->encrypt(number_format($stats['closing_cash']/100,2,'.','')),
                    (int)$_SESSION['user_id'], $yearId]);
            // Folgejahr erhält die festgeschriebenen Endbestände als Anfangsbestände.
            $next = $conn->prepare('SELECT id,status FROM accounting_fiscal_years WHERE start_date=? LIMIT 1');
            $nextStart = (new DateTimeImmutable($year['end_date']))->modify('+1 day')->format('Y-m-d');
            $next->execute([$nextStart]); $nextYear = $next->fetch(PDO::FETCH_ASSOC);
            if ($nextYear && $nextYear['status'] !== 'closed') {
                $conn->prepare('UPDATE accounting_fiscal_years SET opening_bank_enc=?, opening_cash_enc=? WHERE id=?')->execute([
                    $crypto->encrypt(number_format($stats['closing_bank']/100,2,'.','')),
                    $crypto->encrypt(number_format($stats['closing_cash']/100,2,'.','')), (int)$nextYear['id']]);
            }
            $conn->commit();
            accRedirect('Geschäftsjahr wurde endgültig festgeschrieben.');
        }

    }
} catch (Throwable $e) {
    if ($conn->inTransaction()) $conn->rollBack();
    $error = $e instanceof RuntimeException ? $e->getMessage() : 'Die Aktion konnte nicht ausgeführt werden.';
    error_log('Buchführung: '.$e->getMessage());
}

$years=$conn->query('SELECT fy.*,u.full_name AS closed_by_name FROM accounting_fiscal_years fy LEFT JOIN users u ON u.id=fy.closed_by ORDER BY fy.start_date DESC')->fetchAll(PDO::FETCH_ASSOC);
$selectedYearId=(int)($_GET['jahr']??$_POST['fiscal_year_id']??0);
if(!$selectedYearId && $years) {
    foreach($years as $y) if($today->format('Y-m-d') >= $y['start_date'] && $today->format('Y-m-d') <= $y['end_date']) {$selectedYearId=(int)$y['id'];break;}
    if(!$selectedYearId)$selectedYearId=(int)$years[0]['id'];
}
$selectedYear=null;foreach($years as $y)if((int)$y['id']===$selectedYearId)$selectedYear=$y;
if(!$selectedYear && $years){$selectedYear=$years[0];$selectedYearId=(int)$selectedYear['id'];}

$legacyCategoryMap = accLegacyCategoryMap($conn, $crypto);
$categories = $selectedYear ? accYearCategories($conn, $crypto, $selectedYearId, $legacyCategoryMap) : [];
$editing=null;
if(isset($_GET['edit'])){
    $q=$conn->prepare('SELECT * FROM accounting_bookings WHERE id=? AND fiscal_year_id=?');$q->execute([(int)$_GET['edit'],$selectedYearId]);$editing=$q->fetch(PDO::FETCH_ASSOC)?:null;
    if($editing){
        $editing['description']=$crypto->decrypt($editing['description_enc']);
        $editing['amount']=$crypto->decrypt($editing['amount_enc']);
        $editing['paper_reference']=$crypto->decrypt($editing['paper_reference_enc']);
        $editing['category']=!empty($editing['category_enc']) ? $crypto->decrypt($editing['category_enc']) : ($legacyCategoryMap[(int)($editing['category_id']??0)] ?? '');
        if ($selectedYear && $selectedYear['status'] === 'closed') $editing = null;
    }
}
$bookings=[];$bankBalance=0;$cashBalance=0;$incomeTotal=0;$expenseTotal=0;
$openingBankCents=0;$openingCashCents=0;$yearStats=null;
if($selectedYear){
    // Anfangsbestände aus dem Vorjahr ableiten; das erste vorhandene Jahr behält seinen initialen Bestand.
    $prevQ=$conn->prepare('SELECT * FROM accounting_fiscal_years WHERE end_date < ? ORDER BY end_date DESC LIMIT 1');
    $prevQ->execute([$selectedYear['start_date']]); $previousYear=$prevQ->fetch(PDO::FETCH_ASSOC);
    if($previousYear){
        if($previousYear['status']==='closed' && !empty($previousYear['closing_bank_enc']) && !empty($previousYear['closing_cash_enc'])){
            $openingBankCents=accDecryptCents($crypto,$previousYear['closing_bank_enc']);
            $openingCashCents=accDecryptCents($crypto,$previousYear['closing_cash_enc']);
        } else {
            // Für bereits früher geschlossene Jahre ohne gespeicherte Abschlusswerte wird rechnerisch rekonstruiert.
            $prevStats=accYearBalances($conn,$crypto,$previousYear);
            $openingBankCents=$prevStats['closing_bank']; $openingCashCents=$prevStats['closing_cash'];
        }
        if($selectedYear['status']!=='closed'){
            $conn->prepare('UPDATE accounting_fiscal_years SET opening_bank_enc=?,opening_cash_enc=? WHERE id=?')->execute([
                $crypto->encrypt(number_format($openingBankCents/100,2,'.','')),
                $crypto->encrypt(number_format($openingCashCents/100,2,'.','')), $selectedYearId]);
            $selectedYear['opening_bank_enc']=$crypto->encrypt(number_format($openingBankCents/100,2,'.',''));
            $selectedYear['opening_cash_enc']=$crypto->encrypt(number_format($openingCashCents/100,2,'.',''));
        }
    } else {
        $openingBankCents=accDecryptCents($crypto,$selectedYear['opening_bank_enc']);
        $openingCashCents=accDecryptCents($crypto,$selectedYear['opening_cash_enc']);
    }
    $yearStats=accYearBalances($conn,$crypto,$selectedYear);
    $bookings=$yearStats['bookings'];$bankBalance=$yearStats['closing_bank'];$cashBalance=$yearStats['closing_cash'];
    $incomeTotal=$yearStats['income'];$expenseTotal=$yearStats['expense'];
    foreach($bookings as &$b){
        $b['category_name'] = !empty($b['category_enc']) ? (string)$crypto->decrypt($b['category_enc']) : ($legacyCategoryMap[(int)($b['category_id']??0)] ?? '');
    }
    unset($b);
}
$bankControls=[]; $cashCounts=[]; $cashBookingControl=[];
if($selectedYear){
    $q=$conn->prepare('SELECT r.*,u.full_name AS performed_by_name FROM accounting_bank_reconciliations r LEFT JOIN users u ON u.id=r.created_by WHERE r.fiscal_year_id=? ORDER BY r.created_at DESC,r.id DESC');
    $q->execute([$selectedYearId]); foreach($q->fetchAll(PDO::FETCH_ASSOC) as $r){
        $monthKey=date('Y-m',strtotime($r['reconciled_until']));
        $monthStart=$monthKey.'-01';
        if ($migrationMode) {
            // Im Bereinigungsmodus bleibt eine bereits gesetzte Bestätigung gültig.
            $valid = true;
        } else {
            $expected=accControlFingerprint($conn,$selectedYearId,'bank',(new DateTimeImmutable($monthStart))->modify('+1 month')->format('Y-m-d'),$monthStart);
            $storedFingerprint=(string)($r['booking_fingerprint']??'');
            $valid = ($storedFingerprint !== '' && hash_equals($storedFingerprint,$expected));
        }
        if(!isset($bankControls[$monthKey])) $bankControls[$monthKey]=['valid'=>$valid,'record'=>$r];
    }

    // Alle Kassenzählungen laden, nicht nur die des ausgewählten Geschäftsjahres:
    // Eine Zählung bildet den Endpunkt des Prüfintervalls seit der vorherigen Zählung.
    // Dadurch können Intervalle über Monats- und Geschäftsjahresgrenzen hinweg reichen.
    $q=$conn->query('SELECT c.*,u.full_name AS performed_by_name FROM accounting_cash_counts c LEFT JOIN users u ON u.id=c.created_by ORDER BY c.counted_on ASC,c.created_at ASC,c.id ASC');
    $cashCountEventsByDate=[];
    foreach($q->fetchAll(PDO::FETCH_ASSOC) as $c){
        if ($migrationMode) {
            // Keine Fingerprint-Prüfung und keine automatische Entwertung während
            // der Altbestandsbereinigung. Historische Zählungen bleiben erhalten.
            $fingerprintValid = true;
        } else {
            $expectedFingerprint=accControlFingerprint($conn,(int)$c['fiscal_year_id'],'cash',$c['counted_on']);
            $storedFingerprint=(string)($c['booking_fingerprint']??'');
            $fingerprintValid = ($storedFingerprint !== '' && hash_equals($storedFingerprint,$expectedFingerprint));
        }
        // Die Differenz aus den gespeicherten Ist- und Sollwerten ableiten. So bleiben
        // auch importierte Zählungen zuverlässig auswertbar.
        $actual=accDecryptCents($crypto,$c['actual_amount_enc']??null);
        $calculated=accDecryptCents($crypto,$c['calculated_amount_enc']??null);
        $difference=$actual-$calculated;
        $event=['valid'=>$fingerprintValid,'successful'=>$difference===0,'record'=>$c,'difference'=>$difference];
        // Bei mehreren Zählungen am selben Kalendertag gilt die zuletzt gespeicherte.
        $cashCountEventsByDate[$c['counted_on']]=$event;
        if((int)$c['fiscal_year_id']===$selectedYearId) $cashCounts[$c['id']]=$event;
    }

    // Jede Kassenbuchung wird der chronologisch ersten Kassenzählung am oder nach
    // ihrem Buchungsdatum zugeordnet. Diese Zählung prüft genau das Intervall seit
    // der vorherigen Zählung. Bei einer Differenz oder veraltetem Fingerprint bleibt
    // das Intervall ungeprüft; Buchungen nach der letzten Zählung bleiben ebenfalls offen.
    $cashCountEvents=array_values($cashCountEventsByDate);
    foreach($bookings as $booking){
        $isCashMovement=(($booking['booking_type']==='income'||$booking['booking_type']==='expense')&&$booking['payment_channel']==='cash')
            ||($booking['booking_type']==='transfer'&&($booking['transfer_from']==='cash'||$booking['transfer_to']==='cash'));
        if(!$isCashMovement) continue;
        $cashBookingControl[(int)$booking['id']]=false;
        foreach($cashCountEvents as $event){
            if($event['record']['counted_on'] >= $booking['booking_date']){
                $cashBookingControl[(int)$booking['id']]=$event['valid']&&$event['successful'];
                break;
            }
        }
    }
}
if ($cashCounts) {
    $cashCounts = array_values($cashCounts);
    usort($cashCounts, static function(array $a, array $b): int {
        $dateCompare = strcmp((string)$b['record']['counted_on'], (string)$a['record']['counted_on']);
        if ($dateCompare !== 0) return $dateCompare;
        $createdCompare = strcmp((string)$b['record']['created_at'], (string)$a['record']['created_at']);
        return $createdCompare !== 0 ? $createdCompare : ((int)$b['record']['id'] <=> (int)$a['record']['id']);
    });
}
$taxAreaTotals = [];
foreach (['ideell','asset_management','purpose_operation','commercial_business'] as $areaCode) $taxAreaTotals[$areaCode] = ['income'=>0,'expense'=>0];
foreach ($bookings as $taxBooking) {
    $areaCode = $taxBooking['tax_area'] ?? '';
    if (isset($taxAreaTotals[$areaCode]) && in_array($taxBooking['booking_type'], ['income','expense'], true)) {
        $taxAreaTotals[$areaCode][$taxBooking['booking_type']] += (int)$taxBooking['amount_cents'];
    }
}
$openingBankDisplay = number_format($openingBankCents/100,2,'.','');
$openingCashDisplay = number_format($openingCashCents/100,2,'.','');
$defaultBookingDate = $today->format('Y-m-d');
if ($selectedYear && ($defaultBookingDate < $selectedYear['start_date'] || $defaultBookingDate > $selectedYear['end_date'])) $defaultBookingDate = $selectedYear['end_date'];
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Buchungen – Förderverein intern</title>
<link rel="stylesheet" href="styles.css"><link rel="stylesheet" href="includes/navigation.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="icon" type="image/png" href="/intern/assets/icons/favicon.png">
<style>
.accounting-wrap{max-width:1500px;margin:0 auto}.accounting-toolbar{display:flex;justify-content:space-between;align-items:center;gap:14px;flex-wrap:wrap;margin:18px 0}.accounting-toolbar select{min-width:170px}.accounting-stats{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;margin:18px 0}.accounting-stat-group{background:#fff;border:1px solid var(--border-color);border-radius:8px;padding:14px;min-width:0}.accounting-stat-group h3{font-size:.95rem;margin:0 0 12px;color:#344054}.accounting-stat-line{display:flex;justify-content:space-between;align-items:baseline;gap:12px;padding:5px 0;border-bottom:1px solid #eef0f3}.accounting-stat-line:last-child{border-bottom:0;padding-top:9px;margin-top:3px;font-weight:700}.accounting-stat-line small{color:#667085}.accounting-stat-line strong{font-size:1.05rem;white-space:nowrap}.accounting-stat-group .accounting-positive{color:#16803c}.accounting-stat-group .accounting-negative{color:#b42318}.accounting-stat{background:#fff;border:1px solid var(--border-color);border-radius:8px;padding:14px}.accounting-stat small{display:block;color:#667085;margin-bottom:6px}.accounting-stat strong{font-size:1.2rem}.accounting-section{background:#fff;border:1px solid var(--border-color);border-radius:8px;padding:18px;margin:18px 0}.accounting-form-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px}.accounting-form-grid label{display:flex;flex-direction:column;gap:5px;font-weight:600}.accounting-tax-area-label{display:flex;align-items:center;gap:7px;position:relative;flex-wrap:wrap}
.accounting-tax-help{width:18px;height:18px;flex:0 0 18px;padding:0;border:1px solid #667085;border-radius:50%;background:#fff;color:#475467;font:700 12px/16px Arial,sans-serif;text-align:center;cursor:help}
.accounting-tax-tooltip{display:none;position:absolute;z-index:1000;left:calc(100% + 8px);top:50%;transform:translateY(-50%);width:min(390px,calc(100vw - 90px));padding:12px 14px;border:1px solid #cbd5e1;border-radius:7px;background:#fff;color:#344054;box-shadow:0 5px 18px rgba(16,24,40,.18);font-size:12px;font-weight:400;line-height:1.5;cursor:default}
.accounting-tax-tooltip strong{color:#1d2939}.accounting-tax-tooltip em{display:inline-block;margin-top:6px;color:#667085}
.accounting-tax-help:hover+.accounting-tax-tooltip,.accounting-tax-area-label.is-open .accounting-tax-tooltip{display:block}
@media(max-width:700px){.accounting-tax-tooltip{left:0;top:calc(100% + 6px);transform:none;width:min(340px,calc(100vw - 56px))}}
.accounting-form-grid input,.accounting-form-grid select{width:100%;box-sizing:border-box;padding:9px;border:1px solid var(--border-color);border-radius:5px}.accounting-form-grid .wide{grid-column:span 2}.accounting-actions{display:flex;gap:8px;align-items:center;flex-wrap:wrap;margin-top:14px}.accounting-table-wrap{overflow-x:auto}.accounting-table{width:100%;border-collapse:collapse;min-width:950px}.accounting-table th,.accounting-table td{padding:9px 10px;border-bottom:1px solid #e8eaf0;text-align:left;vertical-align:top}.accounting-table th{background:#f7f8fa;white-space:nowrap}.accounting-table .num{text-align:right;white-space:nowrap}.accounting-positive{color:#16803c}.accounting-negative{color:#b42318}.accounting-muted{color:#667085}.accounting-badge{display:inline-block;padding:3px 8px;border-radius:20px;background:#eef2f6;font-size:.82rem}.accounting-year-status{font-size:.85rem;color:#667085}.accounting-inline{display:inline-flex;gap:6px;align-items:center}.accounting-category-row{display:flex;align-items:center;gap:8px;flex-wrap:wrap;padding:8px 0;border-bottom:1px solid #eee}.accounting-category-row form{display:inline-flex;gap:5px}.accounting-category-row input{padding:6px;border:1px solid var(--border-color);border-radius:4px}.accounting-error{background:#fff0f0;color:#9b1c1c;padding:12px;border-radius:6px;margin:12px 0}.accounting-success{background:#edf9f0;color:#176b32;padding:12px;border-radius:6px;margin:12px 0}.accounting-details summary{cursor:pointer;font-weight:600}.accounting-month{border:1px solid var(--border-color);border-radius:8px;margin:12px 0;overflow:hidden;background:#fff}.accounting-month>summary{cursor:pointer;list-style:none;display:flex;align-items:center;gap:12px;padding:12px 15px;background:#f7f8fa;font-weight:700;color:#344054}.accounting-month>summary::-webkit-details-marker{display:none}.accounting-month>summary:after{content:"＋";margin-left:auto;font-size:1.05rem;color:#667085}.accounting-month[open]>summary:after{content:"−"}.accounting-month>summary small{font-weight:400;color:#667085}.accounting-month .accounting-table-wrap{padding:0 10px 8px}.accounting-empty{text-align:center;padding:24px;color:#667085}.accounting-edit-row>td{background:#f8fafc;padding:16px 18px;border-bottom:2px solid var(--border-color)}.accounting-inline-editor{background:#fff;border:1px solid var(--border-color);border-radius:8px;padding:16px}.accounting-inline-editor h3{margin:0 0 14px;font-size:1.05rem}.accounting-new-booking>summary{cursor:pointer;list-style:none;display:flex;align-items:center;gap:9px;font-size:1.05rem;font-weight:700;color:var(--primary-color,#245b8f)}.accounting-new-booking>summary::-webkit-details-marker{display:none}.accounting-new-booking>summary:after{content:"＋";margin-left:auto;font-size:1.25rem}.accounting-new-booking[open]>summary:after{content:"−"}.accounting-new-booking-content{margin-top:18px}.accounting-controls{display:flex;gap:8px;flex-wrap:wrap;padding:5px 10px 7px;border-top:1px solid #f0f1f3;background:#fff}.accounting-control-box{flex:1 1 280px;border:1px solid var(--border-color);border-radius:7px;padding:11px;background:#fff}.accounting-control-box form{display:flex;gap:8px;align-items:end;flex-wrap:wrap}.accounting-control-box label{display:flex;flex-direction:column;gap:4px;font-size:.88rem}.accounting-control-box input{max-width:160px;padding:7px;border:1px solid var(--border-color);border-radius:4px}.accounting-control-status{font-size:.83rem;color:#667085}.accounting-control-ok{color:#16803c}.accounting-control-warn{color:#b54708}.accounting-month-controls{display:flex;gap:10px;flex-wrap:wrap;margin-left:auto;font-size:.82rem;font-weight:500}.accounting-month-controls span{white-space:nowrap}.accounting-action-bar{display:flex;gap:12px;align-items:flex-start;flex-wrap:wrap;margin:12px 0 18px}.accounting-action-detail{flex:1 1 230px;min-width:230px;margin:0!important;padding:0!important;border:0!important;background:transparent!important;box-shadow:none!important}.accounting-action-detail>summary{box-sizing:border-box;min-height:42px;width:100%;padding:10px 18px!important;display:flex;align-items:center;justify-content:flex-start;gap:9px;cursor:pointer;list-style:none;border:1px solid var(--primary-color,#3498db);border-radius:4px;background:#fff;color:var(--primary-color,#3498db);font-size:14px!important;font-weight:600;line-height:20px;transition:background-color .18s,color .18s,border-color .18s}.accounting-action-detail>summary::-webkit-details-marker{display:none}.accounting-action-detail>summary:after{content:"＋";margin-left:auto;font-size:1.1rem;line-height:1}.accounting-action-detail[open]>summary{background:var(--primary-color,#3498db);color:#fff;border-color:var(--primary-color,#3498db)}.accounting-action-detail[open]>summary:after{content:"−"}.accounting-action-detail>summary:hover{filter:brightness(.96)}.accounting-action-detail .accounting-new-booking-content{margin-top:14px}.accounting-action-detail[open] .accounting-new-booking-content,.accounting-action-detail[open] .accounting-cash-count-content{padding:15px;background:#fff;border:1px solid var(--border-color);border-radius:7px}.accounting-action-bar{align-items:stretch}.accounting-action-detail .accounting-cash-count-content{margin-top:14px}.accounting-cash-count-content{margin-top:12px}.accounting-cash-count-content form{display:flex;gap:10px;align-items:end;flex-wrap:wrap}.accounting-cash-count-content label{display:flex;flex-direction:column;gap:4px;font-size:.88rem}.accounting-cash-count-content input{padding:8px;border:1px solid var(--border-color);border-radius:5px}.accounting-bank-control{display:inline-flex;align-items:center;gap:7px}.accounting-bank-control form{display:inline;margin:0}.accounting-bank-check{width:29px;height:27px;display:inline-flex;align-items:center;justify-content:center;border:1px solid #cbd5e1;border-radius:5px;background:#f1f3f5;color:#667085;cursor:pointer}.accounting-bank-check.is-done{background:#edf9f0;border-color:#b7dfc1;color:#16803c}.accounting-bank-log{font-size:.73rem;color:#98a2b3;font-weight:400}.accounting-bank-check:disabled{cursor:default;opacity:.85}
/* Zwei gleich breite Schaltflächen; das jeweils aktive Formular liegt darunter über die volle Breite. */
.accounting-action-area{width:100%;margin:12px 0 18px}
.accounting-action-bar{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;width:100%;margin:0 0 12px}
.accounting-action-tab{box-sizing:border-box;min-height:42px;width:100%;padding:10px 18px;display:flex;align-items:center;gap:9px;cursor:pointer;border:1px solid var(--primary-color,#3498db);border-radius:4px;background:#fff;color:var(--primary-color,#3498db);font-family:inherit;font-size:14px;font-weight:600;line-height:20px;text-align:left;transition:background-color .18s,color .18s,border-color .18s}
.accounting-action-tab .accounting-action-indicator{margin-left:auto;font-size:1.1rem;line-height:1}
.accounting-action-tab.is-active{background:var(--primary-color,#3498db);color:#fff;border-color:var(--primary-color,#3498db)}
.accounting-action-tab:hover{filter:brightness(.96)}
.accounting-action-panel{box-sizing:border-box;width:100%;min-width:0;padding:15px;background:#fff;border:1px solid var(--border-color);border-radius:7px}
.accounting-action-panel[hidden],.accounting-action-content[hidden]{display:none!important}
.accounting-action-content{box-sizing:border-box;width:100%;min-width:0}
.accounting-new-booking-content form,.accounting-new-booking-content .accounting-form-grid{width:100%}
.accounting-new-booking-content .accounting-form-grid{grid-template-columns:repeat(4,minmax(0,1fr))}
.accounting-cash-count-content{margin:0}
.accounting-cash-count-content form{display:flex;gap:10px;align-items:end;flex-wrap:wrap;width:100%}
.accounting-cash-count-content label{display:flex;flex-direction:column;gap:4px;font-size:.88rem}
.accounting-cash-count-content input{padding:8px;border:1px solid var(--border-color);border-radius:5px}
@media(max-width:520px){.accounting-action-bar{grid-template-columns:1fr}.accounting-new-booking-content .accounting-form-grid{grid-template-columns:1fr}.accounting-new-booking-content .accounting-form-grid .wide{grid-column:auto}}
@media(max-width:850px){.accounting-stats{grid-template-columns:1fr 1fr}.accounting-stat-group:last-child{grid-column:span 2}.accounting-form-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:520px){.accounting-stats{grid-template-columns:1fr}.accounting-stat-group:last-child{grid-column:auto}.accounting-form-grid{grid-template-columns:1fr}.accounting-form-grid .wide{grid-column:auto}.accounting-section{padding:13px}.accounting-stat strong{font-size:1rem}}

/* Abschlussaktionen im selben Design wie die geschlossenen Karteikarten-Buttons */
.accounting-finalize-actions{display:flex;gap:12px;align-items:stretch;flex-wrap:wrap;margin-top:16px;}
.accounting-finalize-actions .btn{
    box-sizing:border-box;
    display:inline-flex!important;
    align-items:center;
    justify-content:center;
    width:auto!important;
    min-width:0!important;
    min-height:42px!important;
    height:auto!important;
    padding:10px 18px!important;
    border:1px solid var(--primary-color,#3498db)!important;
    border-radius:4px!important;
    background:#fff!important;
    color:var(--primary-color,#3498db)!important;
    font-family:inherit!important;
    font-size:14px!important;
    font-weight:600!important;
    line-height:20px!important;
    text-align:left;
    white-space:normal;
    box-shadow:none!important;
    cursor:pointer;
}
.accounting-finalize-actions .btn:hover{background:#f5f9fc!important;color:var(--primary-color,#3498db)!important;filter:none;}
@media(max-width:600px){.accounting-finalize-actions .btn{width:100%!important;}}

.accounting-finalize-inline-form{margin:16px 0 0}
.accounting-finalize-inline-form .accounting-finalize-actions{justify-content:flex-end;margin-top:0}
.accounting-finalize-inline-form .btn{
    box-sizing:border-box;display:inline-flex!important;align-items:center;justify-content:center;
    min-height:42px!important;height:auto!important;width:auto!important;min-width:0!important;
    padding:10px 18px!important;border:1px solid var(--primary-color,#3498db)!important;
    border-radius:4px!important;background:#fff!important;color:var(--primary-color,#3498db)!important;
    font-family:inherit!important;font-size:14px!important;font-weight:600!important;line-height:20px!important;
    text-decoration:none!important;white-space:normal;box-shadow:none!important;cursor:pointer;
}
.accounting-finalize-inline-form .btn:hover{background:#f5f9fc!important;color:var(--primary-color,#3498db)!important;filter:none}
@media(max-width:600px){.accounting-finalize-inline-form .btn{width:100%!important}}
</style>
</head>
<body><div class="container">
<header class="app-page-header"><h1><i class="fas fa-book"></i> Buchführung – Buchungen</h1><?php require $appRoot.'/includes/navigation.php'; ?></header>
<div class="accounting-wrap">
<?php if($message):?><div class="accounting-success"><?=accH($message)?></div><?php endif;?>
<?php if($error):?><div class="accounting-error"><?=accH($error)?></div><?php endif;?>
<?php if($migrationMode):?><div class="accounting-control-status" style="margin:10px 0;padding:9px 12px;border-left:4px solid #d6a23a;background:#fff8e8;color:#604b1d"><strong>Bereinigungsmodus aktiv</strong> – Buchungen und Kontrollen können für die Datenübernahme ergänzt und korrigiert werden. Bestehende Kontoabgleiche und Kassenzählungen werden durch solche Korrekturen nicht automatisch als „überholt“ markiert. Es findet keine Änderungsprotokollierung statt.</div><?php endif;?>
<div class="accounting-toolbar">
<form method="get" class="accounting-inline"><label for="jahr">Geschäftsjahr</label><select name="jahr" id="jahr" onchange="this.form.submit()"><?php foreach($years as $y):?><option value="<?=$y['id']?>" <?=$selectedYearId===(int)$y['id']?'selected':''?>><?=accH($y['label'])?> (<?=date('d.m.Y',strtotime($y['start_date']))?> – <?=date('d.m.Y',strtotime($y['end_date']))?>)</option><?php endforeach;?></select></form>
<?php if($selectedYear):?><span class="accounting-year-status">Status: <strong><?=['open'=>'Offen','closed'=>'Festgeschrieben'][$selectedYear['status']]?></strong></span><?php endif;?>
</div>
<?php if($selectedYear):?>
<?php $isCurrentFiscalYear = $today->format('Y-m-d') >= $selectedYear['start_date'] && $today->format('Y-m-d') <= $selectedYear['end_date']; ?>
<div class="accounting-stats accounting-overview">
<div class="accounting-stat-group">
<h3>Kassenbestand zu Beginn des Geschäftsjahres</h3>
<div class="accounting-stat-line"><small>Barkasse</small><strong><?=accH(accCentsToDisplay($yearStats['opening_cash']))?></strong></div>
<div class="accounting-stat-line"><small>Konto</small><strong><?=accH(accCentsToDisplay($yearStats['opening_bank']))?></strong></div>
<div class="accounting-stat-line"><small>Gesamtsumme</small><strong><?=accH(accCentsToDisplay($yearStats['opening_cash'] + $yearStats['opening_bank']))?></strong></div>
</div>
<div class="accounting-stat-group">
<h3>Einnahmen und Ausgaben</h3>
<div class="accounting-stat-line"><small>Einnahmen</small><strong class="accounting-positive"><?=accH(accCentsToDisplay($incomeTotal))?></strong></div>
<div class="accounting-stat-line"><small>Ausgaben</small><strong class="accounting-negative"><?=accH(accCentsToDisplay($expenseTotal))?></strong></div>
<div class="accounting-stat-line"><small>Jahresergebnis</small><strong><?=accH(accCentsToDisplay($incomeTotal - $expenseTotal))?></strong></div>
</div>
<div class="accounting-stat-group">
<h3><?= $isCurrentFiscalYear ? 'Aktueller Stand' : 'Bestand zum Ende des Geschäftsjahres' ?></h3>
<div class="accounting-stat-line"><small>Barkasse</small><strong><?=accH(accCentsToDisplay($cashBalance))?></strong></div>
<div class="accounting-stat-line"><small>Konto</small><strong><?=accH(accCentsToDisplay($bankBalance))?></strong></div>
<div class="accounting-stat-line"><small>Gesamtsumme</small><strong><?=accH(accCentsToDisplay($cashBalance + $bankBalance))?></strong></div>
</div>
</div>
<?php if($selectedYear['status']==='closed'): ?>
<section class="accounting-section">
<h2>Festschreibung <?=accH($selectedYear['label'])?></h2>
<p>Dieses Geschäftsjahr ist endgültig festgeschrieben. Änderungen an Buchungen und zugehörigen Daten sind gesperrt.</p>
<p class="accounting-muted">Festgeschrieben am <?= $selectedYear['closed_at'] ? date('d.m.Y H:i',strtotime($selectedYear['closed_at'])) : '–' ?><?= !empty($selectedYear['closed_by_name']) ? ' · festgeschrieben von '.accH($selectedYear['closed_by_name']) : '' ?></p>
<div class="accounting-stats">
<div class="accounting-stat"><small>Anfangsbestand Bank</small><strong><?=accH(accCentsToDisplay($yearStats['opening_bank']))?></strong></div>
<div class="accounting-stat"><small>Anfangsbestand Barkasse</small><strong><?=accH(accCentsToDisplay($yearStats['opening_cash']))?></strong></div>
<div class="accounting-stat"><small>Einnahmen</small><strong class="accounting-positive"><?=accH(accCentsToDisplay($yearStats['income']))?></strong></div>
<div class="accounting-stat"><small>Ausgaben</small><strong class="accounting-negative"><?=accH(accCentsToDisplay($yearStats['expense']))?></strong></div>
<div class="accounting-stat"><small>Jahresergebnis</small><strong><?=accH(accCentsToDisplay($yearStats['result']))?></strong></div>
<div class="accounting-stat"><small>Endbestand Bank</small><strong><?=accH(accCentsToDisplay($yearStats['closing_bank']))?></strong></div>
<div class="accounting-stat"><small>Endbestand Barkasse</small><strong><?=accH(accCentsToDisplay($yearStats['closing_cash']))?></strong></div>
</div>
<h3>Aufteilung nach steuerlichem Tätigkeitsbereich</h3>
<div class="accounting-table-wrap"><table class="accounting-table" style="min-width:650px"><thead><tr><th>Tätigkeitsbereich</th><th class="num">Einnahmen</th><th class="num">Ausgaben</th><th class="num">Saldo</th></tr></thead><tbody>
<?php foreach ($taxAreaTotals as $areaCode=>$areaTotals): ?><tr><td><?=accH(accTaxAreaLabel($areaCode))?></td><td class="num accounting-positive"><?=accH(accCentsToDisplay($areaTotals['income']))?></td><td class="num accounting-negative"><?=accH(accCentsToDisplay($areaTotals['expense']))?></td><td class="num"><?=accH(accCentsToDisplay($areaTotals['income']-$areaTotals['expense']))?></td></tr><?php endforeach; ?>
</tbody></table></div>
<p class="accounting-muted">Die vollständige Buchungsliste mit Referenzen ist weiter unten einsehbar.</p>
</section>
<?php elseif(!isset($_GET['abschluss']) && $today->format('Y-m-d') > $selectedYear['end_date']): ?>
<section class="accounting-section">
<h2>Geschäftsjahr abschließen</h2>
<p>Das Geschäftsjahr ist beendet. Prüfe vor der endgültigen Festschreibung die nachfolgende Abschlussübersicht. Danach sind Änderungen in diesem Geschäftsjahr technisch gesperrt.</p>
<a class="btn btn-primary" href="buchungen.php?jahr=<?=$selectedYearId?>&abschluss=1">Abschlussübersicht öffnen</a>
</section>
<?php endif; ?>
<?php if(isset($_GET['abschluss']) && $selectedYear && $selectedYear['status']!=='closed' && $today->format('Y-m-d') > $selectedYear['end_date']): ?>
<section class="accounting-section" id="abschluss">
<h2>Abschlussübersicht Geschäftsjahr <?=accH($selectedYear['label'])?></h2>
<p class="accounting-muted">Die Bestände werden aus den Anfangsbeständen und sämtlichen Buchungen chronologisch berechnet. Belegdateien sind optional; das Feld „Referenz“ wird in der Buchungsliste ausgewiesen.</p>
<?php if($yearStats['closing_bank']<0 || $yearStats['closing_cash']<0): ?><div class="accounting-error">Achtung: Mindestens ein rechnerischer Endbestand ist negativ. Bitte vor der Festschreibung prüfen, ob Buchungen oder Anfangsbestände fehlen bzw. fehlerhaft sind.</div><?php endif; ?>
<?php $unassignedTaxAreaCount = 0; foreach ($bookings as $bookingForTaxCheck) { if (in_array($bookingForTaxCheck['booking_type'], ['income','expense'], true) && empty($bookingForTaxCheck['tax_area'])) $unassignedTaxAreaCount++; } ?>
<?php if($unassignedTaxAreaCount > 0): ?><div class="accounting-error"><?= (int)$unassignedTaxAreaCount ?> Einnahmen/Ausgaben haben noch keinen steuerlichen Tätigkeitsbereich. Bitte diese Buchungen bearbeiten und zuordnen. Erst danach ist die Festschreibung möglich.</div><?php endif; ?>
<h3>Aufteilung nach steuerlichem Tätigkeitsbereich</h3>
<div class="accounting-table-wrap"><table class="accounting-table" style="min-width:650px"><thead><tr><th>Tätigkeitsbereich</th><th class="num">Einnahmen</th><th class="num">Ausgaben</th><th class="num">Saldo</th></tr></thead><tbody>
<?php foreach ($taxAreaTotals as $areaCode=>$areaTotals): ?><tr><td><?=accH(accTaxAreaLabel($areaCode))?></td><td class="num accounting-positive"><?=accH(accCentsToDisplay($areaTotals['income']))?></td><td class="num accounting-negative"><?=accH(accCentsToDisplay($areaTotals['expense']))?></td><td class="num"><?=accH(accCentsToDisplay($areaTotals['income']-$areaTotals['expense']))?></td></tr><?php endforeach; ?>
</tbody></table></div>
<form method="post" class="accounting-finalize-inline-form" onsubmit="return confirm('Achtung: Das Geschäftsjahr wird endgültig festgeschrieben. Danach sind Buchungen und zugehörige Daten in diesem Jahr nicht mehr änderbar. Die Festschreibung kann nicht rückgängig gemacht werden. Wirklich fortfahren?')">
<input type="hidden" name="csrf_token" value="<?=accH($csrf)?>"><input type="hidden" name="action" value="finalize_year"><input type="hidden" name="fiscal_year_id" value="<?=$selectedYearId?>">
<div class="accounting-actions accounting-finalize-actions">
<button class="btn" type="submit">Geschäftsjahr endgültig festschreiben</button>
<a class="btn" href="buchungen.php?jahr=<?=$selectedYearId?>">Abbrechen</a>
</div>
</form>
</section>
<?php endif; ?>
<?php if(!$editing && !isset($_GET['abschluss'])): ?>
<div class="accounting-action-area">
<?php if($selectedYear['status']==='closed'): ?>
<section class="accounting-section" id="buchung-form"><h2>Neue Buchung</h2><p>Dieses Geschäftsjahr ist festgeschrieben. Es können keine Buchungen verändert werden.</p></section>
<?php else: ?>
<div class="accounting-action-bar" role="tablist" aria-label="Buchführung: neue Vorgänge">
<button type="button" class="accounting-action-tab" id="tab-neue-buchung" data-accounting-panel="booking" aria-controls="panel-neue-buchung" aria-expanded="false"><i class="fas fa-plus-circle"></i> Neue Buchung anlegen <span class="accounting-action-indicator" aria-hidden="true">＋</span></button>
<?php if($selectedYear['status']!=='closed'): ?>
<button type="button" class="accounting-action-tab" id="tab-kassenzählung" data-accounting-panel="cash" aria-controls="panel-kassenzählung" aria-expanded="false"><i class="fas fa-coins"></i> Neue Kassenzählung erfassen <span class="accounting-action-indicator" aria-hidden="true">＋</span></button>
<?php endif; ?>
</div>
<div class="accounting-action-panel" id="accounting-action-panel" hidden>
<div class="accounting-action-content accounting-new-booking-content" id="panel-neue-buchung" data-accounting-content="booking" hidden>
<form method="post">
<input type="hidden" name="csrf_token" value="<?=accH($csrf)?>"><input type="hidden" name="action" value="save_booking"><input type="hidden" name="booking_id" value="0"><input type="hidden" name="fiscal_year_id" value="<?=$selectedYearId?>">
<div class="accounting-form-grid">
<label>Datum<input type="date" name="booking_date" required min="<?=accH($selectedYear['start_date'])?>" max="<?=accH($selectedYear['end_date'])?>" value="<?=accH($defaultBookingDate)?>"></label>
<label>Buchungsart<select name="booking_type" id="booking_type" onchange="toggleAccountingFields(this.form)"><option value="expense" selected>Ausgabe</option><option value="income">Einnahme</option><option value="transfer">Umbuchung</option></select></label>
<label class="booking-normal">Zahlungsweg<select name="payment_channel"><option value="bank">Konto</option><option value="cash">Barkasse</option></select></label>
<label class="booking-transfer" style="display:none">Von<select name="transfer_from"><option value="bank">Konto</option><option value="cash">Barkasse</option></select></label>
<label class="booking-transfer" style="display:none">Nach<select name="transfer_to"><option value="cash">Barkasse</option><option value="bank">Konto</option></select></label>
<label class="booking-normal">Kategorie<input type="text" name="category" list="accounting-category-options-new" maxlength="150" required autocomplete="off" placeholder="Kategorie auswählen oder neu eingeben"><datalist id="accounting-category-options-new"><?php foreach($categories as $categoryOption):?><option value="<?=accH($categoryOption)?>"></option><?php endforeach;?></datalist><small class="accounting-muted">Vorschläge stammen aus den Buchungen dieses Geschäftsjahres.</small></label>
<label class="booking-normal"><span class="accounting-tax-area-label">Steuerlicher Tätigkeitsbereich <button type="button" class="accounting-tax-help" aria-label="Erläuterungen zu den steuerlichen Tätigkeitsbereichen" aria-describedby="tax-area-help-new">i</button><span class="accounting-tax-tooltip" id="tax-area-help-new" role="tooltip"><strong>Ideeller Bereich:</strong> Mitgliedsbeiträge, Spenden und Zuschüsse sowie Ausgaben zur unmittelbaren Verwirklichung des Satzungszwecks.<br><strong>Vermögensverwaltung:</strong> Einnahmen aus der Nutzung eigenen Vermögens, z. B. Zinsen oder Mieteinnahmen.<br><strong>Zweckbetrieb:</strong> Wirtschaftliche Tätigkeit, die die gemeinnützigen Satzungszwecke unmittelbar verwirklicht und die gesetzlichen Voraussetzungen erfüllt.<br><strong>Steuerpflichtiger wirtschaftlicher Geschäftsbetrieb:</strong> Wirtschaftliche Tätigkeiten wie entgeltliche Verkäufe oder Bewirtung, die nicht als Zweckbetrieb gelten.<br><em>Entscheidend sind die tatsächlichen Umstände der jeweiligen Buchung.</em></span></span><select name="tax_area" required>
<option value="">Bitte auswählen</option><option value="ideell">Ideeller Bereich</option><option value="asset_management">Vermögensverwaltung</option><option value="purpose_operation">Zweckbetrieb</option><option value="commercial_business">Steuerpflichtiger wirtschaftlicher Geschäftsbetrieb</option>
</select></label>
<label>Betrag (€)<input name="amount" inputmode="decimal" required placeholder="0,00"></label>
<label class="wide">Beschreibung<input name="description" maxlength="1000" required></label>
<label class="wide booking-normal">Papierbeleg-Referenz<input name="paper_reference" maxlength="255" placeholder="z. B. Nr. 37"></label>
</div>
<div class="accounting-actions"><button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Buchung speichern</button><button class="btn btn-secondary accounting-panel-cancel" type="button">Abbrechen</button></div>
</form>
</div>
<div class="accounting-action-content accounting-cash-count-content" id="panel-kassenzählung" data-accounting-content="cash" hidden>
<p class="accounting-control-status">Der Sollbestand wird aus allen Kassenbewegungen bis zum gewählten Stichtag berechnet. Eine Differenz wird dokumentiert und nicht automatisch ausgeglichen.</p>
<form method="post"><input type="hidden" name="csrf_token" value="<?=accH($csrf)?>"><input type="hidden" name="action" value="save_cash_count"><input type="hidden" name="fiscal_year_id" value="<?=$selectedYearId?>"><label>Stichtag<input type="date" name="counted_on" required min="<?=accH($selectedYear['start_date'])?>" max="<?=accH(min($today->format('Y-m-d'),$selectedYear['end_date']))?>" value="<?=accH($defaultBookingDate)?>"></label><label>Gezählter Kassenbestand (€)<input name="actual_amount" inputmode="decimal" required placeholder="0,00"></label><label>Notiz (optional)<input name="count_note" maxlength="1000" placeholder="z. B. Zählung nach Einkauf"></label><button class="btn btn-primary" type="submit">Zählung speichern</button><button class="btn btn-secondary accounting-panel-cancel" type="button">Abbrechen</button></form>
<?php if($cashCounts):?><div style="margin-top:10px"><strong>Letzte Kassenzählungen</strong><?php foreach(array_slice(array_values($cashCounts),0,8) as $cc):$cr=$cc['record'];?><div class="accounting-control-status" style="padding:4px 0"><span class="<?=(!$cc['valid']?'accounting-control-warn':($cc['successful']?'accounting-control-ok':'accounting-control-warn'))?>"><?=date('d.m.Y',strtotime($cr['counted_on']))?> · <?= $cc['valid']?($cc['successful']?'Stimmt':'Differenz: '.accCentsToDisplay($cc['difference'])):'Überholt' ?></span><?php if($cr['performed_by_name']):?> · <?=accH($cr['performed_by_name'])?><?php endif;?> · <?=date('d.m.Y H:i',strtotime($cr['created_at']))?></div><?php endforeach;?></div><?php endif;?>
</div>
</div>
<?php endif; ?>
</div>
<?php endif; ?>
<section class="accounting-section" id="buchungen-liste">
<h2>Buchungen im Geschäftsjahr <?=accH($selectedYear['label'])?></h2>
<?php
$bookingsByMonth = [];
foreach ($bookings as $bookingForMonth) {
    $monthKey = date('Y-m', strtotime($bookingForMonth['booking_date']));
    $bookingsByMonth[$monthKey][] = $bookingForMonth;
}
$currentMonthKey = $today->format('Y-m');
$monthNames = [1=>'Januar',2=>'Februar',3=>'März',4=>'April',5=>'Mai',6=>'Juni',7=>'Juli',8=>'August',9=>'September',10=>'Oktober',11=>'November',12=>'Dezember'];
?>
<?php if (!$bookingsByMonth): ?>
<div class="accounting-empty">Für dieses Geschäftsjahr sind noch keine Buchungen vorhanden.</div>
<?php else: foreach ($bookingsByMonth as $monthKey => $monthBookings):
    $monthDate = DateTimeImmutable::createFromFormat('!Y-m', $monthKey);
    $monthLabel = $monthNames[(int)$monthDate->format('n')] . ' ' . $monthDate->format('Y');
    $monthHasEditing = $editing && array_filter($monthBookings, static fn($monthBooking) => (int)$monthBooking['id'] === (int)$editing['id']);
    $monthIsOpen = $monthKey >= $currentMonthKey || (bool)$monthHasEditing;
?>
<details class="accounting-month" <?= $monthIsOpen ? 'open' : '' ?>>
<summary><span><?=accH($monthLabel)?></span><small><?=count($monthBookings)?> <?=count($monthBookings)===1?'Buchung':'Buchungen'?></small><span class="accounting-month-controls"><?php $hasBankMovement=false;$monthCashBookingIds=[];foreach($monthBookings as $mb){if(($mb['booking_type']==='income'||$mb['booking_type']==='expense')&&$mb['payment_channel']==='bank')$hasBankMovement=true;if($mb['booking_type']==='transfer'&&($mb['transfer_from']==='bank'||$mb['transfer_to']==='bank'))$hasBankMovement=true;if((($mb['booking_type']==='income'||$mb['booking_type']==='expense')&&$mb['payment_channel']==='cash')||($mb['booking_type']==='transfer'&&($mb['transfer_from']==='cash'||$mb['transfer_to']==='cash')))$monthCashBookingIds[]=(int)$mb['id'];}if($hasBankMovement):?><span class="<?= isset($bankControls[$monthKey])&&$bankControls[$monthKey]['valid']?'accounting-control-ok':'accounting-control-warn' ?>">Konto <?= isset($bankControls[$monthKey])?($bankControls[$monthKey]['valid']?'✓':'! überholt'):'offen' ?></span><?php endif; ?><?php if($monthCashBookingIds):$cashControl=true;foreach($monthCashBookingIds as $cashBookingId){if(empty($cashBookingControl[$cashBookingId])){$cashControl=false;break;}}?><span class="<?= $cashControl?'accounting-control-ok':'accounting-control-warn' ?>">Barkasse <?= $cashControl?'✓':'offen' ?></span><?php endif;?></span></summary>
<div class="accounting-table-wrap"><table class="accounting-table"><thead><tr><th>Datum</th><th>Beschreibung</th><th>Kategorie</th><th>Tätigkeitsbereich</th><th>Art / Weg</th><th class="num">Betrag</th><th class="num">Bankbestand</th><th class="num">Barkasse</th><th>Referenz</th><th></th></tr></thead><tbody>
<?php foreach ($monthBookings as $b): ?>
<tr id="buchung-row-<?=$b['id']?>"><td><?=date('d.m.Y',strtotime($b['booking_date']))?></td><td><?=accH($b['description'])?></td><td><?=accH($b['category_name']?:'–')?></td><td><?= $b['booking_type']==='transfer' ? '–' : accH(accTaxAreaLabel($b['tax_area'] ?? null)) ?></td><td><?php if($b['booking_type']==='transfer'):?><span class="accounting-badge">Umbuchung</span><br><small><?= $b['transfer_from']==='bank'?'Konto':'Barkasse' ?> → <?= $b['transfer_to']==='bank'?'Konto':'Barkasse' ?></small><?php else:?><span class="accounting-badge"><?=$b['booking_type']==='income'?'Einnahme':'Ausgabe'?></span><br><small><?=$b['payment_channel']==='bank'?'Konto':'Barkasse'?></small><?php endif;?></td>
<td class="num <?= $b['booking_type']==='income'?'accounting-positive':($b['booking_type']==='expense'?'accounting-negative':'') ?>"><?= $b['booking_type']==='expense'?'−':($b['booking_type']==='income'?'+':'') ?><?=accH(accCentsToDisplay($b['amount_cents']))?></td><td class="num"><?=accH(accCentsToDisplay($b['bank_balance']))?></td><td class="num"><?=accH(accCentsToDisplay($b['cash_balance']))?></td><td><?=accH($b['paper_reference']?:'–')?></td><td><?php if($selectedYear['status']!=='closed'): ?><a class="btn btn-secondary" href="buchungen.php?jahr=<?=$selectedYearId?>&edit=<?=$b['id']?>#buchung-row-<?=$b['id']?>" title="Bearbeiten"><i class="fas fa-pen"></i></a><?php endif; ?><?php if($selectedYear['status']!=='closed'):?><form method="post" style="display:inline" onsubmit="return confirm('Diese Buchung wirklich löschen?')"><input type="hidden" name="csrf_token" value="<?=accH($csrf)?>"><input type="hidden" name="action" value="delete_booking"><input type="hidden" name="booking_id" value="<?=$b['id']?>"><input type="hidden" name="fiscal_year_id" value="<?=$selectedYearId?>"><button class="btn btn-secondary" type="submit" title="Löschen"><i class="fas fa-trash"></i></button></form><?php endif;?></td></tr>
<?php if($editing && (int)$editing['id']===(int)$b['id']): ?>
<tr class="accounting-edit-row"><td colspan="10">
<div class="accounting-inline-editor" id="edit-buchung-<?=$b['id']?>">
<h3>Buchung bearbeiten</h3>
<form method="post">
<input type="hidden" name="csrf_token" value="<?=accH($csrf)?>"><input type="hidden" name="action" value="save_booking"><input type="hidden" name="booking_id" value="<?=$b['id']?>"><input type="hidden" name="fiscal_year_id" value="<?=$selectedYearId?>">
<div class="accounting-form-grid">
<label>Datum<input type="date" name="booking_date" required min="<?=accH($selectedYear['start_date'])?>" max="<?=accH($selectedYear['end_date'])?>" value="<?=accH($editing['booking_date'])?>"></label>
<label>Buchungsart<select name="booking_type" id="booking_type" onchange="toggleAccountingFields(this.form)"><option value="expense" <?=$editing['booking_type']==='expense'?'selected':''?>>Ausgabe</option><option value="income" <?=$editing['booking_type']==='income'?'selected':''?>>Einnahme</option><option value="transfer" <?=$editing['booking_type']==='transfer'?'selected':''?>>Umbuchung</option></select></label>
<label class="booking-normal">Zahlungsweg<select name="payment_channel"><option value="bank" <?=$editing['payment_channel']==='bank'?'selected':''?>>Konto</option><option value="cash" <?=$editing['payment_channel']==='cash'?'selected':''?>>Barkasse</option></select></label>
<label class="booking-transfer" style="display:none">Von<select name="transfer_from"><option value="bank" <?=$editing['transfer_from']==='bank'?'selected':''?>>Konto</option><option value="cash" <?=$editing['transfer_from']==='cash'?'selected':''?>>Barkasse</option></select></label>
<label class="booking-transfer" style="display:none">Nach<select name="transfer_to"><option value="cash" <?=$editing['transfer_to']==='cash'?'selected':''?>>Barkasse</option><option value="bank" <?=$editing['transfer_to']==='bank'?'selected':''?>>Konto</option></select></label>
<label class="booking-normal">Kategorie<input type="text" name="category" list="accounting-category-options-edit-<?=$b['id']?>" maxlength="150" required autocomplete="off" placeholder="Kategorie auswählen oder neu eingeben" value="<?=accH($editing['category']??'')?>"><datalist id="accounting-category-options-edit-<?=$b['id']?>"><?php foreach($categories as $categoryOption):?><option value="<?=accH($categoryOption)?>"></option><?php endforeach;?></datalist></label>
<label class="booking-normal"><span class="accounting-tax-area-label">Steuerlicher Tätigkeitsbereich <button type="button" class="accounting-tax-help" aria-label="Erläuterungen zu den steuerlichen Tätigkeitsbereichen" aria-describedby="tax-area-help-<?=$b['id']?>">i</button><span class="accounting-tax-tooltip" id="tax-area-help-<?=$b['id']?>" role="tooltip"><strong>Ideeller Bereich:</strong> Mitgliedsbeiträge, Spenden und Zuschüsse sowie Ausgaben zur unmittelbaren Verwirklichung des Satzungszwecks.<br><strong>Vermögensverwaltung:</strong> Einnahmen aus der Nutzung eigenen Vermögens, z. B. Zinsen oder Mieteinnahmen.<br><strong>Zweckbetrieb:</strong> Wirtschaftliche Tätigkeit, die die gemeinnützigen Satzungszwecke unmittelbar verwirklicht und die gesetzlichen Voraussetzungen erfüllt.<br><strong>Steuerpflichtiger wirtschaftlicher Geschäftsbetrieb:</strong> Wirtschaftliche Tätigkeiten wie entgeltliche Verkäufe oder Bewirtung, die nicht als Zweckbetrieb gelten.<br><em>Entscheidend sind die tatsächlichen Umstände der jeweiligen Buchung.</em></span></span><select name="tax_area" required><option value="">Bitte auswählen</option><option value="ideell" <?=($editing['tax_area']??'')==='ideell'?'selected':''?>>Ideeller Bereich</option><option value="asset_management" <?=($editing['tax_area']??'')==='asset_management'?'selected':''?>>Vermögensverwaltung</option><option value="purpose_operation" <?=($editing['tax_area']??'')==='purpose_operation'?'selected':''?>>Zweckbetrieb</option><option value="commercial_business" <?=($editing['tax_area']??'')==='commercial_business'?'selected':''?>>Steuerpflichtiger wirtschaftlicher Geschäftsbetrieb</option></select></label>
<label>Betrag (€)<input name="amount" inputmode="decimal" required placeholder="0,00" value="<?=accH(number_format((float)$editing['amount'],2,',','.'))?>"></label>
<label class="wide">Beschreibung<input name="description" maxlength="1000" required value="<?=accH($editing['description'])?>"></label>
<label class="wide booking-normal">Papierbeleg-Referenz<input name="paper_reference" maxlength="255" value="<?=accH($editing['paper_reference']??'')?>" placeholder="z. B. Nr. 37"></label>
</div>
<div class="accounting-actions"><button class="btn btn-primary" type="submit">Änderungen übernehmen</button><a class="btn btn-secondary" href="buchungen.php?jahr=<?=$selectedYearId?>#buchung-row-<?=$b['id']?>">Abbrechen</a></div>
</form></div></td></tr>
<?php endif; ?>
<?php endforeach; ?>
</tbody></table></div>
<?php if($hasBankMovement): $bc=$bankControls[$monthKey]??null; $bankDone=$bc && $bc['valid']; ?>
<div class="accounting-controls"><div class="accounting-bank-control">
<span class="accounting-bank-label">Buchungen mit Kontoauszug abgeglichen</span>
<?php if($selectedYear['status']!=='closed'): ?><form method="post"><input type="hidden" name="csrf_token" value="<?=accH($csrf)?>"><input type="hidden" name="action" value="reconcile_bank_month"><input type="hidden" name="fiscal_year_id" value="<?=$selectedYearId?>"><input type="hidden" name="month_key" value="<?=accH($monthKey)?>"><button class="accounting-bank-check <?=$bankDone?'is-done':''?>" type="submit" title="<?=$bankDone?'Kontoauszugabgleich erneut bestätigen':'Buchungen mit dem Kontoauszug abgeglichen'?>" aria-label="Kontoauszugabgleich"><i class="fas fa-check"></i></button></form><?php else: ?><span class="accounting-bank-check <?=$bankDone?'is-done':''?>" title="<?=$bankDone?'Kontoauszug abgeglichen':'Kontoauszug nicht abgeglichen'?>"><i class="fas fa-check"></i></span><?php endif; ?>
<?php if($bc): ?><span class="accounting-bank-log"><?=$bankDone?'Abgeglichen':'Überholt'?><?php if($bc['record']['performed_by_name']):?> · <?=accH($bc['record']['performed_by_name'])?><?php endif;?> · <?=date('d.m.Y H:i',strtotime($bc['record']['created_at']))?></span><?php endif; ?>
</div></div><?php endif; ?>
</details>
<?php endforeach; endif; ?></section>
<?php endif;?>

</div></div>
<script>
function toggleAccountingFields(form){if(!form)return;const type=form.querySelector('[name="booking_type"]');if(!type)return;const transfer=type.value==='transfer';form.querySelectorAll('.booking-normal').forEach(e=>e.style.display=transfer?'none':'');form.querySelectorAll('.booking-transfer').forEach(e=>e.style.display=transfer?'':'none');const taxArea=form.querySelector('[name="tax_area"]');if(taxArea){taxArea.disabled=transfer;taxArea.required=!transfer;}const category=form.querySelector('[name="category"]');if(category){category.disabled=transfer;category.required=!transfer;}}
document.querySelectorAll('form').forEach(form=>{if(form.querySelector('[name="booking_type"]'))toggleAccountingFields(form);});
// Erläuterungssymbol: Mouseover zeigt den Tooltip vorübergehend; Klick schaltet ihn dauerhaft ein/aus.
document.querySelectorAll('.accounting-tax-help').forEach(button=>{button.setAttribute('aria-expanded','false');button.style.cursor='pointer';button.addEventListener('click',event=>{event.preventDefault();event.stopPropagation();const wrapper=button.closest('.accounting-tax-area-label');const open=wrapper.classList.toggle('is-open');button.setAttribute('aria-expanded',open?'true':'false');});});
document.addEventListener('click',event=>{if(!event.target.closest('.accounting-tax-area-label'))document.querySelectorAll('.accounting-tax-area-label.is-open').forEach(wrapper=>{wrapper.classList.remove('is-open');const button=wrapper.querySelector('.accounting-tax-help');if(button)button.setAttribute('aria-expanded','false');});});
document.addEventListener('keydown',event=>{if(event.key==='Escape')document.querySelectorAll('.accounting-tax-area-label.is-open').forEach(wrapper=>{wrapper.classList.remove('is-open');const button=wrapper.querySelector('.accounting-tax-help');if(button)button.setAttribute('aria-expanded','false');});});
// Beide Schaltflächen steuern ein gemeinsames, seitenbreites Formularfeld.
(function(){
 const area=document.querySelector('.accounting-action-area');
 if(!area)return;
 const tabs=[...area.querySelectorAll('[data-accounting-panel]')];
 const panel=area.querySelector('#accounting-action-panel');
 const contents=[...area.querySelectorAll('[data-accounting-content]')];
 const close=()=>{
   tabs.forEach(tab=>{tab.classList.remove('is-active');tab.setAttribute('aria-expanded','false');const indicator=tab.querySelector('.accounting-action-indicator');if(indicator)indicator.textContent='＋';});
   contents.forEach(content=>content.hidden=true);
   if(panel)panel.hidden=true;
 };
 const activate=(name)=>{
   const activeTab=tabs.find(tab=>tab.dataset.accountingPanel===name);
   const activeContent=contents.find(content=>content.dataset.accountingContent===name);
   if(!activeTab||!activeContent)return;
   const wasActive=activeTab.classList.contains('is-active');
   close();
   if(wasActive)return;
   activeTab.classList.add('is-active');
   activeTab.setAttribute('aria-expanded','true');
   const indicator=activeTab.querySelector('.accounting-action-indicator');if(indicator)indicator.textContent='−';
   activeContent.hidden=false;
   panel.hidden=false;
 };
 tabs.forEach(tab=>tab.addEventListener('click',()=>activate(tab.dataset.accountingPanel)));
 area.querySelectorAll('.accounting-panel-cancel').forEach(button=>button.addEventListener('click',close));
 document.addEventListener('click',event=>{if(!area.contains(event.target))close();});
 document.addEventListener('keydown',event=>{if(event.key==='Escape')close();});
 close();
})();
</script>
</body></html>
