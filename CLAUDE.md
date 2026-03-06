# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

MEP ERP (Supply Chain Management System) — a Yii2-based ERP system for managing supply chain workflows including product design, material management, production planning, reporting, and sales/distribution.

- **Framework:** Yii2 Basic Template (PHP >= 7.1, runs on PHP 7.4)
- **Database:** MariaDB 10.4
- **Auth:** Active Directory (LDAP) via `edvlerblog/yii2-adldap-module`, RBAC via `yii\rbac\DbManager`
- **Theme:** AdminLTE (at `themes/adminlte/`)
- **Languages:** en, ru, uz (translations in `_protected/translations/`)
- **Timezone:** Asia/Tashkent

## Commands

```bash
# Install dependencies (uses Composer 1.x)
composer install

# Run migrations
cd _protected && php yii migrate

# Seed initial data
cd _protected && php yii db/seed-all

# Create admin user
cd _protected && php yii db/create-admin :username

# Run tests (Codeception)
cd _protected && vendor/bin/codecept run

# Docker
docker-compose up        # App on :8111, MariaDB on :3006

# Console commands (from _protected/)
php yii <controller>/<action>
```

## Architecture

### Directory Structure

All application code lives under `_protected/` (the Yii2 `basePath`). The web root is the project root — `index.php` bootstraps the app.

- `_protected/controllers/` — ~118 web controllers, most extend `AppController`
- `_protected/models/` — ~282 models (ActiveRecord + Search models with `*Search.php` naming)
- `_protected/views/` — view files organized by controller name
- `_protected/modules/api/` — REST API module (`app\modules\api\ApiModule`)
- `_protected/console/controllers/` — CLI commands (Db, Rbac, Coverage, CurrencyRate, Monitor, etc.)
- `_protected/console/migrations/` — database migrations
- `_protected/components/` — shared components (Helpers, Aliases, BarcodeGenerator, Visitors, PendingInfo, etc.)
- `_protected/services/` — ReportService, TelegramService
- `_protected/enums/` — enum classes (CargoType, ShipMode, FreightInvoiceType, etc.)
- `_protected/rbac/` — RBAC helpers, models, and rules
- `_protected/widgets/` — ActionButtons, Alert
- `_protected/config/` — app config: `web.php`, `console.php`, `db.php`, `ad.php`, `params.php`, `mail.php`

### Key Patterns

- **AppController** (`_protected/controllers/AppController.php`): Base controller for most web controllers. Implements RBAC permission checks in `beforeAction()` using the pattern `{controller-id}-{action-id}`. Actions listed in its whitelist array skip permission checks.
- **Theme override**: Views resolve first from `themes/adminlte/views/`, falling back to `_protected/views/`. Layout is `adminlte`.
- **Config files**: `env.php` (root) sets `YII_DEBUG` and `YII_ENV`. Sensitive config (`db.php`, `ad.php`) is not committed — must be created per environment.
- **Vendor directory**: Lives at `_protected/vendor/` (configured in `composer.json` via `vendor-dir`).
- **`globals.php`**: Defines global helper functions (`vd()` for debug dumping, `divideString()`, `sendMessage()` for Telegram).
- **URL routing**: Uses `cetver/yii2-language-url-manager` with language prefix. REST API rule for `api/document`.
- **i18n**: Source language is `de` (German keys), translated to en/ru/uz.
- **Scheduled tasks**: `cronjobs/` contains batch scripts for coverage, currency rates, monitoring, and reports.
- **`params.php`**: Contains warehouse IDs, contract source IDs, shift configurations, and business-specific constants. These are referenced throughout the codebase via `Yii::$app->params`.
