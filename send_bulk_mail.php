<?php
// send_bulk_mail.php - Versendet EINE Mail im Rahmen des Bulk-Versands
require_once 'auth.php';
checkPermission('edit_fundraising');

header('Content-Type: application/json');
require_once 'db_connection.php';
require_once 'mail_utils.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Methode nicht erlaubt']);
    exit;
}

$input = file_get_contents('php://input');
$data  = json_decode($input, true);

if (!$data || !isset($data['primary_key'])) {
    echo json_encode(['success' => false, 'error' => 'Ungültige Daten']);
    exit;
}

$primaryKey = (int)$data['primary_key'];

// ---------------------------------------------------------------------------
// Rate-Limit prüfen (max. 240 Mails in 60 Minuten, Puffer unter 250/h)
// ---------------------------------------------------------------------------
$limitFile  = 'bulk_mail_timestamps.log';
$maxPerHour = 240;
$now        = time();

$recent = 0;
if (file_exists($limitFile)) {
    $lines = file($limitFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $ts = (int)trim($line);
        if ($ts > $now - 3600) {
            $recent++;
        }
    }
}

if ($recent >= $maxPerHour) {
    echo json_encode([
        'success' => false,
        'error'   => 'rate_limit',
        'message' => "Stundenlimit erreicht ({$recent} Mails in der letzten Stunde). Bitte später erneut versuchen."
    ]);
    exit;
}

try {
    $database = new Database();
    $conn = $database->getConnection();

    // Kontakt laden
    $stmt = $conn->prepare("
        SELECT primary_key, name, anrede, emailadresse, typ, status
        FROM contacts
        WHERE primary_key = :pk
    ");
    $stmt->execute([':pk' => $primaryKey]);
    $contact = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$contact) {
        echo json_encode(['success' => false, 'error' => 'Kontakt nicht gefunden']);
        exit;
    }

    // Re-Check: nur Mailkontakte mit leerem Status
    if ($contact['typ'] !== 'Mail') {
        echo json_encode(['success' => false, 'skipped' => true,
            'error' => 'Kein Mailkontakt', 'name' => $contact['name']]);
        exit;
    }
    if (!empty($contact['status'])) {
        echo json_encode(['success' => false, 'skipped' => true,
            'error' => 'Status nicht mehr "Anfrage ausstehend"', 'name' => $contact['name']]);
        exit;
    }
    if (empty($contact['emailadresse']) ||
        !filter_var($contact['emailadresse'], FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'skipped' => true,
            'error' => 'Keine gültige E-Mail-Adresse', 'name' => $contact['name']]);
        exit;
    }

    // Vorlage laden (identisch zu contact.php)
    $stmt = $conn->prepare("SELECT betreff, mailtext FROM email_templates WHERE is_default = TRUE LIMIT 1");
    $stmt->execute();
    $template = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($template) {
        $betreff = $template['betreff'] ?? 'Spendenanfrage für den Förderverein';
        $text    = $template['mailtext'] ?? '';
    } else {
        $betreff = file_exists('Anschreiben-Betreff.txt')
            ? trim(file_get_contents('Anschreiben-Betreff.txt'))
            : 'Spendenanfrage für den Förderverein';
        $text = file_exists('Anschreiben-Text.txt')
            ? trim(file_get_contents('Anschreiben-Text.txt'))
            : '';
    }

    // Mailtext identisch zu contact.php zusammensetzen
    $mailBody = "Sehr geehrte Damen und Herren, " . ($contact['anrede'] ?? '') . ",\n\n" . $text;

    // Anhänge: alle PDFs, deren Name mit "Anschreiben" beginnt
    $attachments = [];
    if ($handle = opendir('.')) {
        while (false !== ($entry = readdir($handle))) {
            if (strtolower(pathinfo($entry, PATHINFO_EXTENSION)) === 'pdf'
                && stripos($entry, 'Anschreiben') === 0) {
                $attachments[] = $entry;
            }
        }
        closedir($handle);
    }
    sort($attachments);

    // Versand
    $sent = sendEmailWithAttachments(
        $contact['emailadresse'],
        $betreff,
        $mailBody,
        $attachments
    );

    if (!$sent) {
        echo json_encode(['success' => false,
            'error' => 'Mailversand fehlgeschlagen', 'name' => $contact['name']]);
        exit;
    }

    // Status aktualisieren (wie in contact.php)
    $updateStmt = $conn->prepare("
        UPDATE contacts
        SET status = 'Antwort abwarten',
            zuletzt_angeschrieben = CURDATE(),
            updated_at = NOW()
        WHERE primary_key = :pk
    ");
    $updateStmt->execute([':pk' => $primaryKey]);

    // Timestamp für Rate-Limit
    file_put_contents($limitFile, $now . "\n", FILE_APPEND);

    echo json_encode([
        'success' => true,
        'name'    => $contact['name'],
        'email'   => $contact['emailadresse']
    ]);

} catch (Exception $e) {
    error_log("Fehler in send_bulk_mail.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Datenbankfehler: ' . $e->getMessage()]);
}