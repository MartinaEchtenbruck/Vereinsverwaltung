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
.accounting-wrap{max-width:1500px;margin:0 auto}.accounting-toolbar{display:flex;justify-content:space-between;align-items:center;gap:14px;flex-wrap:wrap;margin:18px 0}.accounting-toolbar select{min-width:170px}.accounting-stats{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;margin:18px 0}.accounting-stat-group{background:#fff;border:1px solid var(--border-color);border-radius:8px;padding:14px;min-width:0}.accounting-stat-group h3{font-size:.95rem;margin:0 0 12px;color:#344054}.accounting-stat-line{display:flex;justify-content:space-between;align-items:baseline;gap:12px;padding:5px 0;border-bottom:1px solid #eef0f3}.accounting-stat-line:last-child{border-bottom:0;padding-top:9px;margin-top:3px;font-weight:700}.accounting-stat-line small{color:#667085}.accounting-stat-line strong{font-size:1.05rem;white-space:nowrap}.accounting-stat-group .accounting-positive{color:#16803c}.accounting-stat-group .accounting-negative{color:#b42318}.accounting-stat{background:#fff;border:1px solid var(--border-color);border-radius:8px;padding:14px}.accounting-stat small{display:block;color:#667085;margin-bottom:6px}.accounting-stat strong{font-size:1.2rem}.accounting-section{background:#fff;border:1px solid var(--border-color);border-radius:8px;padding:18px;margin:18px 0}.accounting-form-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px}.accounting-form-grid label{display:flex;flex-direction:column;gap:5px;font-weight:600}.accounting-form-grid input,.accounting-form-grid select{width:100%;box-sizing:border-box;padding:9px;border:1px solid var(--border-color);border-radius:5px}.accounting-form-grid .wide{grid-column:span 2}.accounting-actions{display:flex;gap:8px;align-items:center;flex-wrap:wrap;margin-top:14px}.accounting-table-wrap{overflow-x:auto}.accounting-table{width:100%;border-collapse:collapse;min-width:950px}.accounting-table th,.accounting-table td{padding:9px 10px;border-bottom:1px solid #e8eaf0;text-align:left;vertical-align:top}.accounting-table th{background:#f7f8fa;white-space:nowrap}.accounting-table .num{text-align:right;white-space:nowrap}.accounting-positive{color:#16803c}.accounting-negative{color:#b42318}.accounting-muted{color:#667085}.accounting-badge{display:inline-block;padding:3px 8px;border-radius:20px;background:#eef2f6;font-size:.82rem}.accounting-year-status{font-size:.85rem;color:#667085}.accounting-inline{display:inline-flex;gap:6px;align-items:center}.accounting-category-row{display:flex;align-items:center;gap:8px;flex-wrap:wrap;padding:8px 0;border-bottom:1px solid #eee}.accounting-category-row form{display:inline-flex;gap:5px}.accounting-category-row input{padding:6px;border:1px solid var(--border-color);border-radius:4px}.accounting-error{background:#fff0f0;color:#9b1c1c;padding:12px;border-radius:6px;margin:12px 0}.accounting-success{background:#edf9f0;color:#176b32;padding:12px;border-radius:6px;margin:12px 0}.accounting-details summary{cursor:pointer;font-weight:600}.accounting-month{border:1px solid var(--border-color);border-radius:8px;margin:12px 0;overflow:hidden;background:#fff}.accounting-month>summary{cursor:pointer;list-style:none;display:flex;align-items:center;gap:12px;padding:12px 15px;background:#f7f8fa;font-weight:700;color:#344054}.accounting-month>summary::-webkit-details-marker{display:none}.accounting-month>summary:after{content:"＋";margin-left:auto;font-size:1.05rem;color:#667085}.accounting-month[open]>summary:after{content:"−"}.accounting-month>summary small{font-weight:400;color:#667085}.accounting-month .accounting-table-wrap{padding:0 10px 8px}.accounting-empty{text-align:center;padding:24px;color:#667085}.accounting-edit-row>td{background:#f8fafc;padding:16px 18px;border-bottom:2px solid var(--border-color)}.accounting-inline-editor{background:#fff;border:1px solid var(--border-color);border-radius:8px;padding:16px}.accounting-inline-editor h3{margin:0 0 14px;font-size:1.05rem}.accounting-new-booking>summary{cursor:pointer;list-style:none;display:flex;align-items:center;gap:9px;font-size:1.05rem;font-weight:700;color:var(--primary-color,#245b8f)}.accounting-new-booking>summary::-webkit-details-marker{display:none}.accounting-new-booking>summary:after{content:"＋";margin-left:auto;font-size:1.25rem}.accounting-new-booking[open]>summary:after{content:"−"}.accounting-new-booking-content{margin-top:18px}
@media(max-width:850px){.accounting-stats{grid-template-columns:1fr 1fr}.accounting-stat-group:last-child{grid-column:span 2}.accounting-form-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:520px){.accounting-stats{grid-template-columns:1fr}.accounting-stat-group:last-child{grid-column:auto}.accounting-form-grid{grid-template-columns:1fr}.accounting-form-grid .wide{grid-column:auto}.accounting-section{padding:13px}.accounting-stat strong{font-size:1rem}}
</style>
</head>
<body><div class="container">
<header class="app-page-header"><h1><i class="fas fa-book"></i> Buchführung – Buchungen</h1><?php require $appRoot.'/includes/navigation.php'; ?></header>
<div class="accounting-wrap">
<?php if($message):?><div class="accounting-success"><?=accH($message)?></div><?php endif;?>
<?php if($error):?><div class="accounting-error"><?=accH($error)?></div><?php endif;?>
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
<?php elseif($today->format('Y-m-d') > $selectedYear['end_date']): ?>
<section class="accounting-section">
<h2>Geschäftsjahr abschließen</h2>
<p>Das Geschäftsjahr ist beendet. Prüfe vor der endgültigen Festschreibung die nachfolgende Abschlussübersicht. Danach sind Änderungen in diesem Geschäftsjahr technisch gesperrt.</p>
<a class="btn btn-primary" href="buchungen.php?jahr=<?=$selectedYearId?>&abschluss=1">Abschlussübersicht öffnen</a>
</section>
<?php endif; ?>
<?php if(isset($_GET['abschluss']) && $selectedYear && $selectedYear['status']!=='closed' && $today->format('Y-m-d') > $selectedYear['end_date']): ?>
<section class="accounting-section" id="abschluss">
<h2>Abschlussübersicht Geschäftsjahr <?=accH($selectedYear['label'])?></h2>
<div class="accounting-stats">
<div class="accounting-stat"><small>Anfangsbestand Bank</small><strong><?=accH(accCentsToDisplay($yearStats['opening_bank']))?></strong></div>
<div class="accounting-stat"><small>Anfangsbestand Barkasse</small><strong><?=accH(accCentsToDisplay($yearStats['opening_cash']))?></strong></div>
<div class="accounting-stat"><small>Einnahmen</small><strong class="accounting-positive"><?=accH(accCentsToDisplay($yearStats['income']))?></strong></div>
<div class="accounting-stat"><small>Ausgaben</small><strong class="accounting-negative"><?=accH(accCentsToDisplay($yearStats['expense']))?></strong></div>
<div class="accounting-stat"><small>Jahresergebnis</small><strong><?=accH(accCentsToDisplay($yearStats['result']))?></strong></div>
<div class="accounting-stat"><small>Endbestand Bank</small><strong><?=accH(accCentsToDisplay($yearStats['closing_bank']))?></strong></div>
<div class="accounting-stat"><small>Endbestand Barkasse</small><strong><?=accH(accCentsToDisplay($yearStats['closing_cash']))?></strong></div>
</div>
<p class="accounting-muted">Die Bestände werden aus den Anfangsbeständen und sämtlichen Buchungen chronologisch berechnet. Belegdateien sind optional; das Feld „Referenz“ wird in der Buchungsliste ausgewiesen.</p>
<?php if($yearStats['closing_bank']<0 || $yearStats['closing_cash']<0): ?><div class="accounting-error">Achtung: Mindestens ein rechnerischer Endbestand ist negativ. Bitte vor der Festschreibung prüfen, ob Buchungen oder Anfangsbestände fehlen bzw. fehlerhaft sind.</div><?php endif; ?>
<?php $unassignedTaxAreaCount = 0; foreach ($bookings as $bookingForTaxCheck) { if (in_array($bookingForTaxCheck['booking_type'], ['income','expense'], true) && empty($bookingForTaxCheck['tax_area'])) $unassignedTaxAreaCount++; } ?>
<?php if($unassignedTaxAreaCount > 0): ?><div class="accounting-error"><?= (int)$unassignedTaxAreaCount ?> Einnahmen/Ausgaben haben noch keinen steuerlichen Tätigkeitsbereich. Bitte diese Buchungen bearbeiten und zuordnen. Erst danach ist die Festschreibung möglich.</div><?php endif; ?>
<h3>Aufteilung nach steuerlichem Tätigkeitsbereich</h3>
<div class="accounting-table-wrap"><table class="accounting-table" style="min-width:650px"><thead><tr><th>Tätigkeitsbereich</th><th class="num">Einnahmen</th><th class="num">Ausgaben</th><th class="num">Saldo</th></tr></thead><tbody>
<?php foreach ($taxAreaTotals as $areaCode=>$areaTotals): ?><tr><td><?=accH(accTaxAreaLabel($areaCode))?></td><td class="num accounting-positive"><?=accH(accCentsToDisplay($areaTotals['income']))?></td><td class="num accounting-negative"><?=accH(accCentsToDisplay($areaTotals['expense']))?></td><td class="num"><?=accH(accCentsToDisplay($areaTotals['income']-$areaTotals['expense']))?></td></tr><?php endforeach; ?>
</tbody></table></div>
<h3>Alle Buchungen (<?=count($bookings)?>)</h3>
<div class="accounting-table-wrap"><table class="accounting-table"><thead><tr><th>Buchungs-ID</th><th>Datum</th><th>Beschreibung</th><th>Kategorie</th><th>Steuerlicher Tätigkeitsbereich</th><th>Art / Zahlungsweg</th><th class="num">Betrag</th><th>Referenz</th></tr></thead><tbody>
<?php if(!$bookings): ?><tr><td colspan="8" class="accounting-empty">Keine Buchungen vorhanden.</td></tr><?php else: foreach($bookings as $b): ?><tr><td><?= (int)$b['id'] ?></td><td><?=date('d.m.Y',strtotime($b['booking_date']))?></td><td><?=accH($b['description'])?></td><td><?=accH($b['category_name']?:'–')?></td><td><?= $b['booking_type']==='transfer' ? '–' : accH(accTaxAreaLabel($b['tax_area'] ?? null)) ?></td><td><?= $b['booking_type']==='transfer' ? 'Umbuchung: '.($b['transfer_from']==='bank'?'Konto':'Barkasse').' → '.($b['transfer_to']==='bank'?'Konto':'Barkasse') : ($b['booking_type']==='income'?'Einnahme':'Ausgabe').' · '.($b['payment_channel']==='bank'?'Konto':'Barkasse') ?></td><td class="num"><?=accH(accCentsToDisplay($b['amount_cents']))?></td><td><?=accH($b['paper_reference']?:'–')?></td></tr><?php endforeach; endif; ?>
</tbody></table></div>
<form method="post" onsubmit="return confirm('Achtung: Das Geschäftsjahr wird endgültig festgeschrieben. Danach sind Buchungen und zugehörige Daten in diesem Jahr nicht mehr änderbar. Die Festschreibung kann nicht rückgängig gemacht werden. Wirklich fortfahren?')">
<input type="hidden" name="csrf_token" value="<?=accH($csrf)?>"><input type="hidden" name="action" value="finalize_year"><input type="hidden" name="fiscal_year_id" value="<?=$selectedYearId?>">
<div class="accounting-actions"><button class="btn btn-danger" type="submit">Geschäftsjahr endgültig festschreiben</button><a class="btn btn-secondary" href="buchungen.php?jahr=<?=$selectedYearId?>">Zurück</a></div>
</form>
</section>
<?php endif; ?>
<?php if(!$editing): ?>
<?php if($selectedYear['status']==='closed'): ?>
<section class="accounting-section" id="buchung-form"><h2>Neue Buchung</h2><p>Dieses Geschäftsjahr ist festgeschrieben. Es können keine Buchungen verändert werden.</p></section>
<?php else: ?>
<details class="accounting-section accounting-new-booking" id="buchung-form">
<summary><i class="fas fa-plus-circle"></i> Neue Buchung anlegen</summary>
<div class="accounting-new-booking-content">
<form method="post">
<input type="hidden" name="csrf_token" value="<?=accH($csrf)?>"><input type="hidden" name="action" value="save_booking"><input type="hidden" name="booking_id" value="0"><input type="hidden" name="fiscal_year_id" value="<?=$selectedYearId?>">
<div class="accounting-form-grid">
<label>Datum<input type="date" name="booking_date" required min="<?=accH($selectedYear['start_date'])?>" max="<?=accH($selectedYear['end_date'])?>" value="<?=accH($defaultBookingDate)?>"></label>
<label>Buchungsart<select name="booking_type" id="booking_type" onchange="toggleAccountingFields(this.form)"><option value="expense" selected>Ausgabe</option><option value="income">Einnahme</option><option value="transfer">Umbuchung</option></select></label>
<label class="booking-normal">Zahlungsweg<select name="payment_channel"><option value="bank">Konto</option><option value="cash">Barkasse</option></select></label>
<label class="booking-transfer" style="display:none">Von<select name="transfer_from"><option value="bank">Konto</option><option value="cash">Barkasse</option></select></label>
<label class="booking-transfer" style="display:none">Nach<select name="transfer_to"><option value="cash">Barkasse</option><option value="bank">Konto</option></select></label>
<label class="booking-normal">Kategorie<input type="text" name="category" list="accounting-category-options-new" maxlength="150" required autocomplete="off" placeholder="Kategorie auswählen oder neu eingeben"><datalist id="accounting-category-options-new"><?php foreach($categories as $categoryOption):?><option value="<?=accH($categoryOption)?>"></option><?php endforeach;?></datalist><small class="accounting-muted">Vorschläge stammen aus den Buchungen dieses Geschäftsjahres.</small></label>
<label class="booking-normal">Steuerlicher Tätigkeitsbereich<select name="tax_area" required>
<option value="">Bitte auswählen</option><option value="ideell">Ideeller Bereich</option><option value="asset_management">Vermögensverwaltung</option><option value="purpose_operation">Zweckbetrieb</option><option value="commercial_business">Steuerpflichtiger wirtschaftlicher Geschäftsbetrieb</option>
</select></label>
<label>Betrag (€)<input name="amount" inputmode="decimal" required placeholder="0,00"></label>
<label class="wide">Beschreibung<input name="description" maxlength="1000" required></label>
<label class="wide booking-normal">Papierbeleg-Referenz<input name="paper_reference" maxlength="255" placeholder="z. B. Nr. 37"></label>
</div>
<div class="accounting-actions"><button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Buchung speichern</button></div>
</form>
</div>
</details>
<?php endif; ?>
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
<summary><span><?=accH($monthLabel)?></span><small><?=count($monthBookings)?> <?=count($monthBookings)===1?'Buchung':'Buchungen'?></small></summary>
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
<label class="booking-normal">Steuerlicher Tätigkeitsbereich<select name="tax_area" required><option value="">Bitte auswählen</option><option value="ideell" <?=($editing['tax_area']??'')==='ideell'?'selected':''?>>Ideeller Bereich</option><option value="asset_management" <?=($editing['tax_area']??'')==='asset_management'?'selected':''?>>Vermögensverwaltung</option><option value="purpose_operation" <?=($editing['tax_area']??'')==='purpose_operation'?'selected':''?>>Zweckbetrieb</option><option value="commercial_business" <?=($editing['tax_area']??'')==='commercial_business'?'selected':''?>>Steuerpflichtiger wirtschaftlicher Geschäftsbetrieb</option></select></label>
<label>Betrag (€)<input name="amount" inputmode="decimal" required placeholder="0,00" value="<?=accH(number_format((float)$editing['amount'],2,',','.'))?>"></label>
<label class="wide">Beschreibung<input name="description" maxlength="1000" required value="<?=accH($editing['description'])?>"></label>
<label class="wide booking-normal">Papierbeleg-Referenz<input name="paper_reference" maxlength="255" value="<?=accH($editing['paper_reference']??'')?>" placeholder="z. B. Nr. 37"></label>
</div>
<div class="accounting-actions"><button class="btn btn-primary" type="submit">Änderungen übernehmen</button><a class="btn btn-secondary" href="buchungen.php?jahr=<?=$selectedYearId?>#buchung-row-<?=$b['id']?>">Abbrechen</a></div>
</form></div></td></tr>
<?php endif; ?>
<?php endforeach; ?>
</tbody></table></div>
</details>
<?php endforeach; endif; ?></section>
<?php endif;?>

</div></div>
<script>
function toggleAccountingFields(form){if(!form)return;const type=form.querySelector('[name="booking_type"]');if(!type)return;const transfer=type.value==='transfer';form.querySelectorAll('.booking-normal').forEach(e=>e.style.display=transfer?'none':'');form.querySelectorAll('.booking-transfer').forEach(e=>e.style.display=transfer?'':'none');const taxArea=form.querySelector('[name="tax_area"]');if(taxArea){taxArea.disabled=transfer;taxArea.required=!transfer;}const category=form.querySelector('[name="category"]');if(category){category.disabled=transfer;category.required=!transfer;}}
document.querySelectorAll('form').forEach(form=>{if(form.querySelector('[name="booking_type"]'))toggleAccountingFields(form);});
</script>
</body></html>
