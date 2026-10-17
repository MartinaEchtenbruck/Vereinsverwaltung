Änderung: Verlinkung der Infoboxen im Dashboard-Spendenwidget

Geänderte Dateien:
- app/Modules/Dashboard/Pages/index.php
- app/Modules/Sponsoren/Pages/index.php
- js/app.js
- js/utils.js
- js/tableManager.js

Verhalten:
- Das Spendenwidget selbst ist nicht mehr als Ganzes verlinkt.
- Lokale/Formular/Mail-Infoboxen öffnen die Sponsorenübersicht mit dem jeweiligen Kontakttyp und Status „Anfrage ausstehend“ (leerer Status oder der Text „Anfrage ausstehend“).
- Die vierte Infobox öffnet die Sponsorenübersicht mit Kontakten aller Typen, deren Status „Handlungsbedarf“ ist.
- Vorschläge werden bei diesen Links ausgeschlossen, damit die Liste zu den Kennzahlen der Spendenaktion passt.
- Die drei ersten Kennzahlen zählen jetzt ebenfalls nur Kontakte mit ausstehender Anfrage, nicht mehr Kontakte mit Handlungsbedarf oder ohne Anschreibedatum.

Keine Datenbankänderung erforderlich.
