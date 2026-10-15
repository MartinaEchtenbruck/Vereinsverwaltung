# Spendenverwaltung – Refactoring Phase 7

## Ziel
Die bisherige Startseite `index.php` wurde in das Modul Sponsoren überführt. Die öffentliche URL `/index.php` bleibt unverändert und dient weiterhin als Einstiegspunkt. Das Datenbankschema, die Berechtigungsnamen und die fachliche Logik wurden nicht geändert.

## Änderungen
- Die eigentliche Implementierung der Sponsorenübersicht liegt jetzt unter `app/Modules/Sponsoren/Pages/index.php`.
- Die `index.php` im Projektstamm ist ein schlanker Kompatibilitätseinstiegspunkt.
- Die Modulimplementierung wird gegen direkten Aufruf über ihren Dateipfad geschützt, damit die Seite weiterhin über die gewohnte URL und damit mit den bisherigen relativen Asset- und JavaScript-Pfaden läuft.
- Die Navigation wird weiterhin aus `includes/navigation.php` und `config/navigation.php` geladen; ihre sichtbare Gestaltung wurde in dieser Phase bewusst nicht verändert.
- `docs/ARCHITEKTUR.md` wurde aktualisiert.

## Installation
1. Vorher die Testinstallation sichern.
2. ZIP im Verzeichnis der Testinstallation entpacken und Dateien überschreiben lassen.
3. Die installationsspezifische `db_connection.php` nicht überschreiben; sie ist nicht im ZIP enthalten.
4. Keine Dateien im Projektstamm löschen.

## Tests
Nach der Installation prüfen:
1. `index.php` öffnet die Sponsorenübersicht.
2. Die Kontaktliste wird geladen.
3. Suche, Filter und Kontaktformular funktionieren.
4. Bearbeiten/Statusänderung und der Link zum Erstellen eines Anschreibens funktionieren.
5. Navigation und Berechtigungen verhalten sich wie zuvor.

Alle PHP-Dateien wurden auf Syntax geprüft. Ein Laufzeittest mit dem konkreten Webserver und der Datenbank war hier nicht möglich. Schreibende Aktionen daher weiterhin zunächst nur mit geeigneten Testkontakten testen.
