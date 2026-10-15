<?php
// delete_contact.php - Löscht einen einzelnen Kontakt
require_once dirname(__DIR__, 4) . '/auth.php';
checkPermission('delete_contacts');

header('Content-Type: application/json');
require_once dirname(__DIR__, 4) . '/db_connection.php';

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || !isset($data['primary_key'])) {
    echo json_encode(['success' => false, 'error' => 'Ungültige Daten']);
    exit;
}

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $query = "DELETE FROM contacts WHERE primary_key = :primary_key";
    $stmt = $conn->prepare($query);
    $success = $stmt->execute([':primary_key' => $data['primary_key']]);
    
    if ($success) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Löschen fehlgeschlagen']);
    }
    
} catch (Exception $e) {
    error_log("Fehler in delete_contact.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Datenbankfehler: ' . $e->getMessage()]);
}
?>