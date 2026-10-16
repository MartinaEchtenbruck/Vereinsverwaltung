-- Projektmanagement für Förderverein intern
-- Vor Ausführung die vorhandene Datenbank sichern. Bestehende Tabellen bleiben unverändert.

CREATE TABLE IF NOT EXISTS project_templates (
  id INT NOT NULL AUTO_INCREMENT,
  name VARCHAR(160) NOT NULL,
  description TEXT NULL,
  project_type ENUM('event','project') NOT NULL DEFAULT 'event',
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_by INT NULL,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_project_templates_active (is_active),
  CONSTRAINT fk_project_templates_creator FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS task_templates (
  id INT NOT NULL AUTO_INCREMENT,
  title VARCHAR(200) NOT NULL,
  description TEXT NULL,
  start_offset_days INT NULL COMMENT 'Relativ zum Projektstichtag; negativ = vorher',
  due_offset_days INT NULL COMMENT 'Relativ zum Projektstichtag; negativ = vorher',
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_by INT NULL,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_task_templates_active_order (is_active, sort_order),
  CONSTRAINT fk_task_templates_creator FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS project_template_tasks (
  project_template_id INT NOT NULL,
  task_template_id INT NOT NULL,
  default_selected TINYINT(1) NOT NULL DEFAULT 1,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (project_template_id, task_template_id),
  CONSTRAINT fk_ptt_project_template FOREIGN KEY (project_template_id) REFERENCES project_templates(id) ON DELETE CASCADE,
  CONSTRAINT fk_ptt_task_template FOREIGN KEY (task_template_id) REFERENCES task_templates(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS projects (
  id INT NOT NULL AUTO_INCREMENT,
  template_id INT NULL,
  title VARCHAR(200) NOT NULL,
  description TEXT NULL,
  project_type ENUM('event','project') NOT NULL DEFAULT 'event',
  event_date DATE NULL,
  start_date DATE NULL,
  end_date DATE NULL,
  fiscal_year VARCHAR(9) NOT NULL,
  status ENUM('active','completed','archived') NOT NULL DEFAULT 'active',
  created_by INT NULL,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  completed_at DATETIME NULL,
  PRIMARY KEY (id),
  KEY idx_projects_status_dates (status,event_date,start_date),
  KEY idx_projects_fiscal_year (fiscal_year),
  CONSTRAINT fk_projects_template FOREIGN KEY (template_id) REFERENCES project_templates(id) ON DELETE SET NULL,
  CONSTRAINT fk_projects_creator FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS tasks (
  id INT NOT NULL AUTO_INCREMENT,
  project_id INT NOT NULL,
  task_template_id INT NULL,
  title VARCHAR(200) NOT NULL,
  description TEXT NULL,
  start_date DATE NULL,
  due_date DATE NULL,
  status ENUM('open','in_progress','done') NOT NULL DEFAULT 'open',
  assigned_user_id INT NULL COMMENT 'Freiwillige Übernahme; keine feste Zuweisung aus der Vorlage',
  claimed_at DATETIME NULL,
  sort_order INT NOT NULL DEFAULT 0,
  created_by INT NULL,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  completed_at DATETIME NULL,
  PRIMARY KEY (id),
  KEY idx_tasks_project_status (project_id,status),
  KEY idx_tasks_due_date (due_date),
  KEY idx_tasks_assignee (assigned_user_id),
  CONSTRAINT fk_tasks_project FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
  CONSTRAINT fk_tasks_template FOREIGN KEY (task_template_id) REFERENCES task_templates(id) ON DELETE SET NULL,
  CONSTRAINT fk_tasks_assignee FOREIGN KEY (assigned_user_id) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_tasks_creator FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS task_comments (
  id INT NOT NULL AUTO_INCREMENT,
  task_id INT NOT NULL,
  user_id INT NULL,
  comment TEXT NOT NULL,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_task_comments_task_date (task_id,created_at),
  CONSTRAINT fk_task_comments_task FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
  CONSTRAINT fk_task_comments_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Eigenständige Berechtigung; wird ausschließlich der Rolle Manager zugeordnet.
INSERT INTO permissions (permission_name, description)
SELECT 'ProjectManagement', 'Zugriff auf Projektmanagement, Projektvorlagen, Aufgaben und Kommentare'
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE permission_name='ProjectManagement');

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r JOIN permissions p ON p.permission_name='ProjectManagement'
WHERE r.role_name='Manager'
AND NOT EXISTS (SELECT 1 FROM role_permissions rp WHERE rp.role_id=r.id AND rp.permission_id=p.id);

-- Grundvorlage Sponsorenlauf. Die Tombola-Aufgabe ist eine normale auswählbare Aufgabe und kann beim Anlegen abgewählt werden.
INSERT INTO task_templates (title,description,start_offset_days,due_offset_days,sort_order,is_active)
SELECT 'Plakate gestalten und drucken','Plakate vorbereiten und den Druck organisieren.',-56,-14,10,1
WHERE NOT EXISTS (SELECT 1 FROM task_templates WHERE title='Plakate gestalten und drucken');
INSERT INTO task_templates (title,description,start_offset_days,due_offset_days,sort_order,is_active)
SELECT 'Sponsorenlauf bei der Stadt anmelden','Erforderliche Anmeldung und Abstimmung mit der Stadt erledigen.',-28,-14,20,1
WHERE NOT EXISTS (SELECT 1 FROM task_templates WHERE title='Sponsorenlauf bei der Stadt anmelden');
INSERT INTO task_templates (title,description,start_offset_days,due_offset_days,sort_order,is_active)
SELECT 'Tombola anmelden','Falls eine Tombola geplant ist: erforderliche Anzeige fristgerecht einreichen.',-28,-14,30,1
WHERE NOT EXISTS (SELECT 1 FROM task_templates WHERE title='Tombola anmelden');
INSERT INTO task_templates (title,description,start_offset_days,due_offset_days,sort_order,is_active)
SELECT 'Medaillen organisieren','Medaillenbedarf klären und rechtzeitig bestellen.',-42,-7,40,1
WHERE NOT EXISTS (SELECT 1 FROM task_templates WHERE title='Medaillen organisieren');
INSERT INTO task_templates (title,description,start_offset_days,due_offset_days,sort_order,is_active)
SELECT 'Urkunden vorbereiten','Vorlage vorbereiten, Namen/Angaben prüfen und Urkunden drucken.',-21,-3,50,1
WHERE NOT EXISTS (SELECT 1 FROM task_templates WHERE title='Urkunden vorbereiten');
INSERT INTO task_templates (title,description,start_offset_days,due_offset_days,sort_order,is_active)
SELECT 'Teilnehmerliste vorbereiten','Anmeldung und Teilnehmerliste zusammenführen und prüfen.',-14,-3,60,1
WHERE NOT EXISTS (SELECT 1 FROM task_templates WHERE title='Teilnehmerliste vorbereiten');
INSERT INTO task_templates (title,description,start_offset_days,due_offset_days,sort_order,is_active)
SELECT 'Kaffee und Verpflegung organisieren','Bedarf und Helfer abstimmen; Einkauf bzw. Spenden koordinieren.',-14,-2,70,1
WHERE NOT EXISTS (SELECT 1 FROM task_templates WHERE title='Kaffee und Verpflegung organisieren');

INSERT INTO project_templates (name,description,project_type,is_active)
SELECT 'Sponsorenlauf','Wiederkehrende Aufgaben zur Vorbereitung des Sponsorenlaufs.', 'event', 1
WHERE NOT EXISTS (SELECT 1 FROM project_templates WHERE name='Sponsorenlauf');

INSERT INTO project_template_tasks (project_template_id,task_template_id,default_selected,sort_order,is_active)
SELECT pt.id,tt.id,1,tt.sort_order,1 FROM project_templates pt JOIN task_templates tt
WHERE pt.name='Sponsorenlauf' AND tt.title IN ('Plakate gestalten und drucken','Sponsorenlauf bei der Stadt anmelden','Tombola anmelden','Medaillen organisieren','Urkunden vorbereiten','Teilnehmerliste vorbereiten','Kaffee und Verpflegung organisieren')
AND NOT EXISTS (SELECT 1 FROM project_template_tasks x WHERE x.project_template_id=pt.id AND x.task_template_id=tt.id);
