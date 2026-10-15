# Spendenverwaltung – Testversion, Refactoring Phase 4

Diese Version führt die Benutzerverwaltung in das Modul `app/Modules/Benutzerverwaltung/` über. Die bisherigen PHP-Dateien im Projektstamm bleiben als kompatible Einstiegspunkte erhalten.

## Neu in Phase 4
- Benutzerverwaltung und Rollen/Berechtigungen in `Pages/`
- Registrierung und Passwort-Wiederherstellung in `Pages/`
- AJAX-Prüfung des Benutzernamens in `Actions/`
- Explizite Pfade zu gemeinsamen Dateien im Projektstamm

## Installation
1. Vor der Installation die Testinstallation sichern.
2. ZIP in das Verzeichnis der **Testinstallation** entpacken und vorhandene Dateien überschreiben.
3. Die lokale `db_connection.php` nicht überschreiben; sie ist nicht im ZIP enthalten.
4. Anmeldung, Benutzerverwaltung, Rollen/Berechtigungen, Registrierung sowie Passwort-Reset prüfen.

Die PHP-Syntax wurde geprüft. Ein vollständiger Laufzeittest mit dem konkreten Webserver und der Datenbank wurde nicht durchgeführt. Teste die E-Mail-Funktionen zunächst mit geeigneten Testkonten.
