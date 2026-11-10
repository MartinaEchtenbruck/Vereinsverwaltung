-- Buchführung: Geschäftsjahre, Kategorien, Buchungen und Prüfprotokolle.
-- Geldbeträge und sensible Texte werden ausschließlich als AES-256-GCM-Chiffrate gespeichert.
-- Die Verschlüsselung/Entschlüsselung erfolgt in PHP über MemberEncryption.

CREATE TABLE IF NOT EXISTS accounting_fiscal_years (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    label VARCHAR(5) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status ENUM('open','preparation','closed') NOT NULL DEFAULT 'open',
    opening_bank_enc TEXT NULL,
    opening_cash_enc TEXT NULL,
    closing_bank_enc TEXT NULL,
    closing_cash_enc TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    closed_at DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_accounting_fiscal_year_label (label),
    UNIQUE KEY uq_accounting_fiscal_year_dates (start_date, end_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS accounting_categories (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name_enc TEXT NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    sort_order INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_accounting_categories_active (is_active, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS accounting_bookings (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    fiscal_year_id BIGINT UNSIGNED NOT NULL,
    booking_date DATE NOT NULL,
    booking_type ENUM('income','expense','transfer') NOT NULL,
    payment_channel ENUM('bank','cash') NULL,
    transfer_from ENUM('bank','cash') NULL,
    transfer_to ENUM('bank','cash') NULL,
    category_id BIGINT UNSIGNED NULL,
    description_enc TEXT NOT NULL,
    amount_enc TEXT NOT NULL,
    paper_reference_enc TEXT NULL,
    created_by INT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_accounting_bookings_date (booking_date, id),
    KEY idx_accounting_bookings_year (fiscal_year_id, booking_date),
    KEY idx_accounting_bookings_category (category_id),
    CONSTRAINT fk_accounting_bookings_year FOREIGN KEY (fiscal_year_id)
        REFERENCES accounting_fiscal_years (id) ON DELETE RESTRICT,
    CONSTRAINT fk_accounting_bookings_category FOREIGN KEY (category_id)
        REFERENCES accounting_categories (id) ON DELETE SET NULL,
    CONSTRAINT fk_accounting_bookings_user FOREIGN KEY (created_by)
        REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS accounting_documents (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    booking_id BIGINT UNSIGNED NOT NULL,
    reference_code VARCHAR(20) NOT NULL,
    relative_path VARCHAR(255) NOT NULL,
    original_filename_enc TEXT NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    file_size BIGINT UNSIGNED NOT NULL,
    uploaded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_accounting_document_reference (reference_code),
    KEY idx_accounting_documents_booking (booking_id),
    CONSTRAINT fk_accounting_documents_booking FOREIGN KEY (booking_id)
        REFERENCES accounting_bookings (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS accounting_bank_reconciliations (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    fiscal_year_id BIGINT UNSIGNED NOT NULL,
    reconciled_until DATE NOT NULL,
    performed_on DATE NOT NULL,
    receipts_complete TINYINT(1) NOT NULL DEFAULT 0,
    transactions_complete TINYINT(1) NOT NULL DEFAULT 0,
    note_enc TEXT NULL,
    created_by INT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_accounting_reconciliations_year (fiscal_year_id, reconciled_until),
    CONSTRAINT fk_accounting_reconciliations_year FOREIGN KEY (fiscal_year_id)
        REFERENCES accounting_fiscal_years (id) ON DELETE RESTRICT,
    CONSTRAINT fk_accounting_reconciliations_user FOREIGN KEY (created_by)
        REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS accounting_cash_counts (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    fiscal_year_id BIGINT UNSIGNED NOT NULL,
    counted_on DATE NOT NULL,
    actual_amount_enc TEXT NOT NULL,
    calculated_amount_enc TEXT NOT NULL,
    difference_enc TEXT NOT NULL,
    note_enc TEXT NULL,
    created_by INT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_accounting_cash_counts_year (fiscal_year_id, counted_on),
    CONSTRAINT fk_accounting_cash_counts_year FOREIGN KEY (fiscal_year_id)
        REFERENCES accounting_fiscal_years (id) ON DELETE RESTRICT,
    CONSTRAINT fk_accounting_cash_counts_user FOREIGN KEY (created_by)
        REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO permissions (permission_name, description)
SELECT 'manage_accounting', 'Zugriff auf Buchführung, Buchungen und Kassenprüfung'
WHERE NOT EXISTS (
    SELECT 1 FROM permissions WHERE permission_name = 'manage_accounting'
);
