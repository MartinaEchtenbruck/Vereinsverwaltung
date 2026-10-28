<?php
if (!defined('APP_ENTRY_POINT')) {
    define('APP_ENTRY_POINT', true);
}

/**
 * Kompatibilitätseinstiegspunkt für die bisherige URL /contact.php.
 * Die eigentliche Seite liegt im Sponsorenmodul.
 */
require __DIR__ . '/app/Modules/Sponsoren/Pages/contact.php';
