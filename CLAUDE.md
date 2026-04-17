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
php ../vendor/bin/codecept run acceptance  # acceptance tests only

# Run a single file
php ../vendor/bin/codecept run unit codeception/unit/models/FgInvoicePaymentServiceTest.php
php ../vendor/bin/codecept run functional codeception/functional/LoginCest.php

# Rebuild actor classes after new helpers/pages
php ../vendor/bin/codecept build
```

Functional/acceptance bootstrap expects a running test entrypoint at `http://localhost:8080/index-test.php`. Adjust `_protected/tests/codeception.yml` / `acceptance.suite.yml` if your local setup differs.

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

**Controllers**: All web controllers extend `AppController` (not Yii's base `Controller`). `AppController::beforeAction()` enforces RBAC permission checks using the pattern `{controller-id}-{action-id}` for every action. Actions in the bypass list skip RBAC. When adding a new action, check whether RBAC `auth_item` rows or the bypass list need updating.

**API controllers**: Extend `app\modules\api\controllers\BaseController`. That base wires up CORS, JSON content negotiation, bearer-token auth, verb filtering, and disables CSRF. Keep API controllers thin — delegate filtering/serialization to `_protected/modules/api/search/*`.

**Models**: Follow Yii2 ActiveRecord. Search models (e.g., `ContractSearch`) are separate classes beside the base model — they handle `GridView` filtering and pagination via `search()` returning `ActiveDataProvider`. Some search models accept a second mode argument: `search($params, 'excel')` returns an Excel export instead of a data provider (see `FgInvoicePaymentSearch` and similar). Keep filtering logic in the search model, not in controllers.

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
All translation files live in `_protected/translations/{lang}/app.php` as plain PHP arrays returning `['key' => 'value']`. When adding a new `Yii::t('app', '…')` key, add it to **all three** files (`en/app.php`, `ru/app.php`, `uz/app.php`) — a missing key falls through to the raw string, which looks broken in non-English UIs.

## Codebase conventions

### CRUD-in-modal pattern
Most create/update flows in the web UI are AJAX modals, not full page loads. The typical controller + view pair looks like this:

- `actionIndex` renders a `GridView` with row-level buttons that carry `class="form-modal"` / `class="modalButtonUpdate"` / `class="modalButtonDelete"`. The shared modal JS in the AdminLTE theme reads `value` / `data-href` attributes and issues AJAX requests.
- `actionCreate` / `actionUpdate`:
  - On GET (AJAX): return `$this->renderAjax('_form', $this->formData($model))`.
  - On POST (AJAX): validate + save via a service and return JSON `{status: 1}` on success or `{status: 0, errors: $model->getErrors()}` on failure.
  - On non-AJAX: redirect to `['index']` (no standalone form page).
- `actionValidate` handles ActiveForm's `enableAjaxValidation`: loads the POSTed model and returns `ActiveForm::validate($model)` as JSON. Rebind the `validationUrl` to include `id` when updating.
- `actionDelete` returns JSON `{status: 1|0}` — there's no confirmation view.
- The `_form.php` partial begins with `ActiveForm::begin(['options' => ['class' => 'modalForm', 'data-pjax' => true]])` so the shared JS can intercept submit.

If a controller deviates from this (e.g., a reporting screen with its own layout), follow the existing file in that directory instead of forcing the modal pattern.

### Service layer
Services under `_protected/services/` own complex orchestration and heavy SQL, keeping controllers thin. Conventions:

- Services are plain classes, instantiated with `new FooService()` inside the controller (there's no DI container wiring for them). Controllers often expose a private `getService(): FooService` helper.
- `ReportService` is the home for report queries. Raw SQL via `Yii::$app->db->createCommand($sql, $params)->queryAll()` is expected here; ActiveRecord is reserved for simpler list screens.
- Some report queries rely on MySQL **window functions** (`SUM(...) OVER (PARTITION BY ... ORDER BY ...)`) — the app assumes MySQL 8.0+ / MariaDB 10.2+.
- Save logic (`$service->save($model)`) centralizes validation + `$model->save(false)`, and is where business rules (e.g., linking rows, updating related tables) belong. Controllers call the service and translate its boolean return into the JSON response.

### Number & date formatting
- `app\components\Helpers::numberFormatRemoveZero($value, $decimals = 2)` is the default money/quantity formatter for views (it drops trailing zeros).
- For thousand-separator input fields, the established JS pattern is `.replace(/\B(?=(\d{3})+(?!\d))/g, ' ')` (space separator). See `_protected/views/recept-control/_form.php` and `_protected/views/fg-invoice-payment/_form.php` for reference implementations that also normalize the value before AJAX validation / submit.
- Dates stored as `YYYY-MM-DD` strings are formatted for display with `date('d.m.Y', strtotime($value))`. Timestamps (`created_at`, `updated_at`) use `d.m.Y H:i` — most models expose a `getCreatedAtFormatted()` getter for this.