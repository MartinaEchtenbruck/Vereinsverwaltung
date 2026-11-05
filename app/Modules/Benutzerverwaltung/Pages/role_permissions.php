<?php
/** Interne Moduldatei: nur über den bisherigen Einstiegspunkt aufrufen. */
if (!defined('APP_ENTRY_POINT')) {
    http_response_code(404);
    exit;
}

$appRoot = dirname(__DIR__, 4);
// role_permissions.php - Rollen und Berechtigungen verwalten

// Session nur starten wenn nicht bereits gestartet
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once $appRoot . '/db_connection.php';
require_once $appRoot . '/auth.php';

checkPermission('manage_users');

$message = '';
$error = '';

// Rollen anlegen oder Stammdaten bearbeiten
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['role_action'])) {
    try {
        $database = new Database();
        $conn = $database->getConnection();
        $roleName = trim((string)($_POST['role_name'] ?? ''));
        $roleDescription = trim((string)($_POST['description'] ?? ''));

        if ($roleName === '') {
            throw new RuntimeException('Bitte geben Sie einen Rollennamen ein.');
        }
        if (mb_strlen($roleName) > 100) {
            throw new RuntimeException('Der Rollenname darf höchstens 100 Zeichen lang sein.');
        }
        if (mb_strlen($roleDescription) > 1000) {
            throw new RuntimeException('Die Beschreibung darf höchstens 1000 Zeichen lang sein.');
        }

        if ($_POST['role_action'] === 'create') {
            $stmt = $conn->prepare('SELECT id FROM roles WHERE LOWER(role_name) = LOWER(?) LIMIT 1');
            $stmt->execute([$roleName]);
            if ($stmt->fetchColumn()) {
                throw new RuntimeException('Eine Rolle mit diesem Namen existiert bereits.');
            }
            $stmt = $conn->prepare('INSERT INTO roles (role_name, description) VALUES (?, ?)');
            $stmt->execute([$roleName, $roleDescription]);
            $message = 'Die Rolle wurde erfolgreich angelegt.';
        } elseif ($_POST['role_action'] === 'update') {
            $roleId = (int)($_POST['role_id'] ?? 0);
            if ($roleId <= 0) {
                throw new RuntimeException('Ungültige Rollen-ID.');
            }
            $stmt = $conn->prepare('SELECT id FROM roles WHERE LOWER(role_name) = LOWER(?) AND id <> ? LIMIT 1');
            $stmt->execute([$roleName, $roleId]);
            if ($stmt->fetchColumn()) {
                throw new RuntimeException('Eine andere Rolle mit diesem Namen existiert bereits.');
            }
            $stmt = $conn->prepare('UPDATE roles SET role_name = ?, description = ? WHERE id = ?');
            $stmt->execute([$roleName, $roleDescription, $roleId]);
            $message = 'Die Rolle wurde erfolgreich aktualisiert.';
        }
    } catch (Throwable $e) {
        $error = $e instanceof RuntimeException ? $e->getMessage() : 'Die Rolle konnte nicht gespeichert werden.';
    }
}

// Berechtigungen für Rolle aktualisieren
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['role_id']) && !isset($_POST['role_action'])) {
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
        .roles-heading { display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; }
        .role-item { position:relative; padding-right:48px; }
        .role-edit-btn { position:absolute; right:10px; top:10px; border:0; background:transparent; color:var(--primary-color); cursor:pointer; padding:7px; }
        .role-modal { display:none; position:fixed; inset:0; z-index:10000; background:rgba(0,0,0,.45); align-items:center; justify-content:center; padding:20px; }
        .role-modal.open { display:flex; }
        .role-modal-card { background:#fff; width:min(100%,460px); border-radius:8px; padding:22px; box-shadow:0 10px 35px rgba(0,0,0,.2); }
        .role-modal-card label { display:block; margin:12px 0 5px; font-weight:600; }
        .role-modal-card input, .role-modal-card textarea { width:100%; box-sizing:border-box; padding:10px; border:1px solid var(--border-color); border-radius:4px; }
        .role-modal-actions { display:flex; justify-content:flex-end; gap:8px; margin-top:18px; }
    </style>
    <link rel="stylesheet" href="includes/navigation.css">

    <link rel="icon" type="image/png" sizes="32x32" href="/intern/assets/icons/favicon.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/intern/assets/icons/apple-touch-icon.png">
    <link rel="manifest" href="/intern/manifest.webmanifest">
    <meta name="apple-mobile-web-app-title" content="Förderverein intern">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="theme-color" content="#FFF500">
</head>
<body>
    <div class="container">
        <header class="app-page-header">
            <h1><i class="fas fa-user-shield"></i> Rollen & Berechtigungen</h1>
            <?php require $appRoot . '/includes/navigation.php'; ?>
        </header>
        
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
                <div class="roles-heading"><h3>Verfügbare Rollen</h3><button type="button" class="btn btn-primary" onclick="openRoleModal('create')"><i class="fas fa-plus"></i> Neue Rolle anlegen</button></div>
                <?php foreach ($roles as $role): ?>
                    <div class="role-item" data-role-id="<?php echo $role['id']; ?>">
                        <button type="button" class="role-edit-btn" title="Rolle bearbeiten" aria-label="Rolle bearbeiten" onclick="event.stopPropagation(); openRoleModal('edit', <?php echo (int)$role['id']; ?>)"><i class="fas fa-pen"></i></button>
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
                        
                        <div class="permissions-grid" id="allPermissions">
                            <!-- Alle in der Datenbank vorhandenen Berechtigungen werden per JavaScript eingefügt. -->
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

    <div class="role-modal" id="roleModal" role="dialog" aria-modal="true" aria-labelledby="roleModalTitle">
        <div class="role-modal-card">
            <h3 id="roleModalTitle">Rolle anlegen</h3>
            <form method="POST" id="roleDetailsForm">
                <input type="hidden" name="role_action" id="roleAction" value="create">
                <input type="hidden" name="role_id" id="roleDetailsId" value="">
                <label for="roleNameInput">Rollenname *</label>
                <input type="text" name="role_name" id="roleNameInput" maxlength="100" required>
                <label for="roleDescriptionInput">Beschreibung</label>
                <textarea name="description" id="roleDescriptionInput" rows="4" maxlength="1000"></textarea>
                <div class="role-modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeRoleModal()">Abbrechen</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Speichern</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Rollen- und Berechtigungsdaten von PHP zu JavaScript
        const roles = <?php echo json_encode($roles); ?>;
        const permissions = <?php echo json_encode($permissions); ?>;
        const rolePermissions = <?php echo json_encode($role_permissions); ?>;
        
        let selectedRoleId = null;

        function openRoleModal(action, roleId = null) {
            const modal = document.getElementById('roleModal');
            const role = action === 'edit' ? roles.find(r => Number(r.id) === Number(roleId)) : null;
            if (action === 'edit' && !role) return;
            document.getElementById('roleAction').value = action;
            document.getElementById('roleDetailsId').value = role ? role.id : '';
            document.getElementById('roleModalTitle').textContent = action === 'edit' ? 'Rolle bearbeiten' : 'Neue Rolle anlegen';
            document.getElementById('roleNameInput').value = role ? role.role_name : '';
            document.getElementById('roleDescriptionInput').value = role ? (role.description || '') : '';
            modal.classList.add('open');
            document.getElementById('roleNameInput').focus();
        }
        function closeRoleModal() { document.getElementById('roleModal').classList.remove('open'); }
        document.getElementById('roleModal').addEventListener('click', function(e) { if (e.target === this) closeRoleModal(); });
        document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeRoleModal(); });
        
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
            const permissionsContainer = document.getElementById('allPermissions');
            permissionsContainer.innerHTML = '';

            const currentPermissions = (rolePermissions[roleId] || []).map(Number);

            // Alle Berechtigungen aus der Datenbank anzeigen, unabhängig von ihrem Namen.
            permissions.forEach(permission => {
                const isChecked = currentPermissions.includes(Number(permission.id));
                permissionsContainer.appendChild(createPermissionItem(permission, isChecked));
            });

            if (permissions.length === 0) {
                permissionsContainer.textContent = 'In der Datenbank sind noch keine Berechtigungen angelegt.';
            }
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
