-- Ergänzt die vier steuerlichen Tätigkeitsbereiche für Einnahmen und Ausgaben.
-- Bestehende Buchungen bleiben zunächst NULL, da ihre Zuordnung nicht zuverlässig
-- aus Kategorie, Buchungstext oder Zahlungsweg abgeleitet werden kann.
-- Vor der Festschreibung müssen alle Einnahmen und Ausgaben zugeordnet sein.

ALTER TABLE accounting_bookings
    ADD COLUMN tax_area ENUM(
        'ideell',
        'asset_management',
        'purpose_operation',
        'commercial_business'
    ) NULL AFTER category_id;

CREATE INDEX idx_accounting_bookings_tax_area
    ON accounting_bookings (tax_area);
