-- Benachrichtigungen: einmalig über phpMyAdmin ausführen.
CREATE TABLE IF NOT EXISTS notifications (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id INT NOT NULL,
  notification_type VARCHAR(50) NOT NULL,
  entity_type VARCHAR(30) NOT NULL,
  entity_id BIGINT NOT NULL,
  event_key VARCHAR(64) NOT NULL,
  title VARCHAR(190) NOT NULL,
  message TEXT NOT NULL,
  target_url VARCHAR(500) NOT NULL,
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  status ENUM('open','done') NOT NULL DEFAULT 'open',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  read_at DATETIME NULL,
  completed_at DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_notification_event_user (user_id, notification_type, entity_type, entity_id, event_key),
  KEY idx_notification_user_state (user_id, status, is_read, created_at),
  KEY idx_notification_entity (entity_type, entity_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
