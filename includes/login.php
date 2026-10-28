<?php
/**
 * Kompatibilitätseinstiegspunkt für die bisherige URL /login.php.
 */
define('APP_ENTRY_POINT', true);
require __DIR__ . '/app/Modules/Benutzerverwaltung/Pages/login.php';
