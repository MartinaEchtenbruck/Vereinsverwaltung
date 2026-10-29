<?php
declare(strict_types=1);
require_once __DIR__ . '/common.php';
$userId = webpushRequireLogin();
webpushRequirePostAndCsrf();
webpushReadJsonBody();

try {
    $root = dirname(__DIR__, 2);
    $autoload = $root . '/private/webpush/vendor/autoload.php';
    if (!is_readable($autoload)) throw new RuntimeException('Composer-Autoloader ist nicht erreichbar.');
    require_once $autoload;
    $config = webpushConfig();
    $conn = webpushDatabase();
    $stmt = $conn->prepare('SELECT id, subscription_json FROM webpush_subscriptions WHERE user_id=?');
    $stmt->execute([$userId]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!$rows) webpushJson(['success' => false, 'error' => 'Für diesen Benutzer ist kein Gerät registriert.'], 404);

    $webPush = new \Minishlink\WebPush\WebPush([
        'VAPID' => [
            'subject' => $config['vapid']['subject'],
            'publicKey' => $config['vapid']['public_key'],
            'privateKey' => $config['vapid']['private_key'],
        ],
    ]);
    $sent = 0;
    $failed = 0;
    $invalidIds = [];
    $payload = json_encode([
        'title' => 'Förderverein intern',
        'body' => 'Das ist eine Testnachricht für Web Push.',
        'url' => '/intern/notifications.php',
        'tag' => 'webpush-test-' . $userId,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    foreach ($rows as $row) {
        $subscriptionData = json_decode((string)$row['subscription_json'], true);
        if (!is_array($subscriptionData)) {
            $failed++;
            $invalidIds[] = (int)$row['id'];
            continue;
        }
        $report = $webPush->sendOneNotification(
            \Minishlink\WebPush\Subscription::create($subscriptionData),
            $payload
        );
        if ($report->isSuccess()) {
            $sent++;
        } else {
            $failed++;
            if ($report->isSubscriptionExpired()) $invalidIds[] = (int)$row['id'];
            error_log('Web Push Testversand fehlgeschlagen: ' . $report->getReason());
        }
    }
    if ($invalidIds) {
        $marks = implode(',', array_fill(0, count($invalidIds), '?'));
        $delete = $conn->prepare("DELETE FROM webpush_subscriptions WHERE id IN ($marks) AND user_id=?");
        $delete->execute([...$invalidIds, $userId]);
    }
    webpushJson([
        'success' => $sent > 0,
        'sent' => $sent,
        'failed' => $failed,
        'message' => $sent > 0
            ? 'Der Push-Dienst hat mindestens eine Nachricht angenommen.'
            : 'Der Push-Dienst hat keine Nachricht angenommen. Prüfe das PHP-Fehlerprotokoll.',
    ]);
} catch (Throwable $e) {
    error_log('Web Push Testversand: ' . $e->getMessage());
    webpushJson(['success' => false, 'error' => 'Der Testversand ist fehlgeschlagen. Prüfe Datenbankmigration, Schlüssel und PHP-Fehlerprotokoll.'], 500);
}
