-- Artikelbeschreibungen und bis zu zwei frei definierbare Artikeleigenschaften.
-- Vor Ausführung ein Datenbank-Backup erstellen.
ALTER TABLE sales_items
  ADD COLUMN description TEXT NULL AFTER name;

CREATE TABLE sales_item_options (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  item_id INT UNSIGNED NOT NULL,
  property_no TINYINT UNSIGNED NOT NULL,
  property_name VARCHAR(80) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_sales_item_property (item_id, property_no),
  CONSTRAINT fk_sales_item_option_item FOREIGN KEY (item_id)
    REFERENCES sales_items(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE sales_item_option_values (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  option_id INT UNSIGNED NOT NULL,
  value_text VARCHAR(100) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  UNIQUE KEY uq_sales_option_value (option_id, value_text),
  CONSTRAINT fk_sales_option_value_option FOREIGN KEY (option_id)
    REFERENCES sales_item_options(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE sales_order_items
  ADD COLUMN variant_text VARCHAR(400) NOT NULL DEFAULT '' AFTER item_name;
