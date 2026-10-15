# Architekturübersicht

## Gemeinsamer Kern
- `app/Core/Auth.php`: Authentifizierung und Berechtigungsfunktionen
- `app/Core/Mail.php`: gemeinsame E-Mail-Funktionen
- `auth.php` und `mail_utils.php`: Kompatibilitätseinstiegspunkte
- `logout.php`: bestehender Abmelde-Einstiegspunkt; die Abmeldelogik wird über `app/Core/Auth.php` ausgeführt

## Fachmodule
- `app/Modules/Dashboard/Pages/`: zentrale Startseite für alle angemeldeten Benutzer; zunächst ohne Widgets
- `app/Modules/Sponsoren/Actions/`: Laden, Anlegen, Bearbeiten, Löschen und Statusänderungen von Kontakten
- `app/Modules/Sponsoren/Pages/`: Sponsorenübersicht (`index.php`) und Kontaktseite zum Erstellen und Versenden individueller Anschreiben
- `app/Modules/Spendenaktionen/Pages/`: Seiten zur Verwaltung der Spendenaktion und Bulk-Mail-Vorschau
- `app/Modules/Spendenaktionen/Actions/`: Aktionen zum Zurücksetzen des Kontaktstatus und zum Bulk-Mail-Versand
- `app/Modules/Benutzerverwaltung/Pages/`: Anmeldung, Benutzerverwaltung, Rollen/Berechtigungen, Registrierung und Passwort-Wiederherstellung
- `app/Modules/Benutzerverwaltung/Actions/`: AJAX-Prüfung des Benutzernamens
- `app/Modules/Projekte/` und `app/Modules/Verkauf/`: vorbereitete Bereiche für spätere Erweiterungen

## Navigation
- `config/navigation.php` definiert die Navigationspunkte zentral inklusive Ziel-URL, Symbol und Berechtigung. Der Dashboard-Link ist für alle angemeldeten Benutzer sichtbar; andere Bereiche bleiben berechtigungsabhängig.
- `includes/navigation.php` rendert die für die angemeldete Person erlaubten Punkte und markiert die aktuell geöffnete Seite mit `aria-current="page"`.
- `includes/navigation.css` enthält die gemeinsame Darstellung einschließlich einer dezenten Kennzeichnung des aktiven Bereichs.

## Kompatibilität und Installation
Die Dateien im Projektstamm bleiben als Einstiegspunkte bestehen, damit vorhandene URLs, Formularziele und AJAX-Aufrufe nicht geändert werden müssen. Die Sitzungsinitialisierung erfolgt nur bei Bedarf; nach erfolgreicher Anmeldung wird die Session-ID erneuert. Debug-Ausgaben, die Benutzernamen und Berechtigungen protokollieren, wurden aus dem Authentifizierungskern entfernt. Die bisherigen `index.php` und `login.php` binden die Sponsorenübersicht beziehungsweise Anmeldeseite aus ihren Modulverzeichnissen ein; `dashboard.php` ist der neue Einstiegspunkt zum Dashboard. beide Implementierungen sind gegen direkten Aufruf geschützt. `db_connection.php` ist installationsspezifisch und wird nicht mit dem Refactoring-Paket ausgeliefert. Datenbankschema, bestehende Berechtigungsnamen und fachliche Abläufe sollen unverändert bleiben.


## Schutz interner Moduldateien (Phase 11)
Interne PHP-Dateien unter `app/Modules/` prüfen `APP_ENTRY_POINT` und beenden einen direkten HTTP-Aufruf mit Status 404. Die bisherigen Dateien im Projektstamm setzen dieses Flag und binden die Implementierung ein. Damit bleiben bestehende URLs erhalten, während Moduldateien nicht direkt über ihre internen Pfade aufgerufen werden sollen.


## Sponsorenmodul – Bestandsprüfung (Phase 12)
Die Übersicht, die Kontaktseite und die fünf zugehörigen Kontaktaktionen liegen in `app/Modules/Sponsoren/Pages/` beziehungsweise `app/Modules/Sponsoren/Actions/`. Die gleichnamigen Dateien im Projektstamm sind Kompatibilitätseinstiegspunkte für bestehende URLs und JavaScript-Aufrufe. Es sind keine weiteren Sponsoren-Implementierungen im Projektstamm vorgesehen; bei künftigen Änderungen soll neue fachliche Logik direkt im Modul ergänzt werden.


## Gemeinsame Berechtigungsabfrage (Phase 13)
`app/Core/Auth.php` stellt `loadUserPermissions(PDO $conn, $userId)` bereit. Die Login-Seite verwendet die Funktion über ihre bereits bestehende Datenbankverbindung; `reloadUserPermissions()` nutzt dieselbe Funktion beim erneuten Laden der Session-Rechte. Damit liegt die SQL-Abfrage nur noch an einer Stelle und der Login baut keine zusätzliche Verbindung nur für das Laden der Berechtigungen auf.


## Pfadbehandlung (Phase 14)
In den Dateien der Benutzerverwaltung wird der Anwendungspfad pro Moduldatei einmal über `$appRoot = dirname(__DIR__, 4)` ermittelt und anschließend für lokale PHP-Abhängigkeiten wiederverwendet. Das reduziert wiederholte Pfadberechnungen und macht Einbindungen von `db_connection.php`, `auth.php` und der gemeinsamen Navigation konsistent. Relative CSS-, JavaScript- und Navigations-URLs im HTML bleiben absichtlich unverändert: Die Seiten werden weiterhin über die bisherigen Einstiegspunkte im Projektstamm ausgeliefert, sodass diese URLs relativ zur bisherigen Webadresse aufgelöst werden.


## Abschlussprüfung – Diagnoseausgaben (Phase 15)
Bei der Prüfung wurden dateibasierte Debug-Protokolle aus der Kontaktseite und dem zentralen Mail-Helfer entfernt. Diese Protokolle hätten Empfänger, Betreffzeilen, Kontaktnamen, Dateinamen oder technische Fehlermeldungen im Webverzeichnis speichern können. Der Mail-Helfer protokolliert nur noch einen knappen Versandfehler über das PHP-Server-Log. Die Kontaktseite aktiviert die Anzeige von PHP-Fehlern nicht mehr explizit. Die Benutzernamen-Prüfung protokolliert keine eingegebenen Benutzernamen und gibt bei Datenbankfehlern keine technischen Details an den Browser zurück.


## Dashboard als Startseite
Nach erfolgreicher Anmeldung sowie beim Aufruf der Anmeldeseite durch eine bereits angemeldete Person erfolgt die Weiterleitung zu `dashboard.php`. Auch angemeldete Benutzer, die `register.php` aufrufen, werden zum Dashboard geleitet. Die Sponsorenübersicht bleibt unter der bisherigen URL `index.php` erreichbar. Das Dashboard ist für alle angemeldeten Benutzer zugänglich; die übrigen Navigationspunkte werden weiterhin anhand der jeweiligen Berechtigungen angezeigt.
