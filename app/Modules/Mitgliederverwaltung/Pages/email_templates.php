<?php
if (!defined('APP_ENTRY_POINT')) { http_response_code(404); exit; }
$appRoot = dirname(__DIR__, 4);
if (session_status() === PHP_SESSION_NONE) session_start();
require_once $appRoot . '/auth.php';
checkPermission('manage_members');
require_once $appRoot . '/db_connection.php';
require_once $appRoot . '/app/Modules/Mitgliederverwaltung/Includes/mail_placeholders.php';

if (empty($_SESSION['member_templates_csrf'])) $_SESSION['member_templates_csrf'] = bin2hex(random_bytes(32));
$csrf = $_SESSION['member_templates_csrf'];
$error = '';
$notice = trim((string)($_GET['meldung'] ?? ''));
$editing = null;
$showNewForm = isset($_GET['neu']) && $_GET['neu'] === '1';
$recipientRules = [
    'all_active' => 'Alle aktiven Mitglieder',
    'active_no_end' => 'Aktive Mitglieder ohne Enddatum',
    'active_end_next_year' => 'Aktive Mitglieder mit Enddatum im nächsten Kalenderjahr',
    'active_end_this_year' => 'Aktive Mitglieder mit Enddatum im aktuellen Kalenderjahr',
    'active_or_terminated' => 'Aktive und gekündigte Mitglieder',
    'current_member' => 'Aktuelles Mitglied (z. B. für eine Kündigungsbestätigung)',
    'new_member' => 'Neues Mitglied (wird beim Anlegen eines Mitglieds ausgewählt)',
];
function mt_h($value): string { return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function mt_editor_html($value): string { $value=(string)$value; if (preg_match('/<(?:p|div|br|strong|b|em|i|u|ul|ol|li)\b/i',$value)) return sanitizeMemberMailHtml($value); return nl2br(mt_h($value), false); }

try {
    $db = (new Database())->getConnection();
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!hash_equals($csrf, (string)($_POST['csrf_token'] ?? ''))) throw new RuntimeException('Sitzung abgelaufen. Bitte lade die Seite neu.');
        $action = (string)($_POST['action'] ?? '');
        if ($action === 'save') {
            $id = (int)($_POST['template_id'] ?? 0);
            $name = trim((string)($_POST['template_name'] ?? ''));
            $subject = trim((string)($_POST['betreff'] ?? ''));
            $body = sanitizeMemberMailHtml((string)($_POST['mailtext'] ?? ''));
            $rule = (string)($_POST['recipient_rule'] ?? '');
            if ($name === '' || $subject === '' || $body === '') throw new RuntimeException('Bitte internen Namen, Betreff und Nachrichtentext ausfüllen.');
            if (mb_strlen($name) > 100 || mb_strlen($subject) > 255) throw new RuntimeException('Der interne Name darf maximal 100 und der Betreff maximal 255 Zeichen lang sein.');
            if (!isset($recipientRules[$rule])) throw new RuntimeException('Bitte eine gültige Empfängerregel auswählen.');
            $stmt = $db->prepare('SELECT id FROM email_templates WHERE template_name = ? AND id <> ?');
            $stmt->execute([$name, $id]);
            if ($stmt->fetchColumn()) throw new RuntimeException('Es existiert bereits eine Vorlage mit diesem internen Namen.');
            if ($id > 0) {
                $stmt = $db->prepare('UPDATE email_templates SET template_name=?, betreff=?, mailtext=?, recipient_rule=? WHERE id=?');
                $stmt->execute([$name, $subject, $body, $rule, $id]);
                $message = 'Vorlage wurde aktualisiert.';
            } else {
                $stmt = $db->prepare('INSERT INTO email_templates (template_name, betreff, mailtext, recipient_rule, is_default) VALUES (?, ?, ?, ?, 0)');
                $stmt->execute([$name, $subject, $body, $rule]);
                $message = 'Vorlage wurde angelegt.';
            }
            header('Location: mitglieder_email_vorlagen.php?meldung=' . rawurlencode($message));
            exit;
        }
        if ($action === 'delete') {
            $id = (int)($_POST['template_id'] ?? 0);
            if ($id < 1) throw new RuntimeException('Ungültige Vorlagen-ID.');
            $stmt = $db->prepare('DELETE FROM email_templates WHERE id=?');
            $stmt->execute([$id]);
            header('Location: mitglieder_email_vorlagen.php?meldung=' . rawurlencode('Vorlage wurde gelöscht.'));
            exit;
        }
        throw new RuntimeException('Unbekannte Aktion.');
    }

    if (isset($_GET['edit'])) {
        $stmt = $db->prepare('SELECT id, template_name, betreff, mailtext, recipient_rule FROM email_templates WHERE id=?');
        $stmt->execute([(int)$_GET['edit']]);
        $editing = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$editing) throw new RuntimeException('Vorlage nicht gefunden.');
        if (!isset($recipientRules[$editing['recipient_rule']])) $editing['recipient_rule'] = 'all_active';
    }
    $templates = $db->query('SELECT id, template_name, betreff, mailtext, recipient_rule, updated_at FROM email_templates ORDER BY template_name ASC, id ASC')->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    $error = $e instanceof RuntimeException ? $e->getMessage() : 'Die Vorlagen konnten nicht geladen oder gespeichert werden.';
    if (!($e instanceof RuntimeException)) error_log('Mitglieder-E-Mail-Vorlagen: ' . $e->getMessage());
    $templates = $templates ?? [];
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (($_POST['action'] ?? '') === 'save')) $showNewForm = true;
$placeholders = getAvailableMailPlaceholders();
$form = $editing ?: (($_SERVER['REQUEST_METHOD'] === 'POST' && (($_POST['action'] ?? '') === 'save'))
    ? ['id'=>(int)($_POST['template_id'] ?? 0),'template_name'=>(string)($_POST['template_name'] ?? ''),'betreff'=>(string)($_POST['betreff'] ?? ''),'mailtext'=>(string)($_POST['mailtext'] ?? ''),'recipient_rule'=>(string)($_POST['recipient_rule'] ?? 'all_active')]
    : ['id'=>0,'template_name'=>'','betreff'=>'','mailtext'=>'','recipient_rule'=>'all_active']);
?>
<!doctype html>
<html lang="de">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>E-Mail-Vorlagen – Förderverein intern</title>
<link rel="icon" type="image/png" sizes="32x32" href="/intern/assets/icons/favicon.png">
<link rel="manifest" href="/intern/manifest.webmanifest">
<link rel="stylesheet" href="styles.css"><link rel="stylesheet" href="includes/navigation.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
.mt-wrap{max-width:1250px;margin:24px auto}.mt-card{background:#fff;border-radius:12px;padding:20px;margin:18px 0;box-shadow:0 2px 12px #0000000d}.mt-head{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:14px}.mt-head h2{margin:0;font-size:1.15rem}.mt-btn{border:0;border-radius:7px;padding:9px 13px;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:7px;background:#e9edf2;color:#202938;font:inherit}.mt-btn.primary{background:var(--primary-color,#3498db);color:#fff}.mt-btn.danger{background:var(--danger-color,#e74c3c);color:#fff}.mt-actions{display:flex;gap:8px;flex-wrap:wrap}.mt-alert{padding:12px 15px;border-radius:8px;margin:12px 0;background:#e8f5e9}.mt-alert.error{background:#fdeaea;color:#8b1e1e}.mt-muted{color:#667085;font-size:.9rem}.mt-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}.mt-field{display:flex;flex-direction:column;gap:6px;min-width:0;font-weight:600}.mt-field.full{grid-column:1/-1}.mt-field input,.mt-field select,.mt-field textarea{box-sizing:border-box;width:100%;padding:10px;border:1px solid var(--border-color,#ddd);border-radius:7px;font:inherit;background:#fff}.mt-field textarea{min-height:220px;resize:vertical;line-height:1.5}.mt-editor{min-height:220px;max-height:600px;overflow:auto;padding:10px;border:1px solid var(--border-color,#ddd);border-radius:7px;background:#fff;font-weight:400;line-height:1.5;white-space:normal}.mt-editor:focus{outline:2px solid color-mix(in srgb,var(--primary-color,#3498db) 35%,transparent);border-color:var(--primary-color,#3498db)}.mt-editor p{margin:.4em 0}.mt-toolbar{display:flex;gap:6px;flex-wrap:wrap;margin:6px 0}.mt-toolbar button{min-width:34px;padding:6px 9px;border:1px solid #cbd5e1;border-radius:6px;background:#f8fafc;cursor:pointer}.mt-toolbar button:hover{background:#eaf2f8}.mt-placeholder-list{display:flex;flex-wrap:wrap;gap:7px;margin:8px 0}.mt-token{border:1px solid #cbd5e1;background:#f8fafc;border-radius:6px;padding:6px 9px;cursor:pointer;font: .85rem ui-monospace,monospace}.mt-token:hover{background:#eaf2f8}.mt-hint{font-size:.85rem;color:#667085;font-weight:400}.mt-table{width:100%;border-collapse:collapse}.mt-table th,.mt-table td{padding:11px 12px;border-bottom:1px solid #e8e8e8;text-align:left;vertical-align:top}.mt-table th{color:#34445a}.mt-table td:nth-child(2){min-width:200px}.mt-table td:nth-child(3){min-width:220px}.mt-table .mt-row-actions{white-space:nowrap}.mt-rule{display:inline-block;padding:4px 8px;border-radius:20px;background:#edf2f7;font-size:.82rem}@media(max-width:700px){.mt-card{padding:13px}.mt-grid{grid-template-columns:1fr}.mt-field.full{grid-column:auto}.mt-table th,.mt-table td{padding:9px 7px;font-size:.88rem}.mt-table td:nth-child(3){min-width:160px}}
</style>
</head>
<body><div class="container"><header class="app-page-header"><h1>E-Mail-Vorlagen</h1><?php require $appRoot . '/includes/navigation.php'; ?></header><main class="mt-wrap">
<?php if($error):?><div class="mt-alert error"><?=mt_h($error)?></div><?php endif;?>
<?php if($notice):?><div class="mt-alert"><?=mt_h($notice)?></div><?php endif;?>
<?php if ($editing || $showNewForm): ?>
<section class="mt-card">
<div class="mt-head"><h2><?= $editing ? 'Vorlage bearbeiten' : 'Neue Vorlage anlegen' ?></h2><?php if($editing):?><a class="mt-btn" href="mitglieder_email_vorlagen.php">Abbrechen</a><?php endif;?></div>
<form method="post" id="templateForm"><input type="hidden" name="csrf_token" value="<?=mt_h($csrf)?>"><input type="hidden" name="action" value="save"><input type="hidden" name="template_id" value="<?=mt_h($form['id'])?>">
<div class="mt-grid">
<label class="mt-field">Interner Name<input name="template_name" maxlength="100" required value="<?=mt_h($form['template_name'])?>" placeholder="z. B. Weihnachtsgruß"></label>
<label class="mt-field">Empfängerregel<select name="recipient_rule" required><?php foreach($recipientRules as $key=>$label):?><option value="<?=mt_h($key)?>"<?=($form['recipient_rule']===$key?' selected':'')?>><?=mt_h($label)?></option><?php endforeach;?></select><span class="mt-hint">Die Auswahl wird beim Erstellen einer E-Mail vorbelegt und kann dort geändert werden.</span></label>
<label class="mt-field full">Betreff<input id="mailSubject" name="betreff" maxlength="255" required value="<?=mt_h($form['betreff'])?>"></label>
<div class="mt-field full"><span>Verfügbare Platzhalter</span><div class="mt-placeholder-list"><?php foreach($placeholders as $key=>$meta):?><button type="button" class="mt-token" data-token="{{<?=mt_h($key)?>}}" title="<?=mt_h($meta['description'])?>">{{<?=mt_h($key)?>}}</button><?php endforeach;?></div><span class="mt-hint">Platzhalter können in Betreff und Nachricht eingefügt werden. Die Personalisierung wird beim Versand automatisch erkannt.</span></div>
<div class="mt-field full"><span>Nachrichtentext</span><div class="mt-toolbar" role="toolbar" aria-label="Textformatierung"><button type="button" data-command="bold" title="Fett"><strong>B</strong></button><button type="button" data-command="italic" title="Kursiv"><em>I</em></button><button type="button" data-command="underline" title="Unterstrichen"><u>U</u></button><button type="button" data-command="insertUnorderedList" title="Aufzählung">• Liste</button><button type="button" data-command="insertOrderedList" title="Nummerierte Liste">1. Liste</button></div><div id="mailBody" class="mt-editor" contenteditable="true" role="textbox" aria-multiline="true"><?=mt_editor_html($form['mailtext'])?></div><textarea id="mailBodyValue" name="mailtext" hidden></textarea><span class="mt-hint">Text markieren und über die Leiste formatieren. Erlaubt sind Fett, Kursiv, Unterstreichen und Listen.</span></div>
</div><div class="mt-actions" style="margin-top:14px"><button class="mt-btn primary" type="submit"><i class="fa-solid fa-floppy-disk"></i> <?= $editing ? 'Änderungen speichern' : 'Vorlage speichern' ?></button><?php if($editing):?><a class="mt-btn" href="mitglieder_email_vorlagen.php">Abbrechen</a><?php endif;?></div>
</form>
</section>
<?php endif; ?>
<section class="mt-card"><div class="mt-head"><h2>Gespeicherte Vorlagen</h2><div class="mt-actions"><span class="mt-muted"><?=count($templates)?> Vorlage(n)</span><?php if (!$editing && !$showNewForm): ?><a class="mt-btn primary" href="mitglieder_email_vorlagen.php?neu=1"><i class="fa-solid fa-plus" aria-hidden="true"></i> Neue E-Mail Vorlage anlegen</a><?php endif; ?></div></div>
<?php if(!$templates):?><p class="mt-muted">Es sind noch keine E-Mail-Vorlagen vorhanden.</p><?php else:?><div style="overflow-x:auto"><table class="mt-table"><thead><tr><th>Interner Name</th><th>Betreff</th><th>Empfängerregel</th><th>Zuletzt geändert</th><th>Aktionen</th></tr></thead><tbody>
<?php foreach($templates as $template):?><tr><td><strong><?=mt_h($template['template_name'])?></strong></td><td><?=mt_h($template['betreff'])?></td><td><span class="mt-rule"><?=mt_h($recipientRules[$template['recipient_rule']]??$template['recipient_rule'])?></span></td><td><?=!empty($template['updated_at'])?date('d.m.Y H:i',strtotime($template['updated_at'])):'–'?></td><td class="mt-row-actions"><div class="mt-actions"><a class="mt-btn" href="mitglieder_email_vorlagen.php?edit=<?=(int)$template['id']?>" title="Vorlage bearbeiten" aria-label="Vorlage bearbeiten"><i class="fa-solid fa-pen"></i></a><form method="post" onsubmit="return confirm('Diese E-Mail-Vorlage wirklich löschen?')"><input type="hidden" name="csrf_token" value="<?=mt_h($csrf)?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="template_id" value="<?=(int)$template['id']?>"><button class="mt-btn danger" type="submit" title="Vorlage löschen" aria-label="Vorlage löschen"><i class="fa-solid fa-trash"></i></button></form></div></td></tr><?php endforeach;?>
</tbody></table></div><?php endif;?></section>
</main></div>
<script>
const mailEditor=document.getElementById('mailBody');if(mailEditor){
let lastFocusedMailField=document.getElementById('mailSubject');
function insertIntoEditor(token){mailEditor.focus();const sel=window.getSelection();if(!sel||!sel.rangeCount){mailEditor.append(document.createTextNode(token));return;}const range=sel.getRangeAt(0);if(!mailEditor.contains(range.commonAncestorContainer)){mailEditor.append(document.createTextNode(token));return;}range.deleteContents();const node=document.createTextNode(token);range.insertNode(node);range.setStartAfter(node);range.setEndAfter(node);sel.removeAllRanges();sel.addRange(range);}
['mailSubject','mailBody'].forEach(id=>document.getElementById(id).addEventListener('focus',event=>lastFocusedMailField=event.target));
document.querySelectorAll('.mt-token').forEach(button=>button.addEventListener('mousedown',e=>e.preventDefault()));document.querySelectorAll('.mt-token').forEach(button=>button.addEventListener('click',()=>{if(lastFocusedMailField===mailEditor)insertIntoEditor(button.dataset.token);else{const f=document.getElementById('mailSubject'),start=f.selectionStart??f.value.length,end=f.selectionEnd??f.value.length;f.value=f.value.slice(0,start)+button.dataset.token+f.value.slice(end);f.focus();f.setSelectionRange(start+button.dataset.token.length,start+button.dataset.token.length);}}));
document.querySelectorAll('.mt-toolbar button').forEach(button=>button.addEventListener('mousedown',e=>e.preventDefault()));document.querySelectorAll('.mt-toolbar button').forEach(button=>button.addEventListener('click',()=>{mailEditor.focus();document.execCommand(button.dataset.command,false,null);}));
document.getElementById('templateForm').addEventListener('submit',()=>{document.getElementById('mailBodyValue').value=mailEditor.innerHTML;});

}
</script></body></html>
