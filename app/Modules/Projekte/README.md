# Modul: Projektmanagement

Der Einstiegspunkt `projects.php` bindet `Pages/index.php` ein. Das Modul ist durch die Berechtigung `ProjectManagement` serverseitig geschützt; der Vorlagen-Endpunkt für die Aufgaben-Vorschau liegt in derselben geschützten Seite.

## Enthaltene Funktionen
- Projektübersicht mit Aufgaben nach Fälligkeit (überfällig, diese Woche, nächste Woche, später, ohne Fälligkeit)
- Veranstaltungstermine und allgemeine Projekte mit Start-/Enddatum
- Geschäftsjahr August bis Juli
- Projektvorlagen und unabhängige Aufgabenvorlagen mit relativen Start- und Fälligkeits-Offsets
- Vorschau und Auswahl der Aufgaben beim Erstellen eines Projekts; Vorlagen bleiben unverändert
- Freiwillige Aufgabenübernahme, Statusbearbeitung, Kommentare mit Benutzer und Zeitstempel
- Projektfortschritt und Aufbewahrung abgeschlossener Projekte

## Installation
1. Vorhandene Datenbank sichern.
2. `database/migrations/2026-10-16_project_management.sql` in der bestehenden Förderverein-intern-Datenbank ausführen.
3. Sicherstellen, dass die Rolle exakt `Manager` heißt und der Migrationseintrag in `role_permissions` vorhanden ist. Die Berechtigung wird nicht der Rolle `User` zugewiesen.
4. `projects.php`, `project-management.css`, das Modulverzeichnis und die aktualisierte `config/navigation.php` in die Anwendung übernehmen.
5. Ab- und wieder anmelden, damit die Session die neue Berechtigung lädt.

`db_connection.php` bleibt installationsspezifisch und wird nicht mitgeliefert. Ein Live-Test auf dem Zielserver ist erforderlich.
