<?php
// bulk_mail_preview.php - Vorschau des Bulk-Mailversands
require_once 'auth.php';
checkPermission('edit_fundraising');

require_once 'db_connection.php';

// ---------------------------------------------------------------------------
// E-Mail-Vorlage laden (Standard)
// ---------------------------------------------------------------------------
$betreff = 'Spendenanfrage für den Förderverein';
$text    = 'Standardtext für Spendenanfrage';

// Anhänge, die mitgesendet würden
$attachments = [];

try {
    $database = new Database();
    $conn = $database->getConnection();

    // Standard-Vorlage
    $stmt = $conn->prepare("SELECT betreff, mailtext FROM email_templates WHERE is_default = TRUE LIMIT 1");
    $stmt->execute();
    $template = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($template) {
        $betreff = $template['betreff'] ?? $betreff;
        $text    = $template['mailtext'] ?? $text;
    } else {
        if (file_exists('Anschreiben-Betreff.txt')) {
            $betreff = trim(file_get_contents('Anschreiben-Betreff.txt'));
        }
        if (file_exists('Anschreiben-Text.txt')) {
            $text = trim(file_get_contents('Anschreiben-Text.txt'));
        }
    }

    // Mailkontakte, die noch nicht angeschrieben wurden
    $stmt = $conn->prepare("
        SELECT primary_key, name, anrede, emailadresse, typ, status
        FROM contacts
        WHERE typ = 'Mail'
          AND (status IS NULL OR status = '')
        ORDER BY name ASC
    ");
    $stmt->execute();
    $pendingMailContacts = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    $pendingMailContacts = [];
    $error = 'Datenbankfehler: ' . $e->getMessage();
}

// Anhänge ermitteln: alle PDFs, deren Name mit "Anschreiben" beginnt
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

// Anrede-Zeile pro Kontakt vorbereiten – identisch zu contact.php
// contact.php macht: "Sehr geehrte Damen und Herren, " . ($currentContact['anrede'] ?? '') . ","
function buildAnrede($anrede) {
    return 'Sehr geehrte Damen und Herren, ' . ($anrede ?? '') . ',';
}

// Gesamt-Mailtext für die Vorschau (ohne Kontakt-spezifische Anrede)
$mailBody = "Sehr geehrte Damen und Herren, <Anrede>,\n\n" . $text;
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulk-Mailversand vorbereiten</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .header-links {
            font-size: 14px;
        }
        .header-links a {
            color: var(--primary-color);
            text-decoration: none;
        }
        .header-links a:hover {
            text-decoration: underline;
        }

        .preview-card {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
        }
        .preview-card h2 {
            margin-top: 0;
            color: var(--primary-color);
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 10px;
        }

        .mail-preview {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            padding: 15px;
            white-space: pre-wrap;
            font-family: inherit;
            margin-top: 10px;
        }
        .mail-subject {
            font-weight: 600;
            margin-bottom: 8px;
        }

        .attachment-list {
            margin-top: 10px;
        }
        .attachment-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 0;
            border-bottom: 1px solid #eee;
        }
        .attachment-item:last-child {
            border-bottom: none;
        }
        .attachment-item i {
            color: var(--danger-color);
        }
        .attachment-item .size {
            font-size: 0.85em;
            color: #666;
            margin-left: auto;
        }

        .info-box {
            background-color: #e8f4fd;
            border-left: 4px solid var(--primary-color);
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 25px;
        }

        /* Tabelle im Stil der Hauptliste */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }
        th {
            background-color: var(--light-gray);
            font-weight: 600;
        }
        tr:hover {
            background-color: rgba(52, 152, 219, 0.05);
        }

        .contact-checkbox {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
        .contact-name {
            font-weight: 600;
        }
        .contact-anrede {
            font-size: 0.9em;
            color: #666;
            font-style: italic;
        }
        .no-email {
            color: var(--danger-color);
            font-weight: 600;
        }

        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 25px;
        }
        .actions .btn {
            width: auto;
            padding: 10px 20px;
        }
        .progress-bar-wrapper {
            background: #e9ecef;
            border-radius: 4px;
            height: 24px;
            overflow: hidden;
            margin: 15px 0;
        }
        .progress-bar-fill {
            background-color: var(--primary-color);
            height: 100%;
            width: 0%;
            transition: width 0.3s ease;
        }
        .progress-stats {
            display: flex;
            gap: 25px;
            margin: 15px 0;
            font-size: 0.95em;
            flex-wrap: wrap;
        }
        .progress-stats .stat-success strong { color: var(--success-color); }
        .progress-stats .stat-error   strong { color: var(--danger-color); }
        .progress-stats .stat-skipped strong { color: #666; }
    </style>
    <link rel="stylesheet" href="includes/navigation.css">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1><i class="fas fa-paper-plane"></i> Bulk-Mailversand vorbereiten</h1>
            <?php require __DIR__ . '/includes/navigation.php'; ?>
        </div>

        <!-- Info -->
        <div class="info-box">
            <strong>Bitte prüfen Sie die folgenden Angaben sorgfältig,</strong> bevor Sie den Versand starten.
            Jede hier angehakte Person erhält eine E-Mail mit dem unten stehenden Betreff, Text und den
            aufgeführten Anhängen. Nach dem Versand wird der Status jeder angeschriebenen Person auf
            „Antwort abwarten“ gesetzt.
        </div>
        
        <div id="bulkProgress" class="preview-card" style="display: none;">
            <h2><i class="fas fa-spinner fa-spin"></i> Versand läuft…</h2>
            <div id="progressText" style="font-weight: 600; margin-bottom: 5px;">0 von 0 versendet</div>
            <div class="progress-bar-wrapper">
                <div id="progressBar" class="progress-bar-fill"></div>
            </div>
            <div id="currentContactInfo" class="info-text" style="min-height: 20px;"></div>
            <div class="progress-stats">
                <span class="stat-success">Erfolgreich: <strong id="statSuccess">0</strong></span>
                <span class="stat-error">Fehler: <strong id="statError">0</strong></span>
                <span class="stat-skipped">Übersprungen: <strong id="statSkipped">0</strong></span>
            </div>
            <div class="actions">
                <button type="button" class="btn btn-danger" id="stopBulkSendBtn">
                    <i class="fas fa-stop"></i> Versand stoppen
                </button>
            </div>
        </div>

        <!-- E-Mail-Vorlage -->
        <div class="preview-card">
            <h2><i class="fas fa-envelope-open-text"></i> E-Mail-Vorlage</h2>

            <div class="mail-subject">
                Betreff: <?php echo htmlspecialchars($betreff); ?>
            </div>

            <div class="mail-preview"><?php echo htmlspecialchars($mailBody); ?></div>

            <p class="info-text" style="margin-top: 10px;">
                Die Anrede wird pro Empfänger ersetzt: <code>&lt;Anrede&gt;</code> = 
                „Sehr geehrte Damen und Herren, &lt;Anrede aus Kontakt&gt;,“
            </p>
        </div>

        <!-- Anhänge -->
        <div class="preview-card">
            <h2><i class="fas fa-paperclip"></i> Anhänge</h2>
            <?php if (!empty($attachments)): ?>
                <div class="attachment-list">
                    <?php foreach ($attachments as $file): ?>
                        <div class="attachment-item">
                            <i class="fas fa-file-pdf"></i>
                            <span><?php echo htmlspecialchars($file); ?></span>
                            <span class="size"><?php echo round(filesize($file) / 1024, 2); ?> KB</span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="info-text">Keine Anschreiben-PDFs im Verzeichnis gefunden.</div>
            <?php endif; ?>
        </div>

        <!-- Kontaktliste -->
        <div class="preview-card">
            <h2>
                <i class="fas fa-users"></i> Empfänger
                (<?php echo count($pendingMailContacts); ?> Kontakte)
            </h2>

            <?php if (!empty($pendingMailContacts)): ?>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 40px;">
                                <input type="checkbox" id="selectAll" class="contact-checkbox" checked>
                            </th>
                            <th>Name</th>
                            <th>Anrede (in der Mail)</th>
                            <th>E-Mail</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pendingMailContacts as $c): 
                            $hasEmail = !empty($c['emailadresse']);
                            $anredeLine = buildAnrede($c['anrede'] ?? '');
                        ?>
                            <tr>
                                <td>
                                    <input type="checkbox"
                                           class="contact-checkbox contact-select"
                                           name="contact_ids[]"
                                           value="<?php echo (int)$c['primary_key']; ?>"
                                           <?php echo $hasEmail ? 'checked' : 'disabled'; ?>>
                                </td>
                                <td class="contact-name">
                                    <?php echo htmlspecialchars($c['name'] ?? ''); ?>
                                    <?php if (!$hasEmail): ?>
                                        <span class="no-email">(keine E-Mail – wird übersprungen)</span>
                                    <?php endif; ?>
                                </td>
                                <td class="contact-anrede">
                                    <?php echo htmlspecialchars($anredeLine); ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($c['emailadresse'] ?? ''); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="actions">
                    <a href="edit_fundraising.php" class="btn btn-secondary" style="background-color: var(--button-gray);">
                        <i class="fas fa-times"></i> Abbrechen
                    </a>
                    <button type="button" class="btn btn-primary" id="startBulkSendBtn">
                        <i class="fas fa-paper-plane"></i> Versand starten
                    </button>
                </div>
            <?php else: ?>
                <div class="info-text">
                    Keine Mailkontakte mit Status „Anfrage ausstehend“ gefunden – es gibt nichts zu versenden.
                </div>
                <div class="actions">
                    <a href="edit_fundraising.php" class="btn btn-secondary" style="background-color: var(--button-gray);">
                        <i class="fas fa-arrow-left"></i> Zurück
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // "Alle auswählen"-Checkbox
        const selectAll = document.getElementById('selectAll');
        if (selectAll) {
            selectAll.addEventListener('change', function () {
                document.querySelectorAll('.contact-select:not([disabled])').forEach(cb => {
                    cb.checked = selectAll.checked;
                });
            });
        }

        // ---------- Bulk-Versand ----------
        const startBtn      = document.getElementById('startBulkSendBtn');
        const stopBtn       = document.getElementById('stopBulkSendBtn');
        const progressPanel = document.getElementById('bulkProgress');
        let stopRequested   = false;

        if (startBtn) {
            startBtn.addEventListener('click', async function () {
                const checked = document.querySelectorAll('.contact-select:checked:not([disabled])');
                const ids = Array.from(checked).map(cb => parseInt(cb.value));

                if (ids.length === 0) {
                    alert('Bitte mindestens einen Kontakt auswählen.');
                    return;
                }

                const estMinutes = Math.ceil(ids.length * 16 / 60);
                const confirmed = confirm(
                    'Bulk-Versand starten?\n\n' +
                    'Es werden ' + ids.length + ' E-Mails versendet.\n' +
                    'Zwischen den Mails wird jeweils 16 Sekunden gewartet.\n\n' +
                    'Geschätzte Dauer: ca. ' + estMinutes + ' Minute(n).\n\n' +
                    'Fortfahren?'
                );
                if (!confirmed) return;

                stopRequested = false;
                startBtn.disabled = true;
                progressPanel.style.display = 'block';
                progressPanel.scrollIntoView({ behavior: 'smooth' });

                let success = 0, errors = 0, skipped = 0;
                const total = ids.length;

                for (let i = 0; i < ids.length; i++) {
                    if (stopRequested) break;

                    updateProgress(i, total, success, errors, skipped);

                    const id = ids[i];
                    let result;
                    try {
                        const response = await fetch('send_bulk_mail.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ primary_key: id })
                        });
                        const raw = await response.text();
                        try { result = JSON.parse(raw); }
                        catch (e) {
                            result = { success: false, error: 'Ungültige Serverantwort: ' + raw.substring(0, 200) };
                        }
                    } catch (err) {
                        result = { success: false, error: 'Netzwerkfehler: ' + err.message };
                    }

                    if (result.success) {
                        success++;
                        setCurrentInfo('✓ ' + (result.name || ('Kontakt #' + id)) + ' angeschrieben');
                    } else if (result.skipped) {
                        skipped++;
                        setCurrentInfo('– ' + (result.name || ('Kontakt #' + id)) + ': ' + result.error);
                    } else if (result.error === 'rate_limit') {
                        alert(result.message);
                        stopRequested = true;
                        break;
                    } else {
                        errors++;
                        setCurrentInfo('✗ ' + (result.name || ('Kontakt #' + id)) + ': ' + (result.error || 'Fehler'));
                    }

                    updateProgress(i + 1, total, success, errors, skipped);

                    if (i < ids.length - 1 && !stopRequested) {
                        const waitSec = result.success ? 16 : 3;
                        await countdown(waitSec, () => stopRequested);
                    }
                }

                // Abschluss → zurück zu edit_fundraising.php mit Ergebnis
                window.location.href = 'edit_fundraising.php'
                    + '?bulk_success=' + success
                    + '&bulk_errors=' + errors
                    + '&bulk_skipped=' + skipped
                    + '&bulk_aborted=' + (stopRequested ? '1' : '0');
            });
        }

        if (stopBtn) {
            stopBtn.addEventListener('click', function () {
                if (confirm('Versand wirklich stoppen?\n\nBereits versendete Mails bleiben versendet.')) {
                    stopRequested = true;
                    setCurrentInfo('Stop angefordert – laufender Versand wird noch abgeschlossen…');
                }
            });
        }

        function updateProgress(done, total, success, errors, skipped) {
            document.getElementById('progressText').textContent = done + ' von ' + total + ' versendet';
            document.getElementById('progressBar').style.width = total > 0
                ? Math.round(done / total * 100) + '%'
                : '0%';
            document.getElementById('statSuccess').textContent = success;
            document.getElementById('statError').textContent   = errors;
            document.getElementById('statSkipped').textContent = skipped;
        }

        let currentBaseText = '';
        function setCurrentInfo(text) {
            currentBaseText = text;
            document.getElementById('currentContactInfo').textContent = text;
        }

        function countdown(seconds, isAborted) {
            return new Promise(resolve => {
                let remaining = seconds;
                function tick() {
                    if (isAborted && isAborted()) { resolve(); return; }
                    if (remaining <= 0) {
                        document.getElementById('currentContactInfo').textContent = currentBaseText;
                        resolve();
                        return;
                    }
                    document.getElementById('currentContactInfo').textContent =
                        currentBaseText + ' | nächste in ' + remaining + ' s';
                    remaining--;
                    setTimeout(tick, 1000);
                }
                tick();
            });
        }
    </script>
</body>
</html>