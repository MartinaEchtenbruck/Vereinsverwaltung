// === MODAL-MANAGEMENT ===
class ModalManager {
    static showDeleteConfirmation(contactId) {
        const contact = contacts.find(c => c.PrimaryKey == contactId);
        if (contact) {
            const deleteMessage = document.getElementById('deleteMessage');
            deleteMessage.textContent = `Bist Du sicher, dass Du ${contact.Name} (${contact.Letztes_Ergebnis || contact.Kommentar || 'keine weiteren Informationen'}) löschen möchtest?`;
            
            const modal = document.getElementById('deleteModal');
            modal.style.display = 'flex';
            
            document.getElementById('confirmDelete').onclick = async () => {
                await this.handleDelete(contactId);
                modal.style.display = 'none';
            };
            
            document.getElementById('cancelDelete').onclick = () => {
                modal.style.display = 'none';
            };
        }
    }

    static async handleDelete(contactId) {
        try {
            const result = await ApiService.deleteContact(contactId);
            if (result.success) {
                const contactIndex = contacts.findIndex(c => c.PrimaryKey == contactId);
                const contactName = contacts[contactIndex].Name;
                contacts.splice(contactIndex, 1);
                
                if (currentEditId === contactId) {
                    currentEditId = null;
                }
                TableManager.displayContacts();
                Utils.showNotification(`Kontakt ${contactName} wurde gelöscht.`, 'success');
            } else {
                Utils.showNotification(`Fehler: ${result.error}`, 'error');
            }
        } catch (error) {
            Utils.showNotification('Fehler beim Löschen: ' + error.message, 'error');
        }
    }
}