# Architekturübersicht

## Gemeinsamer Kern
- `app/Core/Auth.php`: Authentifizierung und Berechtigungsfunktionen
- `app/Core/Mail.php`: gemeinsame E-Mail-Funktionen
- `auth.php` und `mail_utils.php`: Kompatibilitätseinstiegspunkte

## Fachmodule
- `app/Modules/Sponsoren/`: Dokumentation des Sponsorenbereichs; bestehende Kontakt-Endpunkte bleiben aus Kompatibilitätsgründen im Projektstamm
- `app/Modules/Spendenaktionen/Pages/`: Seiten zur Verwaltung der Spendenaktion und Bulk-Mail-Vorschau
- `app/Modules/Spendenaktionen/Actions/`: Aktionen zum Zurücksetzen des Kontaktstatus und zum Bulk-Mail-Versand
- `app/Modules/Benutzerverwaltung/Pages/`: Benutzerverwaltung, Rollen/Berechtigungen, Registrierung und Passwort-Wiederherstellung
- `app/Modules/Benutzerverwaltung/Actions/`: AJAX-Prüfung des Benutzernamens
- `app/Modules/Projekte/` und `app/Modules/Verkauf/`: vorbereitete Bereiche für spätere Erweiterungen

## Kompatibilität und Installation
Die Dateien im Projektstamm bleiben als Einstiegspunkte bestehen, damit vorhandene URLs, Formularziele und AJAX-Aufrufe nicht geändert werden müssen. `db_connection.php` ist installationsspezifisch und wird nicht mit dem Refactoring-Paket ausgeliefert. Datenbankschema, bestehende Berechtigungsnamen und fachliche Abläufe sollen unverändert bleiben.
