<?php
/**
 * Zentrale, responsive Hauptnavigation.
 * Bestehende Seitenpfade und Berechtigungsprüfungen bleiben erhalten.
 */
$navigationItems = require __DIR__ . '/../config/navigation.php';
$currentPage = basename(parse_url($_SERVER['SCRIPT_NAME'] ?? '', PHP_URL_PATH) ?: '');
$currentUserName = $_SESSION['full_name'] ?? $_SESSION['username'] ?? 'Benutzer';
$currentUserRole = getUserRole();
$notificationUnreadCount = 0;
try {
    require_once __DIR__ . '/../db_connection.php';
    require_once __DIR__ . '/../app/Core/Notifications.php';
    $notificationDb = (new Database())->getConnection();
    $notificationDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    fvSyncNotifications($notificationDb);
    $notificationUserId = (int)($_SESSION['user_id'] ?? 0);
    $notificationCanSeeTasks = fvNotificationCanSee($notificationDb, $notificationUserId, 'ProjectManagement');
    $notificationCanManageContacts = fvNotificationCanSee($notificationDb, $notificationUserId, 'add_contacts') || fvNotificationCanSee($notificationDb, $notificationUserId, 'edit_contacts');
    $notificationConditions = ['user_id=?', 'is_read=0'];
    $notificationParams = [$notificationUserId];
    if (!$notificationCanSeeTasks) $notificationConditions[] = "notification_type<>'task_overdue'";
    if (!$notificationCanManageContacts) $notificationConditions[] = "notification_type<>'contact_suggestion'";
    $notificationCountStmt = $notificationDb->prepare('SELECT COUNT(*) FROM notifications WHERE '.implode(' AND ', $notificationConditions));
    $notificationCountStmt->execute($notificationParams);
    $notificationUnreadCount = (int)$notificationCountStmt->fetchColumn();
} catch (Throwable $notificationError) {
    // Die Navigation darf bei fehlender Migration nicht die gesamte Anwendung blockieren.
    error_log('Benachrichtigungszähler: '.$notificationError->getMessage());
}

$visibleItems = [];
foreach ($navigationItems as $item) {
    if (!isset($item['permission']) || $item['permission'] === null || hasPermission($item['permission'])) {
        $visibleItems[] = $item;
    }
}
$navigationGroups = [
    'Sponsoren & Spenden' => ['Sponsorenübersicht', 'Spendenaktion'],
    'Verkäufe & Bestellungen' => ['Verkäufe & Bestellungen'],
    'Projekte & Aufgaben' => ['Projektmanagement'],
    'Verwaltung' => ['Benutzerverwaltung', 'Rollen & Rechte'],
];
$dashboardItem = null;
foreach ($visibleItems as $item) {
    if ($item['label'] === 'Dashboard') $dashboardItem = $item;
}
?>
<div class="app-navigation" aria-label="Hauptnavigation">
    <div class="app-navigation-top">
        <a class="app-navigation-brand" href="dashboard.php" aria-label="Förderverein intern – Dashboard">
            <img src="/intern/assets/foerderverein-logo.png" alt="Förderverein Ev. Kindergarten Duisburg-Rumeln e.V.">
            <span><strong>Förderverein</strong><small>Interner Bereich</small></span>
        </a>
        <button class="app-navigation-toggle" type="button" aria-expanded="false" aria-controls="app-navigation-menu">
            <i class="fas fa-bars" aria-hidden="true"></i><span>Menü</span>
        </button>
        <div class="app-navigation-user">
            <a class="app-navigation-user-name app-navigation-profile-link" href="profile.php" title="Profil und Einstellungen öffnen"><i class="fas fa-user-circle" aria-hidden="true"></i> <?= htmlspecialchars((string) $currentUserName, ENT_QUOTES, 'UTF-8') ?></a>
            <a class="app-navigation-notifications<?= $notificationUnreadCount > 0 ? ' has-unread' : '' ?>" href="notifications.php" aria-label="Benachrichtigungen<?= $notificationUnreadCount > 0 ? ': '.$notificationUnreadCount.' ungelesen' : '' ?>" title="Benachrichtigungen">
                <i class="fas fa-bell" aria-hidden="true"></i>
                <?php if ($notificationUnreadCount > 0): ?><span class="app-navigation-notifications-count"><?= $notificationUnreadCount > 99 ? '99+' : $notificationUnreadCount ?></span><?php endif; ?>
                <span class="app-navigation-notifications-label">Benachrichtigungen</span>
            </a>
            <?php if ($currentUserRole !== null && $currentUserRole !== false && $currentUserRole !== ''): ?>
                <span class="app-navigation-role"><?= htmlspecialchars((string) $currentUserRole, ENT_QUOTES, 'UTF-8') ?></span>
            <?php endif; ?>
            <a class="app-navigation-logout" href="logout.php" title="Abmelden"><i class="fas fa-right-from-bracket" aria-hidden="true"></i><span>Abmelden</span></a>
        </div>
    </div>
    <nav class="app-navigation-menu" id="app-navigation-menu" aria-label="Bereiche">
        <?php if ($dashboardItem): ?>
            <a class="app-navigation-link<?= $currentPage === basename($dashboardItem['url']) ? ' is-active' : '' ?>" href="<?= htmlspecialchars($dashboardItem['url'], ENT_QUOTES, 'UTF-8') ?>"<?= $currentPage === basename($dashboardItem['url']) ? ' aria-current="page"' : '' ?>>
                <i class="<?= htmlspecialchars($dashboardItem['icon'], ENT_QUOTES, 'UTF-8') ?>" aria-hidden="true"></i><span>Dashboard</span>
            </a>
        <?php endif; ?>
        <?php foreach ($navigationGroups as $groupLabel => $groupItemLabels): ?>
            <?php
            $items = array_values(array_filter($visibleItems, static fn($item) => in_array($item['label'], $groupItemLabels, true)));
            if (!$items) continue;
            $groupActive = false;
            foreach ($items as $item) if ($currentPage === basename($item['url'])) $groupActive = true;
            ?>
            <div class="app-navigation-group<?= $groupActive ? ' has-active' : '' ?>">
                <button class="app-navigation-link app-navigation-group-toggle" type="button" aria-expanded="false">
                    <span><?= htmlspecialchars($groupLabel, ENT_QUOTES, 'UTF-8') ?></span><i class="fas fa-chevron-down app-navigation-chevron" aria-hidden="true"></i>
                </button>
                <div class="app-navigation-dropdown">
                    <?php foreach ($items as $item): $isCurrentPage = $currentPage === basename($item['url']); ?>
                        <a href="<?= htmlspecialchars($item['url'], ENT_QUOTES, 'UTF-8') ?>"<?= $isCurrentPage ? ' class="is-active" aria-current="page"' : '' ?>>
                            <i class="<?= htmlspecialchars($item['icon'], ENT_QUOTES, 'UTF-8') ?>" aria-hidden="true"></i><span><?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </nav>
</div>
<script>
(function () {
    const nav = document.querySelector('.app-navigation');
    if (!nav || nav.dataset.ready === '1') return;
    nav.dataset.ready = '1';
    const menu = nav.querySelector('.app-navigation-menu');
    const mobileToggle = nav.querySelector('.app-navigation-toggle');
    mobileToggle?.addEventListener('click', function () {
        const open = this.getAttribute('aria-expanded') !== 'true';
        this.setAttribute('aria-expanded', String(open));
        nav.classList.toggle('menu-open', open);
    });
    nav.querySelectorAll('.app-navigation-group-toggle').forEach(function (button) {
        button.addEventListener('click', function (event) {
            event.stopPropagation();
            const group = this.closest('.app-navigation-group');
            const open = this.getAttribute('aria-expanded') !== 'true';
            nav.querySelectorAll('.app-navigation-group').forEach(function (other) {
                other.classList.remove('dropdown-open');
                other.querySelector('.app-navigation-group-toggle')?.setAttribute('aria-expanded', 'false');
            });
            if (open) {
                group.classList.add('dropdown-open');
                this.setAttribute('aria-expanded', 'true');
            }
        });
    });
    document.addEventListener('click', function (event) {
        if (!nav.contains(event.target)) {
            nav.querySelectorAll('.app-navigation-group').forEach(function (group) {
                group.classList.remove('dropdown-open');
                group.querySelector('.app-navigation-group-toggle')?.setAttribute('aria-expanded', 'false');
            });
        }
    });
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            nav.querySelectorAll('.app-navigation-group').forEach(function (group) {
                group.classList.remove('dropdown-open');
                group.querySelector('.app-navigation-group-toggle')?.setAttribute('aria-expanded', 'false');
            });
            nav.classList.remove('menu-open');
            mobileToggle?.setAttribute('aria-expanded', 'false');
        }
    });
})();
</script>
