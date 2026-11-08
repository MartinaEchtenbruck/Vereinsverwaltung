-- Phase 1: Grundlage für Mitgliederstatus und E-Mail-Vorlagen.
-- Vor dem Ausführen ein aktuelles Datenbank-Backup erstellen.

ALTER TABLE members
    ADD COLUMN status VARCHAR(20) NOT NULL DEFAULT 'Aktiv' AFTER end_date,
    ADD INDEX idx_members_status_end_date (status, end_date);

-- Bestehende Datensätze erhalten bewusst den Status Aktiv.
UPDATE members SET status = 'Aktiv';

ALTER TABLE email_templates
    ADD COLUMN recipient_rule VARCHAR(50) NOT NULL DEFAULT 'all_active' AFTER mailtext;

-- Zulässige recipient_rule-Werte (werden in PHP validiert):
-- all_active
-- active_no_end
-- active_end_next_year
-- active_end_this_year
-- active_or_terminated
-- current_member
-- new_member
