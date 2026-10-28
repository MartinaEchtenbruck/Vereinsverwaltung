# Modul: Spendenaktionen

**Zuständigkeit:** Anzeige und Verwaltung der Spendenaktion sowie Vorbereitung und Durchführung des Bulk-Mailversands.

## Implementierungen

- `Pages/edit_fundraising.php` – Übersichts-/Verwaltungsseite
- `Pages/bulk_mail_preview.php` – Vorschau und Vorbereitung des Bulk-Mailversands
- `Actions/reset_fundraising.php` – Zurücksetzen der Kontaktstatus
- `Actions/send_bulk_mail.php` – Einzelner Versand im Rahmen des Bulk-Versands

Die Dateien im Projektstamm (`edit_fundraising.php`, `bulk_mail_preview.php`, `reset_fundraising.php`, `send_bulk_mail.php`) bleiben als Kompatibilitäts-Einstiegspunkte erhalten. Die bisherigen URLs, Formular-/Fetch-Ziele, Berechtigungsnamen und das Datenbankschema bleiben unverändert.

Dateisystemzugriffe auf Vorlagen, PDF-Anhänge und das Rate-Limit-Protokoll werden relativ zum Anwendungsverzeichnis aufgelöst, nicht relativ zum Modulverzeichnis.

**Hinweis:** `send_bulk_mail.php` versendet echte E-Mails und aktualisiert nach erfolgreichem Versand den Kontaktstatus. In der Testumgebung deshalb ausschließlich mit Testkontakten und Testdatenbank prüfen.
