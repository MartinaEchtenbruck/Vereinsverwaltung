<?php
/** Interne Moduldatei: nur über den bisherigen Einstiegspunkt aufrufen. */
if (!defined('APP_ENTRY_POINT')) {
    http_response_code(404);
    exit;
}

$appRoot = dirname(__DIR__, 4);
// forgot_password.php - Passwort vergessen Prozess starten
session_start();
require_once $appRoot . '/db_connection.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    
    if (empty($email)) {
        $error = 'Bitte geben Sie Ihre E-Mail-Adresse ein';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Bitte geben Sie eine gültige E-Mail-Adresse ein';
    } else {
        try {
            $database = new Database();
            $conn = $database->getConnection();
            
            // Prüfen ob Benutzer mit dieser E-Mail existiert
            $stmt = $conn->prepare("SELECT id, username, full_name, email FROM users WHERE email = ? AND is_active = TRUE");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user) {
                // Token für Passwort-Reset erstellen
                $token = bin2hex(random_bytes(32));
                $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));
                
                // Alte Tokens für diesen Benutzer löschen
                $stmt = $conn->prepare("DELETE FROM password_reset_tokens WHERE user_id = ? OR expires_at < NOW()");
                $stmt->execute([$user['id']]);
                
                // Neuen Token speichern
                $stmt = $conn->prepare("INSERT INTO password_reset_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
                
                if ($stmt->execute([$user['id'], $token, $expires_at])) {
                    // E-Mail senden
                    $emailSent = sendPasswordResetEmail($user['email'], $user['full_name'] ?: $user['username'], $token);
                    
                    if ($emailSent) {
                        $message = 'Falls ein Konto mit dieser E-Mail-Adresse existiert, wurden die Wiederherstellungsinformationen versendet.';
                    } else {
                        // Token wieder löschen wenn E-Mail nicht gesendet werden konnte
                        $stmt = $conn->prepare("DELETE FROM password_reset_tokens WHERE token = ?");
                        $stmt->execute([$token]);
                        $error = 'E-Mail konnte nicht gesendet werden. Bitte versuchen Sie es später erneut.';
                    }
                } else {
                    $error = 'Fehler beim Erstellen des Reset-Links. Bitte versuchen Sie es später erneut.';
                }
            } else {
                // Aus Sicherheitsgründen die gleiche Meldung anzeigen
                $message = 'Falls ein Konto mit dieser E-Mail-Adresse existiert, wurden die Wiederherstellungsinformationen versendet.';
            }
            
        } catch (Exception $e) {
            error_log("Password reset error: " . $e->getMessage());
            $error = 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.';
        }
    }
}

/**
 * Sendet eine Passwort-Reset E-Mail
 */
function sendPasswordResetEmail($email, $name, $token) {
    $reset_link = "https://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=" . $token;
    
    $subject = "Passwort zurücksetzen - Spendenkontakte System";
    
    $message = "Hallo " . ($name ?: '') . ",\n\n";
    $message .= "Sie haben angefordert, Ihr Passwort für das Spendenkontakte-System zurückzusetzen.\n\n";
    $message .= "Um ein neues Passwort zu setzen, klicken Sie bitte auf den folgenden Link:\n";
    $message .= $reset_link . "\n\n";
    $message .= "Dieser Link ist 1 Stunde gültig. Wenn Sie keine Passwort-Zurücksetzung angefordert haben, ignorieren Sie diese E-Mail einfach.\n\n";
    $message .= "Mit freundlichen Grüßen\n";
    $message .= "Ihr Spendenkontakte-Team\n\n";
    $message .= "Diese E-Mail wurde automatisch generiert.";
    
    $headers = "From: foerderverein@evkigarumeln.de\r\n";
    $headers .= "Reply-To: foerderverein@evkigarumeln.de\r\n";
    $headers .= "Content-Type: text/plain; charset=utf-8\r\n";
    
    return mail($email, $subject, $message, $headers);
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passwort vergessen - Spendenkontakte</title>
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
            <h1><i class="fas fa-key"></i> Passwort vergessen</h1>
            <p>Geben Sie Ihre E-Mail-Adresse ein um Ihr Passwort zurückzusetzen</p>
        </div>
        
        <?php if ($message): ?>
            <div class="notification success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?>
            </div>
            <div class="links">
                <a href="login.php"><i class="fas fa-arrow-left"></i> Zurück zum Login</a>
            </div>
        <?php else: ?>
        
            <?php if ($error): ?>
                <div class="notification error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" id="forgotPasswordForm">
                <div class="form-group">
                    <label for="email">E-Mail-Adresse</label>
                    <input type="email" id="email" name="email" class="form-control" 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" 
                           required autocomplete="email">
                    <div class="info-text">Geben Sie die E-Mail-Adresse Ihres Kontos ein</div>
                </div>
                
                <button type="submit" class="btn">
                    <i class="fas fa-paper-plane"></i> Passwort zurücksetzen
                </button>
            </form>
            
            <div class="links">
                <a href="login.php"><i class="fas fa-arrow-left"></i> Zurück zum Login</a>
            </div>
            
        <?php endif; ?>
    </div>

    <script>
        // Nur Event-Listener hinzufügen wenn das Formular existiert
        const forgotPasswordForm = document.getElementById('forgotPasswordForm');
        if (forgotPasswordForm) {
            forgotPasswordForm.addEventListener('submit', function(e) {
                const email = document.getElementById('email').value.trim();
                
                if (!email) {
                    e.preventDefault();
                    alert('Bitte geben Sie Ihre E-Mail-Adresse ein.');
                    return false;
                }
                
                if (!confirm('Möchten Sie eine Passwort-Zurücksetzung für diese E-Mail-Adresse anfordern?')) {
                    e.preventDefault();
                    return false;
                }
            });
        }
    </script>
</body>
</html>