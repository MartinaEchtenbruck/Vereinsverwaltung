# Modul Sponsoren

Das Modul bündelt die Sponsorenübersicht, die Kontaktverwaltung und das Erstellen individueller Anschreiben.

## Pages
- `Pages/index.php` – Sponsorenübersicht und Einstieg in die Kontaktverwaltung
- `Pages/contact.php` – individuelles Anschreiben für einen Kontakt erstellen und versenden

## Actions
- `Actions/load_contacts.php` – Kontakte laden
- `Actions/add_contact.php` – Kontakt bzw. Vorschlag anlegen
- `Actions/update_contact.php` – Kontaktdaten bzw. Kommentar bearbeiten
- `Actions/delete_contact.php` – Kontakt löschen
- `Actions/update_contact_status.php` – Kontaktstatus ändern

## Kompatibilität und Abhängigkeiten
Die bisherigen Dateien im Projektstamm (`index.php`, `contact.php`, `load_contacts.php`, `add_contact.php`, `update_contact.php`, `delete_contact.php` und `update_contact_status.php`) bleiben als Kompatibilitätseinstiegspunkte bestehen. Bestehende URLs, Formularziele und JavaScript-Aufrufe müssen daher nicht geändert werden.

Die internen Moduldateien sind gegen direkten HTTP-Aufruf geschützt und werden über die bisherigen Einstiegspunkte eingebunden. Dateipfade zu gemeinsam genutzten Ressourcen und zur installationsspezifischen Datenbankverbindung werden über das Anwendungsverzeichnis aufgelöst. `db_connection.php` gehört nicht in das Refactoring-Paket.
