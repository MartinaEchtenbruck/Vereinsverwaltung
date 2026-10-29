<?php
declare(strict_types=1);

/**
 * Zentraler serverseitiger Web-Push-Versand für Förderverein intern.
 * Darf aus Web-Anfragen und CLI-Prozessen verwendet werden.
 */

if (!function_exists('fvSendWebPushNotification')) {
    /**
     * Sendet eine Push-Nachricht an alle registrierten Geräte eines Benutzers,
     * sofern dieser den betreffenden Benachrichtigungstyp aktiviert hat.
     * Fehler werden protokolliert, aber nicht an den Aufrufer weitergereicht,
     * damit die Datenbankbenachrichtigung bzw. der Cronjob erhalten bleibt.
     */
    function fvSendWebPushNotification(
        PDO $conn,
        int $userId,
        string $notificationType,
        string $title,
        string $message,
        string $targetUrl,
        ?int $notificationId = null
    ): void {
        try {
            if ($userId <= 0 || $notificationType === '') {
                return;
            }

            // Fehlende Präferenzzeile bedeutet standardmäßig aktiviert.
            // Falls die Tabelle selbst fehlt, wird aus Sicherheitsgründen kein Push gesendet.
            try {
                $preference = $conn->prepare(
                    'SELECT push_enabled FROM user_notification_preferences WHERE user_id = ? AND notification_type = ? LIMIT 1'
                );
                $preference->execute([$userId, $notificationType]);
                $pushEnabled = $preference->fetchColumn();
            } catch (Throwable $e) {
                error_log('Web Push: Benutzereinstellungen konnten nicht gelesen werden: ' . $e->getMessage());
                return;
            }

            if ($pushEnabled !== false && (int)$pushEnabled !== 1) {
                return;
            }

            $subscriptionQuery = $conn->prepare(
                'SELECT id, subscription_json FROM webpush_subscriptions WHERE user_id = ?'
            );
            $subscriptionQuery->execute([$userId]);
            $subscriptions = $subscriptionQuery->fetchAll(PDO::FETCH_ASSOC);
            if (!$subscriptions) {
                return;
            }

            $root = dirname(__DIR__, 2);
            $autoload = $root . '/private/webpush/vendor/autoload.php';
            $configFile = $root . '/private/webpush_config.php';
            if (!is_readable($autoload) || !is_readable($configFile)) {
                throw new RuntimeException('Web-Push-Bibliothek oder Konfiguration ist nicht erreichbar.');
            }
            require_once $autoload;
            $config = require $configFile;
            if (!is_array($config)
                || empty($config['vapid']['subject'])
                || empty($config['vapid']['public_key'])
                || empty($config['vapid']['private_key'])) {
                throw new RuntimeException('Web-Push-Konfiguration ist unvollständig.');
            }

            $webPush = new \Minishlink\WebPush\WebPush([
                'VAPID' => [
                    'subject' => $config['vapid']['subject'],
                    'publicKey' => $config['vapid']['public_key'],
                    'privateKey' => $config['vapid']['private_key'],
                ],
            ]);

            $payloadUrl = fvWebPushLocalUrl($targetUrl);
            $payload = json_encode([
                'title' => $title !== '' ? $title : 'Förderverein intern',
                'body' => $message,
                'url' => $payloadUrl,
                'tag' => $notificationId !== null
                    ? 'foerderverein-notification-' . $notificationId
                    : 'foerderverein-' . $notificationType . '-' . $userId,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if ($payload === false) {
                throw new RuntimeException('Push-Nachricht konnte nicht serialisiert werden.');
            }

            $expiredIds = [];
            foreach ($subscriptions as $subscriptionRow) {
                $subscriptionId = (int)$subscriptionRow['id'];
                $subscriptionData = json_decode((string)$subscriptionRow['subscription_json'], true);
                if (!is_array($subscriptionData)) {
                    $expiredIds[] = $subscriptionId;
                    error_log('Web Push: Ungültiges Abonnement, ID ' . $subscriptionId . '.');
                    continue;
                }

                try {
                    $report = $webPush->sendOneNotification(
                        \Minishlink\WebPush\Subscription::create($subscriptionData),
                        $payload
                    );
                    if (!$report->isSuccess()) {
                        error_log('Web Push Versand fehlgeschlagen (Benutzer ' . $userId . ', Abonnement ' . $subscriptionId . '): ' . $report->getReason());
                        if ($report->isSubscriptionExpired()) {
                            $expiredIds[] = $subscriptionId;
                        }
                    }
                } catch (Throwable $e) {
                    error_log('Web Push Versandfehler (Benutzer ' . $userId . ', Abonnement ' . $subscriptionId . '): ' . $e->getMessage());
                }
            }

            if ($expiredIds) {
                $marks = implode(',', array_fill(0, count($expiredIds), '?'));
                $delete = $conn->prepare("DELETE FROM webpush_subscriptions WHERE id IN ($marks)");
                $delete->execute($expiredIds);
            }
        } catch (Throwable $e) {
            error_log('Web Push: Versand für Benutzer ' . $userId . ' fehlgeschlagen: ' . $e->getMessage());
        }
    }

    /** Erlaubt ausschließlich interne Links unter /intern/. */
    function fvWebPushLocalUrl(string $targetUrl): string {
        $targetUrl = trim($targetUrl);
        if ($targetUrl === '') {
            return '/intern/notifications.php';
        }

        if (preg_match('~^https?://~i', $targetUrl)) {
            $parts = parse_url($targetUrl);
            $path = (string)($parts['path'] ?? '');
            if (!str_starts_with($path, '/intern/')) {
                return '/intern/notifications.php';
            }
            return $path . (isset($parts['query']) ? '?' . $parts['query'] : '');
        }

        if (str_starts_with($targetUrl, '/intern/')) {
            return $targetUrl;
        }
        return '/intern/' . ltrim($targetUrl, '/');
    }
}
