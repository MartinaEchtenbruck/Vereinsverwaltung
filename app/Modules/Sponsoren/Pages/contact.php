<?php
/** Interne Moduldatei: nur über den bisherigen Einstiegspunkt aufrufen. */
if (!defined('APP_ENTRY_POINT')) {
    http_response_code(404);
    exit;
}

// contact.php - Anschreiben erstellen
$appRoot = dirname(__DIR__, 4);
// Keep legacy relative file operations anchored at the application root.
chdir($appRoot);
require_once $appRoot . '/auth.php';
require_once $appRoot . '/mail_utils.php';
checkPermission('send_emails');

// Datenbankverbindung einbinden
require_once $appRoot . '/db_connection.php';

// Sicherstellen, dass der PrimaryKey vorhanden ist
if (!isset($_GET['primarykey']) || empty($_GET['primarykey'])) {
    header('Location: index.php');
    exit;
}

$primaryKey = intval($_GET['primarykey']);

// Kontaktdaten aus Datenbank laden
$currentContact = null;
try {
    $database = new Database();
    $conn = $database->getConnection();
    
    if (!$conn) {
        throw new Exception("Datenbankverbindung fehlgeschlagen");
    }
    
    $query = "SELECT * FROM contacts WHERE primary_key = :primary_key";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":primary_key", $primaryKey);
    $stmt->execute();
    $currentContact = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$currentContact) {
        header('Location: index.php');
        exit;
    }
    
    
} catch (Exception $e) {
    error_log("Fehler beim Laden des Kontakts: " . $e->getMessage());
    header('Location: index.php');
    exit;
}

// E-Mail-Vorlage laden mit Fallback
$betreff = 'Spendenanfrage für den Förderverein';
$text = 'Standardtext für Spendenanfrage';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Versuche Standard-Vorlage aus der Datenbank zu laden
    $query = "SELECT betreff, mailtext FROM email_templates WHERE is_default = TRUE LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $emailTemplate = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($emailTemplate) {
        $betreff = $emailTemplate['betreff'] ?? $betreff;
        $text = $emailTemplate['mailtext'] ?? $text;
    } else {
        // Fallback: txt-Dateien
        if (file_exists($appRoot . '/Anschreiben-Betreff.txt')) {
            $betreff = trim(file_get_contents($appRoot . '/Anschreiben-Betreff.txt'));
        }
        if (file_exists($appRoot . '/Anschreiben-Text.txt')) {
            $text = trim(file_get_contents($appRoot . '/Anschreiben-Text.txt'));
        }
    }
    
} catch (Exception $e) {
    error_log("Fehler beim Laden der E-Mail-Vorlage: " . $e->getMessage());
    
    // Fallback zu txt-Dateien
    if (file_exists($appRoot . '/Anschreiben-Betreff.txt')) {
        $betreff = trim(file_get_contents($appRoot . '/Anschreiben-Betreff.txt'));
    }
    if (file_exists($appRoot . '/Anschreiben-Text.txt')) {
        $text = trim(file_get_contents($appRoot . '/Anschreiben-Text.txt'));
    }
}

// PDF-Dateien im Ordner finden
$pdfFiles = [];
$firstAnschreibenPdf = null;
if ($handle = opendir($appRoot)) {
    while (false !== ($entry = readdir($handle))) {
        if (pathinfo($entry, PATHINFO_EXTENSION) === 'pdf') {
            $pdfFiles[] = $entry;
            // Erste Datei die mit "Anschreiben" beginnt merken
            if (!$firstAnschreibenPdf && stripos($entry, 'Anschreiben') === 0) {
                $firstAnschreibenPdf = $entry;
            }
        }
    }
    closedir($handle);
}
sort($pdfFiles); // Sortieren für konsistente Anzeige

// E-Mail senden
$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $betreffText = trim($_POST['betreff']);
    $mailText = trim($_POST['mailtext']);
    $kommentar = isset($_POST['kommentar']) ? trim($_POST['kommentar']) : '';
    $selectedPdfs = isset($_POST['pdfs']) ? $_POST['pdfs'] : [];
    
    // Validierung
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Ungültige E-Mail-Adresse';
    } elseif (empty($betreffText) || empty($mailText)) {
        $error = 'Betreff und E-Mail-Text dürfen nicht leer sein';
    } else {
        try {
            // E-Mail mit Anhängen senden
            $mailSent = sendEmailWithAttachments($email, $betreffText, $mailText, $selectedPdfs);
            
            if ($mailSent) {
                // Nur diesen einen Kontakt in der Datenbank aktualisieren
                try {
                    $database = new Database();
                    $conn = $database->getConnection();
                    
                    $updateQuery = "UPDATE contacts 
                                   SET status = 'Antwort abwarten', 
                                       zuletzt_angeschrieben = CURDATE(), 
                                       kommentar = :kommentar,
                                       updated_at = NOW()
                                   WHERE primary_key = :primary_key";
                    
                    $updateStmt = $conn->prepare($updateQuery);
                    $updateStmt->bindParam(":kommentar", $kommentar);
                    $updateStmt->bindParam(":primary_key", $primaryKey);
                    $updateSuccess = $updateStmt->execute();
                    
                    if ($updateSuccess) {
                        $success = true;
                        // Aktualisierten Kontakt neu laden
                        $stmt = $conn->prepare("SELECT * FROM contacts WHERE primary_key = :primary_key");
                        $stmt->bindParam(":primary_key", $primaryKey);
                        $stmt->execute();
                        $currentContact = $stmt->fetch(PDO::FETCH_ASSOC);
                    } else {
                        $error = 'Fehler beim Aktualisieren des Kontakts in der Datenbank';
                    }
                    
                } catch (Exception $e) {
                    $error = 'Der Kontakt konnte nicht aktualisiert werden. Bitte versuchen Sie es erneut.';
                    error_log("Fehler beim Aktualisieren des Kontakts: " . $e->getMessage());
                }
            } else {
                $error = 'Fehler beim Senden der E-Mail';
            }
        } catch (Exception $e) {
            $error = 'Beim Verarbeiten der Anfrage ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut.';
            error_log("Fehler beim Verarbeiten des Anschreibens: " . $e->getMessage());
        }
    }
}

// E-Mail-Text mit korrekter Formatierung vorbereiten
$mailBody = "Sehr geehrte Damen und Herren, " . ($currentContact['anrede'] ?? '') . ",\n\n" . $text;


?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anschreiben erstellen</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3498db;
            --success-color: #2ecc71;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --light-gray: #f5f5f5;
            --border-color: #ddd;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f9f9f9;
            color: #333;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
        }
        
        h1 {
            color: var(--primary-color);
            margin-top: 0;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 10px;
        }
        
        .contact-info {
            background-color: var(--light-gray);
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            gap: 30px;
        }
        
        .contact-details {
            flex: 1;
        }
        
        .contact-comment {
            flex: 1;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            box-sizing: border-box;
            font-family: inherit;
        }
        
        textarea.form-control {
            min-height: 200px;
            resize: vertical;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-right: 10px;
            transition: background-color 0.2s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }
        
        .btn-secondary {
            background-color: var(--light-gray);
            color: #333;
        }
        
        .btn-success {
            background-color: var(--success-color);
            color: white;
        }
        
        .btn:hover {
            opacity: 0.9;
        }
        
        .notification {
            padding: 10px 15px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        
        .notification.success {
            background-color: rgba(46, 204, 113, 0.2);
            border-left: 4px solid var(--success-color);
            color: #155724;
        }
        
        .notification.error {
            background-color: rgba(231, 76, 60, 0.2);
            border-left: 4px solid var(--danger-color);
            color: #721c24;
        }
        
        .pdf-list {
            margin-top: 10px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            padding: 10px;
            max-height: 200px;
            overflow-y: auto;
        }
        
        .pdf-item {
            margin-bottom: 8px;
            padding: 5px;
            border-bottom: 1px solid #eee;
        }
        
        .pdf-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        
        .actions {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        
        .email-preview {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            padding: 15px;
            margin-top: 10px;
            white-space: pre-wrap;
            font-family: inherit;
            min-height: 100px;
        }
        
        .required::after {
            content: " *";
            color: var(--danger-color);
        }
        
        .info-text {
            font-size: 0.9em;
            color: #666;
            margin-top: 5px;
        }
        
        .spacing {
            margin-top: 30px;
        }
        
        .attachment-info {
            background-color: #e8f4fd;
            border-left: 4px solid var(--primary-color);
            padding: 10px;
            margin-top: 10px;
            border-radius: 4px;
        }
        
        .debug-info {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 10px;
            margin-top: 10px;
            border-radius: 4px;
            font-size: 0.9em;
        }
        
        .template-info {
            background-color: #e8f5e8;
            border-left: 4px solid var(--success-color);
            padding: 10px;
            margin-top: 10px;
            border-radius: 4px;
            font-size: 0.9em;
        }
        
        .user-info {
            text-align: right;
            margin-bottom: 20px;
            font-size: 0.9em;
            color: #666;
        }
    </style>
    <link rel="stylesheet" href="includes/navigation.css">
</head>
<body>
    <div class="container">
        <header class="app-page-header">
            <h1>Anschreiben erstellen</h1>
            <?php require $appRoot . '/includes/navigation.php'; ?>
        </header>
        
        <?php if ($success): ?>
            <div class="notification success">
                <i class="fas fa-check-circle"></i> E-Mail wurde erfolgreich gesendet und Kontakt aktualisiert! Sie werden in 3 Sekunden zur Übersicht weitergeleitet.
                <?php if (!empty($selectedPdfs)): ?>
                <div class="attachment-info">
                    <strong>Mitgesendete Anhänge:</strong><br>
                    <?php foreach ($selectedPdfs as $pdf): ?>
                    • <?php echo htmlspecialchars($pdf); ?><br>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                
                <div class="debug-info">
                    <strong>Datenbank-Update:</strong> Kontakt wurde in der MySQL-Datenbank aktualisiert.
                </div>
            </div>
            <script>
                setTimeout(function() {
                    window.location.href = 'index.php';
                }, 3000);
            </script>
        <?php elseif (!empty($error)): ?>
            <div class="notification error">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                
            </div>
        <?php endif; ?>
        
        <div class="template-info">
            <i class="fas fa-info-circle"></i> Verwendete E-Mail-Vorlage: Standard-Vorlage aus der Datenbank
        </div>
        
        <form method="POST" id="contactForm">
            <div class="contact-info">
                <div class="contact-details">
                    <h3>Kontaktinformationen</h3>
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($currentContact['name'] ?? ''); ?></p>
                    <p><strong>Aktueller Status:</strong> <?php $status = $currentContact['status'] ?? ''; echo htmlspecialchars(empty($status) ? 'Anfrage ausstehend' : $status); ?></p>
                    <p><strong>Zuletzt angeschrieben:</strong> <?php echo htmlspecialchars($currentContact['zuletzt_angeschrieben'] ?? ''); ?></p>
                </div>
                <div class="contact-comment">
                    <h3>Kommentar</h3>
                    <textarea name="kommentar" class="form-control" placeholder="Interne Notizen..." style="height: 120px;"><?php echo htmlspecialchars($currentContact['kommentar'] ?? ''); ?></textarea>
                    <div class="info-text">Dieser Kommentar wird nur intern gespeichert und nicht mitgesendet.</div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="email" class="required">E-Mail-Adresse</label>
                <input type="email" id="email" name="email" class="form-control" 
                       value="<?php echo htmlspecialchars($currentContact['emailadresse'] ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="betreff" class="required">Betreff</label>
                <input type="text" id="betreff" name="betreff" class="form-control" 
                       value="<?php echo htmlspecialchars($betreff); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="mailtext" class="required">E-Mail-Text</label>
                <textarea id="mailtext" name="mailtext" class="form-control" required><?php echo htmlspecialchars($mailBody); ?></textarea>                
            </div>
            
            <?php if (!empty($pdfFiles)): ?>
            <div class="form-group">
                <label>PDF-Anhänge auswählen:</label>
                <div class="info-text">Nur die angehakten Dateien werden als Anhang mitgesendet.</div>
                <div class="pdf-list">
                    <?php foreach ($pdfFiles as $pdfFile): 
                        // $isChecked = ($pdfFile === $firstAnschreibenPdf) ? 'checked' : ''; // Wählt nur das erste PDF aus
                        $isChecked = 'checked'; // wählt alles pdfs aus
                    ?>
                    <div class="pdf-item">
                        <label>
                            <input type="checkbox" name="pdfs[]" value="<?php echo htmlspecialchars($pdfFile); ?>" <?php echo $isChecked; ?>>
                            <?php echo htmlspecialchars($pdfFile); ?>
                            <span class="info-text">(<?php echo round(filesize($appRoot . '/' . $pdfFile) / 1024, 2); ?> KB)</span>
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php else: ?>
            <div class="form-group">
                <div class="info-text">Keine PDF-Dateien im Verzeichnis gefunden.</div>
            </div>
            <?php endif; ?>
            
            <div class="actions">
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Zurück zur Übersicht
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-paper-plane"></i> E-Mail senden
                </button>
            </div>
        </form>
        
        <div class="spacing"></div>
        
        <div class="form-group">
            <label>E-Mail-Vorschau:</label>
            <div class="email-preview" id="emailPreview">
                <!-- Vorschau wird per JavaScript aktualisiert -->
            </div>
        </div>
    </div>

    <script>
        // E-Mail-Vorschau aktualisieren
        function updatePreview() {
            const betreff = document.getElementById('betreff').value;
            const mailtext = document.getElementById('mailtext').value;
            
            document.getElementById('emailPreview').textContent = 
                `Betreff: ${betreff}\n\n${mailtext}`;
        }
        
        // Event-Listener für Formular-Änderungen
        document.getElementById('betreff').addEventListener('input', updatePreview);
        document.getElementById('mailtext').addEventListener('input', updatePreview);
        
        // Initiale Vorschau erstellen
        updatePreview();
        
        // Formular-Submit-Event
        document.getElementById('contactForm').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;
            const betreff = document.getElementById('betreff').value;
            const mailtext = document.getElementById('mailtext').value;
            const selectedPdfs = document.querySelectorAll('input[name="pdfs[]"]:checked');
            
            if (!email || !betreff || !mailtext) {
                e.preventDefault();
                alert('Bitte füllen Sie alle Pflichtfelder aus (markiert mit *).');
                return;
            }
            
            let attachmentText = '';
            if (selectedPdfs.length > 0) {
                attachmentText = '\n\nAnhänge:\n';
                selectedPdfs.forEach(function(pdf) {
                    attachmentText += '• ' + pdf.value + '\n';
                });
            }
            
            if (!confirm('Möchten Sie die E-Mail wirklich senden?\n\n' +
                        'Empfänger: ' + email + '\n' +
                        'Von: foerderverein@evkigarumeln.de\n' +
                        'Anzahl Anhänge: ' + selectedPdfs.length + 
                        attachmentText)) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>
