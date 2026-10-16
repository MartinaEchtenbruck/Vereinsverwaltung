-- Projektvorlagen erhalten eigenständige Aufgaben. Allgemeine Aufgabenvorlagen
-- werden beim Hinzufügen in eine Projektvorlage kopiert und danach unabhängig bearbeitet.
-- Vor Ausführung ein Datenbank-Backup erstellen. Diese Migration einmalig ausführen.

CREATE TABLE project_template_task_items (
  id INT NOT NULL AUTO_INCREMENT,
  project_template_id INT NOT NULL,
  source_task_template_id INT NULL COMMENT 'Optionale Herkunft; Inhalte werden als eigenständige Kopie gespeichert',
  title VARCHAR(200) NOT NULL,
  description TEXT NULL,
  resource_url VARCHAR(2048) NULL,
  start_offset_days INT NULL COMMENT 'Relativ zum Projektstichtag; negativ = vorher',
  due_offset_days INT NULL COMMENT 'Relativ zum Projektstichtag; negativ = vorher',
  default_selected TINYINT(1) NOT NULL DEFAULT 1,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_ptti_template_active (project_template_id, is_active, sort_order),
  KEY idx_ptti_source (source_task_template_id),
  CONSTRAINT fk_ptti_project_template FOREIGN KEY (project_template_id) REFERENCES project_templates(id) ON DELETE CASCADE,
  CONSTRAINT fk_ptti_source_template FOREIGN KEY (source_task_template_id) REFERENCES task_templates(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

ALTER TABLE tasks ADD COLUMN resource_url VARCHAR(2048) NULL AFTER description;

-- Vorhandene Zuordnungen aus der ersten Version werden in eigenständige Aufgaben kopiert.
INSERT INTO project_template_task_items
  (project_template_id, source_task_template_id, title, description, resource_url,
   start_offset_days, due_offset_days, default_selected, sort_order, is_active)
SELECT ptt.project_template_id, tt.id, tt.title, tt.description, NULL,
       tt.start_offset_days, tt.due_offset_days, ptt.default_selected, ptt.sort_order, ptt.is_active
FROM project_template_tasks ptt
JOIN task_templates tt ON tt.id = ptt.task_template_id
WHERE NOT EXISTS (
  SELECT 1 FROM project_template_task_items x
  WHERE x.project_template_id = ptt.project_template_id
    AND x.source_task_template_id = tt.id
);
