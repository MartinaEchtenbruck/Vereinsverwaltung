<?php
declare(strict_types=1);
require_once __DIR__ . '/common.php';
webpushRequireLogin();
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'GET') {
    header('Allow: GET');
    webpushJson(['success' => false, 'error' => 'Methode nicht erlaubt.'], 405);
}
try {
    $config = webpushConfig();
    webpushJson(['success' => true, 'publicKey' => $config['vapid']['public_key']]);
} catch (Throwable $e) {
    error_log('Web Push public-key: ' . $e->getMessage());
    webpushJson(['success' => false, 'error' => 'Der öffentliche Push-Schlüssel ist derzeit nicht verfügbar.'], 500);
}
