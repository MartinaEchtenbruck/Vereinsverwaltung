# Modul Benutzerverwaltung

Dieses Modul bündelt die Seiten und Aktionen für Benutzerkonten, Registrierung, Passwort-Wiederherstellung sowie Rollen und Berechtigungen.

## Struktur
- `Pages/users.php`: Benutzerkonten verwalten
- `Pages/role_permissions.php`: Rollen und Berechtigungen verwalten
- `Pages/register.php`: Registrierung
- `Pages/forgot_password.php`: Passwort-Reset anfordern
- `Pages/reset_password.php`: Passwort mit Token zurücksetzen
- `Actions/check_username.php`: AJAX-Prüfung auf verfügbaren Benutzernamen

Die Dateien im Projektstamm bleiben als kompatible Einstiegspunkte bestehen, damit bestehende URLs, Formulare und AJAX-Aufrufe unverändert bleiben. Die Implementierungen greifen mit expliziten Pfaden auf die im Projektstamm liegende `db_connection.php`, `auth.php` und gemeinsame Includes zu. Datenbankschema und Berechtigungsregeln wurden in dieser Phase nicht geändert.
