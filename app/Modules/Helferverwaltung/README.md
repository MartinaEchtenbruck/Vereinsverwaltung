# Helferverwaltung

Interne Verwaltung: `helferlisten.php`; öffentlicher Anmeldebereich: `/helfen/`.
Vor Nutzung `database/migrations/2026-11-07_01_helpers.sql` ausführen und der gewünschten Rolle das Recht `manage_helpers` zuweisen.
Zeitfenster werden pro Tag gespeichert. Neue Stationen sind standardmäßig in allen Zeitfenstern aktiv; Aktivierung kann je Tag/Station angepasst werden. Kapazität gilt fest pro Station und Zeitfenster.
