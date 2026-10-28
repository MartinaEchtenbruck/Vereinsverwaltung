# Release Notes – Abschlussstand des Refactorings

## Inhalt
Der Abschlussstand bündelt die in den vorangegangenen Phasen vorgenommenen Strukturänderungen und Sicherheitsbereinigungen:

- Gemeinsame Funktionen für Authentifizierung und E-Mail-Versand liegen in `app/Core/`.
- Fachliche Implementierungen liegen in den Modulen `Benutzerverwaltung`, `Sponsoren` und `Spendenaktionen`.
- Die zentral konfigurierte Navigation liegt in `config/navigation.php`; Darstellung und CSS liegen unter `includes/`.
- Bisherige Dateien im Projektstamm bleiben als Kompatibilitätseinstiegspunkte erhalten.
- Interne Moduldateien prüfen das `APP_ENTRY_POINT`-Kennzeichen.
- Die gemeinsame Berechtigungsabfrage ist in `app/Core/Auth.php` gebündelt.
- Pfadbehandlung und Dokumentation wurden in den vorherigen Phasen vereinheitlicht.
- Dateibasierte Debug-Protokolle mit Kontakt- oder E-Mail-Metadaten wurden entfernt; technische Fehlerdetails sollen nicht an den Browser ausgegeben werden.

## Nicht geändert
- Datenbankschema und vorhandene Daten
- installationsspezifische `db_connection.php` und Zugangsdaten
- bestehende URLs der Einstiegspunkte
- fachliche Abläufe, soweit nicht in den vorangegangenen Phasen ausdrücklich beschrieben

## Technische Prüfung
Alle 43 PHP-Dateien wurden mit `php -l` geprüft. Das ist eine Syntaxprüfung, kein Laufzeit- oder Integrationstest. Der finale Stand muss deshalb in der vorgesehenen Testumgebung mit der jeweiligen Datenbank- und Mailkonfiguration überprüft werden.


## Inhaltliche Änderung: Dashboard als Startseite
- Neue zentrale Dashboard-Seite unter `dashboard.php`, zunächst ohne Widgets.
- Erfolgreiche Anmeldung und Aufruf der Login-Seite durch bereits angemeldete Benutzer führen zum Dashboard.
- Der Dashboard-Link ist für alle angemeldeten Benutzer in der globalen Navigation sichtbar.
- Die Sponsorenübersicht bleibt über die bestehende URL `index.php` erreichbar und behält ihre Berechtigungsprüfung.
- Die Sichtbarkeit aller übrigen Bereiche bleibt von den vorhandenen Berechtigungen abhängig.


### Header-Korrektur
- Einheitliches Grid-Layout für Seitenüberschrift und Navigation auf Dashboard und angemeldeten Unterseiten.
- Responsive Umschaltung auf eine Spalte bei schmalen Viewports.
