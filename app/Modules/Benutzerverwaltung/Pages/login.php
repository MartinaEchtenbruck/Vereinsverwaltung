<?php
/**
 * Implementierung der Anmeldeseite.
 * Wird über den bisherigen Einstiegspunkt /login.php eingebunden.
 */
if (!defined('APP_ENTRY_POINT')) {
    http_response_code(404);
    exit;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once dirname(__DIR__, 4) . '/db_connection.php';

// Wenn bereits eingeloggt, weiterleiten
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

// Login verarbeiten
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Bitte Benutzername und Passwort eingeben';
    } else {
        try {
            $database = new Database();
            $conn = $database->getConnection();
            
            // Benutzer abfragen
            $stmt = $conn->prepare("
                SELECT u.id, u.username, u.password_hash, u.full_name, u.email, u.is_active 
                FROM users u 
                WHERE u.username = ? AND u.is_active = TRUE
            ");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password_hash'])) {
                // Session-ID nach erfolgreicher Anmeldung erneuern (Schutz vor Session-Fixation).
                session_regenerate_id(true);

                // Login erfolgreich
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['email'] = $user['email'];
                
                // Letzten Login aktualisieren
                $updateStmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                $updateStmt->execute([$user['id']]);
                
                // Benutzerberechtigungen in Session speichern
                $permStmt = $conn->prepare("
                    SELECT p.permission_name 
                    FROM users u
                    JOIN user_roles ur ON u.id = ur.user_id
                    JOIN role_permissions rp ON ur.role_id = rp.role_id
                    JOIN permissions p ON rp.permission_id = p.id
                    WHERE u.id = ?
                ");
                $permStmt->execute([$user['id']]);
                $_SESSION['permissions'] = $permStmt->fetchAll(PDO::FETCH_COLUMN);
                
                // Weiterleitung zur Hauptseite
                header('Location: index.php');
                exit;
                
            } else {
                $error = 'Ungültige Anmeldedaten oder Benutzer nicht aktiv';
            }
            
        } catch (Exception $e) {
            error_log("Login Fehler: " . $e->getMessage());
            $error = 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Spendenkontakte Verwaltung</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3498db;
            --success-color: #2ecc71;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --light-gray: #f5f5f5;
            --border-color: #ddd;
            --button-gray: #6c757d;
        }
        
        * {
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 40px;
            width: 100%;
            max-width: 400px;
            margin: 20px;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 20px;
        }
        
        .login-header h1 {
            color: var(--primary-color);
            margin: 0 0 10px 0;
            font-size: 28px;
        }
        
        .login-header p {
            color: #666;
            margin: 0;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }
        
        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            font-size: 14px;
            font-family: inherit;
            transition: border-color 0.2s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
        }
        
        .btn {
            width: 100%;
            padding: 10px 20px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.2s;
            font-family: inherit;
        }
        
        .btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }
        
        .notification {
            padding: 10px 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            display: <?php echo $error ? 'block' : 'none'; ?>;
        }
        
        .notification.error {
            background-color: rgba(231, 76, 60, 0.2);
            border-left: 4px solid var(--danger-color);
            color: #721c24;
        }
        
        .test-accounts {
            background-color: #e8f4fd;
            border-left: 4px solid var(--primary-color);
            padding: 15px;
            margin-top: 25px;
            border-radius: 4px;
            font-size: 0.9em;
        }
        
        .test-accounts h4 {
            margin: 0 0 10px 0;
            color: #333;
            font-size: 1em;
        }
        
        .test-account {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding: 5px 0;
            border-bottom: 1px solid rgba(52, 152, 219, 0.2);
        }
        
        .test-account:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        
        .password-note {
            font-size: 0.8em;
            color: var(--danger-color);
            font-weight: 600;
            margin-top: 10px;
            text-align: center;
        }
        
        @media (max-width: 480px) {
            .login-container {
                padding: 30px 20px;
                margin: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1><i class="fas fa-hand-holding-heart"></i> Spendenkontakte</h1>
            <p>Bitte melden Sie sich an</p>
        </div>
        
        <?php if ($error): ?>
            <div class="notification error">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" id="loginForm">
            <div class="form-group">
                <label for="username">Benutzername</label>
                <input type="text" id="username" name="username" class="form-control" 
                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" 
                       required autofocus>
            </div>
            
            <div class="form-group">
                <label for="password">Passwort</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            
            <button type="submit" class="btn">
                <i class="fas fa-sign-in-alt"></i> Anmelden
            </button>
        </form>
		
		<div style="text-align: center; margin-top: 20px;">
			<a href="forgot_password.php" style="color: var(--primary-color); text-decoration: none;">
				<i class="fas fa-key"></i> Passwort vergessen?
			</a>
		</div>
        
	
		<!-- NEU: Registrierungs-Link -->
        <div style="text-align: center; margin-top: 25px; padding-top: 20px; border-top: 1px solid var(--border-color);">
            <p>Noch keinen Account?</p>
            <a href="register.php" style="color: var(--primary-color); text-decoration: none; font-weight: 600;">
                <i class="fas fa-user-plus"></i> Account beantragen
            </a>
        </div>
    </div>

    <script>
        // Einfache Form-Validierung
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;
            
            if (!username || !password) {
                e.preventDefault();
                alert('Bitte füllen Sie beide Felder aus.');
                return false;
            }
        });
        
        // Enter-Taste unterstützen
        document.getElementById('password').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('loginForm').submit();
            }
        });
    </script>
</body>
</html>