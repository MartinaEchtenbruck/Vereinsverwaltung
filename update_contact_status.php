<?php
// update_contact_status.php - Aktualisiert nur den Status eines Kontakts
require_once 'auth.php';
checkPermission('change_status');

header('Content-Type: application/json');
require_once 'db_connection.php';

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || !isset($data['primary_key']) || !isset($data['status'])) {
    echo json_encode(['success' => false, 'error' => 'Ungültige Daten']);
    exit;
}

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $query = "UPDATE contacts SET 
                status = :status,
                updated_at = NOW()
              WHERE primary_key = :primary_key";
    
    $stmt = $conn->prepare($query);
    $success = $stmt->execute([
        ':primary_key' => $data['primary_key'],
        ':status' => $data['status']
    ]);
    
    if ($success) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Status-Update fehlgeschlagen']);
    }
    
} catch (Exception $e) {
    error_log("Fehler in update_contact_status.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Datenbankfehler: ' . $e->getMessage()]);
}
?>