<?php
/**
 * Zentrale Startseite für alle angemeldeten Benutzer.
 * Aufruf ausschließlich über den Einstiegspunkt /dashboard.php.
 */
if (!defined('APP_ENTRY_POINT')) {
    http_response_code(404);
    exit;
}

$appRoot = dirname(__DIR__, 4);
require_once $appRoot . '/auth.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Spendenverwaltung</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="includes/navigation.css">
    <style>
        .dashboard-content {
            min-height: 180px;
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="app-page-header">
            <h1>Dashboard</h1>
            <?php require $appRoot . '/includes/navigation.php'; ?>
        </header>
        <main class="dashboard-content" aria-label="Dashboard-Inhalt">
            <!-- Hier können später Dashboard-Widgets ergänzt werden. -->
        </main>
    </div>
</body>
</html>
