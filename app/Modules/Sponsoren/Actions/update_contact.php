<?php
$appRoot = dirname(__DIR__, 4);
// update_contact.php - Aktualisiert einen einzelnen Kontakt
require_once $appRoot . '/auth.php';

header('Content-Type: application/json');
require_once $appRoot . '/db_connection.php';

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || !isset($data['primary_key'])) {
    echo json_encode(['success' => false, 'error' => 'Ungültige Daten']);
    exit;
}

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Prüfen ob nur Kommentar aktualisiert werden soll
    $isCommentOnlyUpdate = (count($data) === 2 && isset($data['primary_key']) && isset($data['kommentar']));
    
    if ($isCommentOnlyUpdate) {
        // Nur Kommentar-Berechtigung prüfen
        if (!hasPermission('edit_comments')) {
            echo json_encode(['success' => false, 'error' => 'Keine Berechtigung zum Bearbeiten von Kommentaren']);
            exit;
        }
    } else {
        // Vollständige Edit-Berechtigung prüfen
        if (!hasPermission('edit_contacts')) {
            echo json_encode(['success' => false, 'error' => 'Keine Berechtigung zum Bearbeiten von Kontakten']);
            exit;
        }
    }
    
    // Vorbereiten der Update-Felder
    $updateFields = [];
    $params = [':primary_key' => $data['primary_key']];
    
    // Nur vorhandene Felder aktualisieren
    if (isset($data['name'])) {
        $updateFields[] = 'name = :name';
        $params[':name'] = $data['name'];
    }
    if (isset($data['anrede'])) {
        $updateFields[] = 'anrede = :anrede';
        $params[':anrede'] = $data['anrede'];
    }
    if (isset($data['webseite'])) {
        $updateFields[] = 'webseite = :webseite';
        $params[':webseite'] = $data['webseite'];
    }
    if (isset($data['emailadresse'])) {
        $updateFields[] = 'emailadresse = :emailadresse';
        $params[':emailadresse'] = $data['emailadresse'];
    }
    if (isset($data['typ'])) {
        $updateFields[] = 'typ = :typ';
        $params[':typ'] = $data['typ'];
    }
    if (isset($data['formular_link'])) {
        $updateFields[] = 'formular_link = :formular_link';
        $params[':formular_link'] = $data['formular_link'];
    }
    if (isset($data['anschrift'])) {
        $updateFields[] = 'anschrift = :anschrift';
        $params[':anschrift'] = $data['anschrift'];
    }
    if (isset($data['status'])) {
        $updateFields[] = 'status = :status';
        $params[':status'] = $data['status'];
    }
    if (isset($data['kommentar'])) {
        $updateFields[] = 'kommentar = :kommentar';
        $params[':kommentar'] = $data['kommentar'];
    }
    if (isset($data['letztes_ergebnis'])) {
        $updateFields[] = 'letztes_ergebnis = :letztes_ergebnis';
        $params[':letztes_ergebnis'] = $data['letztes_ergebnis'];
    }
    
    // NEU: is_suggestion Feld aktualisieren falls vorhanden
    if (isset($data['is_suggestion'])) {
        $updateFields[] = 'is_suggestion = :is_suggestion';
        $params[':is_suggestion'] = intval($data['is_suggestion']);
    }
    
    // Datum umwandeln falls vorhanden
    if (isset($data['zuletzt_angeschrieben'])) {
        $datum_mysql = null;
        if (!empty($data['zuletzt_angeschrieben'])) {
            $datum_parts = explode('.', $data['zuletzt_angeschrieben']);
            if (count($datum_parts) === 3) {
                $datum_mysql = $datum_parts[2] . '-' . $datum_parts[1] . '-' . $datum_parts[0];
                $updateFields[] = 'zuletzt_angeschrieben = :zuletzt_angeschrieben';
                $params[':zuletzt_angeschrieben'] = $datum_mysql;
            }
        }
    }
    
    // updated_at immer setzen
    $updateFields[] = 'updated_at = NOW()';
    
    if (empty($updateFields)) {
        echo json_encode(['success' => false, 'error' => 'Keine Felder zum Aktualisieren']);
        exit;
    }
    
    $query = "UPDATE contacts SET " . implode(', ', $updateFields) . " WHERE primary_key = :primary_key";
    
    $stmt = $conn->prepare($query);
    $success = $stmt->execute($params);
    
    if ($success) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Update fehlgeschlagen']);
    }
    
} catch (Exception $e) {
    error_log("Fehler in update_contact.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Datenbankfehler: ' . $e->getMessage()]);
}
?>