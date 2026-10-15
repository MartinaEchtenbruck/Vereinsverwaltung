<?php
/**
 * Einheitliche globale Navigation für angemeldete Seiten.
 * Voraussetzung: auth.php wurde bereits eingebunden und die Session läuft.
 */
$navigationItems = require __DIR__ . '/../config/navigation.php';
$currentUserName = $_SESSION['full_name'] ?? $_SESSION['username'] ?? 'Benutzer';
$currentUserRole = getUserRole();
?>
<div class="app-navigation" aria-label="Hauptnavigation">
    <div class="app-navigation-user">
        <span>Eingeloggt als <strong><?= htmlspecialchars((string) $currentUserName, ENT_QUOTES, 'UTF-8') ?></strong></span>
        <?php if ($currentUserRole !== null && $currentUserRole !== false && $currentUserRole !== ''): ?>
            <span class="app-navigation-role">Rolle: <strong><?= htmlspecialchars((string) $currentUserRole, ENT_QUOTES, 'UTF-8') ?></strong></span>
        <?php endif; ?>
    </div>
    <nav class="app-navigation-links" aria-label="Bereiche">
        <?php foreach ($navigationItems as $item): ?>
            <?php if (hasPermission($item['permission'])): ?>
                <a href="<?= htmlspecialchars($item['url'], ENT_QUOTES, 'UTF-8') ?>">
                    <i class="<?= htmlspecialchars($item['icon'], ENT_QUOTES, 'UTF-8') ?>" aria-hidden="true"></i>
                    <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
        <a class="app-navigation-logout" href="logout.php">
            <i class="fas fa-sign-out-alt" aria-hidden="true"></i> Abmelden
        </a>
    </nav>
</div>
