<?php
// users.php - Benutzerverwaltung für Administratoren

// Session nur starten wenn nicht bereits gestartet
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db_connection.php';
require_once 'auth.php';

checkPermission('manage_users');

$message = '';
$error = '';

// Benutzer aktivieren/deaktivieren oder Rolle ändern
if (isset($_GET['action']) && isset($_GET['id'])) {
    $user_id = intval($_GET['id']);
    
    try {
        $database = new Database();
        $conn = $database->getConnection();
        
        if ($_GET['action'] === 'activate') {
            $stmt = $conn->prepare("UPDATE users SET is_active = TRUE WHERE id = ?");
            $stmt->execute([$user_id]);
            
            // Benutzerdaten für E-Mail holen
            $stmt = $conn->prepare("SELECT username, email, full_name FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                // E-Mail an den Benutzer senden
                $emailSent = sendActivationNotification($user['username'], $user['email'], $user['full_name']);
                
                if ($emailSent) {
                    $message = "Benutzer wurde aktiviert und per E-Mail benachrichtigt";
                } else {
                    $message = "Benutzer wurde aktiviert, aber E-Mail konnte nicht gesendet werden";
                }
            } else {
                $message = "Benutzer wurde aktiviert";
            }
            
        } elseif ($_GET['action'] === 'deactivate') {
            $stmt = $conn->prepare("UPDATE users SET is_active = FALSE WHERE id = ?");
            $stmt->execute([$user_id]);
            $message = "Benutzer wurde deaktiviert";
        } elseif ($_GET['action'] === 'delete') {
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND id != ?");
            $stmt->execute([$user_id, $_SESSION['user_id']]);
            $message = "Benutzer wurde gelöscht";
        } elseif ($_GET['action'] === 'update_role' && isset($_GET['role'])) {
            $new_role = $_GET['role'];
            
            // Alte Rollen entfernen
            $stmt = $conn->prepare("DELETE FROM user_roles WHERE user_id = ?");
            $stmt->execute([$user_id]);
            
            // Neue Rolle zuweisen
            $stmt = $conn->prepare("
                INSERT INTO user_roles (user_id, role_id) 
                SELECT ?, id FROM roles WHERE role_name = ?
            ");
            $stmt->execute([$user_id, $new_role]);
            
            $message = "Rolle wurde aktualisiert";
        }
    } catch (Exception $e) {
        $error = "Fehler: " . $e->getMessage();
    }
}

/**
 * Sendet eine Benachrichtigung an den Benutzer über die Freischaltung
 */
function sendActivationNotification($username, $email, $full_name) {
    try {
        $subject = "Ihr Account wurde freigeschaltet - Spendenkontakte System";
        
        $message = "Hallo " . ($full_name ?: $username) . ",\n\n";
        $message .= "Dein Account für das Spendenkontakte-Verwaltungssystem wurde soeben freigeschaltet.\n\n";
        $message .= "Du kannst Dich jetzt mit Deinen Zugangsdaten anmelden:\n";
        $message .= "Benutzername: " . $username . "\n";
        $message .= "Login-Seite: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/login.php\n\n";
        $message .= "Nach dem Login hast Du Zugriff auf das System und kannst:\n";
        $message .= "- Neue Spendenkontakte vorschlagen\n";
        $message .= "- Kontakte mit Kommentaren versehen\n";
		$message .= "- Lokale Kontakte als angefragt markieren\n";
        $message .= "- Den Status von Spendenanfragen ändern und verfolgen\n\n";
        $message .= "Solltest Du Fragen haben oder Probleme beim Login auftreten, melde Dich bitte beim Team vom Förderverein.\n\n";
        $message .= "Mit freundlichen Grüßen\n";
        $message .= "Dein Team vom Förderverein\n\n";
        $message .= "Diese E-Mail wurde automatisch generiert.";
        
        $headers = "From: foerderverein@evkigarumeln.de\r\n";
        $headers .= "Reply-To: foerderverein@evkigarumeln.de\r\n";
        $headers .= "Content-Type: text/plain; charset=utf-8\r\n";
        
        $mailSent = mail($email, $subject, $message, $headers);
        
        if ($mailSent) {
            error_log("Aktivierungs-Benachrichtigung erfolgreich gesendet an: " . $email);
            return true;
        } else {
            error_log("Fehler beim Senden der Aktivierungs-Benachrichtigung an: " . $email);
            return false;
        }
        
    } catch (Exception $e) {
        error_log("Fehler beim Senden der Aktivierungs-Benachrichtigung: " . $e->getMessage());
        return false;
    }
}

// Benutzer und verfügbare Rollen laden
try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Benutzer mit Rollen laden
    $stmt = $conn->prepare("
        SELECT u.*, GROUP_CONCAT(r.role_name) as roles 
        FROM users u 
        LEFT JOIN user_roles ur ON u.id = ur.user_id 
        LEFT JOIN roles r ON ur.role_id = r.id 
        GROUP BY u.id 
        ORDER BY u.created_at DESC
    ");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Verfügbare Rollen laden
    $stmt = $conn->prepare("SELECT role_name, description FROM roles ORDER BY role_name");
    $stmt->execute();
    $available_roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $error = "Fehler beim Laden der Benutzer: " . $e->getMessage();
    $users = [];
    $available_roles = [];
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Benutzerverwaltung - Spendenkontakte</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .user-actions {
            display: flex;
            gap: 8px;
            flex-wrap: nowrap;
            justify-content: center;
            align-items: center;
        }
        
        .action-btn {
            padding: 6px;
            font-size: 14px;
            text-align: center;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }
        
        .action-btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }
        
        .toggle-btn {
            background-color: #6c757d;
            color: white;
        }
        
        .toggle-btn.active {
            background-color: var(--success-color);
            color: white;
        }
        
        .delete-btn {
            background-color: var(--danger-color);
            color: white;
        }
        
        .role-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.8em;
            font-weight: 600;
            margin: 2px;
        }
        .role-admin { background-color: #e3f2fd; color: #1976d2; }
        .role-manager { background-color: #f3e5f5; color: #7b1fa2; }
        .role-user { background-color: #e8f5e8; color: #388e3c; }
        
        .role-select {
            padding: 6px 10px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            font-size: 0.9em;
            width: 100%;
            margin-bottom: 5px;
        }
        
        .role-form {
            display: flex;
            gap: 5px;
            align-items: center;
        }
        
        .user-status {
            font-weight: 600;
            padding: 6px 10px;
            border-radius: 4px;
            display: inline-block;
            min-width: 100px;
            text-align: center;
        }
        
        .status-active { background-color: rgba(46, 204, 113, 0.2); color: #155724; }
        .status-inactive { background-color: rgba(231, 76, 60, 0.2); color: #721c24; }
        
        .user-info {
            display: flex;
            flex-direction: column;
        }
        
        .user-name {
            font-weight: 600;
            margin-bottom: 4px;
        }
        
        .user-email {
            font-size: 0.9em;
            color: #666;
        }
        
        .actions-header {
            text-align: center;
        }
        
        .own-account-notice {
            color: #666;
            font-size: 0.8em;
            text-align: center;
            padding: 8px;
            display: block;
            font-style: italic;
        }
        
        .actions-cell {
            text-align: center;
            vertical-align: middle;
        }
        
        .header-links {
            font-size: 14px;
        }
        .header-links a {
            color: var(--primary-color);
            text-decoration: none;
        }
        .header-links a:hover {
            text-decoration: underline;
        }
        
        .email-notification {
            background-color: rgba(52, 152, 219, 0.1);
            border-left: 4px solid var(--primary-color);
            padding: 10px 15px;
            margin: 10px 0;
            border-radius: 4px;
            font-size: 0.9em;
        }
    </style>
    <link rel="stylesheet" href="includes/navigation.css">
</head>
<body>
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1><i class="fas fa-users-cog"></i> Benutzerverwaltung</h1>
            <?php require __DIR__ . '/includes/navigation.php'; ?>
        </div>
        
        <?php if ($message): ?>
            <div class="notification success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="notification error">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <div class="info-text" style="margin-bottom: 20px; background-color: #e8f4fd; padding: 15px; border-radius: 4px;">
            <i class="fas fa-info-circle"></i> Hier können Sie Benutzerkonten verwalten, freischalten und Rollen zuweisen.
        </div>

        <div class="email-notification">
            <i class="fas fa-envelope"></i> <strong>E-Mail-Benachrichtigung:</strong> 
            Beim Aktivieren eines Benutzers wird automatisch eine E-Mail mit den Login-Daten an den Benutzer gesendet.
        </div>
        
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
                <tr>
                    <th style="padding: 12px 15px; text-align: left; border-bottom: 1px solid var(--border-color); background-color: var(--light-gray);">ID</th>
                    <th style="padding: 12px 15px; text-align: left; border-bottom: 1px solid var(--border-color); background-color: var(--light-gray);">Benutzer</th>
                    <th style="padding: 12px 15px; text-align: left; border-bottom: 1px solid var(--border-color); background-color: var(--light-gray);">Rolle</th>
                    <th style="padding: 12px 15px; text-align: left; border-bottom: 1px solid var(--border-color); background-color: var(--light-gray); width: 120px;">Status</th>
                    <th style="padding: 12px 15px; text-align: left; border-bottom: 1px solid var(--border-color); background-color: var(--light-gray);">Erstellt am</th>
                    <th style="padding: 12px 15px; text-align: left; border-bottom: 1px solid var(--border-color); background-color: var(--light-gray);">Kommentar</th>
                    <th style="padding: 12px 15px; text-align: center; border-bottom: 1px solid var(--border-color); background-color: var(--light-gray); width: 180px; min-width: 180px;" class="actions-header">Aktionen</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $user): 
                        $current_role = !empty($user['roles']) ? explode(',', $user['roles'])[0] : 'user';
                    ?>
                    <tr>
                        <td style="padding: 12px 15px; border-bottom: 1px solid var(--border-color);"><?php echo $user['id']; ?></td>
                        <td style="padding: 12px 15px; border-bottom: 1px solid var(--border-color);">
                            <div class="user-info">
                                <div class="user-name">
                                    <?php echo htmlspecialchars($user['username']); ?>
                                    <?php if ($user['id'] == $_SESSION['user_id']): ?>
                                        <span style="color: var(--primary-color); font-size: 0.8em;">(aktuell)</span>
                                    <?php endif; ?>
                                </div>
                                <div class="user-email">
                                    <?php echo htmlspecialchars($user['email']); ?>
                                </div>
                                <div style="font-size: 0.9em; color: #333; margin-top: 4px;">
                                    <?php echo htmlspecialchars($user['full_name']); ?>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 12px 15px; border-bottom: 1px solid var(--border-color);">
                            <form method="GET" action="users.php" class="role-form">
                                <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                <input type="hidden" name="action" value="update_role">
                                <select name="role" class="role-select" onchange="this.form.submit()">
                                    <?php foreach ($available_roles as $role): ?>
                                        <option value="<?php echo htmlspecialchars($role['role_name']); ?>" 
                                            <?php echo $current_role === $role['role_name'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($role['role_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                            <span class="role-badge role-<?php echo $current_role; ?>">
                                <?php echo htmlspecialchars($current_role); ?>
                            </span>
                        </td>
                        <td style="padding: 12px 15px; border-bottom: 1px solid var(--border-color);">
                            <?php if ($user['is_active']): ?>
                                <span class="user-status status-active">
                                    <i class="fas fa-check-circle"></i> Aktiv
                                </span>
                            <?php else: ?>
                                <span class="user-status status-inactive">
                                    <i class="fas fa-times-circle"></i> Inaktiv
                                </span>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 12px 15px; border-bottom: 1px solid var(--border-color);">
                            <?php echo date('d.m.Y H:i', strtotime($user['created_at'])); ?>
                        </td>
                        <td style="padding: 12px 15px; border-bottom: 1px solid var(--border-color); max-width: 200px;">
                            <?php echo !empty($user['comment']) ? nl2br(htmlspecialchars($user['comment'])) : '<span style="color: #666; font-style: italic;">Kein Kommentar</span>'; ?>
                        </td>
                        <td class="actions-cell" style="padding: 12px 15px; border-bottom: 1px solid var(--border-color);">
                            <div class="user-actions">
                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                    <!-- Aktivieren/Deaktivieren Schalter -->
                                    <?php if ($user['is_active']): ?>
                                        <a href="users.php?action=deactivate&id=<?php echo $user['id']; ?>" 
                                           class="action-btn toggle-btn active"
                                           title="Benutzer deaktivieren"
                                           onclick="return confirm('Benutzer <?php echo htmlspecialchars($user['username']); ?> wirklich deaktivieren?')">
                                            <i class="fas fa-toggle-on"></i>
                                        </a>
                                    <?php else: ?>
                                        <a href="users.php?action=activate&id=<?php echo $user['id']; ?>" 
                                           class="action-btn toggle-btn"
                                           title="Benutzer aktivieren"
                                           onclick="return confirm('Benutzer <?php echo htmlspecialchars($user['username']); ?> wirklich aktivieren?\n\nDem Benutzer wird eine Aktivierungs-E-Mail gesendet.')">
                                            <i class="fas fa-toggle-off"></i>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <!-- Löschen Button -->
                                    <a href="users.php?action=delete&id=<?php echo $user['id']; ?>" 
                                       class="action-btn delete-btn"
                                       title="Benutzer löschen"
                                       onclick="return confirm('Benutzer <?php echo htmlspecialchars($user['username']); ?> wirklich LÖSCHEN? Diese Aktion kann nicht rückgängig gemacht werden!')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                <?php else: ?>
                                    <span class="own-account-notice">
                                        <i class="fas fa-user"></i>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="padding: 20px; text-align: center; color: #666;">
                            <i class="fas fa-users-slash" style="font-size: 2em; margin-bottom: 10px; display: block;"></i>
                            Keine Benutzer gefunden
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <div style="margin-top: 30px; padding: 15px; background-color: #f8f9fa; border-radius: 4px;">
            <h4><i class="fas fa-info-circle"></i> Informationen zur Benutzerverwaltung</h4>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li><strong>Rolle ändern</strong>: Dropdown-Menü verwenden um Benutzer-Rollen zu ändern</li>
                <li><strong>Aktivieren/Deaktivieren</strong>: Schalter-Button (grau=inaktiv, grün=aktiv)</li>
                <li><strong>Löschen</strong>: Mülleimer-Button entfernt Benutzer komplett</li>
                <li>Der eigene Account kann nicht gelöscht oder deaktiviert werden</li>
                <li>Neue Benutzer registrieren sich über den "Account beantragen" Link im Login</li>
                <li><strong>E-Mail-Benachrichtigung</strong>: Beim Aktivieren wird automatisch eine E-Mail mit Login-Daten gesendet</li>
            </ul>
            
            <h5>Verfügbare Rollen:</h5>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <?php foreach ($available_roles as $role): ?>
                    <li><strong><?php echo htmlspecialchars($role['role_name']); ?>:</strong> <?php echo htmlspecialchars($role['description']); ?></li>
                <?php endforeach; ?>
            </ul>
            
            <h5>Action-Buttons Erklärung:</h5>
            <div style="display: flex; gap: 15px; margin-top: 10px; align-items: center;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <button class="action-btn toggle-btn" style="pointer-events: none;">
                        <i class="fas fa-toggle-off"></i>
                    </button>
                    <span>Benutzer aktivieren</span>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <button class="action-btn toggle-btn active" style="pointer-events: none;">
                        <i class="fas fa-toggle-on"></i>
                    </button>
                    <span>Benutzer deaktivieren</span>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <button class="action-btn delete-btn" style="pointer-events: none;">
                        <i class="fas fa-trash"></i>
                    </button>
                    <span>Benutzer löschen</span>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span class="action-btn" style="background-color: transparent; border: 1px dashed #ccc; color: #666; pointer-events: none;">
                        <i class="fas fa-user"></i>
                    </span>
                    <span>Eigener Account</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Bestätigungs-Dialog für kritische Aktionen
        document.addEventListener('DOMContentLoaded', function() {
            const deleteLinks = document.querySelectorAll('.delete-btn');
            deleteLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    if (!confirm('Sind Sie sicher, dass Sie diesen Benutzer löschen möchten?\n\nDiese Aktion kann nicht rückgängig gemacht werden!')) {
                        e.preventDefault();
                    }
                });
            });
            
            // Rolle-Änderung Bestätigung
            const roleSelects = document.querySelectorAll('.role-select');
            roleSelects.forEach(select => {
                select.addEventListener('change', function(e) {
                    const userName = this.closest('tr').querySelector('.user-name').textContent.trim();
                    const newRole = this.value;
                    if (!confirm(`Rolle für Benutzer "${userName}" wirklich zu "${newRole}" ändern?`)) {
                        e.preventDefault();
                        this.form.reset();
                    }
                });
            });
            
            // Aktivierungs-Button mit E-Mail-Hinweis
            const activateLinks = document.querySelectorAll('.toggle-btn:not(.active)');
            activateLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    const userName = this.closest('tr').querySelector('.user-name').textContent.trim();
                    if (!confirm(`Benutzer "${userName}" wirklich aktivieren?\n\nDem Benutzer wird eine Aktivierungs-E-Mail mit Login-Daten gesendet.`)) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
</body>
</html>