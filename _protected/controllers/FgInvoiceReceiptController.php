<?php
namespace app\controllers;

use app\components\Helpers;
use app\models\FgInvoice;
use app\models\ReceptControl;
use Yii;
use app\models\FgInvoiceReceipt;
use app\models\FgInvoiceReceiptSearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * FgInvoiceReceiptController implements the CRUD actions for FgInvoiceReceipt model.
 */
class FgInvoiceReceiptController extends AppController {

  /**
   * Lists all FgInvoiceReceipt models.
   *
   * @return mixed
   */
  public function actionIndex() {
    $searchModel = new FgInvoiceReceiptSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    return $this->render('index', compact('searchModel', 'dataProvider'));
  }

  public function actionValidate($id = null) {
    $model = $id === null ? new FgInvoiceReceipt() : FgInvoiceReceipt::findOne($id);
    if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
      Yii::$app->response->format = Response::FORMAT_JSON;

      return ActiveForm::validate($model);
    }
  }

  /**
   * Creates a new FgInvoiceReceipt model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   *
   * @return mixed
   */
  public function actionCreate() {
    $model = new FgInvoiceReceipt();
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
   * Updates an existing FgInvoiceReceipt model.
   * If update is successful, the browser will be redirected to the 'view' page.
   *
   * @param integer $id
   *
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionUpdate($id) {
    $model = $this->findModel($id);
    $model->amount = Helpers::numberFormatRemoveZero($model->amount, 2, '.', '');
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
   * Deletes an existing FgInvoiceReceipt model.
   * If deletion is successful, the browser will be redirected to the 'index' page.
   *
   * @param integer $id
   *
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionDelete($id) {
    Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
    $model = FgInvoiceReceipt::find()->where(['id' => $id])->one();
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
   * Finds the FgInvoiceReceipt model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   *
   * @param integer $id
   *
   * @return FgInvoiceReceipt the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id) {
    if (($model = FgInvoiceReceipt::findOne($id)) !== null) {
      return $model;
    }
    throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
  }

  private function loadDictionaries() {
    $notPayedInvoices = FgInvoice::find()
                                 ->joinWith('fgInvoiceReceipts')
                                 ->where(['fg_invoice_receipt.id' => null])
                                 ->all();
    $fgInvoices = ArrayHelper::map($notPayedInvoices, 'id', 'invoice_no');
    return compact('fgInvoices');
  }

  public function actionXls() {
    ini_set('memory_limit', '-1');
    $searchModel = new FgInvoiceReceiptSearch();
    $xsl_file = $searchModel->search(Yii::$app->request->queryParams, 'excel');
    $xsl_file->send(Helpers::downloadFileName('fg-invoice-receipt'));
  }

}
