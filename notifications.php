<?php
require_once __DIR__ . '/auth.php';
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
require_once __DIR__ . '/db_connection.php';
require_once __DIR__ . '/app/Core/Notifications.php';
$db = new Database(); $conn = $db->getConnection(); $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$userId = (int)$_SESSION['user_id'];
if (empty($_SESSION['notifications_csrf'])) $_SESSION['notifications_csrf'] = bin2hex(random_bytes(32));
$csrf = $_SESSION['notifications_csrf'];
if (empty($_SESSION['webpush_csrf'])) $_SESSION['webpush_csrf'] = bin2hex(random_bytes(32));
$webpushCsrf = $_SESSION['webpush_csrf'];
try { fvSyncNotifications($conn); } catch (Throwable $e) { error_log('Benachrichtigungen: '.$e->getMessage()); }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($csrf, (string)$_POST['csrf_token'])) { http_response_code(400); exit('Sitzung abgelaufen. Bitte neu laden.'); }
    $id = (int)($_POST['notification_id'] ?? 0); $action = (string)($_POST['notification_action'] ?? '');
    if ($id > 0 && in_array($action, ['read','done'], true)) {
        $q = $conn->prepare("UPDATE notifications SET is_read=1, read_at=COALESCE(read_at,NOW()), status=IF(?='done','done',status), completed_at=IF(?='done',COALESCE(completed_at,NOW()),completed_at) WHERE id=? AND user_id=?");
        $q->execute([$action,$action,$id,$userId]);
    } elseif ($action === 'read_all') {
        $q = $conn->prepare('UPDATE notifications SET is_read=1, read_at=COALESCE(read_at,NOW()) WHERE user_id=? AND is_read=0'); $q->execute([$userId]);
    }
    header('Location: notifications.php'); exit;
}
// Sichtbarkeit wird für jeden Request aus den aktuellen Rechten abgeleitet.
$canSeeTasks = fvNotificationCanSee($conn, $userId, 'ProjectManagement');
$canManageContacts = fvNotificationCanSee($conn, $userId, 'add_contacts') || fvNotificationCanSee($conn, $userId, 'edit_contacts');
$conditions = ['n.user_id=?']; $params = [$userId];
if (!$canSeeTasks) $conditions[] = "n.notification_type<>'task_overdue'";
if (!$canManageContacts) $conditions[] = "n.notification_type<>'contact_suggestion'";
$sql = 'SELECT n.* FROM notifications n WHERE '.implode(' AND ', $conditions).' ORDER BY (n.status=\'open\') DESC, n.is_read ASC, n.created_at DESC';
$q = $conn->prepare($sql); $q->execute($params); $notifications = $q->fetchAll(PDO::FETCH_ASSOC);
$unread = 0; foreach ($notifications as $n) if (!(int)$n['is_read']) $unread++;
function nh($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html><html lang="de"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Benachrichtigungen – Förderverein intern</title><link rel="manifest" href="/intern/manifest.webmanifest"><meta name="theme-color" content="#FFF500"><meta name="apple-mobile-web-app-capable" content="yes"><meta name="apple-mobile-web-app-title" content="Förderverein"><link rel="apple-touch-icon" href="/intern/assets/icons/apple-touch-icon.png"><meta name="webpush-csrf" content="<?=nh($webpushCsrf)?>"><link rel="stylesheet" href="styles.css"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"><link rel="stylesheet" href="includes/navigation.css"><link rel="icon" type="image/png" sizes="32x32" href="/intern/assets/icons/favicon.png"></head><body><div class="container"><header class="app-page-header"><h1>Benachrichtigungen</h1><?php require __DIR__.'/includes/navigation.php'; ?></header><main class="notifications-page">
<section class="webpush-card" aria-labelledby="webpush-heading">
  <div class="webpush-card-copy">
    <h2 id="webpush-heading"><i class="fas fa-bell" aria-hidden="true"></i> Push-Benachrichtigungen</h2>
    <p>Erhalte wichtige Hinweise auch dann, wenn Förderverein intern gerade nicht geöffnet ist.</p>
    <p class="webpush-note">Auf dem iPhone funktioniert Push ab iOS 16.4, wenn diese App über das Symbol auf dem Home-Bildschirm geöffnet wurde.</p>
  </div>
  <div class="webpush-actions">
    <button type="button" id="webpush-enable" class="webpush-button">Push-Benachrichtigungen aktivieren</button>
    <button type="button" id="webpush-disable" class="webpush-button webpush-button-secondary" hidden>Auf diesem Gerät deaktivieren</button>
    <button type="button" id="webpush-test" class="webpush-button webpush-button-secondary" hidden>Testnachricht senden</button>
  </div>
  <p class="webpush-preferences-link"><a href="profile.php#push-einstellungen"><i class="fas fa-sliders" aria-hidden="true"></i> Benachrichtigungen konfigurieren</a></p>
  <p id="webpush-status" class="webpush-status" role="status" aria-live="polite">Status wird geprüft …</p>
</section>
<div class="notifications-toolbar"><p><?= $unread ?> ungelesen · <?= count(array_filter($notifications, fn($n)=>$n['status']==='open')) ?> offen</p><form method="post"><input type="hidden" name="csrf_token" value="<?=nh($csrf)?>"><button type="submit" name="notification_action" value="read_all" class="notifications-secondary">Alle als gelesen markieren</button></form></div>
<?php if (!$notifications): ?><section class="notifications-empty-state"><div class="notifications-empty-icon"><i class="fas fa-bell" aria-hidden="true"></i></div><h2>Keine Benachrichtigungen</h2><p>Aktuell gibt es keine Benachrichtigungen, die für dich sichtbar sind.</p></section><?php else: ?><div class="notifications-list"><?php foreach($notifications as $n): $isOpen=$n['status']==='open'; ?><article class="notification-card <?= !$n['is_read']?'is-unread':'' ?> <?= $isOpen?'is-open':'is-done' ?>"><div class="notification-card-icon <?= $n['notification_type']==='task_overdue'?'is-overdue':'is-contact' ?>"><i class="fas <?= $n['notification_type']==='task_overdue'?'fa-clock':'fa-address-book' ?>" aria-hidden="true"></i></div><div class="notification-card-content"><div class="notification-card-meta"><strong><?=nh($n['title'])?></strong><span><?= $isOpen?'Offen':'Erledigt' ?> · <?=nh(date('d.m.Y H:i',strtotime($n['created_at'])))?></span></div><p><?=nh($n['message'])?></p><div class="notification-card-actions"><a class="notifications-primary" href="<?=nh($n['target_url'])?>"><?= $n['notification_type']==='task_overdue'?'Aufgabe öffnen':'Kontaktvorschläge prüfen' ?> <i class="fas fa-arrow-right" aria-hidden="true"></i></a><?php if(!(int)$n['is_read']):?><form method="post"><input type="hidden" name="csrf_token" value="<?=nh($csrf)?>"><input type="hidden" name="notification_id" value="<?=(int)$n['id']?>"><button class="notifications-secondary" name="notification_action" value="read">Als gelesen markieren</button></form><?php endif;?><?php if($isOpen):?><form method="post"><input type="hidden" name="csrf_token" value="<?=nh($csrf)?>"><input type="hidden" name="notification_id" value="<?=(int)$n['id']?>"><button class="notifications-secondary" name="notification_action" value="done">Erledigt markieren</button></form><?php endif;?></div></div></article><?php endforeach;?></div><?php endif; ?>
</main></div><style>
.notifications-page{width:100%;max-width:none;margin:0;box-sizing:border-box}.notifications-toolbar{display:flex;align-items:center;justify-content:space-between;gap:12px;margin:24px 0 14px;color:#65747b}.notifications-list{display:grid;gap:12px}.notification-card{display:flex;gap:16px;padding:18px;background:#fff;border:1px solid #e2e8ea;border-radius:12px;box-shadow:0 3px 12px rgba(30,45,55,.04)}.notification-card.is-unread{border-left:4px solid #d74b43;background:#fffdfd}.notification-card.is-done{opacity:.78}.notification-card-icon{flex:0 0 42px;height:42px;display:flex;align-items:center;justify-content:center;border-radius:50%;background:#f7e9e7;color:#b83b35}.notification-card-icon.is-contact{background:#fff5d5;color:#8a6b00}.notification-card-content{min-width:0;flex:1}.notification-card-meta{display:flex;justify-content:space-between;gap:12px;align-items:baseline}.notification-card-meta strong{color:#263640}.notification-card-meta span{font-size:12px;color:#7b878d;white-space:nowrap}.notification-card p{margin:8px 0 12px;color:#53636b;line-height:1.5}.notification-card-actions{display:flex;gap:8px;flex-wrap:wrap;align-items:center}.notification-card-actions form,.notifications-toolbar form{margin:0}.notifications-primary,.notifications-secondary{display:inline-flex;align-items:center;gap:7px;border-radius:7px;padding:8px 11px;font:inherit;font-size:13px;text-decoration:none;cursor:pointer}.notifications-primary{background:#f4ed9a;color:#303329;border:1px solid #e6dc79}.notifications-secondary{background:#fff;color:#53636b;border:1px solid #d8e0e3}.notifications-empty-state{text-align:center;padding:38px 24px;background:#fff;border:1px solid #e3e9eb;border-radius:14px}.notifications-empty-icon{display:inline-flex;width:58px;height:58px;align-items:center;justify-content:center;border-radius:50%;background:#eef4f6;color:#577c8b;font-size:24px}.notifications-empty-state h2{margin:16px 0 8px;color:#263640}.notifications-empty-state p{color:#667780}@media(max-width:640px){.notification-card{padding:14px;gap:11px}.notification-card-meta{display:block}.notification-card-meta span{display:block;margin-top:4px}.notifications-toolbar{align-items:flex-start;flex-direction:column}}

.webpush-card{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:14px 24px;margin:20px 0 24px;padding:18px 20px;background:#fff;border:1px solid #e1e8eb;border-radius:12px;box-shadow:0 3px 12px rgba(30,45,55,.04)}
.webpush-card-copy{flex:1 1 280px;min-width:0}.webpush-card h2{margin:0 0 7px;color:#263640;font-size:18px}.webpush-card h2 i{margin-right:7px;color:#8a6b00}.webpush-card p{margin:6px 0;color:#53636b;line-height:1.45}.webpush-note{font-size:12px;color:#667780!important}.webpush-actions{display:flex;gap:8px;flex-wrap:wrap}.webpush-button{display:inline-flex;align-items:center;justify-content:center;border:1px solid #e6dc79;border-radius:7px;background:#f4ed9a;color:#303329;padding:9px 12px;font:inherit;font-size:13px;font-weight:600;cursor:pointer}.webpush-button-secondary{background:#fff;border-color:#d8e0e3;color:#53636b}.webpush-button:disabled{opacity:.6;cursor:wait}.webpush-status{flex-basis:100%;margin:0!important;font-size:13px}.webpush-status.is-error{color:#a83232!important}.webpush-status.is-success{color:#247344!important}.webpush-button[hidden]{display:none!important}
@media(max-width:640px){.webpush-card{align-items:stretch;padding:15px}.webpush-actions{display:grid;grid-template-columns:1fr}.webpush-button{width:100%;box-sizing:border-box}}

.webpush-preferences-link{flex-basis:100%;margin:0!important;font-size:13px}.webpush-preferences-link a{color:#53636b;text-decoration:none}.webpush-preferences-link a:hover{text-decoration:underline;color:#244f62}
</style><script src="js/webpush.js" defer></script></body></html>
