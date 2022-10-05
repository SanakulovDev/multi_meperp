<?php

namespace app\controllers;

use app\models\Customer;
use app\models\Part;
use app\models\PartColor;
use app\models\PartMark;
use app\models\PartType;
use app\models\Unit;
use Yii;
use app\models\SalesPlan;
use app\models\SalesPlanSearch;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * SalesPlanController implements the CRUD actions for SalesPlan model.
 */
class SalesPlanController extends AppController
{
  public $cacheTtl = 3600; // hour
  /**
   * Lists all SalesPlan models.
   * @return mixed
   */
  public function actionIndex()
  {
    $searchModel = new SalesPlanSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    [$partColorsAll, $partMarksAll, $customersAll] = self::allDictionaries();
    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
      'partColorsAll' => $partColorsAll,
      'partMarksAll' => $partMarksAll,
      'customersAll' => $customersAll,
    ]);
  }

  public function allDictionaries()
  {
    $partColorsAll = Yii::$app->cache->getOrSet(
      'partColorsAll',
      function () {
        return PartColor::find()->all();
      },
      $this->cacheTtl
    );

    $partMarksAll = Yii::$app->cache->getOrSet(
      'partMarksAll',
      function () {
        return PartMark::find()->all();
      },
      $this->cacheTtl
    );

    $customersAll = Yii::$app->cache->getOrSet(
      'customersAll',
      function () {
        return Customer::find()->all();
      },
      $this->cacheTtl
    );

    return [
      ArrayHelper::map($partColorsAll, 'name', 'name'),
      ArrayHelper::map($partMarksAll, 'name', 'name'),
      ArrayHelper::map($customersAll, 'id', 'name'),
    ];
  }

  public function actionValidate($id = null)
  {
    $model = $id === null ? new SalesPlan() : SalesPlan::findOne($id);
    if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      return ActiveForm::validate($model);
    }
  }

  /**
   * Creates a new SalesPlan model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   * @return mixed
   */
  public function actionCreate()
  {
    $model = new SalesPlan();

    if (Yii::$app->getRequest()->isAjax) {
      if ($model->load(Yii::$app->request->post())) {
        $model->target_date = $model->target_date . ':01';
        if ($model->save()) {
          $data['status'] = 1;
        } else {
          $data['status'] = 0;
          $data['errors'] = $model->getErrors();
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $data;
      } else {
        [$partColorsAll, $partMarksAll, $customersAll] = self::allDictionaries();
        return $this->renderAjax('_form', compact('model', 'partColorsAll', 'partMarksAll', 'customersAll'));
      }
    } else {
      return $this->redirect(['index']);
    }
  }

  /**
   * Updates an existing SalesPlan model.
   * If update is successful, the browser will be redirected to the 'view' page.
   * @param integer $id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionUpdate($id)
  {
    $model = $this->findModel($id);

    if (Yii::$app->getRequest()->isAjax) {
      if ($model->load(Yii::$app->request->post())) {
        $model->target_date = $model->target_date . ':01';
        if ($model->save()) {
          $data['status'] = 1;
        } else {
          $data['status'] = 0;
          $data['errors'] = $model->getErrors();
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $data;
      } else {
        [$partColorsAll, $partMarksAll, $customersAll] = self::allDictionaries();
        return $this->renderAjax('_form', compact('model', 'partColorsAll', 'partMarksAll', 'customersAll'));
      }
    } else {
      return $this->redirect(['index']);
    }
  }

  /**
   * Deletes an existing SalesPlan model.
   * If deletion is successful, the browser will be redirected to the 'index' page.
   * @param integer $id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionDelete($id)
  {
    Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
    $model = SalesPlan::find()
      ->where(['id' => $id])
      ->one();
    if ($model && $model->delete()) {
      return [
        'status' => 1,
      ];
    }
    return [
      'status' => 0,
    ];
  }

  /**
   * Finds the SalesPlan model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param integer $id
   * @return SalesPlan the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id)
  {
    if (($model = SalesPlan::find()->where(['id'=>$id])->with('part')->one()) !== null) {
      return $model;
    }

    throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
  }
}
