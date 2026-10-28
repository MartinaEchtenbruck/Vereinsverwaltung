-- Projektfarben für Vorlagen und konkrete Projekte
-- Vor Ausführung die Datenbank sichern. Einmalig ausführen.
ALTER TABLE project_templates
  ADD COLUMN color CHAR(7) NOT NULL DEFAULT '#DCEAF0' AFTER description;

ALTER TABLE projects
  ADD COLUMN color CHAR(7) NOT NULL DEFAULT '#DCEAF0' AFTER description;
