<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (empty($_SESSION['webpush_csrf'])) {
    $_SESSION['webpush_csrf'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['webpush_csrf'];
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Push-Benachrichtigungen – Förderverein intern</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="includes/navigation.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="webpush-csrf" content="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
</head>
<body>
<div class="container">
    <header class="app-page-header">
        <h1>Push-Benachrichtigungen</h1>
        <?php require __DIR__ . '/includes/navigation.php'; ?>
    </header>
    <main class="webpush-page">
        <section class="webpush-card">
            <h2>Benachrichtigungen auf diesem Gerät</h2>
            <p>Aktiviere Push-Benachrichtigungen, damit Förderverein intern dich auch erreichen kann, wenn die Seite nicht geöffnet ist.</p>
            <p class="webpush-note">Du musst den Empfang im Browser erlauben. Auf iPhones und iPads ist dafür iOS/iPadOS 16.4 oder neuer und die Installation dieser Web-App über „Zum Home-Bildschirm“ erforderlich.</p>
            <div class="webpush-actions">
                <button type="button" id="webpush-enable" class="webpush-button">Push aktivieren</button>
                <button type="button" id="webpush-disable" class="webpush-button webpush-button-secondary" hidden>Push auf diesem Gerät deaktivieren</button>
                <button type="button" id="webpush-test" class="webpush-button webpush-button-secondary" hidden>Testnachricht senden</button>
            </div>
            <p id="webpush-status" class="webpush-status" role="status" aria-live="polite">Status wird geprüft …</p>
        </section>
    </main>
</div>
<style>
.webpush-page{max-width:820px;margin:24px auto}.webpush-card{padding:24px;background:#fff;border:1px solid #e1e8eb;border-radius:14px;box-shadow:0 3px 14px rgba(28,45,60,.06)}.webpush-card h2{margin-top:0;color:#263e4d}.webpush-card p{line-height:1.55;color:#53636b}.webpush-note{padding:12px 14px;background:#f3f7f9;border-left:4px solid #7796a5;border-radius:4px}.webpush-actions{display:flex;gap:10px;flex-wrap:wrap;margin-top:20px}.webpush-button{border:1px solid #d8dc71;border-radius:8px;background:#fff500;color:#303329;padding:11px 15px;font:inherit;font-weight:650;cursor:pointer}.webpush-button-secondary{background:#fff;border-color:#cbd6dc;color:#344955}.webpush-button:disabled{opacity:.6;cursor:wait}.webpush-status{margin-bottom:0}.webpush-status.is-error{color:#a83232}.webpush-status.is-success{color:#247344}
</style>
<script src="js/webpush.js" defer></script>
</body>
</html>
