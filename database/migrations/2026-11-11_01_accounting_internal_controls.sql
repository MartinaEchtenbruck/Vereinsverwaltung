-- Interne Kontrollen der Buchführung: Fingerprints zur Erkennung nachträglicher Änderungen.
-- Die bestehenden Tabellen accounting_bank_reconciliations und accounting_cash_counts
-- werden aus 2026-11-09_03_accounting.sql weiterverwendet.

ALTER TABLE accounting_bank_reconciliations
    ADD COLUMN booking_fingerprint CHAR(64) NULL AFTER created_at;

ALTER TABLE accounting_cash_counts
    ADD COLUMN booking_fingerprint CHAR(64) NULL AFTER created_at;

