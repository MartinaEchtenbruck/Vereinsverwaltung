# Modul „Verkäufe & Bestellungen“ – Installation

## 1. Datenbank
Vor der Änderung ein Datenbank-Backup erstellen. Anschließend die Datei
`database/migrations/2026-10-19_01_sales_orders.sql` einmalig in der Datenbank
der bestehenden Anwendung ausführen. Die Migration legt ausschließlich neue
`sales_*`-Tabellen und die Berechtigung `Verkauf` an; vorhandene Tabellen
werden nicht verändert. Die Rolle `Manager` erhält die neue Berechtigung.

## 2. Interne Anwendung
Die Dateien aus diesem Paket in das bestehende Verzeichnis der internen
Anwendung (das Verzeichnis, in dem `dashboard.php` liegt) hochladen und
vorhandene Dateien überschreiben. Die bestehenden `db_connection.php`- und
Konfigurationsdateien bleiben unangetastet. Danach aus- und wieder einloggen,
damit die neue Berechtigung in die Sitzung geladen wird. In der Navigation
erscheint „Verkäufe & Bestellungen“.

## 3. Öffentliche Bestellseite
Für die gewünschte öffentliche URL `https://DEINE-DOMAIN/bestellen/` den Ordner
`bestellen` aus dem Paket in das öffentliche Webroot legen, also neben das
Verzeichnis `intern`. Die Seite erkennt die Datenbankverbindung unter
`/intern/db_connection.php` automatisch. Wenn der Ordner stattdessen im
Verzeichnis der internen Anwendung liegt, ist die URL `/intern/bestellen/`.

Die öffentliche Seite benötigt keinen Login. Sie verwendet dieselbe Datenbank,
zeigt nur aktive Aktionen innerhalb des Bestellzeitraums und nur Artikel, die
für den Elternverkauf freigegeben sind.

## Enthaltene Funktionen
- Verkaufsaktionen mit Entwurf, aktiv, Bestellschluss und abgeschlossen
- Artikel mit Einkaufs- und Verkaufspreis; interne Artikel können vom Elternverkauf ausgeschlossen werden
- frei pflegbare Gruppen (initial Gruppe 1 bis Gruppe 4)
- öffentliches Bestellformular mit serverseitiger Preisberechnung, CSRF-Schutz, Honeypot und einfacher IP-basierten Drosselung
- Bestellbestätigung per E-Mail mit BCC an `foerderverein@evkigarumeln.de`
- Bestellverwaltung mit Bezahlt/Gepackt/Ausgegeben, Suche und Filtern
- interne Kindergartenbestellung und zusammengeführte Mengen-/Kostenübersicht
- Versandkosten pro Aktion und Einfrieren von Preisen/Versandkosten beim Abschluss
- Bestellungen werden beim Löschen vollständig aus den Berechnungen entfernt
- Aktionen sind erst ein Jahr nach Abschluss löschbar

## Hinweis
Der PHP-Syntaxcheck aller PHP-Dateien wurde ausgeführt. Ein Live-Test mit der
Produktivdatenbank und dem tatsächlichen Mailserver ist hier nicht möglich.
Nach der Installation bitte zuerst eine Testaktion anlegen, eine Testbestellung
durchführen und E-Mail-Versand sowie Statusänderungen prüfen.
