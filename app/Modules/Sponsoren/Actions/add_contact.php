
<?php
// add_contact.php - Fügt einen neuen Kontakt hinzu
require_once dirname(__DIR__, 4) . '/auth.php';

// Berechtigung prüfen
if (!hasPermission('add_contacts') && !hasPermission('suggest_contacts')) {
    echo json_encode(['success' => false, 'error' => 'Keine Berechtigung zum Hinzufügen von Kontakten oder Vorschlägen']);
    exit;
}

header('Content-Type: application/json');
require_once dirname(__DIR__, 4) . '/db_connection.php';

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || !isset($data['name']) || !isset($data['typ'])) {
    echo json_encode(['success' => false, 'error' => 'Name und Typ sind erforderlich']);
    exit;
}

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Nächsten Primary Key ermitteln
    $stmt = $conn->query("SELECT MAX(primary_key) as max_key FROM contacts");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $nextPrimaryKey = ($result['max_key'] ? $result['max_key'] + 1 : 1);
    
    // is_suggestion - Standard 0, außer explizit gesetzt
    $is_suggestion = isset($data['is_suggestion']) ? (int)$data['is_suggestion'] : 0;
    
    // Validierung
    $typ = $data['typ'];
    switch($typ) {
        case 'Mail':
            if (empty($data['anrede']) || empty($data['emailadresse'])) {
                echo json_encode(['success' => false, 'error' => 'Für Mail-Kontakte sind Anrede und E-Mail-Adresse erforderlich']);
                exit;
            }
            break;
        case 'Formular':
            if (empty($data['formular_link'])) {
                echo json_encode(['success' => false, 'error' => 'Für Formular-Kontakte ist ein Formular-Link erforderlich']);
                exit;
            }
            break;
        case 'Persönlich':
            if (empty($data['anschrift'])) {
                echo json_encode(['success' => false, 'error' => 'Für persönliche Kontakte ist eine Anschrift erforderlich']);
                exit;
            }
            break;
    }
    
    // SQL Query
    $query = "INSERT INTO contacts 
              (primary_key, name, anrede, webseite, emailadresse, typ, formular_link, anschrift, is_suggestion, created_at, updated_at) 
              VALUES 
              (:primary_key, :name, :anrede, :webseite, :emailadresse, :typ, :formular_link, :anschrift, :is_suggestion, NOW(), NOW())";
    
    $stmt = $conn->prepare($query);
    $success = $stmt->execute([
        ':primary_key' => $nextPrimaryKey,
        ':name' => $data['name'],
        ':anrede' => $data['anrede'] ?? '',
        ':webseite' => $data['webseite'] ?? '',
        ':emailadresse' => $data['emailadresse'] ?? '',
        ':typ' => $data['typ'],
        ':formular_link' => $data['formular_link'] ?? '',
        ':anschrift' => $data['anschrift'] ?? '',
        ':is_suggestion' => $is_suggestion
    ]);
    
    if ($success) {
        echo json_encode([
            'success' => true, 
            'new_primary_key' => $nextPrimaryKey,
            'is_suggestion' => $is_suggestion
        ]);
    } else {
        $errorInfo = $stmt->errorInfo();
        echo json_encode(['success' => false, 'error' => 'Datenbankfehler: ' . ($errorInfo[2] ?? 'Unbekannter Fehler')]);
    }
    
} catch (Exception $e) {
    error_log("Fehler in add_contact.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Server Fehler: ' . $e->getMessage()]);
}
?>
