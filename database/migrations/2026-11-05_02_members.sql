CREATE TABLE IF NOT EXISTS member_number_sequence (
  id TINYINT UNSIGNED NOT NULL PRIMARY KEY,
  last_issued_number INT UNSIGNED NOT NULL DEFAULT 0,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO member_number_sequence (id,last_issued_number) VALUES (1,0)
ON DUPLICATE KEY UPDATE id=VALUES(id);

CREATE TABLE IF NOT EXISTS members (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  member_number INT UNSIGNED NOT NULL,
  last_name VARCHAR(150) NOT NULL,
  first_name VARCHAR(150) NOT NULL,
  street_enc TEXT NOT NULL,
  postal_code_enc TEXT NOT NULL,
  city_enc TEXT NOT NULL,
  phone_enc TEXT NULL,
  mobile_enc TEXT NULL,
  email_enc TEXT NULL,
  monthly_fee DECIMAL(10,2) NOT NULL,
  account_holder_enc TEXT NOT NULL,
  bank_name_enc TEXT NOT NULL,
  iban_enc TEXT NOT NULL,
  bic_enc TEXT NOT NULL,
  duration_years SMALLINT UNSIGNED NULL,
  start_date DATE NOT NULL,
  end_date DATE NULL,
  mandate_reference_enc TEXT NULL,
  comment_enc TEXT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_members_member_number (member_number),
  KEY idx_members_name (last_name,first_name),
  KEY idx_members_dates (start_date,end_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
