# Spendenverwaltung – Finales Refactoring-Paket

## Zweck
Dieses Paket fasst den modularisierten Stand nach den Refactoring-Phasen 1–15 zusammen. Phase 16 ist die Abschluss- und Release-Vorbereitung: Die vorhandene Implementierung wurde nicht fachlich neu gestaltet; Dokumentation und Prüfinformationen wurden auf den Abschlussstand gebracht.

## Struktur
- `app/Core/`: gemeinsame Authentifizierungs- und Mail-Funktionen
- `app/Modules/Benutzerverwaltung/`: Seiten und Aktionen der Benutzerverwaltung
- `app/Modules/Sponsoren/`: Sponsorenübersicht, Kontaktseite und Kontaktaktionen
- `app/Modules/Spendenaktionen/`: Spendenaktionen und Bulk-Mail-Funktionen
- `config/` und `includes/`: zentrale Navigationskonfiguration, Rendering und Styles
- `js/`: bestehende JavaScript-Dateien
- Dateien im Projektstamm: Kompatibilitätseinstiegspunkte für die bisherigen URLs

## Installation in der Testumgebung
1. Erstelle eine Sicherung deiner aktuell funktionierenden Testinstallation.
2. Entpacke das ZIP in das Verzeichnis der Testinstallation und lasse vorhandene Dateien überschreiben.
3. Behalte deine installationsspezifische `db_connection.php` unverändert bei. Sie ist absichtlich nicht im Paket enthalten.
4. Lösche keine bisherigen Einstiegspunkt-Dateien im Projektstamm.
5. Öffne die Anwendung und führe die unten aufgeführten Funktionstests durch.

## Dashboard als Startseite
Nach der Anmeldung landen alle Benutzer auf `dashboard.php`. Das Dashboard ist zunächst absichtlich leer und zeigt die globale Navigation. Die Sponsorenübersicht bleibt über `index.php` erreichbar. In der Navigation ist der Dashboard-Link für alle angemeldeten Benutzer sichtbar; weitere Bereiche erscheinen wie bisher nur mit der passenden Berechtigung.

## Empfohlene Funktionstests
- Anmeldung führt zum Dashboard; Dashboard-Link ist auf Unterseiten verfügbar; Rückkehr zum Dashboard funktioniert
- Anmeldung, Navigation, geschützte Seiten und Abmeldung
- Benutzerverwaltung sowie Rollen und Berechtigungen
- Registrierung, Benutzernamen-Prüfung und Passwort-Wiederherstellung
- Sponsorenübersicht: Kontakte laden, anlegen, bearbeiten, Status ändern und löschen
- Individuelle Anschreiben inklusive E-Mail-Versand
- Bulk-Mail-Vorschau und Bulk-Mail-Versand
- Spendenaktion einschließlich der vorhandenen Verwaltungsaktionen
- CSS- und JavaScript-Ladevorgänge sowie Darstellung bei üblichen Fenstergrößen
- Direkter Aufruf interner Moduldateien: diese sollen durch die `APP_ENTRY_POINT`-Prüfung nicht direkt ausgeliefert werden

## Prüfstatus und Grenzen
- Alle 43 PHP-Dateien wurden mit `php -l` auf Syntaxfehler geprüft.
- Das vollständige `js/`-Verzeichnis und die Projektdokumentation sind enthalten.
- `db_connection.php` und `.env`-Dateien sind nicht enthalten.
- Die bisherigen Einstiegspunkte und fachlichen Funktionen wurden im Rahmen des Refactorings beibehalten.
- Ein Live-Test der finalen ZIP-Datei auf dem konkreten Webserver wurde nicht von hier aus durchgeführt. Die Funktionstests müssen daher in der Testinstallation bestätigt werden, bevor dieses Paket produktiv eingesetzt wird.

## Wichtiger Hinweis
Dieses Paket ist ein Abschlussstand für die weitere Prüfung und Übernahme. Es ersetzt keine Sicherung und sollte erst nach erfolgreichem Test in die produktive Installation übernommen werden.


## Header-Stabilisierung

Die angemeldeten Seiten verwenden einen gemeinsamen Seitenkopf (`app-page-header`) mit festem Grid-Verhältnis für Überschrift und Navigation. Dadurch bleibt der rechte Navigationsbereich beim Wechsel zwischen Dashboard und Unterseiten stabil ausgerichtet. Auf schmalen Displays werden Überschrift und Navigation untereinander angeordnet.
