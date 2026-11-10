-- Buchführung: endgültige Festschreibung mit dokumentiertem Benutzer.
-- Einmalig nach 2026-11-09_03_accounting.sql ausführen.

-- Einen eventuell vorhandenen Zwischenstatus in den gewünschten Status "open" überführen.
UPDATE accounting_fiscal_years SET status='open' WHERE status='preparation';

-- Künftig gibt es ausschließlich die Zustände open und closed.
ALTER TABLE accounting_fiscal_years
    MODIFY COLUMN status ENUM('open','closed') NOT NULL DEFAULT 'open',
    ADD COLUMN closed_by INT NULL AFTER closed_at,
    ADD KEY idx_accounting_fiscal_year_closed_by (closed_by),
    ADD CONSTRAINT fk_accounting_fiscal_year_closed_by
        FOREIGN KEY (closed_by) REFERENCES users(id) ON DELETE SET NULL;
