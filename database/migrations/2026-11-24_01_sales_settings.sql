-- Globale Einstellungen für das Modul Verkäufe & Bestellungen.
-- Vor Ausführung Datenbank sichern.
CREATE TABLE IF NOT EXISTS sales_settings (
  setting_key VARCHAR(100) NOT NULL,
  setting_value TEXT NOT NULL,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sales_settings (setting_key, setting_value)
VALUES ('order_confirmation_payment_text', 'Bitte bezahle den Betrag entsprechend den Zahlungsinformationen, die dir der Förderverein mitgeteilt hat.')
ON DUPLICATE KEY UPDATE setting_key = VALUES(setting_key);
