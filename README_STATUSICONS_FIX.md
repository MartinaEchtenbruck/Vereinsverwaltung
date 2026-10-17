Förderverein intern – Status-Icons in der Projekt-Aufgabenliste

Änderung:
- In app/Modules/Projekte/Pages/index.php werden die Statussymbole als Textzeichen (▶, ↶, ✓) ausgegeben. Dadurch sind sie nicht von der extern geladenen Font-Awesome-Webfont abhängig.
- project-management.css erzwingt die Sichtbarkeit der Statusaktionen in den Projekt-Aufgabenkarten.
- Die Stylesheet-URL enthält eine neue Versionskennung, um einen veralteten CSS-Cache zu vermeiden.

Installation:
1. Vorhandene Dateien sichern.
2. ZIP-Inhalt in das Verzeichnis /intern der bestehenden Anwendung kopieren und vorhandene Dateien überschreiben.
3. Anschließend projects.php?view=project&id=2 neu laden.

Keine Datenbankmigration erforderlich.
