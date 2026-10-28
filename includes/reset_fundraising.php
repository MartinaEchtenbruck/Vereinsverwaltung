<?php
if (!defined('APP_ENTRY_POINT')) {
    define('APP_ENTRY_POINT', true);
}

// Kompatibilitäts-Einstiegspunkt; Implementierung im Modul Spendenaktionen.
require __DIR__ . '/app/Modules/Spendenaktionen/Actions/reset_fundraising.php';
