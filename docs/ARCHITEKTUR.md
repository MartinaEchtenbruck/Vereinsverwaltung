# Architekturübersicht

## Gemeinsamer Kern
- `app/Core/Auth.php`: Authentifizierung und Berechtigungsfunktionen
- `app/Core/Mail.php`: gemeinsame E-Mail-Funktionen
- `auth.php` und `mail_utils.php`: Kompatibilitätseinstiegspunkte
- `logout.php`: bestehender Abmelde-Einstiegspunkt; die Abmeldelogik wird über `app/Core/Auth.php` ausgeführt

## Fachmodule
- `app/Modules/Sponsoren/Actions/`: Laden, Anlegen, Bearbeiten, Löschen und Statusänderungen von Kontakten
- `app/Modules/Sponsoren/Pages/`: Sponsorenübersicht (`index.php`) und Kontaktseite zum Erstellen und Versenden individueller Anschreiben
- `app/Modules/Spendenaktionen/Pages/`: Seiten zur Verwaltung der Spendenaktion und Bulk-Mail-Vorschau
- `app/Modules/Spendenaktionen/Actions/`: Aktionen zum Zurücksetzen des Kontaktstatus und zum Bulk-Mail-Versand
- `app/Modules/Benutzerverwaltung/Pages/`: Anmeldung, Benutzerverwaltung, Rollen/Berechtigungen, Registrierung und Passwort-Wiederherstellung
- `app/Modules/Benutzerverwaltung/Actions/`: AJAX-Prüfung des Benutzernamens
- `app/Modules/Projekte/` und `app/Modules/Verkauf/`: vorbereitete Bereiche für spätere Erweiterungen

## Navigation
- `config/navigation.php` definiert die Navigationspunkte zentral inklusive Ziel-URL, Symbol und Berechtigung.
- `includes/navigation.php` rendert die für die angemeldete Person erlaubten Punkte und markiert die aktuell geöffnete Seite mit `aria-current="page"`.
- `includes/navigation.css` enthält die gemeinsame Darstellung einschließlich einer dezenten Kennzeichnung des aktiven Bereichs.

## Kompatibilität und Installation
Die Dateien im Projektstamm bleiben als Einstiegspunkte bestehen, damit vorhandene URLs, Formularziele und AJAX-Aufrufe nicht geändert werden müssen. Die Sitzungsinitialisierung erfolgt nur bei Bedarf; nach erfolgreicher Anmeldung wird die Session-ID erneuert. Debug-Ausgaben, die Benutzernamen und Berechtigungen protokollieren, wurden aus dem Authentifizierungskern entfernt. Die bisherigen `index.php` und `login.php` binden die Sponsorenübersicht beziehungsweise Anmeldeseite aus ihren Modulverzeichnissen ein; beide Implementierungen sind gegen direkten Aufruf geschützt. `db_connection.php` ist installationsspezifisch und wird nicht mit dem Refactoring-Paket ausgeliefert. Datenbankschema, bestehende Berechtigungsnamen und fachliche Abläufe sollen unverändert bleiben.


## Schutz interner Moduldateien (Phase 11)
Interne PHP-Dateien unter `app/Modules/` prüfen `APP_ENTRY_POINT` und beenden einen direkten HTTP-Aufruf mit Status 404. Die bisherigen Dateien im Projektstamm setzen dieses Flag und binden die Implementierung ein. Damit bleiben bestehende URLs erhalten, während Moduldateien nicht direkt über ihre internen Pfade aufgerufen werden sollen.
