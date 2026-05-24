# Changelog — easy2-news

## [1.1.2] — 2026-05-24

### Bugfix
- `templates/news/news_add.php`: redundante Summernote-Script-Tags entfernt — Easy2-PHP8 lädt Summernote bereits selbst wenn `$p === 'news_add'`; die doppelte Initialisierung verhinderte das Rendern des Editors

## [1.1.1] — 2026-05-24

### Installer
- `install.php` neu — einmaliger Browser-Installer; liest `system/config.inc.php`, ersetzt `[prefix]` in `install/sql/_news.sql` und führt die SQL-Statements aus
- Vorab-Checks mit verständlichen Fehlermeldungen (Config, SQL, Plugin, PDO, Prefix)
- Erkennt bereits installierte Tabelle und überspringt erneute Ausführung
- Löscht sich nach erfolgreicher Installation selbst (`unlink`)

### Datenbank
- `install/sql/_news.sql` ergänzt: Seiten-Registrierung (`_sites`) für `newsall`, `news`, `news_add` sowie Menüeinträge (`_menu`) — "News" im Hauptmenü und "News verwalten" unter Einstellungen

## [1.1.0] — 2026-05-24

### Plugin-Loader-Integration
- `system/plugins/news/run.php` neu — integriert sich in den Easy2-PHP8 Plugin-Loader (`system/run.user.php`)
- `run.user.php.example` und `classes.run.user.php.example` entfernt — werden durch `run.php` ersetzt, kein manuelles Eintragen mehr nötig

## [1.0.0] — 2026-04-17

### Erstveröffentlichung
- Eigenentwicklung — **nicht Teil des originalen EASY 2.0 Systems**
- Erweiterung für das EASY 2.0 PHP8 Fork (easy2-php8)

### Features
- News erstellen, bearbeiten, löschen
- News aktiv / inaktiv schalten
- News-Übersichtsseite (`newsall.php`) mit Paginierung
- News-Detailseite (`news.php`)
- News-Widget für die Startseite (`news_widget.php`)
- Admin-Bereich: `news_add.php` mit vollständiger CRUD-Verwaltung

### Editor
- **Summernote 0.8.20** WYSIWYG-Editor (Bootstrap 3 kompatibel, MIT-Lizenz)
- Deutsche Lokalisierung (`summernote-de-DE`)
- Validierung: leerer Inhalt wird vor dem Absenden abgefangen
