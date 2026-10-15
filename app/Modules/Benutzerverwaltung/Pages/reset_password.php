<?php
/** Interne Moduldatei: nur über den bisherigen Einstiegspunkt aufrufen. */
if (!defined('APP_ENTRY_POINT')) {
    http_response_code(404);
    exit;
}

$appRoot = dirname(__DIR__, 4);
// reset_password.php - Passwort zurücksetzen mit Token
session_start();
require_once $appRoot . '/db_connection.php';

$error = '';
$success = '';
$user_id = null;
$username = '';

// Token validieren
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    try {
        $database = new Database();
        $conn = $database->getConnection();
        
        // Token prüfen (nicht abgelaufen und nicht verwendet)
        $stmt = $conn->prepare("
            SELECT prt.user_id, u.username 
            FROM password_reset_tokens prt 
            JOIN users u ON prt.user_id = u.id 
            WHERE prt.token = ? AND prt.expires_at > NOW() AND prt.used = 0 AND u.is_active = TRUE
        ");
        $stmt->execute([$token]);
        $result = $stmt->fetch();
        
        if ($result) {
            $user_id = $result['user_id'];
            $username = $result['username'];
        } else {
            $error = 'Ungültiger oder abgelaufener Link. Bitte fordern Sie einen neuen Passwort-Reset an.';
        }
        
    } catch (Exception $e) {
        error_log("Token validation error: " . $e->getMessage());
        $error = 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.';
    }
} else {
    $error = 'Ungültiger Link. Bitte fordern Sie einen neuen Passwort-Reset an.';
}

// Passwort zurücksetzen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user_id) {
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    
    if (empty($password) || empty($password_confirm)) {
        $error = 'Bitte füllen Sie alle Felder aus';
    } elseif ($password !== $password_confirm) {
        $error = 'Die Passwörter stimmen nicht überein';
    } elseif (strlen($password) < 6) {
        $error = 'Das Passwort muss mindestens 6 Zeichen lang sein';
    } else {
        try {
            $database = new Database();
            $conn = $database->getConnection();
            
            // Passwort hashen und aktualisieren
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            $stmt->execute([$password_hash, $user_id]);
            
            // Token als verwendet markieren
            $stmt = $conn->prepare("UPDATE password_reset_tokens SET used = 1 WHERE token = ?");
            $stmt->execute([$_GET['token']]);
            
            $success = 'Ihr Passwort wurde erfolgreich zurückgesetzt. Sie können sich jetzt mit dem neuen Passwort anmelden.';
            
        } catch (Exception $e) {
            error_log("Password reset error: " . $e->getMessage());
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
    <title>Passwort zurücksetzen - Spendenkontakte</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3498db;
            --success-color: #2ecc71;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --light-gray: #f5f5f5;
            --border-color: #ddd;
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
        
        .container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 40px;
            width: 100%;
            max-width: 500px;
            margin: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: var(--primary-color);
            margin: 0 0 10px 0;
            font-size: 28px;
        }
        
        .header p {
            color: #666;
            margin: 0;
            font-size: 14px;
        }
        
        .user-info {
            background-color: #e8f4fd;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
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
            margin-bottom: 15px;
        }
        
        .btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }
        
        .notification {
            padding: 10px 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            display: block;
        }
        
        .notification.success {
            background-color: rgba(46, 204, 113, 0.2);
            border-left: 4px solid var(--success-color);
            color: #155724;
        }
        
        .notification.error {
            background-color: rgba(231, 76, 60, 0.2);
            border-left: 4px solid var(--danger-color);
            color: #721c24;
        }
        
        .info-text {
            font-size: 0.9em;
            color: #666;
            margin-top: 5px;
        }
        
        .links {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
        }
        
        .links a {
            color: var(--primary-color);
            text-decoration: none;
        }
        
        .links a:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 480px) {
            .container {
                padding: 30px 20px;
                margin: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-key"></i> Passwort zurücksetzen</h1>
            <p>Setzen Sie ein neues Passwort für Ihr Konto</p>
        </div>
        
        <?php if ($success): ?>
            <div class="notification success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
            </div>
            <div class="links">
                <a href="login.php"><i class="fas fa-sign-in-alt"></i> Zum Login</a>
            </div>
        <?php elseif ($error && !$user_id): ?>
            <div class="notification error">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
            <div class="links">
                <a href="forgot_password.php"><i class="fas fa-key"></i> Neuen Reset-Link anfordern</a> | 
                <a href="login.php"><i class="fas fa-arrow-left"></i> Zurück zum Login</a>
            </div>
        <?php else: ?>
        
            <?php if ($error): ?>
                <div class="notification error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($user_id): ?>
                <div class="user-info">
                    <i class="fas fa-user"></i> 
                    <strong>Passwort zurücksetzen für: <?php echo htmlspecialchars($username); ?></strong>
                </div>
            <?php endif; ?>
            
            <form method="POST" id="resetPasswordForm">
                <div class="form-group">
                    <label for="password">Neues Passwort</label>
                    <input type="password" id="password" name="password" class="form-control" required minlength="6" autocomplete="new-password">
                    <div class="info-text">Mindestens 6 Zeichen</div>
                </div>
                
                <div class="form-group">
                    <label for="password_confirm">Passwort bestätigen</label>
                    <input type="password" id="password_confirm" name="password_confirm" class="form-control" required autocomplete="new-password">
                </div>
                
                <button type="submit" class="btn">
                    <i class="fas fa-save"></i> Passwort zurücksetzen
                </button>
            </form>
            
            <div class="links">
                <a href="login.php"><i class="fas fa-arrow-left"></i> Zurück zum Login</a>
            </div>
            
        <?php endif; ?>
    </div>

    <script>
        document.getElementById('resetPasswordForm')?.addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const passwordConfirm = document.getElementById('password_confirm').value;
            
            if (!password || !passwordConfirm) {
                e.preventDefault();
                alert('Bitte füllen Sie alle Felder aus.');
                return false;
            }
            
            if (password.length < 6) {
                e.preventDefault();
                alert('Das Passwort muss mindestens 6 Zeichen lang sein.');
                return false;
            }
            
            if (password !== passwordConfirm) {
                e.preventDefault();
                alert('Die Passwörter stimmen nicht überein.');
                return false;
            }
            
            if (!confirm('Möchten Sie das Passwort wirklich zurücksetzen?')) {
                e.preventDefault();
                return false;
            }
        });
    </script>
</body>
</html>