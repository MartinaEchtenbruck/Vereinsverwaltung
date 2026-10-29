-- Förderverein intern: nutzerweite Push-Präferenzen
-- Fehlende Zeilen werden in der Anwendung als aktiviert interpretiert.
-- Die bestehende Tabelle `notifications` bleibt unverändert.
CREATE TABLE IF NOT EXISTS user_notification_preferences (
    id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    notification_type VARCHAR(100) NOT NULL,
    push_enabled TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_user_notification_type (user_id, notification_type),
    KEY idx_notification_preferences_type (notification_type),
    CONSTRAINT fk_user_notification_preferences_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
