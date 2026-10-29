<?php
declare(strict_types=1);
require_once __DIR__ . '/common.php';
$userId = webpushRequireLogin();
webpushRequirePostAndCsrf();
$data = webpushReadJsonBody();
$endpoint = $data['endpoint'] ?? '';
if (!is_string($endpoint) || strlen($endpoint) > 2000 || !filter_var($endpoint, FILTER_VALIDATE_URL)) {
    webpushJson(['success' => false, 'error' => 'Ungültiger Push-Endpunkt.'], 400);
}
try {
    $conn = webpushDatabase();
    $stmt = $conn->prepare('DELETE FROM webpush_subscriptions WHERE user_id=? AND endpoint_hash=?');
    $stmt->execute([$userId, hash('sha256', $endpoint)]);
    webpushJson(['success' => true]);
} catch (Throwable $e) {
    error_log('Web Push unsubscribe: ' . $e->getMessage());
    webpushJson(['success' => false, 'error' => 'Das Gerät konnte nicht aus der Datenbank entfernt werden.'], 500);
}
