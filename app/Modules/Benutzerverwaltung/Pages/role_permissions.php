<?php
/** Interne Moduldatei: nur über den bisherigen Einstiegspunkt aufrufen. */
if (!defined('APP_ENTRY_POINT')) {
    http_response_code(404);
    exit;
}

// role_permissions.php - Rollen und Berechtigungen verwalten

// Session nur starten wenn nicht bereits gestartet
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once dirname(__DIR__, 4) . '/db_connection.php';
require_once dirname(__DIR__, 4) . '/auth.php';

checkPermission('manage_users');

$message = '';
$error = '';

// Berechtigungen für Rolle aktualisieren
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['role_id'])) {
    $role_id = intval($_POST['role_id']);
    $selected_permissions = $_POST['permissions'] ?? [];
    
    try {
        $database = new Database();
        $conn = $database->getConnection();
        
        // Alte Berechtigungen entfernen
        $stmt = $conn->prepare("DELETE FROM role_permissions WHERE role_id = ?");
        $stmt->execute([$role_id]);
        
        // Neue Berechtigungen hinzufügen
        if (!empty($selected_permissions)) {
            $stmt = $conn->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)");
            foreach ($selected_permissions as $permission_id) {
                $stmt->execute([$role_id, intval($permission_id)]);
            }
        }
        
        $message = "Berechtigungen wurden erfolgreich aktualisiert";
        
    } catch (Exception $e) {
        $error = "Fehler: " . $e->getMessage();
    }
}

// Rollen und Berechtigungen laden
try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Alle Rollen laden
    $stmt = $conn->prepare("SELECT * FROM roles ORDER BY role_name");
    $stmt->execute();
    $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Alle Berechtigungen laden
    $stmt = $conn->prepare("SELECT * FROM permissions ORDER BY permission_name");
    $stmt->execute();
    $permissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Berechtigungen pro Rolle laden
    $role_permissions = [];
    foreach ($roles as $role) {
        $stmt = $conn->prepare("
            SELECT p.id 
            FROM role_permissions rp 
            JOIN permissions p ON rp.permission_id = p.id 
            WHERE rp.role_id = ?
        ");
        $stmt->execute([$role['id']]);
        $role_permissions[$role['id']] = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }
    
} catch (Exception $e) {
    $error = "Fehler beim Laden der Daten: " . $e->getMessage();
    $roles = [];
    $permissions = [];
    $role_permissions = [];
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rollen & Berechtigungen - Spendenkontakte</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .permissions-container {
            display: flex;
            gap: 30px;
            margin-top: 20px;
        }
        
        .roles-list {
            flex: 1;
            min-width: 300px;
        }
        
        .permissions-form {
            flex: 2;
            min-width: 500px;
        }
        
        .role-item {
            padding: 15px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .role-item:hover {
            background-color: #f8f9fa;
        }
        
        .role-item.active {
            background-color: #e3f2fd;
            border-color: var(--primary-color);
        }
        
        .role-name {
            font-weight: 600;
            font-size: 1.1em;
            margin-bottom: 5px;
        }
        
        .role-description {
            color: #666;
            font-size: 0.9em;
        }
        
        .permissions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        
        .permission-group {
            border: 1px solid var(--border-color);
            border-radius: 4px;
            padding: 15px;
        }
        
        .permission-group h4 {
            margin: 0 0 10px 0;
            color: #333;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        
        .permission-item {
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .permission-item label {
            cursor: pointer;
            font-size: 0.9em;
        }
        
        .permission-description {
            font-size: 0.8em;
            color: #666;
            margin-left: 24px;
            margin-top: -5px;
            margin-bottom: 8px;
        }
        
        .no-role-selected {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        
        .form-actions {
            margin-top: 20px;
            text-align: right;
        }
        
        .permission-category {
            background-color: #f8f9fa;
            padding: 10px 15px;
            border-radius: 4px;
            margin-bottom: 15px;
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
    </style>
    <link rel="stylesheet" href="includes/navigation.css">
</head>
<body>
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1><i class="fas fa-user-shield"></i> Rollen & Berechtigungen</h1>
            <?php require dirname(__DIR__, 4) . '/includes/navigation.php'; ?>
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
            <i class="fas fa-info-circle"></i> Hier können Sie Berechtigungen für verschiedene Rollen verwalten. Wählen Sie eine Rolle aus, um deren Berechtigungen anzupassen.
        </div>
        
        <div class="permissions-container">
            <div class="roles-list">
                <h3>Verfügbare Rollen</h3>
                <?php foreach ($roles as $role): ?>
                    <div class="role-item" data-role-id="<?php echo $role['id']; ?>">
                        <div class="role-name"><?php echo htmlspecialchars($role['role_name']); ?></div>
                        <div class="role-description"><?php echo htmlspecialchars($role['description']); ?></div>
                        <small style="color: #666;">
                            <?php echo count($role_permissions[$role['id']] ?? []); ?> Berechtigungen
                        </small>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="permissions-form">
                <div id="noRoleSelected" class="no-role-selected">
                    <i class="fas fa-mouse-pointer" style="font-size: 3em; margin-bottom: 15px; color: #ccc;"></i>
                    <h3>Wählen Sie eine Rolle aus</h3>
                    <p>Klicken Sie auf eine Rolle in der Liste, um deren Berechtigungen zu bearbeiten.</p>
                </div>
                
                <div id="roleForm" style="display: none;">
                    <h3 id="selectedRoleName">Rollenname</h3>
                    <p id="selectedRoleDescription" style="color: #666; margin-bottom: 20px;"></p>
                    
                    <form method="POST" id="permissionsForm">
                        <input type="hidden" name="role_id" id="formRoleId">
                        
                        <div class="permission-category">
                            <h4>Kontakt-Berechtigungen</h4>
                        </div>
                        <div class="permissions-grid" id="contactPermissions">
                            <!-- Wird per JavaScript gefüllt -->
                        </div>
                        
                        <div class="permission-category">
                            <h4>System-Berechtigungen</h4>
                        </div>
                        <div class="permissions-grid" id="systemPermissions">
                            <!-- Wird per JavaScript gefüllt -->
                        </div>
                        
                        <div class="form-actions">
                            <button type="button" class="btn btn-secondary" onclick="resetSelection()">
                                <i class="fas fa-times"></i> Abbrechen
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Berechtigungen speichern
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div style="margin-top: 30px; padding: 15px; background-color: #f8f9fa; border-radius: 4px;">
            <h4><i class="fas fa-info-circle"></i> Informationen zur Berechtigungsverwaltung</h4>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li><strong>Rolle auswählen</strong>: Klicken Sie auf eine Rolle in der linken Liste</li>
                <li><strong>Berechtigungen anpassen</strong>: Setzen/Entfernen Sie Häkchen für die gewünschten Berechtigungen</li>
                <li><strong>Speichern</strong>: Klicken Sie auf "Berechtigungen speichern" um Änderungen zu übernehmen</li>
                <li>Änderungen wirken sich sofort auf alle Benutzer mit dieser Rolle aus</li>
                <li>Die "admin"-Rolle sollte immer alle Berechtigungen behalten</li>
            </ul>
        </div>
    </div>

    <script>
        // Rollen- und Berechtigungsdaten von PHP zu JavaScript
        const roles = <?php echo json_encode($roles); ?>;
        const permissions = <?php echo json_encode($permissions); ?>;
        const rolePermissions = <?php echo json_encode($role_permissions); ?>;
        
        // Kategorien für Berechtigungen
        const permissionCategories = {
            'contact': ['view_contacts', 'add_contacts', 'edit_contacts', 'delete_contacts', 'send_emails', 'change_status', 'edit_comments', 'suggest_contacts'],
            'system': ['view_reports', 'manage_users', 'manage_settings', 'export_data', 'edit_fundraising']
        };
        
        let selectedRoleId = null;
        
        // Rolle auswählen
        document.querySelectorAll('.role-item').forEach(item => {
            item.addEventListener('click', function() {
                const roleId = this.getAttribute('data-role-id');
                selectRole(roleId);
            });
        });
        
        function selectRole(roleId) {
            selectedRoleId = roleId;
            
            // UI aktualisieren
            document.querySelectorAll('.role-item').forEach(item => {
                item.classList.remove('active');
            });
            document.querySelector(`.role-item[data-role-id="${roleId}"]`).classList.add('active');
            
            document.getElementById('noRoleSelected').style.display = 'none';
            document.getElementById('roleForm').style.display = 'block';
            
            // Rolleninformationen anzeigen
            const role = roles.find(r => r.id == roleId);
            document.getElementById('selectedRoleName').textContent = role.role_name;
            document.getElementById('selectedRoleDescription').textContent = role.description;
            document.getElementById('formRoleId').value = roleId;
            
            // Berechtigungsformular füllen
            fillPermissionsForm(roleId);
        }
        
        function fillPermissionsForm(roleId) {
            const contactContainer = document.getElementById('contactPermissions');
            const systemContainer = document.getElementById('systemPermissions');
            
            contactContainer.innerHTML = '';
            systemContainer.innerHTML = '';
            
            const currentPermissions = rolePermissions[roleId] || [];
            
            // Debug-Ausgabe
            console.log('Current permissions for role', roleId, ':', currentPermissions);
            console.log('All permissions:', permissions);
            
            // Berechtigungen nach Kategorien gruppiert anzeigen
            permissions.forEach(permission => {
                // Korrekte Prüfung ob Berechtigung aktiviert ist
                const isChecked = currentPermissions.includes(parseInt(permission.id));
                console.log(`Permission ${permission.id} (${permission.permission_name}): ${isChecked}`);
                
                const permissionItem = createPermissionItem(permission, isChecked);
                
                if (permissionCategories.contact.includes(permission.permission_name)) {
                    contactContainer.appendChild(permissionItem);
                } else if (permissionCategories.system.includes(permission.permission_name)) {
                    systemContainer.appendChild(permissionItem);
                }
            });
        }
        
        function createPermissionItem(permission, isChecked) {
            const group = document.createElement('div');
            group.className = 'permission-group';
            
            const item = document.createElement('div');
            item.className = 'permission-item';
            
            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.name = 'permissions[]';
            checkbox.value = permission.id;
            checkbox.id = `perm_${permission.id}`;
            checkbox.checked = isChecked;
            
            const label = document.createElement('label');
            label.htmlFor = `perm_${permission.id}`;
            label.textContent = permission.permission_name;
            
            item.appendChild(checkbox);
            item.appendChild(label);
            
            const description = document.createElement('div');
            description.className = 'permission-description';
            description.textContent = permission.description;
            
            group.appendChild(item);
            group.appendChild(description);
            
            return group;
        }
        
        function resetSelection() {
            selectedRoleId = null;
            document.getElementById('noRoleSelected').style.display = 'block';
            document.getElementById('roleForm').style.display = 'none';
            document.querySelectorAll('.role-item').forEach(item => {
                item.classList.remove('active');
            });
        }
        
        // Formular-Submit bestätigen
        document.getElementById('permissionsForm').addEventListener('submit', function(e) {
            const role = roles.find(r => r.id == selectedRoleId);
            const checkedPermissions = this.querySelectorAll('input[type="checkbox"]:checked');
            
            if (!confirm(`Möchten Sie die Berechtigungen für die Rolle "${role.role_name}" wirklich aktualisieren?\n\nEs werden ${checkedPermissions.length} Berechtigungen zugewiesen.`)) {
                e.preventDefault();
            }
        });
        
        // Erste Rolle automatisch auswählen (falls vorhanden)
        <?php if (!empty($roles)): ?>
        setTimeout(() => {
            selectRole(<?php echo $roles[0]['id']; ?>);
        }, 100);
        <?php endif; ?>
    </script>
</body>
</html>
