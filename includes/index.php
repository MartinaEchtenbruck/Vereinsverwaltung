<?php
/**
 * Kompatibilitätseinstiegspunkt für die bisherige URL /index.php.
 * Die eigentliche Sponsorenübersicht liegt im Sponsorenmodul.
 */
define('APP_ENTRY_POINT', true);
require __DIR__ . '/app/Modules/Sponsoren/Pages/index.php';
