
<?php
// load_contacts.php - Lädt Kontakte aus der MySQL-Datenbank
require_once dirname(__DIR__, 4) . '/auth.php';
checkPermission('view_contacts');

header('Content-Type: application/json');
require_once dirname(__DIR__, 4) . '/db_connection.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Kontakte aus der Datenbank abrufen - NEUE SORTIERUNG
    $query = "SELECT 
                primary_key as PrimaryKey,
                name as Name,
                anrede as Anrede,
                webseite as Webseite,
                emailadresse as emailadresse,
                status as Status,
                zuletzt_angeschrieben as Zuletzt_angeschrieben,
                kommentar as Kommentar,
                letztes_ergebnis as Letztes_Ergebnis,
                typ as Typ,
                formular_link as formular_link,
                anschrift as anschrift,
                is_suggestion as is_suggestion
              FROM contacts 
              ORDER BY is_suggestion DESC, name ASC";
    
    $stmt = $conn->prepare($query);
    $stmt->execute();
    
    $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Datum formatieren (YYYY-MM-DD -> DD.MM.YYYY)
    foreach ($contacts as &$contact) {
        if (!empty($contact['Zuletzt_angeschrieben']) && $contact['Zuletzt_angeschrieben'] != '0000-00-00') {
            $date = DateTime::createFromFormat('Y-m-d', $contact['Zuletzt_angeschrieben']);
            if ($date) {
                $contact['Zuletzt_angeschrieben'] = $date->format('d.m.Y');
            }
        }
        
        // Sicherstellen, dass Typ immer einen Wert hat
        if (empty($contact['Typ'])) {
            $contact['Typ'] = 'Mail';
        }
    }
    
    // IMMER ein Array zurückgeben (auch wenn leer)
    echo json_encode($contacts);
    
} catch (Exception $e) {
    error_log("Fehler in load_contacts.php: " . $e->getMessage());
    // Fehler als leeres Array zurückgeben, nicht als Fehlerobjekt
    echo json_encode([]);
}
?>
