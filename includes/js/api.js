
class ApiService {
    static async request(url, data = null) {
        try {
            const options = {
                method: data ? 'POST' : 'GET',
                headers: { 'Content-Type': 'application/json' }
            };
            if (data) options.body = JSON.stringify(data);
            
            console.log('API Request:', url, data, options);
            
            const response = await fetch(url, options);
            
            if (!response.ok) {
                // Versuche JSON-Fehler zu lesen, falls vorhanden
                let errorMessage = `HTTP error! status: ${response.status}`;
                try {
                    const errorText = await response.text();
                    console.error('HTTP Error Response Text:', errorText);
                    
                    // Versuche JSON zu parsen
                    try {
                        const errorJson = JSON.parse(errorText);
                        errorMessage = errorJson.error || errorMessage;
                    } catch {
                        // Falls kein JSON, verwende den Text
                        errorMessage = errorText || errorMessage;
                    }
                } catch (textError) {
                    console.error('Could not read error response:', textError);
                }
                
                throw new Error(errorMessage);
            }
            
            const result = await response.json();
            console.log('API Response:', url, result);
            
            return result;
            
        } catch (error) {
            console.error('API Request failed:', url, error);
            return { error: error.message };
        }
    }

    static async loadContacts() {
        return await this.request(CONFIG.API_ENDPOINTS.LOAD_CONTACTS);
    }

    static async addContact(contactData) {
        return await this.request(CONFIG.API_ENDPOINTS.ADD_CONTACT, contactData);
    }

    static async updateContact(contactData) {
        return await this.request(CONFIG.API_ENDPOINTS.UPDATE_CONTACT, contactData);
    }

    static async updateContactComment(primaryKey, comment) {
        console.log('Updating comment for contact:', primaryKey, comment);
        const contactData = {
            primary_key: primaryKey,
            kommentar: comment
        };
        return await this.request(CONFIG.API_ENDPOINTS.UPDATE_CONTACT, contactData);
    }

    static async deleteContact(primaryKey) {
        return await this.request(CONFIG.API_ENDPOINTS.DELETE_CONTACT, { primary_key: primaryKey });
    }

    static async updateContactStatus(primaryKey, status) {
        return await this.request(CONFIG.API_ENDPOINTS.UPDATE_STATUS, {
            primary_key: primaryKey,
            status: status
        });
    }

    static async markFormularAsSent(primaryKey) {
        const currentDate = Utils.getCurrentDate();
        const contactData = {
            primary_key: primaryKey,
            status: 'Antwort abwarten',
            zuletzt_angeschrieben: currentDate
        };
        return await this.request(CONFIG.API_ENDPOINTS.UPDATE_CONTACT, contactData);
    }

    static async markPersoenlichAsAsked(primaryKey, updatedComment) {
        const currentDate = Utils.getCurrentDate();
        
        const currentContact = contacts.find(c => c.PrimaryKey == primaryKey);
        if (!currentContact) return;
        
        const contactData = {
            primary_key: primaryKey,
            name: currentContact.Name,
            anrede: currentContact.Anrede || "",
            webseite: currentContact.Webseite || "",
            emailadresse: currentContact.emailadresse || "",
            typ: currentContact.Typ,
            formular_link: currentContact.formular_link || "",
            anschrift: currentContact.anschrift || "",
            status: 'Antwort abwarten',
            zuletzt_angeschrieben: currentDate,
            kommentar: updatedComment,
            letztes_ergebnis: currentContact.Letztes_Ergebnis || ""
        };
        
        return await this.request(CONFIG.API_ENDPOINTS.UPDATE_CONTACT, contactData);
    }
}
