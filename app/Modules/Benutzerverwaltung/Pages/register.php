<?php
/** Interne Moduldatei: nur über den bisherigen Einstiegspunkt aufrufen. */
if (!defined('APP_ENTRY_POINT')) {
    http_response_code(404);
    exit;
}

$appRoot = dirname(__DIR__, 4);
session_start();
require_once $appRoot . '/db_connection.php';

// Wenn bereits eingeloggt, weiterleiten
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$success = '';

// Registrierung verarbeiten
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $email = trim($_POST['email'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $comment = trim($_POST['comment'] ?? '');
    
    // Validierung
    if (empty($username) || empty($password) || empty($email) || empty($full_name)) {
        $error = 'Bitte füllen Sie alle Pflichtfelder aus';
    } elseif ($password !== $password_confirm) {
        $error = 'Die Passwörter stimmen nicht überein';
    } elseif (strlen($password) < 6) {
        $error = 'Das Passwort muss mindestens 6 Zeichen lang sein';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Bitte geben Sie eine gültige E-Mail-Adresse ein';
    } else {
        try {
            $database = new Database();
            $conn = $database->getConnection();
            
            // Prüfen ob Benutzername bereits existiert
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $error = 'Dieser Benutzername ist bereits vergeben';
            } else {
                // Prüfen ob E-Mail bereits existiert
                $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                if ($stmt->fetch()) {
                    $error = 'Diese E-Mail-Adresse ist bereits registriert';
                } else {
                    // Neuen Benutzer anlegen
                    $password_hash = password_hash($password, PASSWORD_DEFAULT);
                    
                    $stmt = $conn->prepare("
                        INSERT INTO users (username, password_hash, email, full_name, comment, is_active, created_at) 
                        VALUES (?, ?, ?, ?, ?, FALSE, NOW())
                    ");
                    
                    $success = $stmt->execute([$username, $password_hash, $email, $full_name, $comment]);
                    
                    if ($success) {
                        // User-Rolle für den neuen Benutzer zuweisen
                        $newUserId = $conn->lastInsertId();
                        $roleAssigned = assignUserRole($newUserId, 'user');
                        
                        if (!$roleAssigned) {
                            error_log("Fehler: Rolle 'user' konnte nicht für Benutzer $username zugewiesen werden");
                        }
                        
                        // E-Mail an Administratoren senden
                        sendAdminNotification($username, $email, $full_name, $comment);
                        
                        $success = 'Ihr Registrierungsantrag wurde erfolgreich eingereicht. Sie erhalten eine E-Mail, sobald Ihr Account freigeschaltet wurde.';
                    } else {
                        $error = 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.';
                    }
                }
            }
            
        } catch (Exception $e) {
            error_log("Registrierungs Fehler: " . $e->getMessage());
            $error = 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.';
        }
    }
}

/**
 * Weist einem Benutzer eine Rolle zu
 */
function assignUserRole($userId, $roleName) {
    try {
        $database = new Database();
        $conn = $database->getConnection();
        
        // Rollen-ID für die gewünschte Rolle finden
        $stmt = $conn->prepare("SELECT id FROM roles WHERE role_name = ?");
        $stmt->execute([$roleName]);
        $role = $stmt->fetch();
        
        if (!$role) {
            error_log("Rolle '$roleName' nicht gefunden");
            return false;
        }
        
        $roleId = $role['id'];
        
        // Rolle dem Benutzer zuweisen
        $stmt = $conn->prepare("INSERT IGNORE INTO user_roles (user_id, role_id) VALUES (?, ?)");
        $result = $stmt->execute([$userId, $roleId]);
        
        if ($result) {
            error_log("Rolle '$roleName' erfolgreich für Benutzer ID $userId zugewiesen");
            return true;
        } else {
            error_log("Fehler beim Zuweisen der Rolle '$roleName' für Benutzer ID $userId");
            return false;
        }
        
    } catch (Exception $e) {
        error_log("Fehler beim Zuweisen der Rolle: " . $e->getMessage());
        return false;
    }
}

/**
 * Sendet eine Benachrichtigung an alle Administratoren über eine neue Registrierung
 */
function sendAdminNotification($username, $email, $full_name, $comment) {
    try {
        $database = new Database();
        $conn = $database->getConnection();
        
        // Alle aktiven Administratoren finden
        $stmt = $conn->prepare("
            SELECT DISTINCT u.email, u.full_name 
            FROM users u
            JOIN user_roles ur ON u.id = ur.user_id 
            JOIN roles r ON ur.role_id = r.id
            WHERE r.role_name = 'admin' AND u.is_active = TRUE
        ");
        $stmt->execute();
        $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($admins)) {
            error_log("Keine Administratoren für Benachrichtigung gefunden");
            return false;
        }
        
        $adminEmails = array_column($admins, 'email');
        
        // E-Mail an alle Administratoren senden
        $subject = "Neue Benutzerregistrierung - Spendenkontakte System";
        
        $message = "Es hat sich ein neuer Benutzer registriert:\n\n";
        $message .= "Benutzername: " . $username . "\n";
        $message .= "E-Mail: " . $email . "\n";
        $message .= "Vollständiger Name: " . $full_name . "\n";
        $message .= "Registrierungsgrund: " . ($comment ?: 'Kein Kommentar angegeben') . "\n\n";
        $message .= "Bitte schalten Sie den Benutzer im Admin-Bereich frei:\n";
        $message .= "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/users.php\n\n";
        $message .= "Diese E-Mail wurde automatisch generiert.";
        
        $headers = "From: foerderverein@evkigarumeln.de\r\n";
        $headers .= "Reply-To: foerderverein@evkigarumeln.de\r\n";
        $headers .= "Content-Type: text/plain; charset=utf-8\r\n";
        
        $allMailsSent = true;
        // An alle Administratoren senden
        foreach ($adminEmails as $adminEmail) {
            $mailSent = mail($adminEmail, $subject, $message, $headers);
            
            if ($mailSent) {
                error_log("Admin-Benachrichtigung erfolgreich gesendet an: " . $adminEmail);
            } else {
                error_log("Fehler beim Senden der Admin-Benachrichtigung an: " . $adminEmail);
                $allMailsSent = false;
            }
        }
        
        return $allMailsSent;
        
    } catch (Exception $e) {
        error_log("Fehler beim Senden der Admin-Benachrichtigung: " . $e->getMessage());
        return false;
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrierung - Spendenkontakte Verwaltung</title>
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
        
        .register-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 40px;
            width: 100%;
            max-width: 500px;
            margin: 20px;
        }
        
        .register-header, {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 20px;
        }
		
		.register-info {
            text-align: center;
            margin-top: 30px;
        }
        
        .register-header h1 {
            color: var(--primary-color);
            margin: 0 0 10px 0;
            font-size: 28px;
        }
        
        .register-header p {
            color: #666;
            margin: 0;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }
        
        .form-group.required label::after {
            content: " *";
            color: var(--danger-color);
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
        
        .form-control.error {
            border-color: var(--danger-color);
            background-color: rgba(231, 76, 60, 0.05);
        }
        
        .form-control.success {
            border-color: var(--success-color);
            background-color: rgba(46, 204, 113, 0.05);
        }
        
        .validation-feedback {
            display: none;
            font-size: 0.85em;
            margin-top: 5px;
            padding: 5px;
            border-radius: 3px;
        }
        
        .validation-feedback.error {
            display: block;
            color: var(--danger-color);
            background-color: rgba(231, 76, 60, 0.1);
            border-left: 3px solid var(--danger-color);
        }
        
        .validation-feedback.success {
            display: block;
            color: var(--success-color);
            background-color: rgba(46, 204, 113, 0.1);
            border-left: 3px solid var(--success-color);
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
        
        .btn:disabled {
            background-color: var(--button-gray);
            cursor: not-allowed;
            transform: none;
        }
        
        .btn-secondary {
            background-color: var(--button-gray);
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
        
        .login-link {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
        }
        
        .login-link a {
            color: var(--primary-color);
            text-decoration: none;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 480px) {
            .register-container {
                padding: 30px 20px;
                margin: 10px;
            }
        }
    </style>

    <link rel="icon" type="image/png" sizes="32x32" href="/intern/assets/icons/favicon.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/intern/assets/icons/apple-touch-icon.png">
    <link rel="manifest" href="/intern/manifest.webmanifest">
    <meta name="apple-mobile-web-app-title" content="Förderverein intern">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="theme-color" content="#FFF500">
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <h1><i class="fas fa-user-plus"></i> Account beantragen</h1>
            <p>Bitte fülle das Formular aus um einen Zugang zu beantragen</p>
        </div>
        
        <?php if ($success): ?>
            <div class="notification success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
            </div>
            <div class="login-link">
                <a href="login.php"><i class="fas fa-sign-in-alt"></i> Zurück zum Login</a>
            </div>
        <?php else: ?>
        
            <?php if ($error): ?>
                <div class="notification error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" id="registerForm">
                <div class="form-group required">
                    <label for="username">Benutzername</label>
                    <input type="text" id="username" name="username" class="form-control" 
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" 
                           required maxlength="50" autocomplete="username">
                    <div class="info-text">Wähle einen Benutzernamen</div>
                    <div class="validation-feedback" id="usernameFeedback"></div>
                </div>
                
                <div class="form-group required">
                    <label for="email">E-Mail-Adresse</label>
                    <input type="email" id="email" name="email" class="form-control" 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" 
                           required autocomplete="email">
                    <div class="info-text">Deine E-Mail-Adresse für Benachrichtigungen</div>
                    <div class="validation-feedback" id="emailFeedback"></div>
                </div>
                
                <div class="form-group required">
                    <label for="full_name">Vollständiger Name</label>
                    <input type="text" id="full_name" name="full_name" class="form-control" 
                           value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>" 
                           required autocomplete="name">
                    <div class="info-text">Ihr Vor- und Nachname</div>
                </div>
                
                <div class="form-group required">
                    <label for="password">Passwort</label>
                    <input type="password" id="password" name="password" class="form-control" required minlength="6" autocomplete="new-password">
                    <div class="info-text">Mindestens 6 Zeichen</div>
                    <div class="validation-feedback" id="passwordFeedback"></div>
                </div>
                
                <div class="form-group required">
                    <label for="password_confirm">Passwort bestätigen</label>
                    <input type="password" id="password_confirm" name="password_confirm" class="form-control" required autocomplete="new-password">
                    <div class="validation-feedback" id="passwordConfirmFeedback"></div>
                </div>
                
                <div class="form-group">
                    <label for="comment">Kommentar</label>
                    <textarea id="comment" name="comment" class="form-control" rows="3" ><?php echo htmlspecialchars($_POST['comment'] ?? ''); ?></textarea>
                </div>
                
				<div class="register-info">
					<p>Achtung, der Zugang funktioniert erst, wenn Dein Account von uns freigeschaltet wurde. Du bekommst dann eine Benachrichtigung per E-Mail.</p>					
				</div>
				
                <button type="submit" class="btn" id="submitButton">					
                    <i class="fas fa-user-plus"></i> Account beantragen
                </button>
            </form>
            
            <div class="login-link">
                <a href="login.php"><i class="fas fa-arrow-left"></i> Zurück zum Login</a>
            </div>
            
        <?php endif; ?>
    </div>

	 <script>
		// Globale Debug-Funktion
		function testAjaxConnection() {
			console.log('Testing AJAX connection to check_username.php');
			
			const testXhr = new XMLHttpRequest();
			testXhr.open('POST', 'check_username.php', true);
			testXhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
			
			testXhr.onreadystatechange = function() {
				console.log('Test XHR readyState:', testXhr.readyState, 'status:', testXhr.status);
				if (testXhr.readyState === 4) {
					console.log('Test response:', testXhr.responseText);
					if (testXhr.status === 200) {
						try {
							const response = JSON.parse(testXhr.responseText);
							console.log('Parsed test response:', response);
						} catch (e) {
							console.error('Error parsing test response:', e);
						}
					}
				}
			};
			
			testXhr.onerror = function() {
				console.error('Test XHR network error');
			};
			
			testXhr.send('username=testuser');
		}

		// Form-Validierung
		document.addEventListener('DOMContentLoaded', function() {
			const usernameInput = document.getElementById('username');
			const passwordInput = document.getElementById('password');
			const passwordConfirmInput = document.getElementById('password_confirm');
			const emailInput = document.getElementById('email');
			const submitButton = document.getElementById('submitButton');
			const registerForm = document.getElementById('registerForm');
			
			// Debug: Prüfen ob Elemente gefunden wurden
			console.log('Form elements found:', {
				usernameInput: !!usernameInput,
				passwordInput: !!passwordInput,
				passwordConfirmInput: !!passwordConfirmInput,
				emailInput: !!emailInput,
				submitButton: !!submitButton,
				registerForm: !!registerForm
			});
			
			let usernameValid = false;
			let passwordValid = false;
			let passwordMatch = false;
			let emailValid = false;
			let isCheckingUsername = false;
			
			// Event-Listener für Benutzername
			if (usernameInput) {
				usernameInput.addEventListener('blur', checkUsernameAvailability);
				usernameInput.addEventListener('input', function() {
					console.log('Username input:', this.value);
					const feedback = document.getElementById('usernameFeedback');
					const username = this.value.trim();
					
					if (username.length < 3) {
						showValidation(feedback, 'Benutzername muss mindestens 3 Zeichen lang sein', 'error');
						usernameInput.classList.remove('success', 'error');
						usernameInput.classList.add('error');
						usernameValid = false;
					} else {
						hideValidation(feedback);
						usernameInput.classList.remove('error', 'success');
						usernameValid = true;
					}
					updateSubmitButton();
				});
				
				// Test: Direktes Event auslösen
				usernameInput.addEventListener('focus', function() {
					console.log('Username field focused');
				});
			}
			
			// Event-Listener für E-Mail
			if (emailInput) {
				emailInput.addEventListener('input', validateEmail);
				emailInput.addEventListener('blur', validateEmail);
			}
			
			// Event-Listener für Passwort
			if (passwordInput) {
				passwordInput.addEventListener('input', validatePassword);
			}
			
			if (passwordConfirmInput) {
				passwordConfirmInput.addEventListener('input', validatePasswordMatch);
			}
			
			// Formular-Submit-Event
			if (registerForm) {
				registerForm.addEventListener('submit', function(e) {
					console.log('Form submit attempted');
					if (!usernameValid || !passwordValid || !passwordMatch || !emailValid) {
						e.preventDefault();
						alert('Bitte beheben Sie die angezeigten Validierungsfehler bevor Sie das Formular absenden.');
						return false;
					}
					
					if (isCheckingUsername) {
						e.preventDefault();
						alert('Bitte warten Sie, während der Benutzername überprüft wird...');
						return false;
					}
					
					if (!confirm('Möchten Sie den Account wirklich beantragen?\n\nIhr Account muss erst von einem Administrator freigeschaltet werden, bevor Sie sich anmelden können.')) {
						e.preventDefault();
						return false;
					}
				});
			}
			
			function checkUsernameAvailability() {
				console.log('checkUsernameAvailability called');
				const username = usernameInput.value.trim();
				const feedback = document.getElementById('usernameFeedback');
				
				if (username.length < 3) {
					showValidation(feedback, 'Benutzername muss mindestens 3 Zeichen lang sein', 'error');
					usernameInput.classList.remove('success');
					usernameInput.classList.add('error');
					usernameValid = false;
					updateSubmitButton();
					return;
				}
				
				isCheckingUsername = true;
				showValidation(feedback, 'Überprüfe Benutzername...', 'error');
				usernameInput.classList.remove('success', 'error');
				updateSubmitButton();
				
				console.log('Sending AJAX request for username:', username);
				
				// AJAX-Anfrage zur Überprüfung des Benutzernamens
				const xhr = new XMLHttpRequest();
				xhr.open('POST', 'check_username.php', true);
				xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
				
				xhr.onreadystatechange = function() {
					console.log('XHR readyState:', xhr.readyState, 'status:', xhr.status);
					if (xhr.readyState === 4) {
						isCheckingUsername = false;
						console.log('XHR completed, status:', xhr.status, 'response:', xhr.responseText);
						
						if (xhr.status === 200) {
							try {
								const response = JSON.parse(xhr.responseText);
								console.log('Parsed response:', response);
								
								if (response.available) {
									showValidation(feedback, '✓ Benutzername ist verfügbar', 'success');
									usernameInput.classList.remove('error');
									usernameInput.classList.add('success');
									usernameValid = true;
								} else {
									const errorMsg = response.error || 'Dieser Benutzername ist bereits vergeben';
									showValidation(feedback, '✗ ' + errorMsg, 'error');
									usernameInput.classList.remove('success');
									usernameInput.classList.add('error');
									usernameValid = false;
								}
							} catch (e) {
								console.error('Error parsing response:', e, 'Response text:', xhr.responseText);
								showValidation(feedback, '✗ Fehler bei der Überprüfung', 'error');
								usernameInput.classList.remove('success');
								usernameInput.classList.add('error');
								usernameValid = false;
							}
						} else {
							console.error('HTTP error:', xhr.status);
							showValidation(feedback, '✗ Server-Fehler bei der Überprüfung (Status: ' + xhr.status + ')', 'error');
							usernameInput.classList.remove('success');
							usernameInput.classList.add('error');
							usernameValid = false;
						}
						updateSubmitButton();
					}
				};
				
				xhr.onerror = function() {
					console.error('XHR network error');
					isCheckingUsername = false;
					showValidation(feedback, '✗ Netzwerk-Fehler bei der Überprüfung', 'error');
					usernameInput.classList.remove('success');
					usernameInput.classList.add('error');
					usernameValid = false;
					updateSubmitButton();
				};
				
				// Timeout nach 10 Sekunden
				setTimeout(function() {
					if (isCheckingUsername) {
						console.error('XHR timeout');
						xhr.abort();
						isCheckingUsername = false;
						showValidation(feedback, '✗ Zeitüberschreitung bei der Überprüfung', 'error');
						usernameInput.classList.remove('success');
						usernameInput.classList.add('error');
						usernameValid = false;
						updateSubmitButton();
					}
				}, 10000);
				
				xhr.send('username=' + encodeURIComponent(username));
			}
			
			function validatePassword() {
				const password = passwordInput.value;
				const feedback = document.getElementById('passwordFeedback');
				
				console.log('Password validation:', password.length);
				
				if (password.length < 6) {
					showValidation(feedback, '✗ Passwort muss mindestens 6 Zeichen lang sein', 'error');
					passwordInput.classList.remove('success');
					passwordInput.classList.add('error');
					passwordValid = false;
				} else {
					showValidation(feedback, '✓ Passwortlänge okay', 'success');
					passwordInput.classList.remove('error');
					passwordInput.classList.add('success');
					passwordValid = true;
				}
				
				// Auch Passwort-Bestätigung neu validieren
				validatePasswordMatch();
				updateSubmitButton();
			}
			
			function validatePasswordMatch() {
				const password = passwordInput.value;
				const passwordConfirm = passwordConfirmInput.value;
				const feedback = document.getElementById('passwordConfirmFeedback');
				
				console.log('Password match check:', password, 'vs', passwordConfirm);
				
				if (passwordConfirm === '') {
					hideValidation(feedback);
					passwordConfirmInput.classList.remove('error', 'success');
					passwordMatch = false;
				} else if (password === passwordConfirm && password.length >= 6) {
					showValidation(feedback, '✓ Passwörter stimmen überein', 'success');
					passwordConfirmInput.classList.remove('error');
					passwordConfirmInput.classList.add('success');
					passwordMatch = true;
				} else {
					showValidation(feedback, '✗ Passwörter stimmen nicht überein', 'error');
					passwordConfirmInput.classList.remove('success');
					passwordConfirmInput.classList.add('error');
					passwordMatch = false;
				}
				
				updateSubmitButton();
			}
			
			function validateEmail() {
				const email = emailInput.value.trim();
				const feedback = document.getElementById('emailFeedback');
				
				console.log('Email validation:', email);
				
				if (!isValidEmail(email)) {
					showValidation(feedback, '✗ Bitte geben Sie eine gültige E-Mail-Adresse ein', 'error');
					emailInput.classList.remove('success');
					emailInput.classList.add('error');
					emailValid = false;
				} else {
					showValidation(feedback, '✓ Gültige E-Mail-Adresse', 'success');
					emailInput.classList.remove('error');
					emailInput.classList.add('success');
					emailValid = true;
				}
				updateSubmitButton();
			}
			
			function isValidEmail(email) {
				const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
				return emailRegex.test(email);
			}
			
			function showValidation(element, message, type) {
				if (element) {
					element.textContent = message;
					element.className = 'validation-feedback ' + type;
					element.style.display = 'block';
				}
			}
			
			function hideValidation(element) {
				if (element) {
					element.style.display = 'none';
				}
			}
			
			function updateSubmitButton() {
				console.log('Update submit button:', {
					usernameValid, 
					passwordValid, 
					passwordMatch, 
					emailValid, 
					isCheckingUsername
				});
				
				if (usernameValid && passwordValid && passwordMatch && emailValid && !isCheckingUsername) {
					submitButton.disabled = false;
					submitButton.title = '';
				} else {
					submitButton.disabled = true;
					if (isCheckingUsername) {
						submitButton.title = 'Warten Sie auf die Benutzernamen-Überprüfung...';
					} else {
						submitButton.title = 'Bitte füllen Sie alle Felder korrekt aus';
					}
				}
			}
			
			// Initiale Prüfung
			updateSubmitButton();
			
			// Debug-Info
			console.log('Form validation initialized successfully');
			
			// Automatischen Test nach 2 Sekunden ausführen
			setTimeout(testAjaxConnection, 2000);
		});
	</script>
</body>
</html>