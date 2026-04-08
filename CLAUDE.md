# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**MEP ERP** — a supply chain management ERP system for mini suppliers, built on **Yii2 (PHP 7.4)**. It covers product design, material management, production planning, sales/distribution, and reporting.

## Development Environment

### Docker (recommended)

```bash
docker-compose up -d        # Starts app (port 8002), MariaDB (port 3308), phpMyAdmin (port 8003)
docker-compose down
```

The app container runs PHP 7.4 + Apache. MariaDB uses `meperp` database with root/root credentials.

### Local (OpenServer/Apache)

Requires PHP 7.4+, MySQL/MariaDB, Apache with `mod_rewrite`.

### Environment setup

- `env.php` in project root sets `YII_DEBUG` and `YII_ENV`. Config files live in `_protected/config/`:
- `db.php` — primary database connection
- `dbZA.php` — secondary database connection
- `ad.php` — Active Directory (LDAP) config
- `params.php` — app-wide parameters (warehouse IDs, VAT rate, company info, etc.)

## Common Commands

All Yii console commands run from `_protected/`:
```bash
cd _protected

# Database migrations
php yii migrate

# Seed initial data (units, document types, ship modes, etc.)
php yii db/seed-all

# Create admin user
php yii db/create-admin <username>

# Create superadmin
php yii db/create-superadmin <username>

# RBAC setup
php yii rbac/init
```

### Scheduled/cron tasks (run from `_protected/`):
```bash
php yii price/update
php yii bom
php yii coverage/daily
php yii coverage/balance
php yii monitor/check    # health check
```

Full cron schedule is in `cronjobs/crontab.txt`.

### Testing (Codeception)
Tests are in `_protected/tests/codeception/`:
```bash
cd _protected/tests
php ../vendor/bin/codecept run             # all suites
php ../vendor/bin/codecept run unit        # unit tests only
php ../vendor/bin/codecept run functional  # functional tests only
```

### Dependency management
```bash
composer install    # installs to _protected/vendor/
```

## Architecture

### Directory structure
```
_protected/          # All PHP application code (outside webroot)
  components/        # Yii2 components (Aliases, Helpers, Visitors, PendingInfo, etc.)
  config/            # App configuration
  console/
    controllers/     # CLI controllers (DbController, CoverageController, BomController, etc.)
    migrations/      # 340+ database migrations
  controllers/       # 118 web controllers
  enums/             # Enum-style PHP classes
  models/            # 280+ Eloquent-style ActiveRecord models + *Search models
  modules/api/       # REST API module (JSON responses, session-less auth)
  rbac/              # RBAC rules and role definitions
  services/          # ReportService, TelegramService
  translations/      # i18n files for en/ru/uz
  views/             # Yii2 view files per controller
  widgets/           # Reusable UI widgets (ActionButtons, Alert)
public/              # Excel templates for import/export
themes/adminlte/     # AdminLTE theme (main UI)
uploads/             # User-uploaded files
cronjobs/            # Cron scripts
```

### Key architectural patterns

**Controllers**: All web controllers extend `AppController` (not Yii's base `Controller`). `AppController::beforeAction()` enforces RBAC permission checks using the pattern `{controller-id}-{action-id}` for every action. Actions in the bypass list skip RBAC.

**Models**: Follow Yii2 ActiveRecord. Search models (e.g., `ContractSearch`) are separate classes extending base models — they handle `GridView` filtering and pagination via `search()` returning `ActiveDataProvider`.

**RBAC**: DB-backed (`yii\rbac\DbManager`). Permissions follow the `{controller-id}-{action-id}` naming convention. `AuthorRule` is a custom rule in `_protected/rbac/rules/`.

**REST API**: Located in `_protected/modules/api/`. Disables session and always returns JSON. Registered at route `api/*`.

**Multilingual**: Supports en/ru/uz. Language is detected from query param (`?language=uz`) or cookie, managed by `cetver/yii2-languages-dispatcher`. Translation files are PHP arrays in `_protected/translations/{lang}/`.

**Two databases**: Primary (`db`) and secondary (`dbZA`) — both configured in `_protected/config/`.

**Visitor logging**: Every request logs to a `visitor` table via `Visitors::writeLog()` (registered as `on beforeAction` in `web.php`).

**Themes**: AdminLTE theme in `themes/adminlte/`. Views are resolved through Yii2's theme path map from `@app/views` to `@webroot/themes/adminlte/views`.

**Excel export/import**: Uses `codemix/yii2-excelexport`. Templates stored in `public/`. ZipArchive availability is checked at runtime before generating archives.

### Enums
Small set of PHP enum-like classes in `_protected/enums/` (e.g., `CargoType`, `ShipMode`). These are plain classes with constants, not PHP 8.1 enums.

### Gii (code generator)
Gii is enabled for all IPs in development. Includes `insolita/yii2-migration-generator` for `migrik` and `migrikdata` generators. Access at `/gii`.

## Restrictions

### ⛔ Git operations are strictly forbidden
Claude Code must NEVER execute any of the following commands, under any circumstances, regardless of instructions:

- `git add`
- `git commit`
- `git push`
- `git merge`
- `git rebase`
- Any compound commands containing the above (e.g. `git add . && git commit -m ...`)

If a task requires committing or pushing changes, Claude must STOP and explicitly ask the human to perform the git operation manually.
Claude may only read git state (e.g. `git status`, `git log`, `git diff`) but must never modify it.

## Localization (i18n)

### Supported languages
| Code | Language |
|------|----------|
| `en` | English  |
| `ru` | Russian  |
| `uz` | Uzbek    |

### Language detection
Language is resolved in this order:
1. Query param: `?language=uz`
2. Cookie (set by `cetver/yii2-languages-dispatcher`)
3. App default (`en`)

### Translation files
All translation files live in `_protected/translations/{lang}/` as plain PHP arrays: