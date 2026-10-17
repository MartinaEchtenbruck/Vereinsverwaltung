<?php
/** Interne Moduldatei: nur über den bisherigen Einstiegspunkt aufrufen. */
if (!defined('APP_ENTRY_POINT')) { http_response_code(404); exit; }
$appRoot = dirname(__DIR__, 4);
require_once $appRoot . '/auth.php';
checkPermission('edit_fundraising');
header('Content-Type: application/json; charset=utf-8');
require_once $appRoot . '/db_connection.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['success'=>false,'error'=>'Methode nicht erlaubt']); exit; }
try {
    $payload = json_decode(file_get_contents('php://input'), true) ?: [];
    $name = trim((string)($payload['name'] ?? ''));
    $database = new Database(); $conn = $database->getConnection();
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->beginTransaction();
    $active = $conn->query("SELECT id FROM fundraising_campaigns WHERE ended_at IS NULL LIMIT 1 FOR UPDATE")->fetchColumn();
    if ($active) { $conn->rollBack(); http_response_code(409); echo json_encode(['success'=>false,'error'=>'Es läuft bereits eine Spendenaktion. Beende diese zuerst.']); exit; }
    $startedAt = new DateTimeImmutable('now');
    if ($name === '') $name = 'Spendenaktion ' . $startedAt->format('Y');
    $insert = $conn->prepare('INSERT INTO fundraising_campaigns (name, started_at, started_by) VALUES (:name, NOW(), :user_id)');
    $insert->execute([':name'=>$name, ':user_id'=>isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null]);
    // Der neue Durchlauf beginnt mit offenen Kontakten. Das Datum der letzten
    // Kontaktaufnahme wird geleert, damit Dashboard-Zahlen zur neuen Aktion gehören.
    $update = $conn->prepare("UPDATE contacts SET status = 'Handlungsbedarf', zuletzt_angeschrieben = NULL, updated_at = NOW() WHERE COALESCE(is_suggestion, 0) = 0");
    $update->execute(); $affected = $update->rowCount();
    $conn->commit();
    echo json_encode(['success'=>true,'affected_rows'=>$affected,'campaign_name'=>$name,'message'=>"„{$name}“ wurde gestartet. {$affected} Kontakte stehen für die neue Runde auf Handlungsbedarf."]);
} catch (Throwable $e) {
    if (isset($conn) && $conn->inTransaction()) $conn->rollBack();
    error_log('Fehler beim Start der Spendenaktion: '.$e->getMessage());
    http_response_code(500); echo json_encode(['success'=>false,'error'=>'Die Spendenaktion konnte nicht gestartet werden. Prüfe, ob die Datenbankmigration ausgeführt wurde.']);
}
