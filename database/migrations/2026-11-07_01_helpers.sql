-- Helferlisten: Aktionen, Tage, Zeitfenster, Stationen und Anmeldungen
CREATE TABLE IF NOT EXISTS helper_actions (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 title VARCHAR(180) NOT NULL, description TEXT NULL,
 start_date DATE NOT NULL, end_date DATE NOT NULL,
 status ENUM('draft','active','closed') NOT NULL DEFAULT 'draft',
 default_slots JSON NULL, created_by BIGINT UNSIGNED NULL,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 KEY idx_helper_actions_status(status,start_date,end_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS helper_days (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, action_id BIGINT UNSIGNED NOT NULL,
 event_date DATE NOT NULL, slots_json JSON NOT NULL, custom_grid TINYINT(1) NOT NULL DEFAULT 0,
 UNIQUE KEY uq_helper_day(action_id,event_date),
 CONSTRAINT fk_helper_day_action FOREIGN KEY(action_id) REFERENCES helper_actions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS helper_stations (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, action_id BIGINT UNSIGNED NOT NULL,
 name VARCHAR(150) NOT NULL, description VARCHAR(500) NULL, capacity SMALLINT UNSIGNED NOT NULL DEFAULT 1,
 sort_order INT NOT NULL DEFAULT 0, active TINYINT(1) NOT NULL DEFAULT 1,
 CONSTRAINT fk_helper_station_action FOREIGN KEY(action_id) REFERENCES helper_actions(id) ON DELETE CASCADE,
 KEY idx_helper_station_action(action_id,sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS helper_day_stations (
 day_id BIGINT UNSIGNED NOT NULL, station_id BIGINT UNSIGNED NOT NULL, active_slots_json JSON NOT NULL,
 PRIMARY KEY(day_id,station_id),
 CONSTRAINT fk_helper_ds_day FOREIGN KEY(day_id) REFERENCES helper_days(id) ON DELETE CASCADE,
 CONSTRAINT fk_helper_ds_station FOREIGN KEY(station_id) REFERENCES helper_stations(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS helper_registrations (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, action_id BIGINT UNSIGNED NOT NULL,
 full_name VARCHAR(180) NOT NULL, email VARCHAR(254) NOT NULL,
 edit_token_hash CHAR(64) NOT NULL, status ENUM('active','cancelled') NOT NULL DEFAULT 'active',
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 UNIQUE KEY uq_helper_token(edit_token_hash), KEY idx_helper_reg_action(action_id,status),
 CONSTRAINT fk_helper_reg_action FOREIGN KEY(action_id) REFERENCES helper_actions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS helper_assignments (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, registration_id BIGINT UNSIGNED NOT NULL,
 day_id BIGINT UNSIGNED NOT NULL, station_id BIGINT UNSIGNED NOT NULL, slot_key VARCHAR(40) NOT NULL,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY uq_helper_assignment(registration_id,day_id,station_id,slot_key),
 KEY idx_helper_slot(day_id,station_id,slot_key),
 CONSTRAINT fk_helper_assignment_reg FOREIGN KEY(registration_id) REFERENCES helper_registrations(id) ON DELETE CASCADE,
 CONSTRAINT fk_helper_assignment_day FOREIGN KEY(day_id) REFERENCES helper_days(id) ON DELETE CASCADE,
 CONSTRAINT fk_helper_assignment_station FOREIGN KEY(station_id) REFERENCES helper_stations(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO permissions(permission_name,description)
SELECT 'manage_helpers','Helferlisten und Helferanmeldungen verwalten'
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE permission_name='manage_helpers');
