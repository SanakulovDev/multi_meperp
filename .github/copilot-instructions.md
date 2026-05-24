# Copilot instructions for MEP ERP

## Commands

### Environment and dependencies

```bash
composer install
docker-compose up -d
docker-compose down
```

- `composer install` writes dependencies to `_protected/vendor` because `composer.json` overrides `vendor-dir`.
- Root-level `env.php` sets `YII_DEBUG` and `YII_ENV`. Environment-specific secrets and connections live in `_protected/config/`.

### Yii console commands

Run Yii commands from `_protected/`:

```bash
cd _protected
php yii migrate
php yii db/seed-all
php yii db/create-admin <username>
php yii db/create-superadmin <username>
php yii rbac/init
php yii price/update
php yii bom
php yii coverage/daily
php yii coverage/balance
php yii monitor/check
```

### Tests

Run Codeception from `_protected/tests`:

```bash
cd _protected/tests
php ../vendor/bin/codecept run
php ../vendor/bin/codecept run unit
php ../vendor/bin/codecept run functional
php ../vendor/bin/codecept run acceptance
php ../vendor/bin/codecept run unit codeception/unit/models/FgInvoicePaymentServiceTest.php
php ../vendor/bin/codecept run functional codeception/functional/LoginCest.php
php ../vendor/bin/codecept build
```

- Functional tests use the Yii2 module with `codeception/config/functional.php`.
- Functional and acceptance bootstrap default to `http://localhost:8080/index-test.php`; adjust `_protected/tests/codeception.yml` and `acceptance.suite.yml` if your local entrypoint differs.

## High-level architecture

- This is a Yii2 ERP application with three main entry surfaces: the web UI under `_protected/controllers` + `_protected/views`, the REST API under `_protected/modules/api`, and console jobs under `_protected/console/controllers`.
- `_protected/config/web.php` bootstraps application-wide behavior: AdminLTE theming, language dispatching, maintenance mode, RBAC, JSON request parsing, and per-request visitor logging through `app\components\Visitors`.
- Web views are resolved through the theme path map from `@app/views` to `@webroot/themes/adminlte/views`, so UI changes often require checking both controller/view code and the themed template location.
- The REST API is mounted as the `api` module. `ApiModule` disables sessions and forces JSON responses; API controllers are thin and typically delegate filtering/serialization to `modules/api/search/*`.
- The console app has its own config in `_protected/config/console.php`, uses `_protected/console/migrations` for migrations, and is where scheduled jobs and data-maintenance commands live.
- Data access is centered on Yii ActiveRecord models in `_protected/models`. Search models (`*Search`) are a first-class pattern for GridView filtering and pagination, and some of them also switch into Excel export mode.

## Key conventions

- New web controllers should extend `app\controllers\AppController`, not Yii's base controller. `AppController::beforeAction()` enforces RBAC with permission names built as `{controller-id}-{action-id}`; if you add an action, check whether RBAC data or the bypass list must be updated.
- API controllers should extend `app\modules\api\controllers\BaseController`. That base class wires in CORS, JSON content negotiation, bearer-token auth, verb filtering, and disables CSRF validation.
- Translation keys are expected to go through `Yii::t(...)`, with messages stored as PHP arrays in `_protected/translations/{en,ru,uz}`. Language resolution is driven first by the `?language=` query param and then by a cookie.
- Search models are separate classes beside the base ActiveRecord model. Keep filtering/pagination logic there rather than in controllers, and watch for `search($params, $mode = '')` variants that return an Excel export when `$mode === 'excel'`.
- Every non-guest web request is logged via the global `on beforeAction` hook in `web.php`, so changes to controller flow can have side effects on visitor logging.
- The repo keeps most PHP application code outside the web root in `_protected/`; when you change config or bootstrap behavior, check both the root entry scripts (`index.php`, `index-test.php`) and the matching config under `_protected/config/`.
