SPENDENVERWALTUNG – REFACTORING PHASE 1
======================================

Ziel
----
Die technische Struktur wird schrittweise verbessert, ohne die bestehenden URLs, die sichtbare Oberfläche oder das Datenbankschema in dieser Phase zu verändern.

Wichtige Änderungen
-------------------
- Gemeinsame Authentifizierungs- und Berechtigungsfunktionen liegen in app/Core/Auth.php.
- Der bisherige Einstiegspunkt auth.php bleibt als Kompatibilitätsdatei erhalten.
- Die gemeinsame Mailfunktion liegt in app/Core/Mail.php.
- Der bisherige Einstiegspunkt mail_utils.php bleibt als Kompatibilitätsdatei erhalten.
- Die globale Navigation bleibt zentral in config/navigation.php und includes/navigation.php definiert.
- Fachliche Bereiche sind unter app/Modules dokumentiert. Die bestehenden PHP-Einstiegspunkte bleiben vorerst im Projektstamm, damit URLs, Formularziele und AJAX-Endpunkte nicht brechen.

Nicht enthalten
---------------
- db_connection.php: Diese Datei ist absichtlich nicht im Paket. Die passende Verbindungsdatei muss separat in das Projektverzeichnis gelegt werden.
- Datenbank-Backup oder SQL-Dump.
- Laufzeit-Logs.

Installation / Test
-------------------
1. Paket in eine separate Testinstallation entpacken.
2. Die für diese Testinstallation vorgesehene db_connection.php in das Projektverzeichnis kopieren.
3. Keine Datenbankmigration ausführen.
4. PHP-Syntax prüfen und Anmeldung, Kontaktliste, Kontaktbearbeitung, Navigation sowie Benutzer-/Rechteverwaltung testen.
5. E-Mail-Versand zunächst nicht auslösen: Die Funktion kann echte E-Mails versenden.
6. Schreibende Aktionen nur ausführen, wenn die Testinstallation mit einer dafür vorgesehenen Testdatenbank verbunden ist.

Sicherheit
----------
Die produktive Anwendung vor dem Austausch sichern. Keine Produktionsdateien überschreiben, solange die Testversion nicht ausreichend geprüft wurde. Zugangsdaten nicht in ZIP-Dateien oder Versionsverwaltung aufnehmen.

Details: docs/ARCHITEKTUR.md


## Refactoring Phase 3

Die Spendenaktionsseiten und -aktionen sind unter `app/Modules/Spendenaktionen/` organisiert. Die bisherigen Root-Dateien bleiben als kompatible Einstiegspunkte erhalten. `db_connection.php` ist weiterhin installationsspezifisch und nicht Teil dieses ZIP-Pakets. Der Bulk-Mailversand kann echte E-Mails versenden; bitte nur mit Testkontakten prüfen.
