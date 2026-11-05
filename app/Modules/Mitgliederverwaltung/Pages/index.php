<?php
/** Interne Moduldatei: nur über den bisherigen Einstiegspunkt aufrufen. */
if (!defined('APP_ENTRY_POINT')) {
    http_response_code(404);
    exit;
}

$appRoot = dirname(__DIR__, 4);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once $appRoot . '/auth.php';
checkPermission('manage_members');
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mitgliederübersicht – Förderverein intern</title>
    <link rel="icon" type="image/png" sizes="32x32" href="/intern/assets/icons/favicon.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/intern/assets/icons/apple-touch-icon.png">
    <link rel="manifest" href="/intern/manifest.webmanifest">
    <meta name="theme-color" content="#FFF500">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="includes/navigation.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<div class="container">
    <header class="app-page-header">
        <h1>Mitgliederübersicht</h1>
        <?php require $appRoot . '/includes/navigation.php'; ?>
    </header>
    <main>
        <!-- Die Mitgliederverwaltung wird in späteren Schritten ergänzt. -->
    </main>
</div>
</body>
</html>
