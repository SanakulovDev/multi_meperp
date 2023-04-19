<?php
namespace app\controllers;

use app\components\Helpers;
use app\models\Customer;
use app\models\FgInvoice;
use app\models\FgInvoiceReceipt;
use app\models\FgInvoiceWaybill;
use app\models\PaymentType;
use app\models\SalesContract;
use Yii;
use app\models\ReceptControl;
use app\models\ReceptControlSearch;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * ReceptControlController implements the CRUD actions for ReceptControl model.
 */
class ReceptControlController extends AppController
{
    /**
     * Lists all ReceptControl models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ReceptControlSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $dataProvider->sort->defaultOrder = ['date' => SORT_DESC];
        $contracts = ArrayHelper::map(SalesContract::find()->all(), 'id', 'contract_no');
        $customers = ArrayHelper::map(Customer::find()->all(), 'id', 'name');
        $payment_types = ArrayHelper::map(PaymentType::find()->all(), 'id', 'title');
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'contracts' => $contracts,
            'customers' => $customers,
            'payment_types' => $payment_types,
        ]);
    }

    /**
     * Creates a new ReceptControl model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ReceptControl();

        if (Yii::$app->getRequest()->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                $model->amount = trim(str_replace(',', '', $model->amount));
                if ($model->save()) {
                    $data['status'] = 1;
                } else {
                    $data['status'] = 0;
                    $data['errors'] = $model->getErrors();
                }
                Yii::$app->response->format = Response::FORMAT_JSON;
                return $data;
            } else {
                return $this->renderAjax('_form', array_merge(['model' => $model], self::loadDictionaries()));
            }
        } else {
            return $this->redirect(['index']);
        }
    }

    public function actionValidate($id = null) {
        $model = $id === null ? new ReceptControl() : ReceptControl::findOne($id);
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
    }

    /**
     * Updates an existing ReceptControl model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $model->amount = Helpers::numberFormatRemoveZero($model->amount, 2,'.','');
        if (Yii::$app->getRequest()->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    $data['status'] = 1;
                } else {
                    $data['status'] = 0;
                    $data['errors'] = $model->getErrors();
                }
                Yii::$app->response->format = Response::FORMAT_JSON;
                return $data;
            } else {
                return $this->renderAjax('_form', array_merge(['model' => $model], self::loadDictionaries()));
            }
        } else {
            return $this->redirect(['index']);
        }
    }

    /**
     * Deletes an existing ReceptControl model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
        $model = ReceptControl::find()->where(['id' => $id])->one();
        if ($model && $model->delete()) {
            return [
                'status' => 1
            ];
        }
        return [
            'status' => 0
        ];
    }

    /**
     * Finds the ReceptControl model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ReceptControl the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ReceptControl::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    private function loadDictionaries() {
        $customers = ArrayHelper::map(Customer::find()->all(), 'id', 'name');
        return compact('customers');
    }

    public function actionXls()
    {
        ini_set('memory_limit', '-1');
        $searchModel = new ReceptControlSearch();
        $xsl_file = $searchModel->search(Yii::$app->request->queryParams, 'excel');
        $xsl_file->send(Helpers::downloadFileName('receipt'));
    }

    public function actionListByFgInvoice($id){
      Yii::$app->response->format = Response::FORMAT_JSON;
      if(($fgInvoice = FgInvoice::find()->where(['id'=>$id])->with(['customerSalesContract','customer'])->one()) === null) {
        return ['customer'=>null,'contract'=>null, 'receipts'=>[]];
      }

      $query = "
        select 
               rc.id, rc.no, rc.date, rc.amount - ifnull(t.amountSum,0) as amount  
        from recept_control rc left join (
            select recept_control_id, sum(amount) as amountSum from fg_invoice_receipt group by recept_control_id
          )t on rc.id=t.recept_control_id and rc.amount > t.amountSum
        where rc.sales_contract_id=:scid
      ";
      $list = Yii::$app->db->createCommand($query,['scid'=>$fgInvoice->customerSalesContract->id])->queryAll();
      $data = [];
      foreach($list as $item){
        $data[] = ['id' => $item['id'], 'text' => $item['no'].' ('.$item['date'].')', 'amount'=>$item['amount']];
      }

      return ['customer'=>$fgInvoice->customer,'contract'=>$fgInvoice->customerSalesContract, 'receipts'=>$data];
    }

    public function actionListByWaybill($id){
      Yii::$app->response->format = Response::FORMAT_JSON;

      $fgInvoices = FgInvoiceWaybill::find()->where(['waybill_id'=>$id])->select('fg_invoice_id');
      if(($fgInvoice = FgInvoice::find()->where(['in','id', $fgInvoices])->with(['customerSalesContract','customer'])->one()) === null) {
        return ['customer'=>null,'contract'=>null, 'receipts'=>[]];
      }

      $query = "
          select 
                 rc.id, rc.no, rc.date, rc.amount - ifnull(t.amountSum,0) as amount  
          from recept_control rc left join (
              select recept_control_id, sum(amount) as amountSum from receipt_waybill group by recept_control_id
            )t on rc.id=t.recept_control_id and rc.amount > t.amountSum
          where rc.sales_contract_id=:scid
        ";
      $list = Yii::$app->db->createCommand($query,['scid'=>$fgInvoice->customerSalesContract->id])->queryAll();
      $data = [];
      foreach($list as $item){
        $data[] = ['id' => $item['id'], 'text' => $item['no'].' ('.$item['date'].')', 'amount'=>$item['amount']];
      }

      return ['customer'=>$fgInvoice->customer,'contract'=>$fgInvoice->customerSalesContract, 'receipts'=>$data];
    }
}
