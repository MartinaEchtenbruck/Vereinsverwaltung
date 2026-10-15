
class App {
    static async init() {
        // FormManager nur initialisieren wenn der Benutzer die Berechtigung hat
        if (canCreateContacts()) {
            FormManager.init();
        }
        
        // Kontakte laden
        await this.loadContacts();
        
        // Event Listener NACH dem Laden der Kontakte setzen
        EventManager.setupEventListeners();
        
        document.getElementById('loading').style.display = 'none';
        document.getElementById('contactsTable').style.display = 'table';
    }

    static async loadContacts() {
        try {
            const result = await ApiService.loadContacts();
            
            // Prüfen ob ein Fehler zurückkam
            if (result && result.error) {
                Utils.showNotification('Fehler: ' + result.error, 'error');
                document.getElementById('loading').style.display = 'none';
                return;
            }
            
            // Prüfen ob Ergebnis ein Array ist
            if (!Array.isArray(result)) {
                Utils.showNotification('Fehler: Ungültiges Antwortformat', 'error');
                document.getElementById('loading').style.display = 'none';
                return;
            }
            
            contacts = result;
            
            // Tabelle anzeigen
            TableManager.displayContacts();
            
        } catch (error) {
            Utils.showNotification('Fehler beim Laden der Kontakte: ' + error.message, 'error');
            document.getElementById('loading').style.display = 'none';
        }
    }
}

// App starten
document.addEventListener('DOMContentLoaded', () => {
    App.init();
});
