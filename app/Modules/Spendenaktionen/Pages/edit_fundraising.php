<?php
/** Interne Moduldatei: nur über den bisherigen Einstiegspunkt aufrufen. */
if (!defined('APP_ENTRY_POINT')) {
    http_response_code(404);
    exit;
}

$appRoot = dirname(__DIR__, 4);
// edit_fundraising.php - Spendenaktion verwalten
require_once $appRoot . '/auth.php';
checkPermission('edit_fundraising');

require_once $appRoot . '/db_connection.php';

// Aktive Spendenaktion aus der Datenbank laden. Die Migration
// database/migrations/2026-10-16_04_fundraising_campaigns.sql muss zuvor ausgeführt sein.
$currentFundraising = null;
$campaignHistory = [];

// Kontakte zählen (nur zur Anzeige)
$contactCount = 0;
$contactStatusCounts = [
    ''                       => 0,
    'Antwort abwarten'       => 0,
    'Spende zugesagt'        => 0,
    'Handlungsbedarf'        => 0,
    'Spende abgesagt'        => 0,
];

// Mailkontakte, die noch nicht angeschrieben wurden
$pendingMailContacts = 0;

try {
    $database = new Database();
    $conn = $database->getConnection();
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $activeCampaignStmt = $conn->query("SELECT id, name, started_at, ended_at FROM fundraising_campaigns WHERE ended_at IS NULL ORDER BY started_at DESC, id DESC LIMIT 1");
    $currentFundraising = $activeCampaignStmt->fetch(PDO::FETCH_ASSOC) ?: null;
    $campaignHistory = $conn->query("SELECT id, name, started_at, ended_at FROM fundraising_campaigns ORDER BY started_at DESC, id DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);

    $contactCount = (int)$conn->query("SELECT COUNT(*) FROM contacts")->fetchColumn();

    $stmt = $conn->query("SELECT status, COUNT(*) AS cnt FROM contacts GROUP BY status");
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $key = $row['status'] ?? '';
        if (!isset($contactStatusCounts[$key])) {
            $contactStatusCounts[$key] = 0;
        }
        $contactStatusCounts[$key] = (int)$row['cnt'];
    }

    // Mailkontakte, deren Status leer ist (= noch nicht angeschrieben)
    $stmt = $conn->prepare("
        SELECT COUNT(*) FROM contacts
        WHERE typ = 'Mail'
          AND (status IS NULL OR status = '')
    ");
    $stmt->execute();
    $pendingMailContacts = (int)$stmt->fetchColumn();

    // E-Mail-Vorlage laden (Standard)
    $stmt = $conn->prepare("SELECT betreff, mailtext FROM email_templates WHERE is_default = TRUE LIMIT 1");
    $stmt->execute();
    $template = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    $template = false;
}

$betreff = $template['betreff'] ?? 'Spendenanfrage für den Förderverein';
$text    = $template['mailtext'] ?? "Sehr geehrte Damen und Herren,\n\n" .
    "wir wenden uns heute mit einer Bitte an Sie: Unterstützen Sie unsere aktuelle Spendenaktion " .
    "für den Förderverein der Ev. KiTa Garumeln.\n\n" .
    "Jede Spende – ob groß oder klein – hilft uns, die Einrichtung für die Kinder noch schöner zu gestalten.\n\n" .
    "Mit freundlichen Grüßen\nIhr Förderverein";

// PDFs im Verzeichnis finden
$pdfFiles = [];
if ($handle = opendir($appRoot)) {
    while (false !== ($entry = readdir($handle))) {
        if (strtolower(pathinfo($entry, PATHINFO_EXTENSION)) === 'pdf') {
            $pdfFiles[] = $entry;
        }
    }
    closedir($handle);
}
sort($pdfFiles);

// Bulk-Versand-Ergebnis auslesen
$bulkSuccess = isset($_GET['bulk_success']) ? (int)$_GET['bulk_success'] : null;
$bulkErrors  = isset($_GET['bulk_errors'])  ? (int)$_GET['bulk_errors']  : 0;
$bulkSkipped = isset($_GET['bulk_skipped']) ? (int)$_GET['bulk_skipped'] : 0;
$bulkAborted = isset($_GET['bulk_aborted']) && $_GET['bulk_aborted'] === '1';

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spendenaktion verwalten</title>
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

        /* Info-Box */
        .info-box {
            background-color: #e8f4fd;
            border-left: 4px solid var(--primary-color);
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 25px;
        }
        .info-box h3 {
            margin-top: 0;
            color: var(--primary-color);
        }

        /* Aktions-Karte */
        .fundraising-card {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
        }
        .fundraising-card h2 {
            margin-top: 0;
            color: var(--primary-color);
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 10px;
        }
        .meta-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin: 15px 0;
        }
        .meta-item {
            background-color: var(--light-gray);
            border-radius: 4px;
            padding: 10px 15px;
        }
        .meta-item .label {
            font-size: 0.85em;
            color: #666;
        }
        .meta-item .value {
            font-weight: 600;
            font-size: 1.05em;
            margin-top: 2px;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 0.85em;
            font-weight: 600;
        }
        .status-badge.active {
            background-color: rgba(46, 204, 113, 0.15);
            color: #1e7e34;
        }
        .status-badge.finished {
            background-color: rgba(149, 165, 166, 0.2);
            color: #555;
        }

        /* Warn-Box */
        .warning-box {
            background-color: #fff3cd;
            border-left: 4px solid var(--warning-color);
            padding: 15px;
            border-radius: 4px;
            margin: 15px 0;
            color: #856404;
        }
        .warning-box strong {
            color: #856404;
        }

        /* Danger-Zone (rot) */
        .danger-zone {
            border: 1px solid var(--danger-color);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
            background-color: rgba(231, 76, 60, 0.04);
        }
        .danger-zone h2 {
            margin-top: 0;
            color: var(--danger-color);
            border-bottom: 1px solid rgba(231, 76, 60, 0.3);
            padding-bottom: 10px;
        }

        /* Warning-Zone (orange) – für "Alle Mailkontakte anschreiben" */
        .warning-zone {
            border: 1px solid var(--warning-color);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
            background-color: rgba(243, 156, 18, 0.05);
        }
        .warning-zone h2 {
            margin-top: 0;
            color: var(--warning-color);
            border-bottom: 1px solid rgba(243, 156, 18, 0.3);
            padding-bottom: 10px;
        }

        .btn-danger {
            background-color: var(--danger-color);
            color: white;
            padding: 10px 20px;
            width: auto;
        }
        .btn-danger:hover {
            opacity: 0.9;
        }
        .btn-warning {
            background-color: var(--warning-color);
            color: white;
            padding: 10px 20px;
            width: auto;
        }

        /* PDF-Liste */
        .pdf-list {
            border: 1px solid var(--border-color);
            border-radius: 4px;
            padding: 10px;
            max-height: 320px;
            overflow-y: auto;
        }
        .pdf-item {
            padding: 8px 10px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .pdf-item:last-child {
            border-bottom: none;
        }
        .pdf-item .name {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .pdf-item .name i {
            color: var(--danger-color);
        }
        .pdf-item .size {
            font-size: 0.85em;
            color: #666;
        }

        /* Buttons-Leiste */
        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }
        .actions .btn {
            width: auto;
            padding: 10px 20px;
        }

        /* Textarea */
        textarea.form-control {
            min-height: 200px;
            resize: vertical;
            font-family: inherit;
        }

        .section-description {
            color: #666;
            font-size: 0.9em;
            margin-bottom: 15px;
        }

        /* Checkliste vor dem Versand */
        .preflight-checklist {
            background-color: #fff;
            border: 1px solid var(--warning-color);
            border-radius: 4px;
            padding: 15px;
            margin: 15px 0;
        }
        .preflight-checklist h4 {
            margin: 0 0 10px 0;
            color: var(--warning-color);
        }
        .preflight-checklist ul {
            margin: 0;
            padding-left: 20px;
        }
        .preflight-checklist li {
            margin-bottom: 6px;
        }
    </style>
    <link rel="stylesheet" href="includes/navigation.css">

    <link rel="icon" type="image/png" sizes="32x32" href="/intern/assets/icons/favicon.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/intern/assets/icons/apple-touch-icon.png">
    <link rel="manifest" href="/intern/manifest.webmanifest">
    <meta name="apple-mobile-web-app-title" content="Förderverein intern">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="theme-color" content="#FFF500">
</head>
<body>
    <div class="container">
        <?php if ($bulkSuccess !== null): ?>
            <div class="notification <?php echo ($bulkErrors === 0 && !$bulkAborted) ? 'success' : 'error'; ?>"
                 style="display: block;">
                <i class="fas fa-<?php echo ($bulkErrors === 0 && !$bulkAborted) ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                <strong>Bulk-Versand <?php echo $bulkAborted ? 'vorzeitig gestoppt' : 'abgeschlossen'; ?></strong><br>
                Erfolgreich versendet: <strong><?php echo $bulkSuccess; ?></strong><br>
                <?php if ($bulkErrors > 0): ?>
                    Fehler: <strong><?php echo $bulkErrors; ?></strong><br>
                <?php endif; ?>
                <?php if ($bulkSkipped > 0): ?>
                    Übersprungen: <strong><?php echo $bulkSkipped; ?></strong><br>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <!-- Header -->
        <header class="app-page-header">
            <h1><i class="fas fa-hand-holding-heart"></i> Spendenaktion verwalten</h1>
            <?php require $appRoot . '/includes/navigation.php'; ?>
        </header>

        <!-- Aktuelle Aktion und Start/Ende -->
        <div class="fundraising-card">
            <h2><i class="fas fa-bullhorn"></i> Spendenaktion</h2>
            <?php if ($currentFundraising): ?>
                <div class="meta-grid">
                    <div class="meta-item"><div class="label">Aktuelle Aktion</div><div class="value"><?php echo htmlspecialchars($currentFundraising['name']); ?></div></div>
                    <div class="meta-item"><div class="label">Gestartet am</div><div class="value"><?php echo htmlspecialchars(date('d.m.Y', strtotime($currentFundraising['started_at']))); ?></div></div>
                    <div class="meta-item"><div class="label">Status</div><div class="value"><span class="status-badge active">Aktiv</span></div></div>
                </div>
                <div class="info-box" style="margin-top:20px;">Die Spendenaktion läuft. Beim Beenden wird das Enddatum gespeichert. Die Kontaktstatus bleiben zunächst unverändert und sind nach dem Beenden weiterhin in der Sponsorenübersicht sichtbar.</div>
                <div class="actions" style="margin-top:16px"><button type="button" class="btn btn-warning" id="endFundraisingBtn"><i class="fas fa-flag-checkered"></i> &nbsp;Spendenaktion beenden</button></div>
            <?php else: ?>
                <div class="info-box" style="margin-top:0">Aktuell läuft keine Spendenaktion. Starte eine neue Aktion, sobald die Kontaktaufnahme beginnen soll. Dabei werden die Kontaktstatus für die neue Runde zurückgesetzt.</div>
                <div class="form-group" style="margin-top:18px;max-width:520px">
                    <label for="fundraisingName">Name der Spendenaktion (optional)</label>
                    <input type="text" id="fundraisingName" class="form-control" maxlength="150" placeholder="Standard: Spendenaktion <?php echo date('Y'); ?>">
                    <small>Wenn du keinen Namen angibst, wird automatisch „Spendenaktion <?php echo date('Y'); ?>“ verwendet.</small>
                </div>
                <div class="actions" style="margin-top:14px"><button type="button" class="btn btn-primary" id="startFundraisingBtn"><i class="fas fa-play"></i> &nbsp;Neue Spendenaktion starten</button></div>
            <?php endif; ?>
        </div>

        <?php if (!empty($campaignHistory)): ?>
        <div class="fundraising-card">
            <h2><i class="fas fa-clock-rotate-left"></i> Bisherige Spendenaktionen</h2>
            <div style="overflow-x:auto"><table style="width:100%;border-collapse:collapse"><thead><tr><th style="text-align:left;padding:9px;border-bottom:1px solid #e4e9ed">Name</th><th style="text-align:left;padding:9px;border-bottom:1px solid #e4e9ed">Beginn</th><th style="text-align:left;padding:9px;border-bottom:1px solid #e4e9ed">Ende</th><th style="text-align:left;padding:9px;border-bottom:1px solid #e4e9ed">Status</th></tr></thead><tbody>
            <?php foreach ($campaignHistory as $campaign): ?>
                <tr><td style="padding:9px;border-bottom:1px solid #edf0f2"><?php echo htmlspecialchars($campaign['name']); ?></td><td style="padding:9px;border-bottom:1px solid #edf0f2"><?php echo htmlspecialchars(date('d.m.Y', strtotime($campaign['started_at']))); ?></td><td style="padding:9px;border-bottom:1px solid #edf0f2"><?php echo $campaign['ended_at'] ? htmlspecialchars(date('d.m.Y', strtotime($campaign['ended_at']))) : '—'; ?></td><td style="padding:9px;border-bottom:1px solid #edf0f2"><?php echo $campaign['ended_at'] ? 'Beendet' : 'Aktiv'; ?></td></tr>
            <?php endforeach; ?>
            </tbody></table></div>
        </div>
        <?php endif; ?>

        <!-- E-Mail-Vorlage -->
        <div class="fundraising-card">
            <h2><i class="fas fa-envelope-open-text"></i> E-Mail-Vorlage</h2>
            <p class="section-description">
                Diese Vorlage wird beim Erstellen neuer Anschreiben (contact.php) als Standard verwendet.
            </p>

            <form>
                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="betreff">Betreff</label>
                    <input type="text" id="betreff" class="form-control"
                           value="<?php echo htmlspecialchars($betreff); ?>">
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="mailtext">E-Mail-Text</label>
                    <textarea id="mailtext" class="form-control"><?php echo htmlspecialchars($text); ?></textarea>                    
                </div>

                <div class="actions">
                    <button type="submit" class="btn btn-primary" disabled>
                        <i class="fas fa-save"></i> &nbsp;Vorlage speichern
                    </button>
                </div>
            </form>
        </div>

        <!-- PDF-Anhänge -->
        <div class="fundraising-card">
            <h2><i class="fas fa-paperclip"></i> Verf&uuml;gbare PDF-Anh&auml;nge</h2>
            <p class="section-description">
                Diese PDF-Dateien liegen im Verzeichnis und k&ouml;nnen beim Mailversand ausgew&auml;hlt werden.
                Das Hochladen oder L&ouml;schen von Dateien muss aktuell noch &uuml;ber den FTP Zugang vorgenommen werden.
            </p>

            <?php if (!empty($pdfFiles)): ?>
                <div class="pdf-list">
                    <?php foreach ($pdfFiles as $pdfFile): ?>
                        <div class="pdf-item">
                            <div class="name">
                                <i class="fas fa-file-pdf"></i>
                                <?php echo htmlspecialchars($pdfFile); ?>
                            </div>
                            <div class="size">
                                <?php echo round(filesize($pdfFile) / 1024, 2); ?> KB
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="info-text">Keine PDF-Dateien im Verzeichnis gefunden.</div>
            <?php endif; ?>
        </div>
        
        <!-- Warning Zone: Alle Mailkontakte anschreiben -->
        <div class="warning-zone">
            <h2><i class="fas fa-paper-plane"></i> Alle Mailkontakte anschreiben</h2>
            <p class="section-description">
                Mit dieser Aktion werden <strong>alle Mailkontakte, die noch nicht angeschrieben wurden</strong>, 
                automatisch per E-Mail kontaktiert. Der Status dieser Kontakte wird anschlie&szlig;end auf 
                &bdquo;Antwort abwarten&ldquo; gesetzt und das Datum &bdquo;zuletzt angeschrieben&ldquo; aktualisiert.
            </p>

            <div class="warning-box">
                Diese Aktion versendet E-Mails an <strong><?php echo (int)$pendingMailContacts; ?> noch nicht angeschriebene 
                Mailkontakte</strong>. Es werden die oben angezeigte e-Mail-Vorlage und Anh&auml;nge verwendet. Sollen die 
                Mails jetzt versendet werden?
            </div>

            <div class="preflight-checklist">
                <h4><i class="fas fa-clipboard-check"></i> Checkliste vor dem Versand</h4>
                <ul>
                    <li>E-Mail-Vorlage (Betreff &amp; Text) ist aktuell und korrekt.</li>
                    <li>Die gew&uuml;nschten PDF-Anh&auml;nge sind ausgew&auml;hlt und liegen im Verzeichnis.</li>
                    <li>Die Anzahl der betroffenen Kontakte (<?php echo (int)$pendingMailContacts; ?>) erscheint plausibel.</li>
                    <li>Kein anderes Skript oder Prozess versendet parallel E-Mails.</li>
                </ul>
            </div>

            <div class="actions">
                <button type="button" class="btn btn-primary" id="prepareBulkMailBtn">
                    <i class="fas fa-paper-plane"></i> &nbsp;Bulk Mailversand vorbereiten
                </button>
            </div>
        </div>

    </div>

<script>
    // ----- Neue Spendenaktion starten: bisheriges Zurücksetzen inklusive -----
    const startBtn = document.getElementById('startFundraisingBtn');
    if (startBtn) {
        startBtn.addEventListener('click', async function (e) {
            e.preventDefault();
            const name = (document.getElementById('fundraisingName')?.value || '').trim();
            const confirmText = 'Eine neue Spendenaktion starten?\n\nDabei werden die Kontaktstatus und die zuletzt angeschrieben-Daten für eine neue Runde zurückgesetzt.';
            if (!confirm(confirmText)) return;
            startBtn.disabled = true;
            const originalHtml = startBtn.innerHTML;
            startBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Wird gestartet…';
            try {
                const response = await fetch('reset_fundraising.php', {
                    method: 'POST', headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'start', name })
                });
                const result = await response.json();
                if (result.success) { alert(result.message); window.location.reload(); }
                else { alert('Fehler: ' + (result.error || 'Unbekannter Fehler')); startBtn.disabled = false; startBtn.innerHTML = originalHtml; }
            } catch (err) { alert('Netzwerkfehler: ' + err.message); startBtn.disabled = false; startBtn.innerHTML = originalHtml; }
        });
    }

    // ----- Aktive Spendenaktion beenden -----
    const endBtn = document.getElementById('endFundraisingBtn');
    if (endBtn) {
        endBtn.addEventListener('click', async function (e) {
            e.preventDefault();
            if (!confirm('Spendenaktion jetzt beenden? Das Enddatum wird gespeichert. Die Kontaktstatus werden nicht verändert.')) return;
            endBtn.disabled = true;
            const originalHtml = endBtn.innerHTML;
            endBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Wird beendet…';
            try {
                const response = await fetch('end_fundraising.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ action: 'end' }) });
                const result = await response.json();
                if (result.success) { alert(result.message); window.location.reload(); }
                else { alert('Fehler: ' + (result.error || 'Unbekannter Fehler')); endBtn.disabled = false; endBtn.innerHTML = originalHtml; }
            } catch (err) { alert('Netzwerkfehler: ' + err.message); endBtn.disabled = false; endBtn.innerHTML = originalHtml; }
        });
    }

    // ----- Bulk-Mailversand vorbereiten -----
    const prepareBtn = document.getElementById('prepareBulkMailBtn');
    if (prepareBtn) {
        prepareBtn.addEventListener('click', function (e) {
            e.preventDefault();
            window.location.href = 'bulk_mail_preview.php';
        });
    }
    
</script>
</body>
</html>