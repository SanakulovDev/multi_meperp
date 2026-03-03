<?php
namespace app\controllers;

use app\components\Helpers;
use app\models\Lms;
use app\models\LmsSearch;
use app\models\Part;
use app\models\Supplier;
use app\models\Warehouse;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

class LmsController extends AppController {

  /**
   * Lists all Lms models.
   *
   * @return mixed
   */
  public function actionIndex() {
    $searchModel = new LmsSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    $suppliers = ArrayHelper::map(Supplier::find()->all(), 'id', 'name');

    return $this->render('index', array_merge([
                                                'searchModel' => $searchModel,
                                                'dataProvider' => $dataProvider,
                                                'suppliers' => $suppliers ?? []
                                              ], self::loadDictionaries()));
  }

  /**
   * Creates a new Lms model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   *
   * @return mixed
   */
  public function actionCreate() {
    $model = new Lms();
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
        return $this->renderAjax('_form', array_merge(['model' => $model, 'suppliers' => []], self::loadDictionaries()));
      }
    } else {
      return $this->redirect(['index']);
    }
  }

  /**
   * Updates an existing Lms model.
   * If update is successful, the browser will be redirected to the 'view' page.
   *
   * @param integer $id
   *
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionUpdate($id) {
    $model = $this->findModel($id);
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
        return $this->renderAjax('_form', array_merge(['model' => $model, 'suppliers' => []], self::loadDictionaries()));
      }
    } else {
      return $this->redirect(['index']);
    }
  }

  /**
   * Deletes an existing Lms model.
   * If deletion is successful, the browser will be redirected to the 'index' page.
   *
   * @param integer $id
   *
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionDelete($id) {
    Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
    $model = Lms::find()->where(['id' => $id])->one();
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
   * Finds the Lms model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   *
   * @param integer $id
   *
   * @return Lms the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id) {
    if (($model = Lms::findOne($id)) !== null) {
      return $model;
    }
    throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
  }

  private function loadDictionaries() {
    $parts = ArrayHelper::map(Part::find()->where(['status' => Part::STATUS_ACTIVE])->all(), 'id', 'partinfo');
    $warehouses = ArrayHelper::map(Warehouse::find()->where(['status' => Warehouse::STATUS_ACTIVE, 'warehouse_type' => Warehouse::TYPE_PHYSICAL])->all(), 'id', 'name');

    return compact('parts', 'warehouses');
  }

  public function actionXls() {
    ini_set('memory_limit', '-1');
    $searchModel = new LmsSearch();
    $xsl_file = $searchModel->search(Yii::$app->request->queryParams, 'excel');
    $xsl_file->send(Helpers::downloadFileName('lms'));
  }

  public function actionValidate($id = null) {
    $model = $id === null ? new Lms() : Lms::findOne($id);
    if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
      Yii::$app->response->format = Response::FORMAT_JSON;

      return ActiveForm::validate($model);
    }
  }
}
