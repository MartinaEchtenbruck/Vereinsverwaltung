# Modul: Sponsorenverwaltung

**Zuständigkeit:** Kontaktübersicht und Pflege von Sponsoren-/Spendenkontakten.

## Bestehende öffentliche Einstiegspunkte

Die PHP-Dateien bleiben in Phase 1 im Projektstamm, damit URLs, AJAX-Endpunkte und relative Formularpfade unverändert bleiben:

- `index.php` – Kontaktübersicht
- `load_contacts.php` – Kontakte laden
- `add_contact.php`, `update_contact.php`, `delete_contact.php` – Kontaktänderungen
- `update_contact_status.php` – Statusänderungen
- `contact.php` – Einzelanschreiben
- `bulk_mail_preview.php`, `send_bulk_mail.php` – Serienmail-Ablauf

**Nächster Refactoring-Schritt:** fachliche Abläufe aus den öffentlichen Einstiegspunkten in Controller-/Service-Dateien dieses Moduls verlagern. Die bisherigen Dateien sollen dabei als kompatible Einstiegspunkte bestehen bleiben.
