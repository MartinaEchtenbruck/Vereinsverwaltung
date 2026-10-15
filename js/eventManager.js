
class EventManager {
    static setupEventListeners() {
        // Such- und Filter-Events
        const searchInput = document.getElementById('searchInput');
        const contactTypeFilter = document.getElementById('contactTypeFilter');
        
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                this.handleSearch(e.target.value);
            });
        }
        
        if (contactTypeFilter) {
            contactTypeFilter.addEventListener('change', () => {
                TableManager.displayContacts();
            });
        }
        
        // Status-Filter Events
        document.querySelectorAll('.filter-container input[type="checkbox"]').forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                TableManager.displayContacts();
            });
        });

        // Formular-Events (nur wenn Berechtigung vorhanden UND Formular existiert)
        if (canCreateContacts()) {
            const addContactForm = document.getElementById('addContactForm');
            if (addContactForm) {
                addContactForm.addEventListener('submit', (e) => {
                    FormManager.handleFormSubmit(e);
                });
            }
            
            const newTypeSelect = document.getElementById('newType');
            if (newTypeSelect) {
                newTypeSelect.addEventListener('change', () => {
                    FormManager.updateFormFields();
                });
            }
        }
    }

    static handleSearch(searchTerm) {
        // Sicherstellen, dass contacts Array existiert
        if (!contacts || !Array.isArray(contacts)) {
            return;
        }
        
        let filteredContacts = contacts.filter(contact => 
            contact.Name.toLowerCase().includes(searchTerm.toLowerCase()) ||
            contact.emailadresse.toLowerCase().includes(searchTerm.toLowerCase()) ||
            (contact.Kommentar && contact.Kommentar.toLowerCase().includes(searchTerm.toLowerCase())) ||
            (contact.Letztes_Ergebnis && contact.Letztes_Ergebnis.toLowerCase().includes(searchTerm.toLowerCase())) ||
            (contact.Webseite && contact.Webseite.toLowerCase().includes(searchTerm.toLowerCase())) ||
            (contact.Anrede && contact.Anrede.toLowerCase().includes(searchTerm.toLowerCase())) ||
            (contact.formular_link && contact.formular_link.toLowerCase().includes(searchTerm.toLowerCase())) ||
            (contact.anschrift && contact.anschrift.toLowerCase().includes(searchTerm.toLowerCase()))
        );
        
        // NEU: Sortierung für Suchergebnisse beibehalten
        filteredContacts.sort((a, b) => {
            // Zuerst nach is_suggestion sortieren (Vorschläge zuerst)
            if (b.is_suggestion !== a.is_suggestion) {
                return b.is_suggestion - a.is_suggestion; // DESC
            }
            // Dann alphabetisch nach Name
            return a.Name.localeCompare(b.Name); // ASC
        });
        
        TableManager.displayContacts(filteredContacts);
    }

    static handleEditClick(contactId) {
        if (currentEditId === contactId) {
            TableManager.cancelEdit(contactId);
        } else {
            currentEditId = contactId;
            TableManager.displayContacts();
            
            setTimeout(() => {
                const contact = contacts.find(c => c.PrimaryKey == contactId);
                if (contact) {
                    Utils.updateEditFormFields(contactId, contact.Typ);
                }
            }, 100);
        }
    }

    static handleEmailClick(contactId) {
        window.location.href = `contact.php?primarykey=${contactId}`;
    }
}
