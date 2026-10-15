<?php
/** Interne Moduldatei: nur über den bisherigen Einstiegspunkt aufrufen. */
if (!defined('APP_ENTRY_POINT')) {
    http_response_code(404);
    exit;
}

$appRoot = dirname(__DIR__, 4);
// check_username.php
session_start();
require_once $appRoot . '/db_connection.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $data = [];
    parse_str($input, $data);
    $username = trim($data['username'] ?? '');
    
    
    if (empty($username)) {
        echo json_encode(['available' => false, 'error' => 'Username empty']);
        exit;
    }
    
    try {
        $database = new Database();
        $conn = $database->getConnection();
        
        // Prüfen ob Benutzername bereits existiert
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        
        if ($stmt->fetch()) {
            echo json_encode(['available' => false, 'error' => 'Username already taken']);
        } else {
            echo json_encode(['available' => true, 'message' => 'Username available']);
        }
        
    } catch (Exception $e) {
        error_log("Username check error: " . $e->getMessage());
        echo json_encode(['available' => false, 'error' => 'Die Prüfung ist derzeit nicht möglich.']);
    }
} else {
    echo json_encode(['available' => false, 'error' => 'Invalid request method']);
}
?>