<?php
namespace app\controllers;

use app\components\Helpers;
use app\enums\ContainerType;
use app\enums\FreightInvoiceType;
use app\models\Carrier;
use app\models\Container;
use app\models\Currency;
use app\models\DeliveryTerm;
use app\models\FreightInvoice;
use app\models\FreightInvoiceSearch;
use app\models\Route;
use app\models\Unit;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * FreightInvoiceController implements the CRUD actions for FreightInvoice model.
 */
class FreightInvoiceController extends Controller {

  private function loadDictionaries() {
    $invoiceType = FreightInvoiceType::list();
    $routes = ArrayHelper::map(Route::find()->all(), 'id', 'name');
    $deliveryTerms = ArrayHelper::map(DeliveryTerm::find()->all(), 'id', 'name');
    $carriers = ArrayHelper::map(Carrier::find()->all(), 'id', 'company_name');
    $units = ArrayHelper::map(Unit::find()->all(), 'id', 'unit_value');
    $currencies = ArrayHelper::map(Currency::find()->all(), 'id', 'code');
    $containers = ArrayHelper::map(Container::find()->select(['id, TRIM(TRAILING " - " FROM concat(container_no," - ",container_type)) AS container_no'])->all(), 'id', 'container_no');
    return compact(
      'invoiceType',
      'routes',
      'deliveryTerms',
      'carriers',
      'units',
      'currencies',
      'containers'
    );
  }

  /**
   * Lists all FreightInvoice models.
   *
   * @return mixed
   */
  public function actionIndex() {

    $searchModel = new FreightInvoiceSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    return $this->render('index',
      array_merge(
        [
          'searchModel' => $searchModel ?? null,
          'dataProvider' => $dataProvider
        ], self::loadDictionaries()
      )
    );
  }

  /**
   * Displays a single FreightInvoice model.
   *
   * @param integer $id
   *
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionView($id) {
    return $this->render('view',
      array_merge(
        [
          'model' => $this->findModel($id)
        ], self::loadDictionaries()
      )
    );
  }

  /**
   * Creates a new FreightInvoice model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   *
   * @return mixed
   */
  public function actionCreate() {
    $model = new FreightInvoice();
    if($model->load(Yii::$app->request->post())) {
//      echo "<pre>"; var_dump(Yii::$app->request->post());echo "</pre>";
//      echo "<pre>"; print_r($model);echo "</pre>";
      if($model->save()) {
        return $this->redirect(['view', 'id' => $model->id]);
      } else {
        $errMsg = "<i><u><strong>FreightInvoice:</strong></u></i>".Helpers::arrayToStringRecursive($model->errors);
      }
    }
    return $this->render('create',
      array_merge(
        [
          'model' => $model,
          'errMsg' => $errMsg ?? null,
        ], self::loadDictionaries()
      )
    );
  }

  /**
   * Updates an existing FreightInvoice model.
   * If update is successful, the browser will be redirected to the 'view' page.
   *
   * @param integer $id
   *
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionUpdate($id) {
    $model = $this->findModel($id);
    if($model->load(Yii::$app->request->post())) {
      if($model->save()) {
        return $this->redirect(['view', 'id' => $model->id]);
      } else {
        $errMsg = "<i><u><strong>FreightInvoice:</strong></u></i>".Helpers::arrayToStringRecursive($model->errors);
      }
    }
    return $this->render('update',
      array_merge(
        [
          'model' => $model,
          'errMsg' => $errMsg ?? null,
        ], self::loadDictionaries()
      )
    );
  }

  /**
   * Deletes an existing FreightInvoice model.
   * If deletion is successful, the browser will be redirected to the 'index' page.
   *
   * @param integer $id
   *
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionDelete($id) {
    $this->findModel($id)->delete();
    return $this->redirect(['index']);
  }

  /**
   * Finds the FreightInvoice model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   *
   * @param integer $id
   *
   * @return FreightInvoice the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id) {
    if(($model = FreightInvoice::findOne($id)) !== null) {
      return $model;
    }
    throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
  }

}
