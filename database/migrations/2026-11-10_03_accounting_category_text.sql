-- Kategorien werden künftig direkt an der Buchung als verschlüsselter Text gespeichert.
-- category_id und accounting_categories bleiben zunächst als Legacy-Bestand erhalten,
-- damit bereits vorhandene (insbesondere festgeschriebene) Buchungen unverändert
-- angezeigt werden können. Neue und bearbeitete Buchungen verwenden category_enc.

ALTER TABLE accounting_bookings
    ADD COLUMN category_enc TEXT NULL AFTER category_id;
