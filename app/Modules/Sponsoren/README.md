# Modul Sponsoren

Das Modul bündelt die Kontaktverwaltung und das Erstellen individueller Anschreiben.

## Actions
- `Actions/load_contacts.php` – Kontakte laden
- `Actions/add_contact.php` – Kontakt bzw. Vorschlag anlegen
- `Actions/update_contact.php` – Kontaktdaten bzw. Kommentar bearbeiten
- `Actions/delete_contact.php` – Kontakt löschen
- `Actions/update_contact_status.php` – Kontaktstatus ändern

## Pages
- `Pages/contact.php` – individuelles Anschreiben für einen Kontakt erstellen und versenden

Die bisherigen Dateien im Projektstamm bleiben als Kompatibilitätseinstiegspunkte bestehen. Dadurch ändern sich bestehende URLs und JavaScript-Aufrufe nicht. Die Seite `contact.php` greift für Datenbankverbindung, Navigation, Vorlagen, PDFs und Protokolldateien weiterhin auf das Anwendungsverzeichnis im Projektstamm zu.
