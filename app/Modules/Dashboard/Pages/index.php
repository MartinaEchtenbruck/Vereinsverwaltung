<?php
/**
 * Zentrale Startseite für alle angemeldeten Benutzer.
 * Aufruf ausschließlich über den Einstiegspunkt /dashboard.php.
 */
if (!defined('APP_ENTRY_POINT')) {
    http_response_code(404);
    exit;
}

$appRoot = dirname(__DIR__, 4);
require_once $appRoot . '/auth.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$showProjectTasks = hasPermission('ProjectManagement');
$showFundraisingWidget = hasPermission('view_contacts');
$activeFundraising = null;
$fundraisingStats = [
    'total' => 0, 'contacted' => 0, 'open_total' => 0, 'action_needed_status' => 0,
    'open' => ['Persönlich' => 0, 'Formular' => 0, 'Mail' => 0],
    'results' => ['Spende zugesagt' => 0, 'Spende abgesagt' => 0, 'Antwort abwarten' => 0],
];
$fundraisingLoadError = false;
$dashboardTasks = [];
$dashboardGroups = ['overdue'=>[], 'this_week'=>[], 'next_week'=>[], 'demnaechst'=>[]];
if ($showFundraisingWidget) {
    try {
        require_once $appRoot . '/db_connection.php';
        $fundraisingDatabase = new Database();
        $fundraisingDb = $fundraisingDatabase->getConnection();
        $fundraisingDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Die Kennzahlen beziehen sich nur auf eine explizit gestartete, aktive Spendenaktion.
        $activeStmt = $fundraisingDb->query("SELECT id, name, started_at FROM fundraising_campaigns WHERE ended_at IS NULL ORDER BY started_at DESC, id DESC LIMIT 1");
        $activeFundraising = $activeStmt->fetch(PDO::FETCH_ASSOC) ?: null;

        if ($activeFundraising) {
            // Vorschläge sind keine bestätigten Adressen der laufenden Spendenaktion.
            $fundraisingRows = $fundraisingDb->query("SELECT typ,
                    COUNT(*) AS amount,
                    SUM(CASE WHEN status IN ('Spende zugesagt', 'Spende abgesagt', 'Antwort abwarten', 'Handlungsbedarf') THEN 1 ELSE 0 END) AS contacted,
                    SUM(CASE WHEN status = 'Spende zugesagt' THEN 1 ELSE 0 END) AS promised,
                    SUM(CASE WHEN status = 'Spende abgesagt' THEN 1 ELSE 0 END) AS declined,
                    SUM(CASE WHEN status = 'Antwort abwarten' THEN 1 ELSE 0 END) AS waiting,
                    SUM(CASE WHEN COALESCE(status, '') IN ('', 'Anfrage ausstehend') THEN 1 ELSE 0 END) AS open_count,
                    SUM(CASE WHEN status = 'Handlungsbedarf' THEN 1 ELSE 0 END) AS action_needed_status
                FROM contacts
                WHERE COALESCE(is_suggestion, 0) = 0
                GROUP BY typ")->fetchAll(PDO::FETCH_ASSOC);

            foreach ($fundraisingRows as $row) {
                $type = $row['typ'] ?: 'Mail';
                if (!array_key_exists($type, $fundraisingStats['open'])) $type = 'Mail';
                $fundraisingStats['total'] += (int)$row['amount'];
                $fundraisingStats['contacted'] += (int)$row['contacted'];
                $fundraisingStats['open'][$type] += (int)$row['open_count'];
                $fundraisingStats['open_total'] += (int)$row['open_count'];
                $fundraisingStats['action_needed_status'] += (int)$row['action_needed_status'];
                $fundraisingStats['results']['Spende zugesagt'] += (int)$row['promised'];
                $fundraisingStats['results']['Spende abgesagt'] += (int)$row['declined'];
                $fundraisingStats['results']['Antwort abwarten'] += (int)$row['waiting'];
            }
        }
    } catch (Throwable $e) {
        error_log('Dashboard-Spendenwidget: ' . $e->getMessage());
        $fundraisingLoadError = true;
    }
}
if ($showProjectTasks) {
    try {
        require_once $appRoot . '/db_connection.php';
        $database = new Database();
        $dashboardDb = $database->getConnection();
        $dashboardDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // Für den schnellen Überblick werden nur Titel und Fälligkeit benötigt.
        // Aufgaben ohne Fälligkeitsdatum sowie Aufgaben ab nächster Woche bleiben außen vor.
        $query = $dashboardDb->query("SELECT t.id, t.title, t.start_date, t.due_date
            FROM tasks t
            LEFT JOIN projects p ON p.id=t.project_id
            WHERE t.status<>'done' AND (t.project_id IS NULL OR p.status='active')
                AND t.due_date IS NOT NULL
            ORDER BY t.due_date, t.id");
        $dashboardTasks = $query->fetchAll(PDO::FETCH_ASSOC);

        // Dieselbe Gruppierungslogik wie in der Projektmanagement-Aufgabenübersicht:
        // Überfälligkeit richtet sich nach dem Fälligkeitsdatum und hat Vorrang.
        // Bei noch nicht überfälligen Aufgaben entscheidet der Arbeitsbeginn,
        // damit länger laufende Aufgaben bereits in der Woche ihres Arbeitsbeginns erscheinen.
        $today = new DateTimeImmutable('today');
        $monday = new DateTimeImmutable('monday this week');
        $nextMonday = $monday->modify('+1 week');
        $afterNextMonday = $monday->modify('+2 weeks');
        $fourWeeks = $monday->modify('+4 weeks');
        foreach ($dashboardTasks as $task) {
            if (empty($task['due_date'])) continue;
            $due = new DateTimeImmutable($task['due_date']);

            if ($due < $today) {
                $dashboardGroups['overdue'][] = $task;
                continue;
            }

            $start = !empty($task['start_date'])
                ? new DateTimeImmutable($task['start_date'])
                : $due;
            if ($start < $today) $start = $today;

            if ($start < $nextMonday) {
                $dashboardGroups['this_week'][] = $task;
            }
        }
    } catch (Throwable $e) {
        error_log('Dashboard-Aufgabenwidget: ' . $e->getMessage());
        // Die übrigen Dashboard-Bereiche bleiben auch bei einem Fehler im Aufgabenwidget sichtbar.
        $dashboardTasks = [];
        $dashboardGroups = ['overdue'=>[], 'this_week'=>[], 'next_week'=>[], 'demnaechst'=>[]];
    }
}
function dashboardEscape($value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
$fundraisingProgress = $fundraisingStats['total'] > 0
    ? min(100, (int)round($fundraisingStats['contacted'] / $fundraisingStats['total'] * 100))
    : 0;
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Spendenverwaltung</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="includes/navigation.css">
    <?php if ($showProjectTasks): ?><link rel="stylesheet" href="project-management.css"><?php endif; ?>
    <style>
        .dashboard-content { min-height: 120px; }
        .dashboard-project-tasks { margin-top: 14px; }
        .dashboard-project-tasks.pm-section { padding: 14px 16px; margin-bottom: 14px; }
        .dashboard-project-tasks .pm-section-heading { align-items:center; margin-bottom:10px; }
        .dashboard-project-tasks .pm-section-heading h2 { margin:0; font-size:1.12rem; }
        .dashboard-project-tasks .pm-week-grid { grid-template-columns:repeat(2,minmax(0,1fr)); gap:10px; }
        .dashboard-project-tasks .pm-week-column { border-top-width:3px; }
        .dashboard-project-tasks .pm-week-column h3 { padding:7px 10px; font-size:.82rem; }
        .dashboard-project-tasks .pm-week-column h3 span { min-width:20px; text-align:center; padding:0 6px; font-size:.76rem; }
        .dashboard-project-tasks .pm-task-row { padding:0; border-top:1px solid #edf0f2; }
        .dashboard-project-tasks .pm-task-tile { margin:0; padding:7px 10px; border:0; border-radius:0; background:transparent; box-shadow:none; cursor:pointer; }
        .dashboard-project-tasks .pm-task-tile:hover { background:#f5f8f9; box-shadow:none; }
        .dashboard-project-tasks .pm-task-title { display:block; overflow-wrap:anywhere; font-size:.86rem; line-height:1.3; font-weight:600; }
        .dashboard-project-tasks .pm-empty { margin:0; padding:8px 10px; font-size:.78rem; }
        .fundraising-widget { display:block; margin:14px 0 18px; padding:15px 16px; color:inherit; background:#fff; border:1px solid #e4e9ed; border-radius:12px; box-shadow:0 2px 9px rgba(28,45,60,.045); }
        .fundraising-widget-head { display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:12px; }
        .fundraising-widget-title { margin:0; color:#233746; font-size:1.12rem; line-height:1.25; font-weight:700; }
        .fundraising-widget-subtitle { margin:3px 0 0; color:#6b7882; font-size:.78rem; }
        .fundraising-widget-section-title { margin:0 0 7px; color:#64727b; font-size:.68rem; font-weight:700; letter-spacing:.05em; text-transform:uppercase; }
        .fundraising-open-grid { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:7px; }
        .fundraising-open-card { display:flex; align-items:center; gap:9px; min-width:0; padding:8px 10px; border:1px solid #e7ecef; border-radius:8px; background:#f8fafb; color:inherit; text-decoration:none; transition:background .15s,border-color .15s; }
        .fundraising-open-card:hover,.fundraising-open-card:focus-visible { background:#eef4f7; border-color:#b8cbd5; outline:2px solid transparent; }
        .fundraising-open-card .number { flex:0 0 auto; color:#263e4d; font-size:1.35rem; line-height:1; font-weight:750; }
        .fundraising-open-card .label { min-width:0; color:#4c5d68; font-size:.73rem; line-height:1.2; }
        .fundraising-open-card .label i,.fundraising-result-link .label i { font-size:.62rem; opacity:.65; }
        .fundraising-bottom { display:grid; grid-template-columns:minmax(180px,.9fr) minmax(0,1.5fr); gap:16px; align-items:center; margin-top:12px; }
        .fundraising-progress { min-width:0; }
        .fundraising-progress-label { display:flex; justify-content:space-between; gap:8px; margin-bottom:5px; color:#5c6b75; font-size:.72rem; }
        .fundraising-progress-track { height:6px; overflow:hidden; border-radius:99px; background:#edf0f2; }
        .fundraising-progress-fill { height:100%; border-radius:99px; background:#5b8b75; }
        .fundraising-results { display:grid; grid-template-columns:auto repeat(3,minmax(0,1fr)); gap:6px; align-items:stretch; }
        .fundraising-result { min-width:0; padding:6px 8px; border-radius:7px; background:#f7f9fa; }
        .fundraising-result .number { display:block; font-size:1.15rem; line-height:1.1; font-weight:750; }
        .fundraising-result .label { display:block; margin-top:2px; color:#64727b; font-size:.68rem; line-height:1.2; }
        .fundraising-result.positive { background:#eef7f0; border:1px solid #c8e1cf; } .fundraising-result.positive .number { color:#287447; }
        .fundraising-result.negative { background:#fbf0ef; border:1px solid #ecd0cd; } .fundraising-result.negative .number { color:#a94c45; }
        .fundraising-result.waiting { background:#f1f4f6; border:1px solid #d4dfe4; } .fundraising-result.waiting .number { color:#5c6c77; }
        .fundraising-result.total { padding:6px 9px; border:1px solid #e7ecef; background:#f8fafb; }
        .fundraising-result.total .number { color:#263e4d; font-size:1.2rem; }
        .fundraising-result.total .label { color:#4c5d68; font-weight:700; }
        .fundraising-result-link { display:block; color:inherit; text-decoration:none; transition:background .15s,border-color .15s; }
        .fundraising-result-link:hover,.fundraising-result-link:focus-visible { background:#fff; border-color:#496779; outline:2px solid #496779; outline-offset:1px; }
        .fundraising-widget-error { margin:0; color:#8d4c35; font-size:.82rem; }
        @media (max-width:760px) {
            .dashboard-project-tasks .pm-week-grid { grid-template-columns:repeat(2,minmax(0,1fr)); }
            .fundraising-open-grid { grid-template-columns:repeat(2,minmax(0,1fr)); }
            .fundraising-bottom { grid-template-columns:minmax(0,1fr); gap:10px; }
        }
        @media (max-width:460px) {
            .dashboard-project-tasks .pm-week-grid { grid-template-columns:minmax(0,1fr); }
            .fundraising-results { grid-template-columns:repeat(2,minmax(0,1fr)); }
        }

    </style>

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
        <header class="app-page-header">
            <h1>Dashboard</h1>
            <?php require $appRoot . '/includes/navigation.php'; ?>
        </header>
        <main class="dashboard-content <?= $showProjectTasks ? 'pm-main' : '' ?>" aria-label="Dashboard-Inhalt">
            <?php if ($showProjectTasks): ?>
                <?php if (isset($_GET['saved'])): ?><div class="pm-alert pm-success">Aufgabenstatus wurde aktualisiert.</div><?php endif; ?>
                <section class="pm-section dashboard-project-tasks">
                    <div class="pm-section-heading"><div><h2>Dringende Aufgaben</h2></div><a class="pm-button small secondary" href="projects.php">Alle Aufgaben</a></div>
                    <div class="pm-week-grid">
                        <?php $dashboardLabels = ['overdue'=>'Überfällig','this_week'=>'Diese Woche']; foreach ($dashboardLabels as $groupKey => $groupLabel): ?>
                            <section class="pm-week-column pm-<?= dashboardEscape($groupKey) ?>">
                                <h3><?= dashboardEscape($groupLabel) ?><span><?= count($dashboardGroups[$groupKey]) ?></span></h3>
                                <?php if (!$dashboardGroups[$groupKey]): ?><p class="pm-empty">Keine Aufgaben</p>
                                <?php else: foreach ($dashboardGroups[$groupKey] as $task): $taskUrl = 'projects.php?view=task&amp;id=' . (int)$task['id']; ?>
                                    <article class="pm-task-row pm-task-tile" data-task-url="<?= $taskUrl ?>" tabindex="0" role="link" aria-label="Aufgabe öffnen: <?= dashboardEscape($task['title']) ?>">
                                        <a class="pm-task-title" href="<?= $taskUrl ?>"><?= dashboardEscape($task['title']) ?></a>
                                    </article>
                                <?php endforeach; endif; ?>
                            </section>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>
            <?php if ($showFundraisingWidget): ?>
                <section class="fundraising-widget" aria-label="Übersicht der aktuellen Spendenaktion">
                    <div class="fundraising-widget-head">
                        <div>
                            <h2 class="fundraising-widget-title">Aktuelle Spendenaktion</h2>
                            <?php if ($activeFundraising): ?>
                                <p class="fundraising-widget-subtitle"><?= dashboardEscape($activeFundraising['name']) ?> · gestartet am <?= dashboardEscape(date('d.m.Y', strtotime($activeFundraising['started_at']))) ?></p>
                            <?php else: ?>
                                <p class="fundraising-widget-subtitle">Aktuell läuft keine Spendenaktion.</p>
                            <?php endif; ?>
                        </div>

                    </div>
                    <?php if ($fundraisingLoadError): ?>
                        <p class="fundraising-widget-error">Die Kennzahlen konnten gerade nicht geladen werden.</p>
                    <?php elseif (!$activeFundraising): ?>
                        <p class="fundraising-widget-error" style="color:#65747d">Eine neue Spendenaktion kann im Bereich „Spendenaktion verwalten“ gestartet werden.</p>
                    <?php else: ?>
                        <h3 class="fundraising-widget-section-title">Noch zu tun</h3>
                        <div class="fundraising-open-grid">
                            <a class="fundraising-open-card" href="index.php?contact_type=Pers%C3%B6nlich&amp;contact_status=pending&amp;exclude_suggestions=1" aria-label="Lokale Kontakte mit ausstehender Anfrage anzeigen"><span class="number"><?= $fundraisingStats['open']['Persönlich'] ?></span><span class="label">Lokal ansprechen <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></span></a>
                            <a class="fundraising-open-card" href="index.php?contact_type=Formular&amp;contact_status=pending&amp;exclude_suggestions=1" aria-label="Formularkontakte mit ausstehender Anfrage anzeigen"><span class="number"><?= $fundraisingStats['open']['Formular'] ?></span><span class="label">Formulare ausfüllen <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></span></a>
                            <a class="fundraising-open-card" href="index.php?contact_type=Mail&amp;contact_status=pending&amp;exclude_suggestions=1" aria-label="Mail-Kontakte mit ausstehender Anfrage anzeigen"><span class="number"><?= $fundraisingStats['open']['Mail'] ?></span><span class="label">E-Mails schreiben <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></span></a>
                            <a class="fundraising-open-card" href="index.php?contact_status=action_needed&amp;exclude_suggestions=1" aria-label="Alle Kontakte mit Handlungsbedarf anzeigen"><span class="number"><?= $fundraisingStats['action_needed_status'] ?></span><span class="label">Handlungsbedarf <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></span></a>
                        </div>
                        <div class="fundraising-bottom">
                            <div class="fundraising-progress">
                                <div class="fundraising-progress-label"><span>Kontaktaufnahme</span><strong><?= $fundraisingStats['contacted'] ?>/<?= $fundraisingStats['total'] ?></strong></div>
                                <div class="fundraising-progress-track" role="progressbar" aria-label="Anteil kontaktierter Adressen" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?= $fundraisingProgress ?>"><div class="fundraising-progress-fill" style="width:<?= $fundraisingProgress ?>%"></div></div>
                            </div>
                            <div>
                                <h3 class="fundraising-widget-section-title">Bisherige Rückmeldungen</h3>
                                <div class="fundraising-results">
                                    <div class="fundraising-result total"><span class="number"><?= $fundraisingStats['results']['Spende zugesagt'] + $fundraisingStats['results']['Spende abgesagt'] + $fundraisingStats['results']['Antwort abwarten'] ?></span><span class="label">Angefragt</span></div>
                                    <a class="fundraising-result positive fundraising-result-link" href="index.php?contact_status=promised&amp;exclude_suggestions=1" aria-label="Alle Kontakte mit Spende zugesagt anzeigen"><span class="number"><?= $fundraisingStats['results']['Spende zugesagt'] ?></span><span class="label">Zugesagt <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></span></a>
                                    <a class="fundraising-result negative fundraising-result-link" href="index.php?contact_status=declined&amp;exclude_suggestions=1" aria-label="Alle Kontakte mit Spende abgesagt anzeigen"><span class="number"><?= $fundraisingStats['results']['Spende abgesagt'] ?></span><span class="label">Abgesagt <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></span></a>
                                    <a class="fundraising-result waiting fundraising-result-link" href="index.php?contact_status=waiting&amp;exclude_suggestions=1" aria-label="Alle Kontakte mit Antwort ausstehend anzeigen"><span class="number"><?= $fundraisingStats['results']['Antwort abwarten'] ?></span><span class="label">Antwort ausstehend <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></span></a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </section>
            <?php endif; ?>
        </main>
        <?php if ($showProjectTasks): ?>
        <script>
        document.querySelectorAll('.pm-task-tile[data-task-url]').forEach(card => {
            const go = () => window.location.href = card.dataset.taskUrl.replace('&amp;', '&');
            card.addEventListener('click', event => { if (!event.target.closest('a,button,form,input,textarea,select,label')) go(); });
            card.addEventListener('keydown', event => { if ((event.key === 'Enter' || event.key === ' ') && !event.target.closest('a,button,form,input,textarea,select,label')) { event.preventDefault(); go(); } });
        });
        </script>
        <?php endif; ?>
    </div>
</body>
</html>
