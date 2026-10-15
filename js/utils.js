
class Utils {
    static showNotification(message, type = 'success') {
        const notification = document.getElementById('notification');
        if (!notification) {
            console.warn('Notification element not found');
            return;
        }
        notification.textContent = message;
        notification.className = `notification ${type}`;
        notification.style.display = 'block';
        setTimeout(() => notification.style.display = 'none', 3000);
    }

    static applyStatusFilter(contactsToFilter) {
        const filterAntwort = document.getElementById('filterAntwort')?.checked || false;
        const filterHandlungsbedarf = document.getElementById('filterHandlungsbedarf')?.checked || false;
        const filterZugesagt = document.getElementById('filterZugesagt')?.checked || false;
        const filterAbgesagt = document.getElementById('filterAbgesagt')?.checked || false;
        const filterAnfrageAusstehend = document.getElementById('filterAnfrageAusstehend')?.checked || false;
        
        let filteredContacts = contactsToFilter.filter(contact => {
            // Leeren Status als "Anfrage ausstehend" behandeln
            if (!contact.Status || contact.Status === '') {
                return filterAnfrageAusstehend;
            }
            switch(contact.Status) {
                case 'Antwort abwarten': return filterAntwort;
                case 'Handlungsbedarf': return filterHandlungsbedarf;
                case 'Spende zugesagt': return filterZugesagt;
                case 'Spende abgesagt': return filterAbgesagt;
                default: return true;
            }
        });

        // NEU: Sortierung beibehalten - Vorschläge zuerst, dann alphabetisch
        filteredContacts.sort((a, b) => {
            // Zuerst nach is_suggestion sortieren (Vorschläge zuerst)
            if (b.is_suggestion !== a.is_suggestion) {
                return b.is_suggestion - a.is_suggestion; // DESC
            }
            // Dann alphabetisch nach Name
            return a.Name.localeCompare(b.Name); // ASC
        });

        return filteredContacts;
    }

    static getStatusClass(status) {
        return CONFIG.STATUS_CLASSES[status] || '';
    }

    static getTypBadge(typ) {
        return CONFIG.TYP_BADGES[typ] || { text: '', class: '' };
    }

    static updateEditFormFields(contactId, type) {
        const mailFields = document.getElementById(`editMailFields_${contactId}`);
        const formularFields = document.getElementById(`editFormularFields_${contactId}`);
        const persoenlichFields = document.getElementById(`editPersoenlichFields_${contactId}`);
        
        if (mailFields) mailFields.style.display = type === 'Mail' ? 'flex' : 'none';
        if (formularFields) formularFields.style.display = type === 'Formular' ? 'flex' : 'none';
        if (persoenlichFields) persoenlichFields.style.display = type === 'Persönlich' ? 'flex' : 'none';
        
        this.updateEditRequiredFields(contactId, type);
    }

    static updateEditRequiredFields(contactId, type) {
        const anredeField = document.getElementById(`editAnrede_${contactId}`);
        const emailField = document.getElementById(`editEmail_${contactId}`);
        const formularLinkField = document.getElementById(`editFormularLink_${contactId}`);
        const anschriftField = document.getElementById(`editAnschrift_${contactId}`);
        
        if (anredeField) anredeField.required = type === 'Mail';
        if (emailField) emailField.required = type === 'Mail';
        if (formularLinkField) formularLinkField.required = type === 'Formular';
        if (anschriftField) anschriftField.required = type === 'Persönlich';
    }

    static extractDomain(url) {
        if (!url) return '';
        try {
            let domain = url.replace(/^(https?:\/\/)?(www\.)?/, '');
            domain = domain.split('/')[0];
            return domain;
        } catch (error) {
            return url;
        }
    }

    static generateContactInfoHTML(contact) {
        const typBadge = this.getTypBadge(contact.Typ);
        let contactDetail = '';
        
        switch(contact.Typ) {
            case 'Mail':
                contactDetail = `<div class="contact-email">${contact.emailadresse || ''}</div>`;
                break;
            case 'Formular':
                const domain = this.extractDomain(contact.formular_link);
                if (domain && contact.formular_link) {
                    contactDetail = `<a href="${contact.formular_link}" target="_blank" class="website-link" title="${contact.formular_link}">${domain}</a>`;
                } else {
                    contactDetail = `<div class="contact-website">${contact.formular_link || ''}</div>`;
                }
                break;
            case 'Persönlich':
                contactDetail = `<div class="contact-address">${contact.anschrift || ''}</div>`;
                break;
        }
        
        // Vorschlag-Badge hinzufügen
        const suggestionBadge = contact.is_suggestion 
            ? '<span class="suggestion-badge" title="Kontaktvorschlag">Vorschlag</span>' 
            : '';
        
        return `
            <div class="contact-info">
                <div class="contact-name">${contact.Name} ${suggestionBadge}</div>
                ${contactDetail}
                <span class="typ-badge ${typBadge.class}">${typBadge.text}</span>
            </div>
        `;
    }

    static generateActionButtonsHTML(contact) {
        let buttons = '';
        
        // Edit-Button anzeigen wenn vollständige Edit-Berechtigung vorhanden
        const hasFullEditPermission = canEditContacts();
        
        if (hasFullEditPermission) {
            buttons += `<button class="btn" data-id="${contact.PrimaryKey}" title="Kontakt bearbeiten">
                <i class="fas fa-edit"></i>
            </button>`;
        }
        
        // Kommentar-Button NUR anzeigen wenn Kommentar-Berechtigung vorhanden 
        // ABER KEINE vollständige Edit-Berechtigung (um Dopplung zu vermeiden)
        if (canEditComments() && !hasFullEditPermission) {
            buttons += `<button class="btn" data-id="${contact.PrimaryKey}" title="Kommentar bearbeiten">
                <i class="fas fa-comment"></i>
            </button>`;
        }
        
        // Email-Button nur anzeigen wenn Berechtigung vorhanden  
        if (canSendEmails() && contact.Typ === 'Mail') {
            buttons += `<button class="btn" data-id="${contact.PrimaryKey}" title="Anschreiben">
                <i class="fas fa-envelope"></i>
            </button>`;
        }
        
        // Formular-Button (immer sichtbar für berechtigte User)
        if (contact.Typ === 'Formular') {
            buttons += `<button class="btn" data-id="${contact.PrimaryKey}" title="Formular abgeschickt">
                <i class="fas fa-check-circle"></i>
            </button>`;
        }
        
        // Persönlich-Button (immer sichtbar für berechtigte User)
        if (contact.Typ === 'Persönlich') {
            buttons += `<button class="btn" data-id="${contact.PrimaryKey}" title="Persönlich angefragt">
                <i class="fas fa-user-check"></i>
            </button>`;
        }
        
        // Delete-Button nur anzeigen wenn Berechtigung vorhanden
        if (canDeleteContacts()) {
            buttons += `<button class="btn" data-id="${contact.PrimaryKey}" title="Löschen">
                <i class="fas fa-trash"></i>
            </button>`;
        }
        
        return `
            <div class="actions-column">
                ${buttons}
            </div>
        `;
    }

    static getCurrentDate() {
        const now = new Date();
        const day = String(now.getDate()).padStart(2, '0');
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const year = now.getFullYear();
        return `${day}.${month}.${year}`;
    }

    static formatDateForDisplay(dateString) {
        if (!dateString || dateString === '0000-00-00' || dateString === '') {
            return '';
        }
        
        if (dateString.includes('.')) {
            return dateString;
        }
        
        try {
            const parts = dateString.split('-');
            if (parts.length === 3) {
                return `${parts[2]}.${parts[1]}.${parts[0]}`;
            }
        } catch (error) {
            console.error('Fehler beim Formatieren des Datums:', error);
        }
        
        return dateString;
    }

    static askForRequesterName() {
        return prompt('Wer hat den lokalen Kontakt angefragt?', '');
    }

    static updateCommentWithRequester(contact, requesterName) {
        const currentDate = this.getCurrentDate();
        const newEntry = `${currentDate}: Angefragt von ${requesterName}`;
        
        if (!contact.Kommentar || contact.Kommentar.trim() === '') {
            return newEntry;
        } else {
            return `${contact.Kommentar}\n${newEntry}`;
        }
    }

    static askForCommentUpdate(contact) {
        const currentComment = contact.Kommentar || '';
        return prompt('Kommentar bearbeiten:', currentComment);
    }
}
