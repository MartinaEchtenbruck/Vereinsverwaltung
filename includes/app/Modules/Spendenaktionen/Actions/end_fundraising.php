<?php
if (!defined('APP_ENTRY_POINT')) { http_response_code(404); exit; }
$appRoot = dirname(__DIR__, 4);
require_once $appRoot . '/auth.php';
checkPermission('edit_fundraising');
header('Content-Type: application/json; charset=utf-8');
require_once $appRoot . '/db_connection.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['success'=>false,'error'=>'Methode nicht erlaubt']); exit; }
try {
    $database = new Database(); $conn = $database->getConnection();
    $stmt = $conn->prepare("UPDATE fundraising_campaigns SET ended_at = NOW(), ended_by = :user_id WHERE ended_at IS NULL");
    $stmt->execute([':user_id'=>isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null]);
    if ($stmt->rowCount() < 1) { echo json_encode(['success'=>false,'error'=>'Es läuft aktuell keine Spendenaktion.']); exit; }
    echo json_encode(['success'=>true,'message'=>'Die Spendenaktion wurde beendet. Das Enddatum ist gespeichert.']);
} catch (Throwable $e) {
    error_log('Fehler beim Beenden der Spendenaktion: '.$e->getMessage());
    http_response_code(500); echo json_encode(['success'=>false,'error'=>'Die Spendenaktion konnte nicht beendet werden. Prüfe, ob die Datenbankmigration ausgeführt wurde.']);
}
