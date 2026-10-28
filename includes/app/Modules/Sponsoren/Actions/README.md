# Modul Sponsoren – Phase 2

Die serverseitigen Kontakt-Endpunkte sind in `Actions/` gebündelt. Die bisherigen Dateien im Projektstamm bleiben als Kompatibilitäts-Einstiegspunkte bestehen, damit bestehende JavaScript-Aufrufe und URLs unverändert funktionieren.

## Zuordnung
- `Actions/load_contacts.php`: Kontaktliste laden
- `Actions/add_contact.php`: Kontakt anlegen
- `Actions/update_contact.php`: Kontaktdaten bzw. Kommentar aktualisieren
- `Actions/delete_contact.php`: Kontakt löschen
- `Actions/update_contact_status.php`: Kontaktstatus aktualisieren

Die Aktionen binden Authentifizierung und Datenbank über absolute Dateipfade zum Projektstamm ein. Dadurch sind sie nicht vom aktuellen Arbeitsverzeichnis abhängig. Geschäftslogik, Berechtigungsnamen, JSON-Antworten und SQL wurden in dieser Phase nicht absichtlich verändert.

`index.php` und `contact.php` bleiben vorerst im Projektstamm, da sie die bestehende Oberfläche und mehrere relative URLs bzw. Ressourcen verwenden. Sie können in einer späteren Phase mit Kompatibilitäts-Einstiegspunkten in das Modul verschoben werden.
