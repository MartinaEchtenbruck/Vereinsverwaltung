-- Phase 3: Versandaufträge und Empfängerhistorie für Mitglieder-E-Mails.
-- Voraussetzung: Phase 1 wurde ausgeführt (members.status und email_templates.recipient_rule).

CREATE TABLE IF NOT EXISTS member_mail_batches (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    subject_enc TEXT NOT NULL,
    body_enc LONGTEXT NOT NULL,
    recipient_count INT UNSIGNED NOT NULL DEFAULT 0,
    sent_count INT UNSIGNED NOT NULL DEFAULT 0,
    failed_count INT UNSIGNED NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_member_mail_batches_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS member_mail_recipients (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    batch_id BIGINT UNSIGNED NOT NULL,
    member_id BIGINT UNSIGNED NULL,
    member_number INT UNSIGNED NOT NULL,
    recipient_name_enc TEXT NOT NULL,
    recipient_email_enc TEXT NOT NULL,
    send_status ENUM('pending','sent','failed') NOT NULL DEFAULT 'pending',
    sent_at DATETIME NULL,
    error_message VARCHAR(500) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_member_mail_batch_member (batch_id, member_id),
    KEY idx_member_mail_recipients_status (send_status),
    CONSTRAINT fk_member_mail_recipients_batch FOREIGN KEY (batch_id)
        REFERENCES member_mail_batches (id) ON DELETE CASCADE,
    CONSTRAINT fk_member_mail_recipients_member FOREIGN KEY (member_id)
        REFERENCES members (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
