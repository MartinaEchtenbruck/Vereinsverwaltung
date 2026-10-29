<?php
declare(strict_types=1);
require_once __DIR__ . '/common.php';
$userId = webpushRequireLogin();
webpushRequirePostAndCsrf();
$data = webpushReadJsonBody();

$endpoint = $data['endpoint'] ?? '';
$keys = $data['keys'] ?? null;
if (!is_string($endpoint) || strlen($endpoint) > 2000 || !filter_var($endpoint, FILTER_VALIDATE_URL)) {
    webpushJson(['success' => false, 'error' => 'Ungültiger Push-Endpunkt.'], 400);
}
$endpointParts = parse_url($endpoint);
if (($endpointParts['scheme'] ?? '') !== 'https' || empty($endpointParts['host'])) {
    webpushJson(['success' => false, 'error' => 'Der Push-Endpunkt muss eine HTTPS-Adresse sein.'], 400);
}
if (!is_array($keys) || empty($keys['p256dh']) || empty($keys['auth']) || !is_string($keys['p256dh']) || !is_string($keys['auth'])) {
    webpushJson(['success' => false, 'error' => 'Das Geräte-Abonnement enthält keine gültigen Schlüssel.'], 400);
}
if (strlen($keys['p256dh']) > 256 || strlen($keys['auth']) > 256) {
    webpushJson(['success' => false, 'error' => 'Die Abonnementdaten sind ungültig.'], 400);
}

$normalized = [
    'endpoint' => $endpoint,
    'expirationTime' => isset($data['expirationTime']) ? $data['expirationTime'] : null,
    'keys' => ['p256dh' => $keys['p256dh'], 'auth' => $keys['auth']],
];
$json = json_encode($normalized, JSON_UNESCAPED_SLASHES);
if ($json === false) webpushJson(['success' => false, 'error' => 'Das Abonnement konnte nicht verarbeitet werden.'], 400);

try {
    $conn = webpushDatabase();
    $stmt = $conn->prepare("INSERT INTO webpush_subscriptions (user_id, endpoint_hash, subscription_json, user_agent, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW()) ON DUPLICATE KEY UPDATE user_id=VALUES(user_id), subscription_json=VALUES(subscription_json), user_agent=VALUES(user_agent), updated_at=NOW()");
    $stmt->execute([
        $userId,
        hash('sha256', $endpoint),
        $json,
        substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 500),
    ]);
    webpushJson(['success' => true]);
} catch (Throwable $e) {
    error_log('Web Push subscribe: ' . $e->getMessage());
    webpushJson(['success' => false, 'error' => 'Das Gerät konnte nicht gespeichert werden. Wurde die Web-Push-Datenbankmigration ausgeführt?'], 500);
}
