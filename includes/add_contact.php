<?php
if (!defined('APP_ENTRY_POINT')) {
    define('APP_ENTRY_POINT', true);
}

// Kompatibilitäts-Einstiegspunkt; Implementierung im Modul Sponsoren.
require __DIR__ . '/app/Modules/Sponsoren/Actions/add_contact.php';
