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
$dashboardCsrf = '';
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
    require_once $appRoot . '/db_connection.php';
    $database = new Database();
    $dashboardDb = $database->getConnection();
    $dashboardDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if (empty($_SESSION['pm_csrf'])) $_SESSION['pm_csrf'] = bin2hex(random_bytes(32));
    $dashboardCsrf = $_SESSION['pm_csrf'];
    $query = $dashboardDb->query("SELECT t.*, p.title AS project_title, p.color AS project_color, u.full_name AS assignee
        FROM tasks t JOIN projects p ON p.id=t.project_id
        LEFT JOIN users u ON u.id=t.assigned_user_id
        WHERE p.status='active' AND t.status<>'done'
        ORDER BY COALESCE(t.start_date, t.due_date), t.due_date, t.id");
    $dashboardTasks = $query->fetchAll(PDO::FETCH_ASSOC);
    $today = new DateTimeImmutable('today');
    $monday = new DateTimeImmutable('monday this week');
    $nextMonday = $monday->modify('+1 week');
    $afterNextMonday = $monday->modify('+2 weeks');
    $fourWeeks = $monday->modify('+4 weeks');
    foreach ($dashboardTasks as $task) {
        // Ohne Fälligkeit bleibt die Aufgabe außerhalb der Übersicht.
        if (!$task['due_date']) continue;
        $due = new DateTimeImmutable($task['due_date']);

        // Eine abgelaufene Frist hat immer Vorrang vor dem Startdatum.
        if ($due < $today) {
            $group = 'overdue';
        } else {
            // Früher sichtbar machen: primär nach Arbeitsbeginn gruppieren.
            // Bereits gestartete Aufgaben ohne überfällige Frist bleiben in "Diese Woche".
            $start = !empty($task['start_date'])
                ? new DateTimeImmutable($task['start_date'])
                : $due;
            if ($start < $today) $start = $today;

            if ($start < $nextMonday) $group = 'this_week';
            elseif ($start < $afterNextMonday) $group = 'next_week';
            elseif ($start < $fourWeeks) $group = 'demnaechst';
            else continue;
        }
        $dashboardGroups[$group][] = $task;
    }
}
function dashboardEscape($value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function dashboardColor($value): string {
    $value = strtoupper(trim((string)$value));
    return preg_match('/^#[0-9A-F]{6}$/', $value) ? $value : '#DCEAF0';
}
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
        .dashboard-content { min-height: 180px; }
        .dashboard-project-tasks { margin-top: 24px; }
        .fundraising-widget { display:block; margin:24px 0 28px; padding:22px 24px; color:inherit; background:#fff; border:1px solid #e4e9ed; border-radius:14px; box-shadow:0 3px 14px rgba(28,45,60,.06); }
        .fundraising-open-card { display:block; color:inherit; text-decoration:none; transition:background .18s ease, border-color .18s ease, box-shadow .18s ease; }
        .fundraising-open-card:hover, .fundraising-open-card:focus-visible { background:#eef4f7; border-color:#b8cbd5; box-shadow:0 3px 10px rgba(28,45,60,.08); outline:2px solid transparent; }
        .fundraising-open-card:focus-visible { outline:2px solid #496779; outline-offset:2px; }
        .fundraising-widget-head { display:flex; justify-content:space-between; align-items:flex-start; gap:16px; margin-bottom:20px; }
        .fundraising-widget-title { margin:0; color:#233746; font-size:1.5rem; line-height:1.2; font-weight:700; }
        .fundraising-widget-subtitle { margin:6px 0 0; color:#6b7882; font-size:.9rem; }
        .fundraising-widget-link { flex-shrink:0; color:#496779; font-size:.9rem; font-weight:600; }
        .fundraising-widget-section-title { margin:0 0 12px; color:#51616c; font-size:.8rem; font-weight:700; letter-spacing:.06em; text-transform:uppercase; }
        .fundraising-open-grid { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:12px; }
        .fundraising-open-card { min-width:0; padding:15px 16px; border:1px solid #e7ecef; border-radius:10px; background:#f8fafb; }
        .fundraising-open-card .number { display:block; margin-bottom:5px; color:#263e4d; font-size:1.9rem; line-height:1.15; font-weight:750; }
        .fundraising-open-card .label { color:#4c5d68; font-size:.9rem; }
        .fundraising-results { display:grid; grid-template-columns:minmax(0,1fr); gap:10px; margin-top:22px; }
        .fundraising-result { padding:11px 13px; border-radius:9px; background:#f7f9fa; }
        .fundraising-result .number { display:block; font-size:1.45rem; line-height:1.2; font-weight:750; }
        .fundraising-result .label { display:block; margin-top:4px; color:#64727b; font-size:.8rem; }
        .fundraising-result.positive { background:#eef7f0; border:1px solid #91c4a0; } .fundraising-result.positive .number { color:#287447; }
        .fundraising-result.negative { background:#fbf0ef; border:1px solid #d99a94; } .fundraising-result.negative .number { color:#a94c45; }
        .fundraising-result.waiting { background:#f1f4f6; border:1px solid #aabcc7; } .fundraising-result.waiting .number { color:#5c6c77; }
        .fundraising-result.total { display:grid; grid-template-columns:minmax(180px, .65fr) minmax(0, 2fr); align-items:stretch; gap:14px; padding:15px 16px; border:1px solid #e7ecef; border-radius:10px; background:#f8fafb; }
        .fundraising-total-summary { display:flex; flex-direction:column; justify-content:center; padding:0 12px 0 0; }
        .fundraising-result.total .number { color:#263e4d; font-size:1.9rem; }
        .fundraising-result.total .label { margin-top:0; color:#4c5d68; font-size:.9rem; font-weight:700; }
        .fundraising-result-breakdown { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:10px; min-width:0; }
        .fundraising-result-link { display:block; color:inherit; text-decoration:none; transition:background .18s ease, border-color .18s ease, box-shadow .18s ease; }
        .fundraising-result-link:hover, .fundraising-result-link:focus-visible { background:#fff; border-color:#496779; box-shadow:0 2px 8px rgba(28,45,60,.1); outline:2px solid #496779; outline-offset:2px; }
        .fundraising-progress { margin-top:18px; }
        .fundraising-progress-label { display:flex; justify-content:space-between; gap:12px; margin-bottom:8px; color:#5c6b75; font-size:.85rem; }
        .fundraising-progress-track { height:8px; overflow:hidden; border-radius:99px; background:#edf0f2; }
        .fundraising-progress-fill { height:100%; border-radius:99px; background:#5b8b75; }
        .fundraising-widget-error { margin:0; color:#8d4c35; font-size:.9rem; }
        @media (max-width:680px) { .fundraising-widget { padding:18px; } .fundraising-widget-head { flex-direction:column; } .fundraising-open-grid { grid-template-columns:repeat(2,minmax(0,1fr)); } .fundraising-result.total { grid-template-columns:minmax(0,1fr); } .fundraising-result-breakdown { grid-template-columns:repeat(3,minmax(0,1fr)); } .fundraising-result-breakdown .fundraising-result { padding:10px 8px; } }
        @media (max-width:460px) { .fundraising-result-breakdown { grid-template-columns:minmax(0,1fr); } }
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
                    <div class="pm-section-heading"><div><h2>Aufgaben nach Fälligkeit</h2></div><a class="pm-button small secondary" href="projects.php">Alle Aufgaben</a></div>
                    <div class="pm-week-grid">
                        <?php $dashboardLabels = ['overdue'=>'Überfällig','this_week'=>'Diese Woche','next_week'=>'Nächste Woche','demnaechst'=>'Demnächst']; foreach ($dashboardLabels as $groupKey => $groupLabel): ?>
                            <section class="pm-week-column pm-<?= dashboardEscape($groupKey) ?>">
                                <h3><?= dashboardEscape($groupLabel) ?><span><?= count($dashboardGroups[$groupKey]) ?></span></h3>
                                <?php if (!$dashboardGroups[$groupKey]): ?><p class="pm-empty">Keine Aufgaben</p>
                                <?php else: foreach ($dashboardGroups[$groupKey] as $task): $taskColor = dashboardColor($task['project_color'] ?? null); ?>
                                    <article class="pm-task-row pm-task-tile" style="--project-color:<?= dashboardEscape($taskColor) ?>" data-task-url="projects.php?view=task&amp;id=<?= (int)$task['id'] ?>" tabindex="0" role="link" aria-label="Aufgabe öffnen: <?= dashboardEscape($task['title']) ?>">
                                        <a class="pm-task-title" href="projects.php?view=task&amp;id=<?= (int)$task['id'] ?>"><?= dashboardEscape($task['title']) ?></a>
                                        <div class="pm-task-project"><?= dashboardEscape($task['project_title']) ?></div>
                                        <?php if (!empty($task['assignee'])): ?><div class="pm-task-meta"><span class="pm-assignee">Übernommen: <?= dashboardEscape($task['assignee']) ?></span></div><?php endif; ?>
                                        <div class="pm-task-status-actions" aria-label="Aufgabenstatus ändern">
                                            <?php if ($task['status'] !== 'in_progress'): ?>
                                                <form method="post" action="projects.php"><input type="hidden" name="csrf_token" value="<?= dashboardEscape($dashboardCsrf) ?>"><input type="hidden" name="action" value="set_task_status"><input type="hidden" name="task_id" value="<?= (int)$task['id'] ?>"><input type="hidden" name="status" value="in_progress"><input type="hidden" name="return_to" value="dashboard"><button class="pm-icon-button pm-icon-progress" type="submit" title="Als in Bearbeitung markieren" aria-label="Als in Bearbeitung markieren"><i class="fa-solid fa-play"></i></button></form>
                                            <?php else: ?>
                                                <form method="post" action="projects.php"><input type="hidden" name="csrf_token" value="<?= dashboardEscape($dashboardCsrf) ?>"><input type="hidden" name="action" value="set_task_status"><input type="hidden" name="task_id" value="<?= (int)$task['id'] ?>"><input type="hidden" name="status" value="open"><input type="hidden" name="return_to" value="dashboard"><button class="pm-icon-button pm-icon-open" type="submit" title="Wieder auf offen setzen" aria-label="Wieder auf offen setzen"><i class="fa-solid fa-rotate-left"></i></button></form>
                                            <?php endif; ?>
                                            <form method="post" action="projects.php"><input type="hidden" name="csrf_token" value="<?= dashboardEscape($dashboardCsrf) ?>"><input type="hidden" name="action" value="set_task_status"><input type="hidden" name="task_id" value="<?= (int)$task['id'] ?>"><input type="hidden" name="status" value="done"><input type="hidden" name="return_to" value="dashboard"><button class="pm-icon-button pm-icon-done" type="submit" title="Als erledigt markieren" aria-label="Als erledigt markieren"><i class="fa-regular fa-square-check"></i></button></form>
                                        </div>
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
                        <h3 class="fundraising-widget-section-title">Was ist noch zu tun?</h3>
                        <div class="fundraising-open-grid">
                            <a class="fundraising-open-card" href="index.php?contact_type=Pers%C3%B6nlich&amp;contact_status=pending&amp;exclude_suggestions=1" aria-label="Lokale Kontakte mit ausstehender Anfrage anzeigen"><span class="number"><?= $fundraisingStats['open']['Persönlich'] ?></span><span class="label">Lokale Kontakte ansprechen <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></span></a>
                            <a class="fundraising-open-card" href="index.php?contact_type=Formular&amp;contact_status=pending&amp;exclude_suggestions=1" aria-label="Formularkontakte mit ausstehender Anfrage anzeigen"><span class="number"><?= $fundraisingStats['open']['Formular'] ?></span><span class="label">Formulare ausfüllen <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></span></a>
                            <a class="fundraising-open-card" href="index.php?contact_type=Mail&amp;contact_status=pending&amp;exclude_suggestions=1" aria-label="Mail-Kontakte mit ausstehender Anfrage anzeigen"><span class="number"><?= $fundraisingStats['open']['Mail'] ?></span><span class="label">E-Mails schreiben <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></span></a>
                            <a class="fundraising-open-card" href="index.php?contact_status=action_needed&amp;exclude_suggestions=1" aria-label="Alle Kontakte mit Handlungsbedarf anzeigen"><span class="number"><?= $fundraisingStats['action_needed_status'] ?></span><span class="label">Kontakte mit Handlungsbedarf <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></span></a>
                        </div>
                        <div class="fundraising-progress">
                            <div class="fundraising-progress-label"><span>Kontaktaufnahme</span><strong><?= $fundraisingStats['contacted'] ?> von <?= $fundraisingStats['total'] ?> Adressen</strong></div>
                            <div class="fundraising-progress-track" role="progressbar" aria-label="Anteil kontaktierter Adressen" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?= $fundraisingProgress ?>"><div class="fundraising-progress-fill" style="width:<?= $fundraisingProgress ?>%"></div></div>
                        </div>
                        <h3 class="fundraising-widget-section-title" style="margin-top:22px">Ergebnis der bisherigen Ansprache</h3>
                        <div class="fundraising-results">
                            <section class="fundraising-result total" aria-label="Angefragte Kontakte insgesamt">
                                <div class="fundraising-total-summary">
                                    <span class="label">Angefragte Kontakte:</span>
                                    <span class="number"><?= $fundraisingStats['results']['Spende zugesagt'] + $fundraisingStats['results']['Spende abgesagt'] + $fundraisingStats['results']['Antwort abwarten'] ?></span>
                                </div>
                                <div class="fundraising-result-breakdown">
                                    <a class="fundraising-result positive fundraising-result-link" href="index.php?contact_status=promised&amp;exclude_suggestions=1" aria-label="Alle Kontakte mit Spende zugesagt anzeigen"><span class="number"><?= $fundraisingStats['results']['Spende zugesagt'] ?></span><span class="label">Zugesagt <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></span></a>
                                    <a class="fundraising-result negative fundraising-result-link" href="index.php?contact_status=declined&amp;exclude_suggestions=1" aria-label="Alle Kontakte mit Spende abgesagt anzeigen"><span class="number"><?= $fundraisingStats['results']['Spende abgesagt'] ?></span><span class="label">Abgesagt <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></span></a>
                                    <a class="fundraising-result waiting fundraising-result-link" href="index.php?contact_status=waiting&amp;exclude_suggestions=1" aria-label="Alle Kontakte mit Antwort ausstehend anzeigen"><span class="number"><?= $fundraisingStats['results']['Antwort abwarten'] ?></span><span class="label">Antwort ausstehend <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></span></a>
                                </div>
                            </section>
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
