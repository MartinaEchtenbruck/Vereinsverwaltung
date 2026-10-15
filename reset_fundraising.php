<?php
// reset_fundraising.php - Setzt den Status aller Kontakte zurück
require_once 'auth.php';
checkPermission('edit_fundraising');

header('Content-Type: application/json');
require_once 'db_connection.php';

// Nur POST erlauben
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Methode nicht erlaubt']);
    exit;
}

try {
    $database = new Database();
    $conn = $database->getConnection();

    // Ein einziges UPDATE – nur Kontakte mit tatsächlich gesetztem Status
    $stmt = $conn->prepare("
        UPDATE contacts
        SET status = '',
            updated_at = NOW()
        WHERE status IS NOT NULL AND status <> ''
    ");
    $stmt->execute();
    $affected = $stmt->rowCount();

    echo json_encode([
        'success'       => true,
        'affected_rows' => $affected,
        'message'       => $affected === 0
            ? 'Keine Kontakte mit gesetztem Status gefunden – nichts zu tun.'
            : "Status von {$affected} Kontakten wurde zurückgesetzt."
    ]);

} catch (Exception $e) {
    error_log("Fehler in reset_fundraising.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Datenbankfehler: ' . $e->getMessage()]);
}