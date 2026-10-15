
<?php
session_start();

function checkPermission($permission_name) {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
    
    // Wenn keine Berechtigungen in Session, neu laden
    if (!isset($_SESSION['permissions']) || empty($_SESSION['permissions'])) {
        reloadUserPermissions();
    }
    
    if (!in_array($permission_name, $_SESSION['permissions'])) {
        http_response_code(403);
        // Für API-Aufrufe JSON zurückgeben
        if (strpos($_SERVER['REQUEST_URI'], '.php') !== false && 
            $_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Zugriff verweigert: Sie haben keine Berechtigung für diese Aktion.']);
            exit;
        } else {
            die('Zugriff verweigert: Sie haben keine Berechtigung für diese Aktion.');
        }
    }
    
    return true;
}

function hasPermission($permission_name) {
    if (!isset($_SESSION['user_id'])) return false;
    
    // Wenn keine Berechtigungen in Session, neu laden
    if (!isset($_SESSION['permissions']) || empty($_SESSION['permissions'])) {
        reloadUserPermissions();
    }
    
    return isset($_SESSION['permissions']) && in_array($permission_name, $_SESSION['permissions']);
}

function reloadUserPermissions() {
    require_once 'db_connection.php';
    $database = new Database();
    $conn = $database->getConnection();
    
    $stmt = $conn->prepare("
        SELECT p.permission_name 
        FROM users u
        JOIN user_roles ur ON u.id = ur.user_id
        JOIN role_permissions rp ON ur.role_id = rp.role_id
        JOIN permissions p ON rp.permission_id = p.id
        WHERE u.id = ? AND u.is_active = TRUE
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $permissions = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    
    $_SESSION['permissions'] = $permissions;
    
    // DEBUG: Log permissions
    error_log("DEBUG: Reloaded permissions for user " . $_SESSION['username'] . ": " . implode(', ', $permissions));
}

function getUserRole() {
    if (!isset($_SESSION['user_id'])) return null;
    
    require_once 'db_connection.php';
    $database = new Database();
    $conn = $database->getConnection();
    
    $stmt = $conn->prepare("
        SELECT r.role_name 
        FROM users u
        JOIN user_roles ur ON u.id = ur.user_id
        JOIN roles r ON ur.role_id = r.id
        WHERE u.id = ?
        LIMIT 1
    ");
    $stmt->execute([$_SESSION['user_id']]);
    
    return $stmt->fetchColumn();
}

function logout() {
    $_SESSION = array();
    session_destroy();
    header('Location: login.php');
    exit;
}
?>
