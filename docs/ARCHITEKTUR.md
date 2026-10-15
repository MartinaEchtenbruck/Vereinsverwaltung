# Architekturübersicht

## Gemeinsamer Kern
- `app/Core/Auth.php`: Authentifizierung und Berechtigungsfunktionen
- `app/Core/Mail.php`: gemeinsame E-Mail-Funktionen
- `auth.php` und `mail_utils.php`: Kompatibilitätseinstiegspunkte

## Fachmodule
- `app/Modules/Sponsoren/Actions/`: Laden, Anlegen, Bearbeiten, Löschen und Statusänderungen von Kontakten
- `app/Modules/Sponsoren/Pages/`: Sponsorenübersicht (`index.php`) und Kontaktseite zum Erstellen und Versenden individueller Anschreiben
- `app/Modules/Spendenaktionen/Pages/`: Seiten zur Verwaltung der Spendenaktion und Bulk-Mail-Vorschau
- `app/Modules/Spendenaktionen/Actions/`: Aktionen zum Zurücksetzen des Kontaktstatus und zum Bulk-Mail-Versand
- `app/Modules/Benutzerverwaltung/Pages/`: Benutzerverwaltung, Rollen/Berechtigungen, Registrierung und Passwort-Wiederherstellung
- `app/Modules/Benutzerverwaltung/Actions/`: AJAX-Prüfung des Benutzernamens
- `app/Modules/Projekte/` und `app/Modules/Verkauf/`: vorbereitete Bereiche für spätere Erweiterungen

## Kompatibilität und Installation
Die Dateien im Projektstamm bleiben als Einstiegspunkte bestehen, damit vorhandene URLs, Formularziele und AJAX-Aufrufe nicht geändert werden müssen. Die bisherige `index.php` bindet die Sponsorenübersicht aus `app/Modules/Sponsoren/Pages/index.php` ein; der Modulcode ist gegen direkten Aufruf geschützt. `db_connection.php` ist installationsspezifisch und wird nicht mit dem Refactoring-Paket ausgeliefert. Datenbankschema, bestehende Berechtigungsnamen und fachliche Abläufe sollen unverändert bleiben.
