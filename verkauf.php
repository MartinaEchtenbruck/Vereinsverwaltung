<?php
define('APP_ENTRY_POINT', true);
if (isset($_GET['aktion']) && (int)$_GET['aktion'] > 0) {
    require __DIR__ . '/app/Modules/Verkauf/Pages/bearbeiten.php';
} else {
    require __DIR__ . '/app/Modules/Verkauf/Pages/index.php';
}
