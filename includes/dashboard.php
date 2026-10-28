<?php
/**
 * Einstiegspunkt für das Dashboard.
 * Dient allen angemeldeten Benutzern als zentrale Startseite.
 */
define('APP_ENTRY_POINT', true);
require __DIR__ . '/app/Modules/Dashboard/Pages/index.php';
