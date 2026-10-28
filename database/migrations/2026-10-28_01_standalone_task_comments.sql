-- Kommentare für projektunabhängige Aufgaben. Vor Ausführung Datenbank-Backup erstellen.
CREATE TABLE IF NOT EXISTS standalone_task_comments (
  id INT NOT NULL AUTO_INCREMENT,
  standalone_task_id INT NOT NULL,
  user_id INT NOT NULL,
  comment TEXT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_standalone_task_comments_task (standalone_task_id, created_at),
  CONSTRAINT fk_standalone_task_comments_task FOREIGN KEY (standalone_task_id) REFERENCES standalone_tasks(id) ON DELETE CASCADE,
  CONSTRAINT fk_standalone_task_comments_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
