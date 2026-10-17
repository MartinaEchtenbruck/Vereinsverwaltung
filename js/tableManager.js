class TableManager {
    static displayContacts(contactsToDisplay = contacts) {
        const contactsBody = document.getElementById('contactsBody');
        const selectedType = document.getElementById('contactTypeFilter');
        
        // Null-Checks für DOM-Elemente
        if (!contactsBody) {
            console.error('contactsBody element not found');
            return;
        }
        
        // Sicherstellen, dass contactsToDisplay ein Array ist
        if (!Array.isArray(contactsToDisplay)) {
            console.error('contactsToDisplay is not an array:', contactsToDisplay);
            contactsToDisplay = [];
        }
        
        let filteredContacts = selectedType && selectedType.value !== 'alle' 
            ? contactsToDisplay.filter(contact => contact.Typ === selectedType.value)
            : contactsToDisplay;
        filteredContacts = Utils.applyStatusFilter(filteredContacts);
        
        contactsBody.innerHTML = this.generateTableHTML(filteredContacts);
        this.attachTableEventListeners();
    }

    static generateTableHTML(filteredContacts) {
        // Sicherstellen, dass filteredContacts ein Array ist
        if (!Array.isArray(filteredContacts)) {
            console.error('filteredContacts is not an array:', filteredContacts);
            return '<tr><td colspan="7" style="text-align: center;">Fehler beim Laden der Kontakte</td></tr>';
        }

        if (filteredContacts.length === 0) {
            return '<tr><td colspan="7" style="text-align: center;">Keine Kontakte gefunden</td></tr>';
        }

        return filteredContacts.map(contact => {
            const isEditing = currentEditId == contact.PrimaryKey;
            const statusClass = Utils.getStatusClass(contact.Status);
            const isSuggestion = contact.is_suggestion;
            const canAcceptSuggestion = isSuggestion && canAddContacts();
            
            // Status-Änderungsberechtigung prüfen - Funktion direkt aufrufen
            const canChangeStatusForContact = canChangeContactStatus(contact);
            
            return `
                <tr class="${statusClass} ${isEditing ? 'edit-row' : ''}">
                    <td>${contact.PrimaryKey}</td>
                    <td>${Utils.generateContactInfoHTML(contact)}</td>
                    <td>
                        <select class="status-select" data-id="${contact.PrimaryKey}" ${canChangeStatusForContact ? '' : 'disabled'}>
                            <option value="" ${(!contact.Status || contact.Status === '' || contact.Status === 'Anfrage ausstehend') ? 'selected' : ''} class="status-option">Anfrage ausstehend</option>
                            <option value="Antwort abwarten" ${contact.Status === 'Antwort abwarten' ? 'selected' : ''} class="status-option">Antwort abwarten</option>
                            <option value="Spende zugesagt" ${contact.Status === 'Spende zugesagt' ? 'selected' : ''} class="status-option">Spende zugesagt</option>
                            <option value="Handlungsbedarf" ${contact.Status === 'Handlungsbedarf' ? 'selected' : ''} class="status-option">Handlungsbedarf</option>
                            <option value="Spende abgesagt" ${contact.Status === 'Spende abgesagt' ? 'selected' : ''} class="status-option">Spende abgesagt</option>
                        </select>
                    </td>
                    <td>${Utils.formatDateForDisplay(contact.Zuletzt_angeschrieben)}</td>
                    <td>${contact.Kommentar || ''}</td>
                    <td>${contact.Letztes_Ergebnis || ''}</td>
                    <td>${Utils.generateActionButtonsHTML(contact)}</td>
                </tr>
                ${isEditing ? this.generateEditFormHTML(contact) : ''}
            `;
        }).join('');
    }

    static generateEditFormHTML(contact) {
        const isSuggestion = contact.is_suggestion;
        const canAcceptSuggestion = isSuggestion && canAddContacts();
        
        return `
            <tr class="edit-row ${Utils.getStatusClass(contact.Status)}">
                <td colspan="7">
                    <div class="edit-form active">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                            <h4>
                                ${isSuggestion ? '<i class="fas fa-lightbulb" style="color: #f39c12; margin-right: 8px;"></i>' : ''}
                                Kontakt bearbeiten
                                ${isSuggestion ? '<span class="suggestion-badge" style="margin-left: 10px;">Vorschlag</span>' : ''}
                            </h4>
                        </div>
                        
                        <form class="contact-edit-form" data-id="${contact.PrimaryKey}">
                            <div class="form-row">
                                <div class="form-group required">
                                    <label for="editName_${contact.PrimaryKey}">Name</label>
                                    <input type="text" id="editName_${contact.PrimaryKey}" class="form-control" value="${contact.Name}" required>
                                </div>
                                <div class="form-group required">
                                    <label for="editType_${contact.PrimaryKey}">Kontakttyp</label>
                                    <select id="editType_${contact.PrimaryKey}" class="form-control" required>
                                        <option value="Mail" ${contact.Typ === 'Mail' ? 'selected' : ''}>Mail-Kontakt</option>
                                        <option value="Formular" ${contact.Typ === 'Formular' ? 'selected' : ''}>Formular-Kontakt</option>
                                        <option value="Persönlich" ${contact.Typ === 'Persönlich' ? 'selected' : ''}>Lokaler Kontakt</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-row" id="editMailFields_${contact.PrimaryKey}">
                                <div class="form-group required" id="editAnredeGroup_${contact.PrimaryKey}">
                                    <label for="editAnrede_${contact.PrimaryKey}">Anrede</label>
                                    <input type="text" id="editAnrede_${contact.PrimaryKey}" class="form-control" value="${contact.Anrede}" placeholder="z.B. Herr, Frau, Dr.">
                                </div>
                                <div class="form-group required" id="editEmailGroup_${contact.PrimaryKey}">
                                    <label for="editEmail_${contact.PrimaryKey}">E-Mail</label>
                                    <input type="email" id="editEmail_${contact.PrimaryKey}" class="form-control" value="${contact.emailadresse}">
                                </div>
                            </div>
                            
                            <div class="form-row" id="editFormularFields_${contact.PrimaryKey}">
                                <div class="form-group required">
                                    <label for="editFormularLink_${contact.PrimaryKey}">Formular-Link</label>
                                    <input type="text" id="editFormularLink_${contact.PrimaryKey}" class="form-control" value="${contact.formular_link || ''}" placeholder="https://...">
                                </div>
                            </div>
                            
                            <div class="form-row" id="editPersoenlichFields_${contact.PrimaryKey}">
                                <div class="form-group required anschrift-field">
                                    <label for="editAnschrift_${contact.PrimaryKey}">Anschrift</label>
                                    <input type="text" id="editAnschrift_${contact.PrimaryKey}" class="form-control" value="${contact.anschrift || ''}" placeholder="Vollständige Anschrift">
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="editWebsite_${contact.PrimaryKey}">Webseite</label>
                                    <input type="text" id="editWebsite_${contact.PrimaryKey}" class="form-control" value="${contact.Webseite}">
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="editStatus_${contact.PrimaryKey}">Status</label>
                                    <select id="editStatus_${contact.PrimaryKey}" class="form-control">
                                        <option value=""></option>
                                        <option value="Antwort abwarten" ${contact.Status === 'Antwort abwarten' ? 'selected' : ''}>Antwort abwarten</option>
                                        <option value="Spende zugesagt" ${contact.Status === 'Spende zugesagt' ? 'selected' : ''}>Spende zugesagt</option>
                                        <option value="Handlungsbedarf" ${contact.Status === 'Handlungsbedarf' ? 'selected' : ''}>Handlungsbedarf</option>
                                        <option value="Spende abgesagt" ${contact.Status === 'Spende abgesagt' ? 'selected' : ''}>Spende abgesagt</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="editZuletzt_${contact.PrimaryKey}">Zuletzt kontaktiert</label>
                                    <input type="text" id="editZuletzt_${contact.PrimaryKey}" class="form-control" value="${Utils.formatDateForDisplay(contact.Zuletzt_angeschrieben)}">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="editKommentar_${contact.PrimaryKey}">Kommentar</label>
                                    <textarea id="editKommentar_${contact.PrimaryKey}" class="form-control" rows="3">${contact.Kommentar || ''}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="editErgebnis_${contact.PrimaryKey}">Letztes Ergebnis</label>
                                    <input type="text" id="editErgebnis_${contact.PrimaryKey}" class="form-control" value="${contact.Letztes_Ergebnis || ''}">
                                </div>
                            </div>
                            
                            ${canAcceptSuggestion ? `
                                <div class="suggestion-notice" style="background: #e8f5e8; border: 1px solid #2ecc71; border-radius: 4px; padding: 15px; margin: 15px 0;">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <i class="fas fa-info-circle" style="color: var(--success-color); font-size: 1.2em;"></i>
                                        <div>
                                            <strong>Vorschlag annehmen</strong>
                                            <p style="margin: 5px 0 0 0; font-size: 0.9em;">
                                                Durch das Speichern wird dieser Vorschlag als regulärer Kontakt übernommen 
                                                und aus der Vorschlagsliste entfernt.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            ` : ''}
                            
                            <div class="edit-buttons">
                                <button type="button" class="btn btn-cancel" data-id="${contact.PrimaryKey}" style="width: auto; padding: 8px 16px;">
                                    <i class="fas fa-times" style="margin-right:5px;"></i> Abbrechen
                                </button>
                                <button type="submit" class="btn ${canAcceptSuggestion ? 'btn-success' : 'btn-save'}" style="width: auto; padding: 8px 16px;">
                                    <i class="fas ${canAcceptSuggestion ? 'fa-check-circle' : 'fa-save'}" style="margin-right:5px;"></i> ${canAcceptSuggestion ? ' Vorschlag annehmen' : ' Änderungen speichern'}
                                </button>
                            </div>
                        </form>
                    </div>
                </td>
            </tr>
        `;
    }

    static attachTableEventListeners() {
        // Status-Änderungen (nur wenn Berechtigung vorhanden)
        if (canChangeStatus()) {
            document.querySelectorAll('.status-select').forEach(select => {
                select.addEventListener('change', (e) => {
                    const contactId = e.target.getAttribute('data-id');
                    const newStatus = e.target.value;
                    this.handleStatusChange(contactId, newStatus);
                });
            });
        }

        // Edit-Buttons (nur wenn Berechtigung vorhanden)
        if (canEditContacts()) {
            document.querySelectorAll('.btn .fa-edit').forEach(icon => {
                const button = icon.closest('.btn');
                if (button) {
                    button.addEventListener('click', (e) => {
                        const contactId = button.getAttribute('data-id');
                        EventManager.handleEditClick(contactId);
                    });
                }
            });
        }

        // Kommentar-Buttons (nur wenn Kommentar-Berechtigung vorhanden UND KEINE Edit-Berechtigung)
        if (canEditComments() && !canEditContacts()) {
            document.querySelectorAll('.btn .fa-comment').forEach(icon => {
                const button = icon.closest('.btn');
                if (button) {
                    button.addEventListener('click', (e) => {
                        const contactId = button.getAttribute('data-id');
                        this.handleCommentEdit(contactId);
                    });
                }
            });
        }

        // Delete-Buttons (nur wenn Berechtigung vorhanden)
        if (canDeleteContacts()) {
            document.querySelectorAll('.btn .fa-trash').forEach(icon => {
                const button = icon.closest('.btn');
                if (button) {
                    button.addEventListener('click', (e) => {
                        const contactId = button.getAttribute('data-id');
                        ModalManager.showDeleteConfirmation(contactId);
                    });
                }
            });
        }

        // Email-Buttons (nur wenn Berechtigung vorhanden)
        if (canSendEmails()) {
            document.querySelectorAll('.btn .fa-envelope').forEach(icon => {
                const button = icon.closest('.btn');
                if (button) {
                    button.addEventListener('click', (e) => {
                        const contactId = button.getAttribute('data-id');
                        EventManager.handleEmailClick(contactId);
                    });
                }
            });
        }

        // Formular-Buttons (immer verfügbar für berechtigte User)
        document.querySelectorAll('.btn .fa-check-circle').forEach(icon => {
            const button = icon.closest('.btn');
            if (button) {
                button.addEventListener('click', (e) => {
                    const contactId = button.getAttribute('data-id');
                    this.handleFormularSent(contactId);
                });
            }
        });

        // Persönlich-Buttons (immer verfügbar für berechtigte User)
        document.querySelectorAll('.btn .fa-user-check').forEach(icon => {
            const button = icon.closest('.btn');
            if (button) {
                button.addEventListener('click', (e) => {
                    const contactId = button.getAttribute('data-id');
                    this.handlePersoenlichAsked(contactId);
                });
            }
        });

        // Edit-Formulare (nur wenn Berechtigung vorhanden)
        if (canEditContacts()) {
            document.querySelectorAll('.contact-edit-form').forEach(form => {
                form.addEventListener('submit', (e) => {
                    e.preventDefault();
                    const contactId = form.getAttribute('data-id');
                    this.handleEditSubmit(contactId);
                });
            });

            // Cancel-Buttons
            document.querySelectorAll('.btn-cancel').forEach(button => {
                if (button) {
                    button.addEventListener('click', (e) => {
                        const contactId = button.getAttribute('data-id');
                        this.cancelEdit(contactId);
                    });
                }
            });

            // Typ-Änderungen in Edit-Formularen
            document.querySelectorAll('select[id^="editType_"]').forEach(select => {
                select.addEventListener('change', (e) => {
                    const contactId = e.target.id.replace('editType_', '');
                    const newType = e.target.value;
                    Utils.updateEditFormFields(contactId, newType);
                });
            });
        }
    }

    static async handleEditSubmit(contactId) {
        if (!canEditContacts()) {
            Utils.showNotification('Sie haben keine Berechtigung zum Bearbeiten von Kontakten.', 'error');
            return;
        }

        const contactIndex = contacts.findIndex(c => c.PrimaryKey == contactId);
        if (contactIndex === -1) return;

        try {
            const contact = contacts[contactIndex];
            const isSuggestion = contact.is_suggestion;
            const canAcceptSuggestion = isSuggestion && canAddContacts();
            
            const contactData = {
                primary_key: contactId,
                name: document.getElementById(`editName_${contactId}`).value,
                anrede: document.getElementById(`editAnrede_${contactId}`).value,
                webseite: document.getElementById(`editWebsite_${contactId}`).value,
                emailadresse: document.getElementById(`editEmail_${contactId}`).value,
                typ: document.getElementById(`editType_${contactId}`).value,
                formular_link: document.getElementById(`editFormularLink_${contactId}`)?.value || '',
                anschrift: document.getElementById(`editAnschrift_${contactId}`)?.value || '',
                status: document.getElementById(`editStatus_${contactId}`).value,
                zuletzt_angeschrieben: document.getElementById(`editZuletzt_${contactId}`).value,
                kommentar: document.getElementById(`editKommentar_${contactId}`).value,
                letztes_ergebnis: document.getElementById(`editErgebnis_${contactId}`).value
            };

            // WICHTIG: is_suggestion auf 0 setzen wenn es sich um einen Vorschlag handelt
            // und der Benutzer die Berechtigung hat, diesen anzunehmen
            if (canAcceptSuggestion) {
                contactData.is_suggestion = 0;
            }

            const result = await ApiService.updateContact(contactData);
            if (result.success) {
                // Kontaktdaten aktualisieren
                Object.assign(contacts[contactIndex], {
                    Name: contactData.name,
                    Anrede: contactData.anrede,
                    Webseite: contactData.webseite,
                    emailadresse: contactData.emailadresse,
                    Typ: contactData.typ,
                    formular_link: contactData.formular_link,
                    anschrift: contactData.anschrift,
                    Status: contactData.status,
                    Zuletzt_angeschrieben: contactData.zuletzt_angeschrieben,
                    Kommentar: contactData.kommentar,
                    Letztes_Ergebnis: contactData.letztes_ergebnis
                });

                // is_suggestion aktualisieren falls geändert
                if (canAcceptSuggestion) {
                    contacts[contactIndex].is_suggestion = 0;
                }

                const successMessage = canAcceptSuggestion 
                    ? `Vorschlag "${contactData.name}" wurde als regulärer Kontakt übernommen.` 
                    : 'Kontakt wurde erfolgreich aktualisiert.';
                    
                Utils.showNotification(successMessage, 'success');
                currentEditId = null;
                this.displayContacts();
            } else {
                Utils.showNotification(`Fehler: ${result.error}`, 'error');
            }
        } catch (error) {
            Utils.showNotification('Fehler beim Speichern: ' + error.message, 'error');
        }
    }

    static cancelEdit(contactId) {
        currentEditId = null;
        this.displayContacts();
    }

    static async handleCommentEdit(contactId) {
        if (!canEditComments()) {
            Utils.showNotification('Sie haben keine Berechtigung zum Bearbeiten von Kommentaren.', 'error');
            return;
        }

        const contact = contacts.find(c => c.PrimaryKey == contactId);
        if (!contact) {
            Utils.showNotification('Kontakt nicht gefunden.', 'error');
            return;
        }

        const newComment = Utils.askForCommentUpdate(contact);
        if (newComment !== null) {
            try {
                console.log('Starting comment update for contact:', contactId);
                const result = await ApiService.updateContactComment(contactId, newComment);
                
                if (result.success) {
                    const contactIndex = contacts.findIndex(c => c.PrimaryKey == contactId);
                    if (contactIndex !== -1) {
                        contacts[contactIndex].Kommentar = newComment;
                    }
                    Utils.showNotification('Kommentar wurde erfolgreich aktualisiert.', 'success');
                    this.displayContacts();
                } else {
                    console.error('Comment update failed:', result.error);
                    Utils.showNotification(`Fehler: ${result.error}`, 'error');
                }
            } catch (error) {
                console.error('Error updating comment:', error);
                Utils.showNotification('Fehler beim Aktualisieren des Kommentars: ' + error.message, 'error');
            }
        }
    }

    static async handleStatusChange(contactId, newStatus) {
        if (!canChangeStatus()) {
            Utils.showNotification('Sie haben keine Berechtigung zum Ändern des Status.', 'error');
            return;
        }

        // Zusätzliche Prüfung für E-Mail-Kontakte
        const contact = contacts.find(c => c.PrimaryKey == contactId);
        if (contact && contact.Typ === 'Mail' && !canSendEmails()) {
            Utils.showNotification('Sie haben keine Berechtigung zum Ändern des Status von E-Mail-Kontakten.', 'error');
            return;
        }

        try {
            const result = await ApiService.updateContactStatus(contactId, newStatus);
            if (result.success) {
                const contactIndex = contacts.findIndex(c => c.PrimaryKey == contactId);
                if (contactIndex !== -1) {
                    contacts[contactIndex].Status = newStatus;
                }
                Utils.showNotification('Status wurde aktualisiert.', 'success');
                this.displayContacts();
            } else {
                Utils.showNotification(`Fehler: ${result.error}`, 'error');
                this.displayContacts();
            }
        } catch (error) {
            Utils.showNotification('Fehler beim Aktualisieren des Status: ' + error.message, 'error');
            this.displayContacts();
        }
    }

    static async handleFormularSent(contactId) {
        const contact = contacts.find(c => c.PrimaryKey == contactId);
        if (!contact) return;

        if (confirm(`Möchten Sie bestätigen, dass das Formular für ${contact.Name} abgeschickt wurde?\n\nStatus wird auf "Antwort abwarten" gesetzt und das Datum aktualisiert.`)) {
            try {
                const result = await ApiService.markFormularAsSent(contactId);
                if (result.success) {
                    const contactIndex = contacts.findIndex(c => c.PrimaryKey == contactId);
                    if (contactIndex !== -1) {
                        contacts[contactIndex].Status = 'Antwort abwarten';
                        contacts[contactIndex].Zuletzt_angeschrieben = Utils.getCurrentDate();
                    }
                    Utils.showNotification('Formular wurde als abgeschickt markiert.', 'success');
                    this.displayContacts();
                } else {
                    Utils.showNotification(`Fehler: ${result.error}`, 'error');
                }
            } catch (error) {
                Utils.showNotification('Fehler beim Aktualisieren: ' + error.message, 'error');
            }
        }
    }

    static async handlePersoenlichAsked(contactId) {
        const contact = contacts.find(c => c.PrimaryKey == contactId);
        if (!contact) return;

        const requesterName = Utils.askForRequesterName();
        if (requesterName && requesterName.trim() !== '') {
            const updatedComment = Utils.updateCommentWithRequester(contact, requesterName.trim());
            
            if (confirm(`Möchten Sie bestätigen, dass der lokale Kontakt ${contact.Name} von ${requesterName} angefragt wurde?\n\nStatus wird auf "Antwort abwarten" gesetzt, das Datum aktualisiert und der Kommentar ergänzt.`)) {
                try {
                    const result = await ApiService.markPersoenlichAsAsked(contactId, updatedComment);
                    if (result.success) {
                        const contactIndex = contacts.findIndex(c => c.PrimaryKey == contactId);
                        if (contactIndex !== -1) {
                            contacts[contactIndex].Status = 'Antwort abwarten';
                            contacts[contactIndex].Zuletzt_angeschrieben = Utils.getCurrentDate();
                            contacts[contactIndex].Kommentar = updatedComment;
                        }
                        Utils.showNotification('Lokaler Kontakt wurde als angefragt markiert.', 'success');
                        this.displayContacts();
                    } else {
                        Utils.showNotification(`Fehler: ${result.error}`, 'error');
                    }
                } catch (error) {
                    Utils.showNotification('Fehler beim Aktualisieren: ' + error.message, 'error');
                }
            }
        } else if (requesterName !== null) {
            Utils.showNotification('Bitte geben Sie einen Namen ein.', 'error');
        }
    }
}