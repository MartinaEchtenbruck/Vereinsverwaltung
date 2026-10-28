<?php
/**
 * Einstiegspunkt für die Benachrichtigungsseite.
 * Der Bereich wird schrittweise ausgebaut; aktuell dient die Seite als Ziel
 * für das Benachrichtigungssymbol in der globalen Navigation.
 */
require_once __DIR__ . '/auth.php';

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
    <title>Benachrichtigungen – Förderverein intern</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="includes/navigation.css">
    <link rel="icon" type="image/png" sizes="32x32" href="/intern/assets/icons/favicon.png">
</head>
<body>
    <div class="container">
        <header class="app-page-header">
            <h1>Benachrichtigungen</h1>
            <?php require __DIR__ . '/includes/navigation.php'; ?>
        </header>
        <main class="notifications-page">
            <section class="notifications-empty-state" aria-labelledby="notifications-title">
                <div class="notifications-empty-icon"><i class="fas fa-bell" aria-hidden="true"></i></div>
                <h2 id="notifications-title">Benachrichtigungen</h2>
                <p>Der Benachrichtigungsbereich ist vorbereitet. Die Hinweise zu neuen Kontaktvorschlägen und überfälligen Aufgaben werden in einem nächsten Schritt ergänzt.</p>
            </section>
        </main>
    </div>
    <style>
        .notifications-page { max-width: 980px; margin: 0 auto; }
        .notifications-empty-state { margin-top: 28px; padding: 36px 24px; text-align: center; background: #fff; border: 1px solid #e3e9eb; border-radius: 14px; box-shadow: 0 4px 18px rgba(30,45,55,.05); }
        .notifications-empty-icon { display: inline-flex; width: 58px; height: 58px; align-items: center; justify-content: center; border-radius: 50%; background: #eef4f6; color: #577c8b; font-size: 24px; }
        .notifications-empty-state h2 { margin: 16px 0 8px; color: #263640; font-size: 22px; }
        .notifications-empty-state p { max-width: 600px; margin: 0 auto; color: #667780; line-height: 1.6; }
    </style>
</body>
</html>
