<?php

namespace app\controllers;

use app\components\Helpers;
use app\models\Customer;
use app\models\CustomerSearch;
use Yii;
use yii\db\Exception;
use yii\web\NotFoundHttpException;

/**
 * CustomerController implements the CRUD actions for Customer model.
 */
class CustomerController extends AppController
{

  /**
   * Lists all Customer models.
   * @return mixed
   */
  public function actionIndex()
  {
    $searchModel = new CustomerSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
    ]);
  }

  /**
   * Displays a single Customer model.
   * @param integer $id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionView($id)
  {
    return $this->render('view', [
      'model' => $this->findModel($id),
    ]);
  }

  /**
   * Creates a new Customer model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   * @return mixed
   */
  public function actionCreate()
  {
    $model = new Customer();
    if ($model->load(Yii::$app->request->post()) && $model->save()) {
      return $this->redirect(['view', 'id' => $model->id]);
    }
    return $this->render('create', [
      'model' => $model,
    ]);
  }

  /**
   * Updates an existing Customer model.
   * If update is successful, the browser will be redirected to the 'view' page.
   * @param integer $id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionUpdate($id)
  {
    $model = $this->findModel($id);
    if ($model->load(Yii::$app->request->post()) && $model->save()) {
      return $this->redirect(['view', 'id' => $model->id]);
    }
    return $this->render('update', [
      'model' => $model,
    ]);
  }

  /**
   * Deletes an existing Customer model.
   * If deletion is successful, the browser will be redirected to the 'index' page.
   * @param integer $id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionDelete($id)
  {
    $model = $this->findModel($id);
    try {
      $model->delete();
      Yii::$app->session->setFlash('success', Yii::t('app', 'Deleted successfully'));
    } catch (Exception $e) {
      if ($e->errorInfo[1] == 1451) {
        Yii::$app->session->setFlash('error', Yii::t('app', 'error_delete_fr_key'));
      } else {
        throw $e;
      }
    }
    return $this->redirect(['index']);
  }

  /**
   * Finds the Customer model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param integer $id
   * @return Customer the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id)
  {
    if (($model = Customer::findOne($id)) !== null) {
      return $model;
    }
    throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
  }

  public function actionXls()
  {
    ini_set('memory_limit', '-1');
    $searchModel = new CustomerSearch();
    $xsl_file = $searchModel->search(Yii::$app->request->queryParams, 'excel');
    $xsl_file->send(Helpers::downloadFileName('customers'));
  }
}
