<?php
namespace app\controllers;

use app\enums\FreightInvoiceType;
use app\models\Carrier;
use app\models\DeliveryTerm;
use app\models\FreightInvoice;
use app\models\FreightInvoiceSearch;
use app\models\Route;
use Yii;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * FreightInvoiceController implements the CRUD actions for FreightInvoice model.
 */
class KARTIK extends Controller {

  private function loadDictionaries() {
    $invoiceType = FreightInvoiceType::list();
    $routes = ArrayHelper::map(Route::find()->all(), 'id', 'name');
    $deliveryTerms = ArrayHelper::map(DeliveryTerm::find()->all(), 'id', 'name');
    $carriers = ArrayHelper::map(Carrier::find()->all(), 'id', 'company_name');
    return compact(
      'invoiceType',
      'routes',
      'deliveryTerms',
      'carriers'
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
          'searchModel' => $searchModel,
          'dataProvider' => $dataProvider,
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
   */
  public function actionView($id) {
    $model = $this->findModel($id);
    $providerFreightInvoiceDetail = new ArrayDataProvider([
      'allModels' => $model->freightInvoiceDetails,
    ]);
    return $this->render('view',
      array_merge(
        [
          'model' => $this->findModel($id),
          'providerFreightInvoiceDetail' => $providerFreightInvoiceDetail,
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
    if($model->loadAll(Yii::$app->request->post()) && $model->saveAll()) {
      return $this->redirect(['view', 'id' => $model->id]);
    } else {
      return $this->render('create',
        array_merge(
          [
            'model' => $model,
          ], self::loadDictionaries()
        )
      );
    }
  }

  /**
   * Updates an existing FreightInvoice model.
   * If update is successful, the browser will be redirected to the 'view' page.
   *
   * @param integer $id
   *
   * @return mixed
   */
  public function actionUpdate($id) {
    $model = $this->findModel($id);
    if($model->loadAll(Yii::$app->request->post()) && $model->saveAll()) {
      return $this->redirect(['view', 'id' => $model->id]);
    } else {
      return $this->render('update',
        array_merge(
          [
            'model' => $model,
          ], self::loadDictionaries()
        )
      );
    }
  }

  /**
   * Deletes an existing FreightInvoice model.
   * If deletion is successful, the browser will be redirected to the 'index' page.
   *
   * @param integer $id
   *
   * @return mixed
   */
  public function actionDelete($id) {
    $this->findModel($id)->deleteWithRelated();
    return $this->redirect(['index']);
  }

  /**
   * Creates a new FreightInvoice model by another data,
   * so user don't need to input all field from scratch.
   * If creation is successful, the browser will be redirected to the 'view' page.
   *
   * @param type $id
   *
   * @return type
   */
  public function actionSaveAsNew($id) {
    $model = $this->findModel($id);
    if($model->load(Yii::$app->request->post()) && $model->save()) {
      return $this->redirect(['view', 'id' => $model->id]);
    } else {
      return $this->render('saveAsNew',
        array_merge(
          [
            'model' => $model,
          ], self::loadDictionaries()
        )
      );
    }
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
    } else {
      throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
  }

  /**
   * Action to load a tabular form grid
   * for FreightInvoiceDetail
   *
   * @return mixed
   * @author Jiwantoro Ndaru <jiwanndaru@gmail.com>
   * @author Yohanes Candrajaya <moo.tensai@gmail.com>
   */
  public function actionAddFreightInvoiceDetail() {
    if(Yii::$app->request->isAjax) {
      $row = Yii::$app->request->post('FreightInvoiceDetail');
      if((Yii::$app->request->post('isNewRecord') && Yii::$app->request->post('_action') == 'load' && empty($row)) || Yii::$app->request->post('_action') == 'add')
        $row[] = [];
      return $this->renderAjax('_formFreightInvoiceDetail', ['row' => $row]);
    } else {
      throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
  }

}
