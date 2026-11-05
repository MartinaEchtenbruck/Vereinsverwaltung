-- Eigenständige Berechtigung für die spätere Mitgliederverwaltung.
-- Die Berechtigung wird absichtlich keiner Rolle automatisch zugewiesen.
INSERT INTO permissions (permission_name, description)
SELECT 'manage_members', 'Zugriff auf die Mitgliederverwaltung und Mitgliederübersicht'
WHERE NOT EXISTS (
    SELECT 1 FROM permissions WHERE permission_name = 'manage_members'
);
