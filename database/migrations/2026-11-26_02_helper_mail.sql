-- Dauerhafte schichtbezogene Hinweise und Versandprotokoll für Helfer-E-Mails.
-- Vor dem Ausführen ein aktuelles Datenbank-Backup erstellen.

CREATE TABLE IF NOT EXISTS helper_shift_notes (
    day_id BIGINT UNSIGNED NOT NULL,
    station_id BIGINT UNSIGNED NOT NULL,
    slot_key VARCHAR(40) NOT NULL,
    note TEXT NOT NULL,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (day_id, station_id, slot_key),
    CONSTRAINT fk_helper_shift_note_station_day
        FOREIGN KEY (day_id, station_id)
        REFERENCES helper_day_stations (day_id, station_id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS helper_mail_log (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    action_id BIGINT UNSIGNED NOT NULL,
    registration_id BIGINT UNSIGNED NULL,
    recipient_name VARCHAR(180) NOT NULL,
    recipient_email VARCHAR(254) NOT NULL,
    template_name VARCHAR(100) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    send_status ENUM('sent','failed') NOT NULL,
    error_message TEXT NULL,
    sent_at DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_helper_mail_action_registration (action_id, registration_id, send_status, sent_at),
    CONSTRAINT fk_helper_mail_action FOREIGN KEY (action_id)
        REFERENCES helper_actions (id) ON DELETE CASCADE,
    CONSTRAINT fk_helper_mail_registration FOREIGN KEY (registration_id)
        REFERENCES helper_registrations (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
