<?php
require_once __DIR__ . '/auth.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require_once __DIR__ . '/db_connection.php';

$db = new Database();
$conn = $db->getConnection();
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$userId = (int) $_SESSION['user_id'];

if (empty($_SESSION['profile_csrf'])) {
    $_SESSION['profile_csrf'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['profile_csrf'];
$message = '';
$error = '';

/*
 * Zentrale Liste der Push-Typen. Neue Typen können hier ergänzt werden.
 * Fehlende Datenbankzeilen bedeuten standardmäßig: Push aktiviert.
 */
$pushTypes = [
    'task_overdue' => [
        'label' => 'Überfällige Aufgaben',
        'description' => 'Hinweise zu Aufgaben, deren Fälligkeit überschritten ist.',
    ],
    'contact_suggestion' => [
        'label' => 'Kontaktvorschläge',
        'description' => 'Hinweise zu neuen Kontaktvorschlägen, die geprüft werden sollen.',
    ],
];

function profileH($value): string {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token']) || !hash_equals($csrf, (string) $_POST['csrf_token'])) {
            throw new RuntimeException('Die Sitzung ist abgelaufen. Bitte lade die Seite neu und versuche es erneut.');
        }

        $action = (string) ($_POST['action'] ?? '');
        if ($action === 'save_profile') {
            $fullName = trim((string) ($_POST['full_name'] ?? ''));
            $email = trim((string) ($_POST['email'] ?? ''));

            if ($fullName === '') {
                throw new RuntimeException('Bitte gib einen Namen ein.');
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new RuntimeException('Bitte gib eine gültige E-Mail-Adresse ein.');
            }

            $check = $conn->prepare('SELECT id FROM users WHERE email = ? AND id <> ? LIMIT 1');
            $check->execute([$email, $userId]);
            if ($check->fetchColumn()) {
                throw new RuntimeException('Diese E-Mail-Adresse wird bereits von einem anderen Benutzerkonto verwendet.');
            }

            $update = $conn->prepare('UPDATE users SET full_name = ?, email = ? WHERE id = ?');
            $update->execute([$fullName, $email, $userId]);
            $_SESSION['full_name'] = $fullName;
            $_SESSION['email'] = $email;
            $message = 'Deine Profildaten wurden gespeichert.';
        } elseif ($action === 'save_push_preferences') {
            $conn->beginTransaction();
            $save = $conn->prepare(
                'INSERT INTO user_notification_preferences (user_id, notification_type, push_enabled)
                 VALUES (?, ?, ?)
                 ON DUPLICATE KEY UPDATE push_enabled = VALUES(push_enabled), updated_at = CURRENT_TIMESTAMP'
            );
            foreach ($pushTypes as $type => $_details) {
                $enabled = isset($_POST['push_types'][$type]) ? 1 : 0;
                $save->execute([$userId, $type, $enabled]);
            }
            $conn->commit();
            $message = 'Deine Benachrichtigungseinstellungen wurden gespeichert.';
        } else {
            throw new RuntimeException('Unbekannte Aktion.');
        }
    }

    $stmt = $conn->prepare('SELECT id, username, full_name, email, created_at, is_active FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        http_response_code(404);
        exit('Benutzerkonto nicht gefunden.');
    }

    $prefsStmt = $conn->prepare('SELECT notification_type, push_enabled FROM user_notification_preferences WHERE user_id = ?');
    $prefsStmt->execute([$userId]);
    $savedPrefs = [];
    foreach ($prefsStmt->fetchAll(PDO::FETCH_ASSOC) as $pref) {
        $savedPrefs[$pref['notification_type']] = (int) $pref['push_enabled'];
    }
} catch (Throwable $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    error_log('Profilseite: ' . $e->getMessage());
    $error = $e instanceof RuntimeException ? $e->getMessage() : 'Die Daten konnten nicht verarbeitet werden. Bitte prüfe, ob die SQL-Migration bereits ausgeführt wurde.';
    $user = $user ?? null;
    if (!$user) {
        $stmt = $conn->prepare('SELECT id, username, full_name, email, created_at, is_active FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }
    $savedPrefs = $savedPrefs ?? [];
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mein Profil – Förderverein intern</title>
<link rel="stylesheet" href="styles.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="includes/navigation.css">
<link rel="icon" type="image/png" sizes="32x32" href="/intern/assets/icons/favicon.png">
<link rel="manifest" href="/intern/manifest.webmanifest">
<meta name="theme-color" content="#FFF500">
<style>
.profile-page{width:100%;max-width:none;box-sizing:border-box;margin:22px 0 40px}.profile-card{background:#fff;border:1px solid #e2e8eb;border-radius:14px;padding:22px;margin-bottom:18px;box-shadow:0 3px 12px rgba(30,45,55,.04)}.profile-card h2{margin:0 0 8px;color:#263640;font-size:20px}.profile-intro{margin:0 0 18px;color:#64747c;line-height:1.5}.profile-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}.profile-field{display:flex;flex-direction:column;gap:6px}.profile-field label{font-weight:600;color:#394c57}.profile-field input{box-sizing:border-box;width:100%;padding:11px 12px;border:1px solid #d5dfe3;border-radius:8px;font:inherit;color:#263640;background:#fff}.profile-field input[readonly]{background:#f4f6f7;color:#65747b}.profile-help{font-size:12px;color:#74828a}.profile-actions{display:flex;justify-content:flex-end;margin-top:18px}.profile-button{display:inline-flex;align-items:center;justify-content:center;gap:8px;border:1px solid #e6dc79;border-radius:8px;background:#f4ed9a;color:#303329;padding:10px 14px;font:inherit;font-weight:600;text-decoration:none;cursor:pointer}.profile-button-secondary{background:#fff;border-color:#d8e0e3;color:#53636b}.profile-message{padding:12px 15px;border-radius:8px;margin:16px 0}.profile-message.success{background:#eaf7ee;color:#21683b}.profile-message.error{background:#fff0ef;color:#9a302b}.profile-pref{display:flex;align-items:flex-start;gap:12px;padding:15px 0;border-bottom:1px solid #edf0f1}.profile-pref:last-of-type{border-bottom:0}.profile-pref input{width:19px;height:19px;margin-top:2px;accent-color:#6b8b59;flex:0 0 auto}.profile-pref strong{display:block;color:#263640}.profile-pref p{margin:4px 0 0;color:#65747b;font-size:14px;line-height:1.45}.profile-status{display:inline-block;padding:4px 9px;border-radius:20px;background:<?= !empty($user['is_active']) ? "'#e8f5eb'" : "'#fff0ef'" ?>;color:<?= !empty($user['is_active']) ? "'#21683b'" : "'#9a302b'" ?>;font-size:13px;font-weight:600}@media(max-width:640px){.profile-card{padding:16px}.profile-grid{grid-template-columns:1fr}.profile-actions{justify-content:stretch}.profile-actions .profile-button{width:100%}}
</style>
</head>
<body>
<div class="container">
<header class="app-page-header">
<h1>Mein Profil</h1>
<?php require __DIR__ . '/includes/navigation.php'; ?>
</header>
<main class="profile-page">
<?php if ($message !== ''): ?><div class="profile-message success" role="status"><i class="fas fa-check-circle" aria-hidden="true"></i> <?= profileH($message) ?></div><?php endif; ?>
<?php if ($error !== ''): ?><div class="profile-message error" role="alert"><i class="fas fa-exclamation-circle" aria-hidden="true"></i> <?= profileH($error) ?></div><?php endif; ?>

<section class="profile-card" aria-labelledby="profile-heading">
<h2 id="profile-heading"><i class="fas fa-user-circle" aria-hidden="true"></i> Persönliche Daten</h2>
<p class="profile-intro">Hier kannst du deine Kontaktdaten aktualisieren. Dein Benutzername kann nicht selbst geändert werden.</p>
<form method="post" action="profile.php">
<input type="hidden" name="csrf_token" value="<?= profileH($csrf) ?>">
<input type="hidden" name="action" value="save_profile">
<div class="profile-grid">
<div class="profile-field"><label for="username">Benutzername</label><input id="username" value="<?= profileH($user['username'] ?? '') ?>" readonly><span class="profile-help">Wird zur Anmeldung verwendet.</span></div>
<div class="profile-field"><label for="full_name">Name</label><input id="full_name" name="full_name" maxlength="255" required value="<?= profileH($user['full_name'] ?? '') ?>"></div>
<div class="profile-field"><label for="email">E-Mail-Adresse</label><input id="email" name="email" type="email" maxlength="254" required value="<?= profileH($user['email'] ?? '') ?>"></div>
<div class="profile-field"><label>Kontostatus</label><div><span class="profile-status"><?= !empty($user['is_active']) ? 'Aktiv' : 'Inaktiv' ?></span></div><span class="profile-help">Ein Konto kann nur durch die Administration aktiviert oder deaktiviert werden.</span></div>
<div class="profile-field"><label>Mitglied seit</label><input value="<?= profileH(isset($user['created_at']) ? date('d.m.Y', strtotime($user['created_at'])) : '') ?>" readonly></div>
</div>
<div class="profile-actions"><button class="profile-button" type="submit"><i class="fas fa-save" aria-hidden="true"></i> Profildaten speichern</button></div>
</form>
</section>

<section class="profile-card" id="push-einstellungen" aria-labelledby="push-heading">
<h2 id="push-heading"><i class="fas fa-bell" aria-hidden="true"></i> Push-Benachrichtigungen</h2>
<p class="profile-intro">Diese Auswahl gilt für dein Benutzerkonto auf allen Geräten. Ob Push auf einem bestimmten Gerät technisch aktiviert ist, stellst du separat auf der <a href="notifications.php">Benachrichtigungsseite</a> ein.</p>
<form method="post" action="profile.php#push-einstellungen">
<input type="hidden" name="csrf_token" value="<?= profileH($csrf) ?>">
<input type="hidden" name="action" value="save_push_preferences">
<?php foreach ($pushTypes as $type => $details): ?>
<label class="profile-pref">
<input type="checkbox" name="push_types[<?= profileH($type) ?>]" value="1" <?= ($savedPrefs[$type] ?? 1) === 1 ? 'checked' : '' ?>>
<span><strong><?= profileH($details['label']) ?></strong><p><?= profileH($details['description']) ?></p></span>
</label>
<?php endforeach; ?>
<div class="profile-actions"><button class="profile-button" type="submit"><i class="fas fa-save" aria-hidden="true"></i> Benachrichtigungseinstellungen speichern</button></div>
</form>
</section>
<p><a class="profile-button profile-button-secondary" href="notifications.php"><i class="fas fa-arrow-left" aria-hidden="true"></i> Zurück zu den Benachrichtigungen</a></p>
</main>
</div>
</body>
</html>
