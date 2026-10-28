<?php
if (!defined('APP_ENTRY_POINT')) {
    define('APP_ENTRY_POINT', true);
}

// Compatibility entry point; implementation lives in app/Modules/Benutzerverwaltung.
require __DIR__ . '/app/Modules/Benutzerverwaltung/Pages/role_permissions.php';
