<?php
namespace app\controllers;

use app\components\Helpers;
use app\models\Customer;
use app\models\FgInvoicePayment;
use app\models\FgInvoicePaymentSearch;
use app\models\SalesContract;
use app\models\Waybill;
use app\services\FgInvoicePaymentService;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * FgInvoicePaymentController implements CRUD actions for FgInvoicePayment model.
 */
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

        $customers = ArrayHelper::map(Customer::find()->orderBy('name')->all(), 'id', 'name');
        $contracts = ArrayHelper::map(SalesContract::find()->orderBy('contract_no')->all(), 'id', 'contract_no');
        $waybills  = ArrayHelper::map(Waybill::find()->orderBy('waybill_no')->all(), 'id', 'waybill_no');

        return $this->render('index', compact('searchModel', 'dataProvider', 'customers', 'contracts', 'waybills'));
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
            Yii::$app->response->format = Response::FORMAT_JSON;

            if ($this->getService()->save($model)) {
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

        if (!Yii::$app->request->isAjax) {
            return $this->redirect(['index']);
        }

        if ($model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            if ($this->getService()->save($model)) {
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
     * AJAX endpoint: returns waybills + customer name for a given sales_contract_id.
     * Listed in AppController bypass list — no RBAC check.
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
        $contracts = ArrayHelper::map(
            SalesContract::find()->with('customer')->orderBy('contract_no')->all(),
            'id',
            'contractInfo'
        );
        return compact('model', 'contracts');
    }
}
