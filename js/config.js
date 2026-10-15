// config.js - Korrigierte Version
const CONFIG = {
    API_ENDPOINTS: {
        LOAD_CONTACTS: 'load_contacts.php',
        ADD_CONTACT: 'add_contact.php',
        UPDATE_CONTACT: 'update_contact.php',
        DELETE_CONTACT: 'delete_contact.php',
        UPDATE_STATUS: 'update_contact_status.php'
    },
    
    STATUS_CLASSES: {
        'Spende zugesagt': 'status-zugesagt',
        'Handlungsbedarf': 'status-handlungsbedarf',
        'Spende abgesagt': 'status-abgesagt',
        '': 'status-anfrage-ausstehend'
    },
    
    TYP_BADGES: {
        'Mail': { text: 'Mail', class: 'typ-mail' },
        'Formular': { text: 'Formular', class: 'typ-formular' },
        'Persönlich': { text: 'Lokal', class: 'typ-persoenlich' }
    }
};

// Globale Variablen
let contacts = [];
let currentEditId = null;

// WICHTIG: Funktionen mit function deklarieren für Hoisting
function hasPermission(permission) {
    if (typeof USER_PERMISSIONS === 'undefined') {
        return false;
    }
    
    if (!USER_PERMISSIONS || !Array.isArray(USER_PERMISSIONS)) {
        return false;
    }
    
    return USER_PERMISSIONS.includes(permission);
}

// Berechtigungs-Check für Aktionen - als function deklarieren für Hoisting
function canAddContacts() { return hasPermission('add_contacts'); }
function canEditContacts() { return hasPermission('edit_contacts'); }
function canDeleteContacts() { return hasPermission('delete_contacts'); }
function canSendEmails() { return hasPermission('send_emails'); }
function canChangeStatus() { return hasPermission('change_status'); }
function canEditComments() { return hasPermission('edit_comments'); }
function canViewContacts() { return hasPermission('view_contacts'); }
function canSuggestContacts() { return hasPermission('suggest_contacts'); }

// Kombinierte Berechtigung für Kontakterstellung
function canCreateContacts() { return canAddContacts() || canSuggestContacts(); }

// Prüfen ob es sich um einen Vorschlag handelt
function isSuggestionContact() { return canSuggestContacts() && !canAddContacts(); }

// Erweiterte Berechtigungsprüfung für Status-Änderungen
function canChangeContactStatus(contact) {
    if (!canChangeStatus()) return false;
    
    if (contact && contact.Typ === 'Mail') {
        return canSendEmails();
    }
    
    return true;
}