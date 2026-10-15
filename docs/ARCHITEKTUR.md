# Architektur und Refactoring-Plan

## Ziel

Die bestehende, produktiv genutzte PHP-Anwendung wird schrittweise in fachliche Module und eine gemeinsame technische Kernschicht aufgeteilt. In Phase 1 bleiben die öffentlichen URLs, das HTML/Frontend-Verhalten und das Datenbankschema unverändert.

## Aktuelle Struktur nach Phase 1

```text
/
├── index.php, contact.php, users.php, ...  Öffentliche Einstiegspunkte und Endpunkte
├── auth.php                                Kompatibilitäts-Einstiegspunkt
├── mail_utils.php                          Kompatibilitäts-Einstiegspunkt
├── config/
│   └── navigation.php                      Zentrale Navigationseinträge
├── includes/
│   ├── navigation.php                      Gemeinsamer Navigation-Renderer
│   └── navigation.css
├── app/
│   ├── Core/
│   │   ├── Auth.php                        Authentifizierung und Berechtigungen
│   │   └── Mail.php                        Gemeinsamer Mailversand
│   └── Modules/
│       ├── Sponsoren/
│       ├── Spendenaktionen/
│       ├── Benutzerverwaltung/
│       ├── Projekte/
│       └── Verkauf/
├── js/                                     Bestehende Frontend-Dateien
└── styles.css                              Bestehende globale Styles
```

## Warum die öffentlichen PHP-Dateien vorerst im Projektstamm bleiben

Die Dateien sind zugleich URLs, Formularziele und AJAX-Endpunkte. Ein sofortiges Verschieben könnte relative Pfade, Weiterleitungen, Dateizugriffe und JavaScript-Requests brechen. Deshalb bleiben diese Einstiegspunkte zunächst stabil. Die fachliche Implementierung kann danach einzeln in die Modulverzeichnisse verschoben werden, während die bisherigen URLs als dünne Kompatibilitäts-Einstiegspunkte bestehen bleiben.

## Regeln für die nächsten Schritte

1. Keine Datenbankmigration ohne gesonderte Planung und Freigabe.
2. Keine Änderung des sichtbaren Frontend-Verhaltens in der Strukturierungsphase.
3. Bestehende URLs und AJAX-Endpunkte bleiben kompatibel.
4. Berechtigungen müssen sowohl in der Navigation als auch serverseitig geprüft werden.
5. E-Mail-Versand und schreibende Datenbankoperationen nur gezielt testen.
6. Nach jeder kleinen Änderung PHP-Syntax prüfen und die wichtigsten Abläufe in einer getrennten Testinstallation testen.
7. Zugangsdaten aus `db_connection.php` gehören nicht in ein Quellcodepaket oder Versionsverwaltung.

## Phase 1 umgesetzt

- Authentifizierungs-/Berechtigungsfunktionen aus `auth.php` nach `app/Core/Auth.php` verlagert.
- Gemeinsame Mailfunktion aus `mail_utils.php` nach `app/Core/Mail.php` verlagert.
- Die Dateien `auth.php` und `mail_utils.php` bleiben als Kompatibilitäts-Einstiegspunkte bestehen.
- Der Datenbank-Include in der Authentifizierung verwendet einen expliziten Dateipfad.
- Fachliche Modulgrenzen dokumentiert; vorhandene Endpunkte und UI-Dateien bleiben an ihren bisherigen Orten.
- Datenbankschema und sichtbare Oberfläche nicht verändert.

## Phase 2: Sponsoren-Endpunkte

Die fünf Kontakt-Endpunkte wurden in `app/Modules/Sponsoren/Actions/` verlagert. Die Dateien im Projektstamm sind dünne Kompatibilitäts-Einstiegspunkte und behalten ihre bisherigen Namen und URLs. Bestehende JavaScript-Aufrufe müssen daher nicht angepasst werden. Die Implementierungen binden `auth.php` und `db_connection.php` über Pfade relativ zum Modulverzeichnis ein.

Bewusst noch nicht verschoben wurden die UI-Seiten `index.php` und `contact.php`; sie enthalten relative Links, Ressourcen und Formularlogik. Die Datenbankstruktur und fachliche Logik wurden in Phase 2 nicht absichtlich geändert.
