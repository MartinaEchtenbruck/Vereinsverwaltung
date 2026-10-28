-- Verwaltung von Spendenaktionen mit Start- und Enddatum.
-- Vor der Verwendung einmalig in der Förderverein-intern-Datenbank ausführen.
CREATE TABLE IF NOT EXISTS fundraising_campaigns (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(150) NOT NULL,
    started_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ended_at DATETIME NULL DEFAULT NULL,
    started_by INT NULL,
    ended_by INT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_fundraising_campaign_active (ended_at, started_at),
    CONSTRAINT fk_fundraising_campaign_started_by FOREIGN KEY (started_by) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_fundraising_campaign_ended_by FOREIGN KEY (ended_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
