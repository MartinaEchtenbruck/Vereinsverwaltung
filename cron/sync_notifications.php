<?php
/**
 * Cronjob: Benachrichtigungen unabhängig von Seitenaufrufen synchronisieren.
 * Darf ausschließlich über PHP CLI ausgeführt werden, nicht per HTTP.
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$root = dirname(__DIR__);
require_once $root . '/db_connection.php';
require_once $root . '/app/Core/Notifications.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    fvSyncNotifications($conn);
    echo '[' . date('Y-m-d H:i:s') . "] Benachrichtigungen erfolgreich synchronisiert.\n";
} catch (Throwable $e) {
    error_log('Cronjob Benachrichtigungen: ' . $e->getMessage());
    fwrite(STDERR, '[' . date('Y-m-d H:i:s') . '] Fehler bei der Benachrichtigungssynchronisierung. Details stehen im PHP-Fehlerprotokoll.\n');
    exit(1);
}
