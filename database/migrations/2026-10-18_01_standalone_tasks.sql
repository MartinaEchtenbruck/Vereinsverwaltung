-- Erweiterung Projektmanagement: eigenständige Aufgaben mit absoluten Daten und Wiederholung.
-- Vor Ausführung ein Datenbank-Backup erstellen. Einmalig ausführen.
CREATE TABLE IF NOT EXISTS standalone_tasks (
  id INT NOT NULL AUTO_INCREMENT,
  title VARCHAR(200) NOT NULL,
  description TEXT NULL,
  resource_url VARCHAR(2048) NULL,
  start_date DATE NOT NULL,
  due_date DATE NOT NULL,
  status ENUM('open','in_progress','done') NOT NULL DEFAULT 'open',
  assigned_user_id INT NULL,
  claimed_at DATETIME NULL,
  recurrence_every INT NULL COMMENT 'Wiederholungsintervall; NULL = keine Wiederholung',
  recurrence_unit ENUM('week','month','year') NULL,
  recurrence_source_id INT NULL COMMENT 'Vorgängeraufgabe dieser Wiederholungsreihe; jede erledigte Aufgabe erzeugt höchstens eine Folgeaufgabe',
  created_by INT NULL,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  completed_at DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_standalone_recurrence_source (recurrence_source_id),
  KEY idx_standalone_status_dates (status,start_date,due_date),
  KEY idx_standalone_assignee (assigned_user_id),
  CONSTRAINT fk_standalone_assignee FOREIGN KEY (assigned_user_id) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_standalone_creator FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_standalone_recurrence_source FOREIGN KEY (recurrence_source_id) REFERENCES standalone_tasks(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
