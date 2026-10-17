# Modul: Projektmanagement

Der Einstiegspunkt `projects.php` bindet `Pages/index.php` ein. Das Modul ist durch die Berechtigung `ProjectManagement` serverseitig geschützt; der Vorlagen-Endpunkt für die Aufgaben-Vorschau liegt in derselben geschützten Seite.

## Enthaltene Funktionen
- Projektübersicht mit Aufgaben nach Fälligkeit (überfällig, diese Woche, nächste Woche und Demnächst (bis zum Beginn der fünften Kalenderwoche))
- Veranstaltungstermine und allgemeine Projekte mit Start-/Enddatum
- Geschäftsjahr August bis Juli
- Editierbare Projektvorlagen mit eigenen, projektspezifischen Aufgaben und relativen Start- und Fälligkeits-Offsets
- Allgemeine Aufgabenvorlagen als optionale Bausteine; beim Einfügen wird eine unabhängige Kopie in die Projektvorlage angelegt
- Ressourcen-/Dokumentlinks direkt an einer Aufgabe; Link, Beschreibung und Fristen werden in konkrete Projekte kopiert
- Vorschau und Auswahl der Aufgaben beim Erstellen eines Projekts; Änderungen an Vorlagen verändern bereits angelegte Projekte nicht
- Freiwillige Aufgabenübernahme, Statusbearbeitung, Kommentare mit Benutzer und Zeitstempel
- Projektfortschritt und Aufbewahrung abgeschlossener Projekte

## Installation
1. Vorhandene Datenbank sichern.
2. `database/migrations/2026-10-16_project_management.sql` in der bestehenden Förderverein-intern-Datenbank ausführen (falls noch nicht geschehen).
3. Einmalig `database/migrations/2026-10-16_02_project_template_task_items.sql` ausführen. Diese Migration legt eigenständige Aufgaben innerhalb von Projektvorlagen an, kopiert bestehende Zuordnungen aus der ersten Version und ergänzt `tasks.resource_url`.
4. Sicherstellen, dass die Rolle exakt `Manager` heißt und der Migrationseintrag in `role_permissions` vorhanden ist. Die Berechtigung wird nicht der Rolle `User` zugewiesen.
5. `projects.php`, `project-management.css`, das Modulverzeichnis und die aktualisierte `config/navigation.php` in die Anwendung übernehmen.
6. Ab- und wieder anmelden, damit die Session die neue Berechtigung lädt.

`db_connection.php` bleibt installationsspezifisch und wird nicht mitgeliefert. Ein Live-Test auf dem Zielserver ist erforderlich.
