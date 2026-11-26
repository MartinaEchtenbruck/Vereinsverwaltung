-- Stornierungen von Elternbestellungen nachvollziehbar speichern.
-- Vor Ausführung bitte ein Datenbank-Backup erstellen.
ALTER TABLE sales_orders
  ADD COLUMN is_cancelled TINYINT(1) NOT NULL DEFAULT 0 AFTER is_issued,
  ADD COLUMN cancelled_at DATETIME NULL AFTER is_cancelled,
  ADD COLUMN cancelled_by INT NULL AFTER cancelled_at;
