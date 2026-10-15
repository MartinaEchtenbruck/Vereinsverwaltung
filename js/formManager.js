
class FormManager {
    static init() {
        // Nur initialisieren wenn der Benutzer die Berechtigung hat und das Formular existiert
        if (!canCreateContacts()) {
            return;
        }
        
        const newTypeSelect = document.getElementById('newType');
        if (!newTypeSelect) {
            return;
        }
        
        this.updateFormFields();
        this.setupAutoComplete();
    }

    static updateFormFields() {
        const typeSelect = document.getElementById('newType');
        if (!typeSelect) return;
        
        const type = typeSelect.value;
        
        const mailFields = document.getElementById('mailFields');
        const formularFields = document.getElementById('formularFields');
        const persoenlichFields = document.getElementById('persoenlichFields');
        
        if (mailFields) mailFields.style.display = type === 'Mail' ? 'flex' : 'none';
        if (formularFields) formularFields.style.display = type === 'Formular' ? 'flex' : 'none';
        if (persoenlichFields) persoenlichFields.style.display = type === 'Persönlich' ? 'flex' : 'none';
        
        this.updateRequiredFields(type);
    }

    static updateRequiredFields(type) {
        const anredeField = document.getElementById('newAnrede');
        const emailField = document.getElementById('newEmail');
        const formularLinkField = document.getElementById('newFormularLink');
        const anschriftField = document.getElementById('newAnschrift');
        
        if (anredeField) anredeField.required = type === 'Mail';
        if (emailField) emailField.required = type === 'Mail';
        if (formularLinkField) formularLinkField.required = type === 'Formular';
        if (anschriftField) anschriftField.required = type === 'Persönlich';
    }

    static setupAutoComplete() {
        const nameField = document.getElementById('newName');
        const anredeField = document.getElementById('newAnrede');
        const emailField = document.getElementById('newEmail');
        const websiteField = document.getElementById('newWebsite');
        const typeSelect = document.getElementById('newType');
        
        if (!nameField || !anredeField || !emailField || !websiteField || !typeSelect) {
            return;
        }
        
        nameField.addEventListener('blur', (e) => {
            const name = e.target.value.trim();
            const contactType = typeSelect.value;
            
            if (contactType === 'Mail' && name && !anredeField.value.trim()) {
                anredeField.value = `liebes Team von ${name}`;
            }
        });

        emailField.addEventListener('blur', (e) => {
            const email = e.target.value.trim();
            const contactType = typeSelect.value;
            
            if (contactType === 'Mail' && email && !websiteField.value.trim()) {
                const domain = email.split('@')[1];
                if (domain) {
                    websiteField.value = `https://${domain}`;
                }
            }
        });
    }

    static async handleFormSubmit(e) {
        e.preventDefault();
        
        const formData = this.collectFormData();
        if (formData && this.validateFormData(formData)) {
            await this.submitForm(formData);
        }
    }

    static collectFormData() {
        const typeSelect = document.getElementById('newType');
        if (!typeSelect) return null;
        
        const type = typeSelect.value;
        
        return {
            name: document.getElementById('newName')?.value.trim() || '',
            typ: type,
            anrede: document.getElementById('newAnrede')?.value.trim() || '',
            emailadresse: document.getElementById('newEmail')?.value.trim() || '',
            webseite: document.getElementById('newWebsite')?.value.trim() || '',
            formular_link: document.getElementById('newFormularLink')?.value.trim() || '',
            anschrift: document.getElementById('newAnschrift')?.value.trim() || '',
            status: "",
            kommentar: "",
            letztes_ergebnis: ""
        };
    }

    static validateFormData(data) {
        if (!data.name) {
            Utils.showNotification('Bitte geben Sie einen Namen ein.', 'error');
            return false;
        }

        switch(data.typ) {
            case 'Mail':
                if (!data.anrede) {
                    Utils.showNotification('Bitte geben Sie eine Anrede ein.', 'error');
                    return false;
                }
                if (!data.emailadresse) {
                    Utils.showNotification('Bitte geben Sie eine E-Mail-Adresse ein.', 'error');
                    return false;
                }
                break;
            case 'Formular':
                if (!data.formular_link) {
                    Utils.showNotification('Bitte geben Sie einen Formular-Link ein.', 'error');
                    return false;
                }
                break;
            case 'Persönlich':
                if (!data.anschrift) {
                    Utils.showNotification('Bitte geben Sie eine Anschrift ein.', 'error');
                    return false;
                }
                break;
        }

        return true;
    }

    static async submitForm(formData) {
        try {
            const isSuggestion = isSuggestionContact();
            
            const submitData = {
                ...formData,
                is_suggestion: isSuggestion ? 1 : 0
            };
            
            const result = await ApiService.addContact(submitData);
            
            if (result.success) {
                const newContact = {
                    PrimaryKey: result.new_primary_key,
                    Name: formData.name,
                    Anrede: formData.anrede || "",
                    Webseite: formData.webseite || "",
                    emailadresse: formData.emailadresse || "",
                    Status: "",
                    Zuletzt_angeschrieben: "",
                    Kommentar: "",
                    Letztes_Ergebnis: "",
                    Typ: formData.typ,
                    formular_link: formData.formular_link || "",
                    anschrift: formData.anschrift || "",
                    is_suggestion: isSuggestion ? 1 : 0
                };
                
                contacts.push(newContact);
                TableManager.displayContacts();
                
                const message = isSuggestion 
                    ? `Kontaktvorschlag ${formData.name} wurde erfolgreich erstellt.` 
                    : `Kontakt ${formData.name} wurde erfolgreich angelegt.`;
                
                Utils.showNotification(message, 'success');
                
                // Formular zurücksetzen
                document.getElementById('addContactForm').reset();
                this.updateFormFields();
                
            } else {
                Utils.showNotification(`Fehler: ${result.error}`, 'error');
            }
        } catch (error) {
            Utils.showNotification('Fehler beim Anlegen des Kontakts: ' + error.message, 'error');
        }
    }
}
