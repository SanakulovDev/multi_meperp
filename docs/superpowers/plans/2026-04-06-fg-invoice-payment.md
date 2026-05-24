# FgInvoicePayment Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build a new payment module where the user selects a sales contract → sees matching TTN (waybill) numbers → picks one or more waybills → enters payment number, date, amount — and the customer name auto-populates readonly from the contract.

**Architecture:** Two new DB tables (`fg_invoice_payment` + `fg_invoice_payment_waybill` pivot). A `FgInvoicePaymentService` handles the transaction-safe save and the waybill/customer lookup. `FgInvoicePaymentController` handles HTTP only, following the same AJAX modal pattern as `FgInvoiceReceiptController`. Old `receipt_waybill` / `recept_control` tables are untouched.

**Tech Stack:** Yii2 (PHP 7.4), ActiveRecord, Codeception unit tests, select2 multi-select (jQuery), codemix/yii2-excelexport, yii\behaviors\TimestampBehavior + BlameableBehavior.

---

## File Map

| Action | Path |
|--------|------|
| Create | `_protected/console/migrations/m260406_000000_create_fg_invoice_payment_table.php` |
| Create | `_protected/models/FgInvoicePayment.php` |
| Create | `_protected/models/FgInvoicePaymentWaybill.php` |
| Create | `_protected/models/FgInvoicePaymentSearch.php` |
| Create | `_protected/services/FgInvoicePaymentService.php` |
| Create | `_protected/controllers/FgInvoicePaymentController.php` |
| Create | `_protected/views/fg-invoice-payment/index.php` |
| Create | `_protected/views/fg-invoice-payment/_form.php` |
| Create | `_protected/tests/codeception/unit/models/FgInvoicePaymentServiceTest.php` |
| Modify | `_protected/controllers/AppController.php` (add `list-waybills-by-contract` to bypass list) |

---

## Task 1: Database Migration

**Files:**
- Create: `_protected/console/migrations/m260406_000000_create_fg_invoice_payment_table.php`

- [ ] **Step 1: Create the migration file**

```php
<?php
use yii\db\Migration;

class m260406_000000_create_fg_invoice_payment_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';

        $this->createTable('{{%fg_invoice_payment}}', [
            'id'                => $this->primaryKey(11),
            'no'                => $this->string(100)->notNull(),
            'date'              => $this->date()->notNull(),
            'sales_contract_id' => $this->integer(11)->notNull(),
            'amount'            => $this->decimal(25, 10)->notNull(),
            'created_at'        => $this->integer(11)->notNull(),
            'created_by'        => $this->integer(11)->notNull(),
            'updated_by'        => $this->integer(11)->null()->defaultValue(null),
            'updated_at'        => $this->integer(11)->null()->defaultValue(null),
        ], $tableOptions);

        $this->addForeignKey(
            'fk_fg_invoice_payment_sales_contract_id',
            '{{%fg_invoice_payment}}', 'sales_contract_id',
            '{{%sales_contract}}', 'id',
            'RESTRICT', 'RESTRICT'
        );

        $this->createTable('{{%fg_invoice_payment_waybill}}', [
            'id'         => $this->primaryKey(11),
            'payment_id' => $this->integer(11)->notNull(),
            'waybill_id' => $this->integer(11)->notNull(),
        ], $tableOptions);

        $this->addForeignKey(
            'fk_fgipw_payment_id',
            '{{%fg_invoice_payment_waybill}}', 'payment_id',
            '{{%fg_invoice_payment}}', 'id',
            'CASCADE', 'CASCADE'
        );
        $this->addForeignKey(
            'fk_fgipw_waybill_id',
            '{{%fg_invoice_payment_waybill}}', 'waybill_id',
            '{{%waybill}}', 'id',
            'RESTRICT', 'RESTRICT'
        );

        // RBAC permissions
        Yii::$app->db->createCommand(
            "INSERT IGNORE `auth_item`(`name`, `type`)
            VALUES
            ('fg-invoice-payment-index',2),
            ('fg-invoice-payment-create',2),
            ('fg-invoice-payment-update',2),
            ('fg-invoice-payment-delete',2),
            ('fg-invoice-payment-xls',2)"
        )->execute();

        Yii::$app->db->createCommand(
            "INSERT IGNORE `auth_item_child`(`parent`, `child`)
            VALUES
            ('admin','fg-invoice-payment-index'),
            ('admin','fg-invoice-payment-create'),
            ('admin','fg-invoice-payment-update'),
            ('admin','fg-invoice-payment-delete'),
            ('admin','fg-invoice-payment-xls'),
            ('superadmin','fg-invoice-payment-index'),
            ('superadmin','fg-invoice-payment-create'),
            ('superadmin','fg-invoice-payment-update'),
            ('superadmin','fg-invoice-payment-delete'),
            ('superadmin','fg-invoice-payment-xls')"
        )->execute();

        Yii::$app->getAuthManager()->invalidateCache();
    }

    public function safeDown()
    {
        $this->dropTable('{{%fg_invoice_payment_waybill}}');
        $this->dropTable('{{%fg_invoice_payment}}');
    }
}
```

- [ ] **Step 2: Run the migration**

```bash
cd _protected
php yii migrate --migrationPath=@app/console/migrations
```

Expected: `Applied 1 migration.`

- [ ] **Step 3: Commit**

```bash
git add _protected/console/migrations/m260406_000000_create_fg_invoice_payment_table.php
git commit -m "feat: add migration for fg_invoice_payment tables and RBAC permissions"
```

---

## Task 2: ActiveRecord Models

**Files:**
- Create: `_protected/models/FgInvoicePayment.php`
- Create: `_protected/models/FgInvoicePaymentWaybill.php`

- [ ] **Step 1: Create `FgInvoicePayment.php`**

```php
<?php
namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;

/**
 * @property int    $id
 * @property string $no
 * @property string $date
 * @property int    $sales_contract_id
 * @property float  $amount
 * @property int    $created_at
 * @property int    $created_by
 * @property int    $updated_at
 * @property int    $updated_by
 *
 * @property SalesContract         $salesContract
 * @property FgInvoicePaymentWaybill[] $paymentWaybills
 * @property Waybill[]             $waybills
 * @property User                  $createdBy
 * @property User                  $updatedBy
 */
class FgInvoicePayment extends \yii\db\ActiveRecord
{
    /** @var int[] virtual — holds selected waybill IDs from the form */
    public $waybill_ids = [];

    public static function tableName()
    {
        return 'fg_invoice_payment';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            BlameableBehavior::className(),
        ];
    }

    public function rules()
    {
        return [
            [['no', 'date', 'sales_contract_id', 'amount'], 'required'],
            [['sales_contract_id', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['amount'], 'number', 'min' => 0.0001],
            [['date'], 'safe'],
            [['no'], 'string', 'max' => 100],
            [['waybill_ids'], 'required', 'message' => Yii::t('app', 'At least one waybill must be selected.')],
            [['waybill_ids'], 'each', 'rule' => ['integer']],
            [['sales_contract_id'], 'exist', 'skipOnError' => true,
                'targetClass' => SalesContract::className(),
                'targetAttribute' => ['sales_contract_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'                => Yii::t('app', 'ID'),
            'no'                => Yii::t('app', 'Receipt number'),
            'date'              => Yii::t('app', 'Date'),
            'sales_contract_id' => Yii::t('app', 'Sales contract'),
            'amount'            => Yii::t('app', 'Amount'),
            'waybill_ids'       => Yii::t('app', 'Waybills (TTN)'),
            'created_at'        => Yii::t('app', 'Created at'),
            'created_by'        => Yii::t('app', 'Created by'),
            'updated_at'        => Yii::t('app', 'Updated at'),
            'updated_by'        => Yii::t('app', 'Updated by'),
        ];
    }

    public function getSalesContract(): ActiveQuery
    {
        return $this->hasOne(SalesContract::className(), ['id' => 'sales_contract_id']);
    }

    public function getPaymentWaybills(): ActiveQuery
    {
        return $this->hasMany(FgInvoicePaymentWaybill::className(), ['payment_id' => 'id']);
    }

    public function getWaybills(): ActiveQuery
    {
        return $this->hasMany(Waybill::className(), ['id' => 'waybill_id'])
                    ->via('paymentWaybills');
    }

    public function getCreatedBy(): ActiveQuery
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    public function getUpdatedBy(): ActiveQuery
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    public function getCreatedAtFormatted(): string
    {
        return !empty($this->created_at) ? date('d.m.Y H:i', $this->created_at) : '';
    }

    public function getUpdatedAtFormatted(): string
    {
        return !empty($this->updated_at) ? date('d.m.Y H:i', $this->updated_at) : '';
    }

    /** Comma-separated waybill numbers for display in GridView */
    public function getWaybillNos(): string
    {
        return implode(', ', array_column($this->waybills, 'waybill_no'));
    }
}
```

- [ ] **Step 2: Create `FgInvoicePaymentWaybill.php`**

```php
<?php
namespace app\models;

use Yii;

/**
 * @property int $id
 * @property int $payment_id
 * @property int $waybill_id
 *
 * @property FgInvoicePayment $payment
 * @property Waybill          $waybill
 */
class FgInvoicePaymentWaybill extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'fg_invoice_payment_waybill';
    }

    public function rules()
    {
        return [
            [['payment_id', 'waybill_id'], 'required'],
            [['payment_id', 'waybill_id'], 'integer'],
            [['payment_id'], 'exist', 'skipOnError' => true,
                'targetClass' => FgInvoicePayment::className(),
                'targetAttribute' => ['payment_id' => 'id']],
            [['waybill_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Waybill::className(),
                'targetAttribute' => ['waybill_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'         => Yii::t('app', 'ID'),
            'payment_id' => Yii::t('app', 'Payment'),
            'waybill_id' => Yii::t('app', 'Waybill'),
        ];
    }

    public function getPayment()
    {
        return $this->hasOne(FgInvoicePayment::className(), ['id' => 'payment_id']);
    }

    public function getWaybill()
    {
        return $this->hasOne(Waybill::className(), ['id' => 'waybill_id']);
    }
}
```

- [ ] **Step 3: Commit**

```bash
git add _protected/models/FgInvoicePayment.php _protected/models/FgInvoicePaymentWaybill.php
git commit -m "feat: add FgInvoicePayment and FgInvoicePaymentWaybill ActiveRecord models"
```

---

## Task 3: Search Model

**Files:**
- Create: `_protected/models/FgInvoicePaymentSearch.php`

- [ ] **Step 1: Create `FgInvoicePaymentSearch.php`**

```php
<?php
namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Search model for FgInvoicePayment GridView filtering and Excel export.
 */
class FgInvoicePaymentSearch extends FgInvoicePayment
{
    /** @var string filter by customer name (virtual) */
    public $customer;
    /** @var string filter by contract number (virtual) */
    public $contract;

    public function rules()
    {
        return [
            [['id', 'sales_contract_id', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['no', 'date', 'customer', 'contract'], 'safe'],
            [['amount'], 'number'],
            [['waybill_ids'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search(array $params, string $mode = '')
    {
        $query = FgInvoicePayment::find()
            ->joinWith(['salesContract.customer']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => ['defaultOrder' => ['date' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'fg_invoice_payment.id'                => $this->id,
            'fg_invoice_payment.sales_contract_id' => $this->sales_contract_id,
            'fg_invoice_payment.amount'            => $this->amount,
        ]);

        $query->andFilterWhere(['like', 'fg_invoice_payment.no',   $this->no])
              ->andFilterWhere(['like', 'fg_invoice_payment.date',  $this->date])
              ->andFilterWhere(['like', 'customer.name',            $this->customer])
              ->andFilterWhere(['like', 'sales_contract.contract_no', $this->contract]);

        if ($mode === 'excel') {
            $query->joinWith(['createdBy', 'updatedBy' => function ($q) {
                $q->from(['u2' => User::tableName()]);
            }]);

            return \Yii::createObject([
                'class'  => 'codemix\excelexport\ExcelFile',
                'sheets' => [
                    'Payment data' => [
                        'class'      => 'codemix\excelexport\ActiveExcelSheet',
                        'query'      => $query,
                        'attributes' => [
                            'id',
                            'salesContract.customer.name',
                            'salesContract.contract_no',
                            'no',
                            'date',
                            'amount',
                            'waybillNos',
                            'createdBy.fullname',
                            'createdAtFormatted',
                            'updatedBy.fullname',
                            'updatedAtFormatted',
                        ],
                        'titles' => [
                            1  => Yii::t('app', 'Customer'),
                            2  => Yii::t('app', 'Contract'),
                            7  => Yii::t('app', 'Created by'),
                            8  => Yii::t('app', 'Created at'),
                            9  => Yii::t('app', 'Updated by'),
                            10 => Yii::t('app', 'Updated at'),
                        ],
                    ],
                ],
            ]);
        }

        return $dataProvider;
    }
}
```

- [ ] **Step 2: Commit**

```bash
git add _protected/models/FgInvoicePaymentSearch.php
git commit -m "feat: add FgInvoicePaymentSearch model with GridView and Excel support"
```

---

## Task 4: Service (TDD)

**Files:**
- Create: `_protected/services/FgInvoicePaymentService.php`
- Create: `_protected/tests/codeception/unit/models/FgInvoicePaymentServiceTest.php`

- [ ] **Step 1: Write the failing unit test**

```php
<?php
// _protected/tests/codeception/unit/models/FgInvoicePaymentServiceTest.php
namespace tests\codeception\unit\models;

use app\models\FgInvoicePayment;
use app\models\FgInvoicePaymentWaybill;
use app\services\FgInvoicePaymentService;
use tests\codeception\unit\DbTestCase;
use Yii;

class FgInvoicePaymentServiceTest extends DbTestCase
{
    private FgInvoicePaymentService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new FgInvoicePaymentService();
    }

    public function testGetWaybillsByContractReturnsEmptyArrayWhenContractNotFound()
    {
        $result = $this->service->getWaybillsByContract(999999);
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testGetCustomerByContractReturnsNullWhenNotFound()
    {
        $result = $this->service->getCustomerByContract(999999);
        $this->assertNull($result);
    }

    public function testSaveReturnsFalseWhenModelInvalid()
    {
        $model = new FgInvoicePayment();
        // no required fields set → invalid
        $result = $this->service->save($model, []);
        $this->assertFalse($result);
    }
}
```

- [ ] **Step 2: Run the test to confirm it fails (class not found)**

```bash
cd _protected/tests
php ../vendor/bin/codecept run unit models/FgInvoicePaymentServiceTest --no-colors 2>&1 | tail -10
```

Expected output contains: `Error` or `Class 'app\services\FgInvoicePaymentService' not found`

- [ ] **Step 3: Create `FgInvoicePaymentService.php`**

```php
<?php
namespace app\services;

use app\models\FgInvoicePayment;
use app\models\FgInvoicePaymentWaybill;
use app\models\FgInvoice;
use app\models\FgInvoiceWaybill;
use app\models\SalesContract;
use app\models\Customer;
use Yii;

class FgInvoicePaymentService
{
    /**
     * Returns waybills linked to any FgInvoice of the given sales contract.
     *
     * Chain: SalesContract → FgInvoice (via contract_no + customer_id)
     *        → FgInvoiceWaybill → Waybill
     *
     * @return array  [['id' => int, 'text' => string], ...]
     */
    public function getWaybillsByContract(int $contractId): array
    {
        $contract = SalesContract::findOne($contractId);
        if ($contract === null) {
            return [];
        }

        $waybillRows = FgInvoiceWaybill::find()
            ->alias('fiw')
            ->innerJoinWith(['fgInvoice fi' => function ($q) use ($contract) {
                $q->andWhere([
                    'fi.contract'    => $contract->contract_no,
                    'fi.customer_id' => $contract->customer_id,
                ]);
            }])
            ->innerJoinWith('waybill w')
            ->select(['w.id', 'w.waybill_no'])
            ->distinct()
            ->asArray()
            ->all();

        return array_map(fn($row) => [
            'id'   => (int) $row['id'],
            'text' => $row['waybill_no'],
        ], $waybillRows);
    }

    /**
     * Returns the Customer linked to the given SalesContract.
     */
    public function getCustomerByContract(int $contractId): ?Customer
    {
        $contract = SalesContract::find()
            ->with('customer')
            ->where(['id' => $contractId])
            ->one();

        return $contract ? $contract->customer : null;
    }

    /**
     * Saves the payment and its pivot waybill rows in one transaction.
     * Populates model errors on failure.
     *
     * @param FgInvoicePayment $model
     * @param int[]            $waybillIds
     */
    public function save(FgInvoicePayment $model, array $waybillIds): bool
    {
        $model->waybill_ids = $waybillIds;

        if (!$model->validate()) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            if (!$model->save(false)) {
                $transaction->rollBack();
                return false;
            }

            // Delete existing pivot rows (for update)
            FgInvoicePaymentWaybill::deleteAll(['payment_id' => $model->id]);

            foreach ($waybillIds as $waybillId) {
                $pivot = new FgInvoicePaymentWaybill();
                $pivot->payment_id = $model->id;
                $pivot->waybill_id = (int) $waybillId;
                if (!$pivot->save()) {
                    $transaction->rollBack();
                    return false;
                }
            }

            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage(), 'fg-invoice-payment');
            return false;
        }
    }
}
```

- [ ] **Step 4: Run tests to confirm they pass**

```bash
cd _protected/tests
php ../vendor/bin/codecept run unit models/FgInvoicePaymentServiceTest --no-colors 2>&1 | tail -10
```

Expected: `OK (3 tests, 3 assertions)`

- [ ] **Step 5: Commit**

```bash
git add _protected/services/FgInvoicePaymentService.php \
        _protected/tests/codeception/unit/models/FgInvoicePaymentServiceTest.php
git commit -m "feat: add FgInvoicePaymentService with unit tests"
```

---

## Task 5: Controller

**Files:**
- Create: `_protected/controllers/FgInvoicePaymentController.php`
- Modify: `_protected/controllers/AppController.php` (line 43 — bypass list)

- [ ] **Step 1: Add `list-waybills-by-contract` to the RBAC bypass list in `AppController.php`**

In `_protected/controllers/AppController.php`, find the `in_array` bypass list (around line 43) and add the new action:

```php
// Before (existing last item):
      'list-by-waybill'
    ])) {

// After:
      'list-by-waybill',
      'list-waybills-by-contract'
    ])) {
```

- [ ] **Step 2: Create `FgInvoicePaymentController.php`**

```php
<?php
namespace app\controllers;

use app\components\Helpers;
use app\models\FgInvoicePayment;
use app\models\FgInvoicePaymentSearch;
use app\models\SalesContract;
use app\services\FgInvoicePaymentService;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

class FgInvoicePaymentController extends AppController
{
    private function getService(): FgInvoicePaymentService
    {
        return new FgInvoicePaymentService();
    }

    public function actionIndex()
    {
        $searchModel  = new FgInvoicePaymentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', compact('searchModel', 'dataProvider'));
    }

    public function actionValidate($id = null)
    {
        $model = $id === null ? new FgInvoicePayment() : $this->findModel($id);
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
    }

    public function actionCreate()
    {
        $model = new FgInvoicePayment();

        if (!Yii::$app->request->isAjax) {
            return $this->redirect(['index']);
        }

        if ($model->load(Yii::$app->request->post())) {
            $waybillIds = (array) Yii::$app->request->post('waybill_ids', []);
            Yii::$app->response->format = Response::FORMAT_JSON;

            if ($this->getService()->save($model, $waybillIds)) {
                return ['status' => 1];
            }
            return ['status' => 0, 'errors' => $model->getErrors()];
        }

        return $this->renderAjax('_form', $this->formData($model));
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->amount = Helpers::numberFormatRemoveZero($model->amount, 2, '.', '');
        // Pre-fill virtual waybill_ids for form
        $model->waybill_ids = ArrayHelper::getColumn($model->paymentWaybills, 'waybill_id');

        if (!Yii::$app->request->isAjax) {
            return $this->redirect(['index']);
        }

        if ($model->load(Yii::$app->request->post())) {
            $waybillIds = (array) Yii::$app->request->post('waybill_ids', []);
            Yii::$app->response->format = Response::FORMAT_JSON;

            if ($this->getService()->save($model, $waybillIds)) {
                return ['status' => 1];
            }
            return ['status' => 0, 'errors' => $model->getErrors()];
        }

        return $this->renderAjax('_form', $this->formData($model));
    }

    public function actionDelete($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = FgInvoicePayment::find()->where(['id' => $id])->one();
        if ($model && $model->delete()) {
            return ['status' => 1];
        }
        return ['status' => 0];
    }

    public function actionXls()
    {
        ini_set('memory_limit', '-1');
        $searchModel = new FgInvoicePaymentSearch();
        $file = $searchModel->search(Yii::$app->request->queryParams, 'excel');
        $file->send(Helpers::downloadFileName('fg-invoice-payment'));
    }

    /**
     * AJAX: returns waybills + customer for a given sales_contract_id.
     * Bypasses RBAC (added to AppController bypass list).
     */
    public function actionListWaybillsByContract($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $service  = $this->getService();
        $customer = $service->getCustomerByContract((int) $id);
        $waybills = $service->getWaybillsByContract((int) $id);

        return [
            'customer' => $customer ? ['name' => $customer->name] : null,
            'waybills' => $waybills,
        ];
    }

    protected function findModel($id): FgInvoicePayment
    {
        if (($model = FgInvoicePayment::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    private function formData(FgInvoicePayment $model): array
    {
        $contracts = ArrayHelper::map(SalesContract::find()->all(), 'id', 'contract_no');
        return compact('model', 'contracts');
    }
}
```

- [ ] **Step 3: Commit**

```bash
git add _protected/controllers/FgInvoicePaymentController.php \
        _protected/controllers/AppController.php
git commit -m "feat: add FgInvoicePaymentController and register bypass action"
```

---

## Task 6: Views

**Files:**
- Create: `_protected/views/fg-invoice-payment/index.php`
- Create: `_protected/views/fg-invoice-payment/_form.php`

- [ ] **Step 1: Create `index.php`**

```php
<?php
use app\components\Helpers;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\FgInvoicePaymentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'FG Invoice Payments');
$this->params['breadcrumbs'][] = $this->title;

$canCreate = Yii::$app->user->can('fg-invoice-payment-create');
$canUpdate = Yii::$app->user->can('fg-invoice-payment-update');
$canDelete = Yii::$app->user->can('fg-invoice-payment-delete');
$canXls    = Yii::$app->user->can('fg-invoice-payment-xls');
?>
<div class="fg-invoice-payment-index">
    <p class="pull-right">
        <?php if ($canCreate): ?>
            <?= Html::a(Yii::t('app', 'btn-create'), ['create'], [
                'class' => 'btn btn-success btn-sm form-modal',
                'style' => 'margin-right:5px',
            ]) ?>
        <?php endif; ?>
        <?php if ($canXls): ?>
            <?= Html::a(Yii::t('app', 'btn-download'),
                ['xls', 'FgInvoicePaymentSearch' => ($_GET['FgInvoicePaymentSearch'] ?? null)],
                ['class' => 'btn btn-info btn-sm']
            ) ?>
        <?php endif; ?>
    </p>

    <?php Pjax::begin(['id' => 'pjaxGrid']); ?>
    <?= GridView::widget([
        'dataProvider'    => $dataProvider,
        'filterModel'     => $searchModel,
        'summary'         => Yii::t('app', 'Showing {begin}-{end} of {totalCount} item.'),
        'rowOptions'      => ['style' => 'white-space:nowrap;vertical-align:middle;'],
        'options'         => ['style' => 'overflow:auto;clear:both'],
        'emptyText'       => Yii::t('app', 'No results found.'),
        'tableOptions'    => ['class' => 'sm_filter table table-striped table-bordered table-condensed table-sm-padding_2_0'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'class'    => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
                'header'   => '<i class="fa fa-fw fa-gears"></i>',
                'headerOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;color:#3c8dbc;'],
                'contentOptions' => ['style' => 'min-width:50px;text-align:center;vertical-align:middle;'],
                'buttons' => [
                    'update' => function ($url, $model) use ($canUpdate) {
                        if (!$canUpdate) return false;
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', false, [
                            'class' => 'modalButtonUpdate',
                            'value' => $url,
                            'title' => Yii::t('app', 'Edit'),
                        ]);
                    },
                    'delete' => function ($url, $model) use ($canDelete) {
                        if (!$canDelete) return false;
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', false, [
                            'class'     => 'modalButtonDelete',
                            'data-href' => $url,
                            'data-grid' => 'pjaxGrid',
                            'title'     => Yii::t('app', 'Delete'),
                        ]);
                    },
                ],
                'visible' => $canUpdate || $canDelete,
            ],
            [
                'attribute' => 'customer',
                'value'     => fn($model) => $model->salesContract->customer->name ?? '',
            ],
            [
                'attribute' => 'contract',
                'value'     => fn($model) => $model->salesContract->contract_no ?? '',
            ],
            'no',
            'date',
            [
                'label' => Yii::t('app', 'Waybills (TTN)'),
                'value' => fn($model) => $model->waybillNos,
            ],
            [
                'attribute'     => 'amount',
                'contentOptions' => ['style' => 'text-align:right;vertical-align:middle;'],
                'value'         => fn($model) => Helpers::numberFormatRemoveZero($model->amount),
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
```

- [ ] **Step 2: Create `_form.php`**

```php
<?php
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this     yii\web\View */
/* @var $model    app\models\FgInvoicePayment */
/* @var $contracts array [id => contract_no] */

$validationUrl = ['validate'];
if (!$model->isNewRecord) {
    $validationUrl['id'] = $model->id;
}

$form = ActiveForm::begin([
    'id'                  => $model->formName(),
    'enableAjaxValidation' => true,
    'validateOnType'      => false,
    'validationUrl'       => $validationUrl,
    'options'             => ['data-pjax' => true, 'class' => 'modalForm'],
]);
?>

<div class="row">
    <div class="col-sm-6">
        <?= $form->field($model, 'sales_contract_id')->dropDownList($contracts, [
            'id'    => 'fginvoicepayment-sales_contract_id',
            'class' => 'select2',
            'prompt' => '',
        ]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'no')->textInput(['maxlength' => true]) ?>
    </div>
</div>

<div class="row">
    <div class="col-sm-6">
        <?= $form->field($model, 'waybill_ids[]')->widget(
            \kartik\select2\Select2::class,
            [
                'data'          => [],   // populated by JS
                'options'       => ['multiple' => true, 'id' => 'fginvoicepayment-waybill_ids'],
                'pluginOptions' => ['allowClear' => true],
            ]
        ) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'date')->widget(
            \kartik\datetime\DateTimePicker::class,
            [
                'options'       => ['placeholder' => 'YYYY-MM-DD'],
                'pluginOptions' => [
                    'autoclose'     => true,
                    'format'        => 'yyyy-mm-dd',
                    'minView'       => 2,
                    'todayBtn'      => true,
                ],
            ]
        ) ?>
    </div>
</div>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group has-success">
            <label class="control-label"><?= Yii::t('app', 'Customer') ?></label>
            <input type="text" id="fginvoicepayment-customer-name"
                   class="form-control" readonly aria-invalid="false">
        </div>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'amount')->textInput(['type' => 'number', 'step' => 'any']) ?>
    </div>
</div>

<?php if (!$model->isNewRecord): ?>
    <table class="table table-bordered table-condensed">
        <tr>
            <th><?= Yii::t('app', 'Created by') ?></th>
            <th><?= Yii::t('app', 'Created at') ?></th>
            <th><?= Yii::t('app', 'Updated by') ?></th>
            <th><?= Yii::t('app', 'Updated at') ?></th>
        </tr>
        <tr>
            <td><?= $model->createdBy->fullname ?? '' ?></td>
            <td><?= $model->createdAtFormatted ?></td>
            <td><?= $model->updatedBy->fullname ?? '' ?></td>
            <td><?= $model->updatedAtFormatted ?></td>
        </tr>
    </table>
<?php endif; ?>

<?php ActiveForm::end(); ?>

<?php
$ajaxUrl      = Url::to(['fg-invoice-payment/list-waybills-by-contract'], true);
$isUpdate     = $model->isNewRecord ? 0 : 1;
$contractId   = (int)($model->sales_contract_id ?? 0);
$selectedIds  = json_encode($model->waybill_ids ?: []);

$script = <<<JS
(function () {
    function loadWaybills(contractId, selectedIds) {
        if (!contractId) return;
        $.get('$ajaxUrl', { id: contractId }, function (data) {
            var \$sel = $('#fginvoicepayment-waybill_ids');
            \$sel.find('option').remove();
            if (data.customer) {
                $('#fginvoicepayment-customer-name').val(data.customer.name);
            }
            $.each(data.waybills, function () {
                \$sel.append(new Option(this.text, this.id));
            });
            if (selectedIds && selectedIds.length) {
                \$sel.val(selectedIds).trigger('change');
            }
        });
    }

    $('#fginvoicepayment-sales_contract_id').on('select2:select', function (e) {
        loadWaybills(e.params.data.id, []);
    });

    if ($isUpdate === 1) {
        loadWaybills($contractId, $selectedIds);
    }
})();
JS;
$this->registerJs($script, \yii\web\View::POS_END);
```

- [ ] **Step 3: Commit**

```bash
git add _protected/views/fg-invoice-payment/index.php \
        _protected/views/fg-invoice-payment/_form.php
git commit -m "feat: add FgInvoicePayment index and form views"
```

---

## Task 7: Manual Smoke Test

- [ ] **Step 1: Open the index page**

Navigate to `/fg-invoice-payment/index`. Verify the GridView renders with correct columns (Customer, Contract, Payment No, Date, Waybills, Amount).

- [ ] **Step 2: Test Create modal**

Click "Create". Verify:
- Contract select2 is populated.
- On contract select, waybill multi-select is filled and customer name appears readonly.
- Submitting with no waybills shows validation error "At least one waybill must be selected."
- Valid submit creates a record and the grid refreshes.

- [ ] **Step 3: Test Update modal**

Click edit on a record. Verify:
- Contract, waybills, date, no, amount are pre-populated.
- Customer name shows readonly.
- Saving updates the record.

- [ ] **Step 4: Test Delete**

Delete a record. Verify it disappears from the grid.

- [ ] **Step 5: Test Excel export**

Click Download. Verify the file downloads and contains correct columns.

- [ ] **Step 6: Final commit**

```bash
git add .
git commit -m "feat: complete FgInvoicePayment module (migration, models, service, controller, views)"
```

---

## Self-Review Notes

- **Spec coverage:** All 6 spec sections covered: DB schema (Task 1), Architecture (Tasks 2–5), Form UI (Task 6), Index view (Task 6), Validation (Tasks 2+4), RBAC (Task 1+5).
- **Placeholders:** None. All code blocks are complete.
- **Type consistency:** `waybill_ids` is `int[]` throughout — declared in model, passed from controller, used in service. `getWaybillNos()` in model returns `string`. `FgInvoicePaymentSearch::$customer` and `$contract` are virtual string fields consistent with filter queries.
- **RBAC bypass:** `list-waybills-by-contract` added to `AppController` bypass list in Task 5 Step 1 and referenced in Task 6 Step 2 JS.
