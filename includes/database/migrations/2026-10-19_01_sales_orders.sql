-- Modul Verkäufe & Bestellungen. Vor Ausführung Datenbank sichern.
CREATE TABLE IF NOT EXISTS sales_groups (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY(id),
  UNIQUE KEY uq_sales_group_name(name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sales_groups(name,sort_order)
SELECT seed.name,seed.sort_order FROM (
  SELECT 'Gruppe 1' AS name,1 AS sort_order UNION ALL
  SELECT 'Gruppe 2',2 UNION ALL SELECT 'Gruppe 3',3 UNION ALL SELECT 'Gruppe 4',4
) seed
WHERE NOT EXISTS (SELECT 1 FROM sales_groups);

CREATE TABLE IF NOT EXISTS sales_actions (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  title VARCHAR(180) NOT NULL,
  description TEXT NULL,
  start_date DATE NOT NULL,
  end_date DATE NOT NULL,
  status ENUM('draft','active','closed','completed') NOT NULL DEFAULT 'draft',
  shipping_cents INT UNSIGNED NOT NULL DEFAULT 0,
  created_by INT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  completed_at DATETIME NULL,
  completed_by INT NULL,
  PRIMARY KEY(id),
  KEY idx_sales_actions_public(status,start_date,end_date),
  KEY idx_sales_actions_completed(status,completed_at),
  CONSTRAINT fk_sales_action_creator FOREIGN KEY(created_by) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_sales_action_completer FOREIGN KEY(completed_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS sales_items (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  action_id INT UNSIGNED NOT NULL,
  name VARCHAR(180) NOT NULL,
  purchase_price_cents INT UNSIGNED NOT NULL DEFAULT 0,
  sale_price_cents INT UNSIGNED NOT NULL DEFAULT 0,
  sold_to_parents TINYINT(1) NOT NULL DEFAULT 1,
  sort_order INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY(id),
  KEY idx_sales_items_action(action_id,sort_order),
  CONSTRAINT fk_sales_item_action FOREIGN KEY(action_id) REFERENCES sales_actions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS sales_orders (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  action_id INT UNSIGNED NOT NULL,
  order_number VARCHAR(32) NOT NULL,
  customer_name VARCHAR(160) NOT NULL,
  customer_email VARCHAR(190) NOT NULL,
  group_id INT UNSIGNED NOT NULL,
  total_cents INT UNSIGNED NOT NULL DEFAULT 0,
  is_paid TINYINT(1) NOT NULL DEFAULT 0,
  is_packed TINYINT(1) NOT NULL DEFAULT 0,
  is_issued TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY(id),
  UNIQUE KEY uq_sales_order_number(order_number),
  KEY idx_sales_orders_action(action_id,created_at),
  KEY idx_sales_orders_group(group_id),
  CONSTRAINT fk_sales_order_action FOREIGN KEY(action_id) REFERENCES sales_actions(id) ON DELETE CASCADE,
  CONSTRAINT fk_sales_order_group FOREIGN KEY(group_id) REFERENCES sales_groups(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS sales_order_items (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  order_id BIGINT UNSIGNED NOT NULL,
  item_id INT UNSIGNED NULL,
  item_name VARCHAR(180) NOT NULL,
  quantity INT UNSIGNED NOT NULL,
  unit_price_cents INT UNSIGNED NOT NULL,
  line_total_cents INT UNSIGNED NOT NULL,
  PRIMARY KEY(id),
  KEY idx_sales_order_items_order(order_id),
  KEY idx_sales_order_items_item(item_id),
  CONSTRAINT fk_sales_order_item_order FOREIGN KEY(order_id) REFERENCES sales_orders(id) ON DELETE CASCADE,
  CONSTRAINT fk_sales_order_item_item FOREIGN KEY(item_id) REFERENCES sales_items(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS sales_internal_order_items (
  action_id INT UNSIGNED NOT NULL,
  item_id INT UNSIGNED NOT NULL,
  quantity INT UNSIGNED NOT NULL DEFAULT 0,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY(action_id,item_id),
  CONSTRAINT fk_sales_internal_action FOREIGN KEY(action_id) REFERENCES sales_actions(id) ON DELETE CASCADE,
  CONSTRAINT fk_sales_internal_item FOREIGN KEY(item_id) REFERENCES sales_items(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO permissions(permission_name,description)
SELECT 'Verkauf','Verwaltung von Verkaufsaktionen, Artikeln und Bestellungen'
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE permission_name='Verkauf');

INSERT INTO role_permissions(role_id,permission_id)
SELECT r.id,p.id FROM roles r JOIN permissions p ON p.permission_name='Verkauf'
WHERE r.role_name='Manager'
AND NOT EXISTS (SELECT 1 FROM role_permissions rp WHERE rp.role_id=r.id AND rp.permission_id=p.id);
