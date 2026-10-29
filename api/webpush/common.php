<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/auth.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, private');

function webpushJson(array $data, int $status = 200): never
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function webpushRequireLogin(): int
{
    $userId = (int)($_SESSION['user_id'] ?? 0);
    if ($userId <= 0) webpushJson(['success' => false, 'error' => 'Bitte melde dich erneut an.'], 401);
    return $userId;
}

function webpushRequirePostAndCsrf(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        header('Allow: POST');
        webpushJson(['success' => false, 'error' => 'Methode nicht erlaubt.'], 405);
    }
    $expected = (string)($_SESSION['webpush_csrf'] ?? '');
    $received = (string)($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    if ($expected === '' || $received === '' || !hash_equals($expected, $received)) {
        webpushJson(['success' => false, 'error' => 'Sicherheitsprüfung fehlgeschlagen. Bitte lade die Seite neu.'], 403);
    }
}

function webpushDatabase(): PDO
{
    require_once dirname(__DIR__, 2) . '/db_connection.php';
    $database = new Database();
    $conn = $database->getConnection();
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $conn;
}

function webpushConfig(): array
{
    $file = dirname(__DIR__, 2) . '/private/webpush_config.php';
    if (!is_file($file) || !is_readable($file)) {
        throw new RuntimeException('Web-Push-Konfiguration ist nicht erreichbar.');
    }
    $config = require $file;
    if (!is_array($config) || empty($config['vapid']['subject']) || empty($config['vapid']['public_key']) || empty($config['vapid']['private_key'])) {
        throw new RuntimeException('Web-Push-Konfiguration ist unvollständig.');
    }
    return $config;
}

function webpushReadJsonBody(): array
{
    $raw = file_get_contents('php://input');
    $data = json_decode($raw ?: '', true);
    if (!is_array($data)) webpushJson(['success' => false, 'error' => 'Ungültige JSON-Anfrage.'], 400);
    return $data;
}
