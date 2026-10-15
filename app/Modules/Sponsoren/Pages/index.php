
<?php
/**
 * Implementierung der Sponsorenübersicht.
 * Wird ausschließlich über den bisherigen Einstiegspunkt /index.php eingebunden.
 */
if (!defined('APP_ENTRY_POINT')) {
    http_response_code(404);
    exit;
}

$appRoot = dirname(__DIR__, 4);
require_once $appRoot . '/auth.php';
checkPermission('view_contacts');
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spendenkontakte Verwaltung</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
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
        
        .add-contact-form {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 0;
            margin: 20px 0;
            max-height: 0;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .add-contact-form.expanded {
            padding: 20px;
            max-height: 1000px;
        }
        
        .add-contact-form h3 {
            margin-top: 0;
            color: var(--primary-color);
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 10px;
        }
        
        .form-buttons {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
        }

        /* NEU: CSS für Vorschlag-Badge */
        .suggestion-badge {
            background-color: #ffc107;
            color: #212529;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.7em;
            margin-left: 8px;
            font-weight: bold;
        }
    </style>
    <link rel="stylesheet" href="includes/navigation.css">
</head>
<body>
    <div class="container">
        <!-- Header mit Benutzerinfo und Add-Button -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1>Spendenkontakte Verwaltung</h1>
            <div style="display: flex; align-items: center; gap: 15px;">                
                <?php require $appRoot . '/includes/navigation.php'; ?>
            </div>
        </div>
        
        <div class="search-filter-container">
            <div class="search-container">
                <input type="text" id="searchInput" class="search-input" placeholder="Kontakte durchsuchen...">
            </div>
						            
            <div class="filter-container">
                <div class="filter-group">
                    <label for="contactTypeFilter" class="filter-label">Kontakttyp:</label>
                    <select id="contactTypeFilter" class="filter-select">
                        <option value="alle" selected>Alle Kontakte</option>
                        <option value="Mail">Mail-Kontakte</option>
                        <option value="Formular">Formular-Kontakte</option>
                        <option value="Persönlich">Lokale Kontakte</option>
                    </select>
                </div>
                
                <span class="filter-label">Statusfilter:</span>
                <div class="filter-checkbox">
                    <input type="checkbox" id="filterAnfrageAusstehend" checked>
                    <label for="filterAnfrageAusstehend">Anfrage ausstehend</label>
                </div>
                <div class="filter-checkbox">
                    <input type="checkbox" id="filterAntwort" checked>
                    <label for="filterAntwort">Antwort abwarten</label>
                </div>
                <div class="filter-checkbox">
                    <input type="checkbox" id="filterHandlungsbedarf" checked>
                    <label for="filterHandlungsbedarf">Handlungsbedarf</label>
                </div>
                <div class="filter-checkbox">
                    <input type="checkbox" id="filterZugesagt">
                    <label for="filterZugesagt">Spende zugesagt</label>
                </div>
                <div class="filter-checkbox">
                    <input type="checkbox" id="filterAbgesagt">
                    <label for="filterAbgesagt">Spende abgesagt</label>
                </div>
            </div>
			
			<?php if (hasPermission('add_contacts') || hasPermission('suggest_contacts')): ?>
                <button id="toggleFormBtn" class="btn btn-primary" style="padding: 8px 12px;">
                    <i class="fas fa-plus"></i> 
                    <?php echo hasPermission('add_contacts') ? 'Neuer Kontakt' : 'Kontakt vorschlagen'; ?>
                </button>
            <?php endif; ?>
        </div>
        
        <div id="notification" class="notification"></div>
        
        <!-- Ausgeblendetes Formular -->
        <?php if (hasPermission('add_contacts') || hasPermission('suggest_contacts')): ?>
        <div id="addContactFormContainer" class="add-contact-form">
            <h3><?php echo hasPermission('add_contacts') ? 'Neuen Kontakt hinzufügen' : 'Kontakt vorschlagen'; ?></h3>
            <form id="addContactForm">
                <div class="form-row">
                    <div class="form-group required">
                        <label for="newName">Name</label>
                        <input type="text" id="newName" class="form-control" required>
                    </div>
                    <div class="form-group required">
                        <label for="newType">Kontakttyp</label>
                        <select id="newType" class="form-control" required>
                            <option value="Mail" selected>Mail-Kontakt</option>
                            <option value="Formular">Formular-Kontakt</option>
                            <option value="Persönlich">Lokaler Kontakt</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row" id="mailFields">
                    <div class="form-group required" id="anredeGroup">
                        <label for="newAnrede">Anrede</label>
                        <input type="text" id="newAnrede" class="form-control" placeholder="z.B. Herr, Frau, Dr.">
                    </div>
                    <div class="form-group required" id="emailGroup">
                        <label for="newEmail">E-Mail</label>
                        <input type="email" id="newEmail" class="form-control">
                    </div>
                </div>
                
                <div class="form-row" id="formularFields" style="display: none;">
                    <div class="form-group required">
                        <label for="newFormularLink">Formular-Link</label>
                        <input type="text" id="newFormularLink" class="form-control" placeholder="https://...">
                    </div>
                </div>
                
                <div class="form-row" id="persoenlichFields" style="display: none;">
                    <div class="form-group required">
                        <label for="newAnschrift">Anschrift</label>
                        <input type="text" id="newAnschrift" class="form-control" placeholder="Vollständige Anschrift">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="newWebsite">Webseite</label>
                        <input type="text" id="newWebsite" class="form-control">
                    </div>
                </div>
                
                <div class="form-buttons">
                    <button type="button" id="cancelAddContact" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Abbrechen
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus"></i> 
                        <?php echo hasPermission('add_contacts') ? 'Eintrag anlegen' : 'Vorschlag erstellen'; ?>
                    </button>
                </div>
            </form>
        </div>
        <?php endif; ?>
        
        <div id="loading" class="loading">
            <i class="fas fa-spinner fa-spin"></i> Lade Kontakte...
        </div>
        
        <table id="contactsTable" style="display: none;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th class="status-column">Status</th>
                    <th>Zuletzt kontaktiert</th>
                    <th>Kommentar</th>
                    <th>Letztes Ergebnis</th>
                    <th class="actions-header">Aktionen</th>
                </tr>
            </thead>
            <tbody id="contactsBody">
                <!-- Kontakte werden dynamisch eingefügt -->
            </tbody>
        </table>
    </div>
    
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h3>Kontakt löschen</h3>
            <p id="deleteMessage">Bist Du sicher, dass Du diesen Kontakt löschen möchtest?</p>
            <div class="modal-buttons">
                <button id="cancelDelete" class="btn">
                    <i class="fas fa-times"></i> Abbrechen
                </button>
                <button id="confirmDelete" class="btn" style="background-color: var(--danger-color);">
                    <i class="fas fa-trash"></i> Löschen
                </button>
            </div>
        </div>
    </div>

    <!-- JavaScript-Dateien in korrekter Reihenfolge -->
    <script>
        // Benutzerberechtigungen an Frontend übergeben - ZUERST definieren
        const USER_PERMISSIONS = <?php echo json_encode($_SESSION['permissions'] ?? []); ?>;
        console.log('User Permissions:', USER_PERMISSIONS);
    </script>
    
    <script src="js/config.js"></script>
    <script src="js/utils.js"></script>
    <script src="js/api.js"></script>
    <script src="js/tableManager.js"></script>
    <script src="js/formManager.js"></script>
    <script src="js/modalManager.js"></script>
    <script src="js/eventManager.js"></script>
    <script src="js/app.js"></script>
    
    <script>
        // Toggle-Funktionalität für das Formular
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('toggleFormBtn');
            const formContainer = document.getElementById('addContactFormContainer');
            const cancelBtn = document.getElementById('cancelAddContact');
            
            if (toggleBtn && formContainer) {
                toggleBtn.addEventListener('click', function() {
                    const isExpanded = formContainer.classList.toggle('expanded');
                    
                    if (isExpanded) {
                        toggleBtn.innerHTML = '<i class="fas fa-times"></i> Abbrechen';
                        toggleBtn.style.backgroundColor = 'var(--button-gray)';
                    } else {
                        toggleBtn.innerHTML = '<i class="fas fa-plus"></i> ' + 
                            (<?php echo hasPermission('add_contacts') ? 'true' : 'false'; ?> ? 'Neuer Kontakt' : 'Kontakt vorschlagen');
                        toggleBtn.style.backgroundColor = '';
                        // Formular zurücksetzen wenn ausgeblendet
                        document.getElementById('addContactForm').reset();
                        FormManager.updateFormFields();
                    }
                });
            }
            
            if (cancelBtn && formContainer) {
                cancelBtn.addEventListener('click', function() {
                    formContainer.classList.remove('expanded');
                    if (toggleBtn) {
                        toggleBtn.innerHTML = '<i class="fas fa-plus"></i> ' + 
                            (<?php echo hasPermission('add_contacts') ? 'true' : 'false'; ?> ? 'Neuer Kontakt' : 'Kontakt vorschlagen');
                        toggleBtn.style.backgroundColor = '';
                    }
                    // Formular zurücksetzen
                    document.getElementById('addContactForm').reset();
                    FormManager.updateFormFields();
                });
            }
            
            // Formular zurücksetzen nach erfolgreichem Absenden
            const originalFormSubmit = FormManager.handleFormSubmit;
            FormManager.handleFormSubmit = async function(e) {
                await originalFormSubmit.call(this, e);
                // Formular ausblenden nach erfolgreichem Absenden
                if (formContainer && formContainer.classList.contains('expanded')) {
                    formContainer.classList.remove('expanded');
                    if (toggleBtn) {
                        toggleBtn.innerHTML = '<i class="fas fa-plus"></i> ' + 
                            (<?php echo hasPermission('add_contacts') ? 'true' : 'false'; ?> ? 'Neuer Kontakt' : 'Kontakt vorschlagen');
                        toggleBtn.style.backgroundColor = '';
                    }
                }
            };
        });
    </script>
</body>
</html>
