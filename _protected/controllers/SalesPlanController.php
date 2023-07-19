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
use app\models\SalesPlanShort;
use app\models\Model;
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
    $status = 1;
    if(isset(Yii::$app->request->queryParams['SalesPlanSearch']['status'])){
      $status = Yii::$app->request->queryParams['SalesPlanSearch']['status'];
    }
    [$partColorsAll, $partMarksAll, $customersAll] = self::allDictionaries();
    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
      'partColorsAll' => $partColorsAll,
      'partMarksAll' => $partMarksAll,
      'customersAll' => $customersAll,
      'status'        => $status,
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
    $modelMain = new SalesPlanShort();
    $models = [new SalesPlan()];
    if (Yii::$app->getRequest()->isAjax) {
      if ($modelMain->load(Yii::$app->request->post())) {
        // vd($modelMain);
          $models = Model::createMultiple(SalesPlan::classname());
          Model::loadMultiple($models, Yii::$app->request->post());
          $valid = $modelMain->validate();
          $valid = Model::validateMultiple($models) ;
          $valid = true;
          if($valid){
            $transaction = \Yii::$app->db->beginTransaction();
  
            try{
              $flag = true;
                  foreach ($models as $index => $model) {
                    // vd($model);
                      $model->target_date .=  '-01';
                      $model->customer_id = $modelMain->customer_id;
                      $model->clearErrors();
                      // vd($model->save(false));
                      if (! ($flag = $model->save(false))) {
                        // vd(123);
                        $data['status'] = 0;
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        $data['errors'] = $model->getErrors();

                      }
                  }
              if ($flag) {
                  $transaction->commit();
                  Yii::$app->response->format = Response::FORMAT_JSON;
                  $data['status'] = 1;

                  return $data;
              }
            } catch (Exception $e) {
              $transaction->rollBack();
              $data['status'] = 0;
              Yii::$app->response->format = Response::FORMAT_JSON;
              return $data;
            }
          }


      } else {
        [$partColorsAll, $partMarksAll, $customersAll] = self::allDictionaries();
        $parts = ArrayHelper::map(Part::find()->all(), 'id', 'part_name');
        return $this->renderAjax('_form2', [
          'modelMain' => $modelMain,
          'models' => (empty($models)) ? [new SalesPlan()] : $models,
          'partColorsAll' => $partColorsAll,
          'partMarksAll' => $partMarksAll,
          'customersAll' => $customersAll,
          'parts2'   => $parts
        ]);
      }
    } else {
      return $this->redirect(['index']);
    }
  }
  public function actionCreate2()
  {
    $model = new SalesPlan();

    if (Yii::$app->getRequest()->isAjax) {
      if ($model->load(Yii::$app->request->post())) {
        $model->target_date = $model->target_date . ':01';
        if ($model->save(false)) {
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
  public function actionCreateDay()
  {
    $modelMain = new SalesPlanShort();
    $models = [new SalesPlan()];
    if (Yii::$app->getRequest()->isAjax) {
      if ($modelMain->load(Yii::$app->request->post())) {
        // vd($modelMain);
          $models = Model::createMultiple(SalesPlan::classname());
          Model::loadMultiple($models, Yii::$app->request->post());
          $valid = $modelMain->validate();
          $valid = Model::validateMultiple($models) ;
          $valid = true;
          if($valid){
            $transaction = \Yii::$app->db->beginTransaction();
  
            try{
              $flag = true;
                  foreach ($models as $index => $model) {
                    // vd($model);
                      $model->customer_id = $modelMain->customer_id;
                      $model->status = 2;
                      $model->clearErrors();
                      // vd($model->save(false));
                      if (! ($flag = $model->save(false))) {
                        // vd(123);
                        $data['status'] = 0;
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        $data['errors'] = $model->getErrors();

                      }
                  }
              if ($flag) {
                  $transaction->commit();
                  Yii::$app->response->format = Response::FORMAT_JSON;
                  $data['status'] = 1;

                  return $data;
              }
            } catch (Exception $e) {
              $transaction->rollBack();
              $data['status'] = 0;
              Yii::$app->response->format = Response::FORMAT_JSON;
              return $data;
            }
          }


      } else {
        [$partColorsAll, $partMarksAll, $customersAll] = self::allDictionaries();
        return $this->renderAjax('_form_day', [
          'modelMain' => $modelMain,
          'models' => (empty($models)) ? [new SalesPlan()] : $models,
          'partColorsAll' => $partColorsAll,
          'partMarksAll' => $partMarksAll,
          'customersAll' => $customersAll,
        ]);
      }
    } else {
      return $this->redirect(['index']);
    }
  }
  public function actionCreateDay2()
  {
    $model = new SalesPlan();

    if (Yii::$app->getRequest()->isAjax) {
      if ($model->load(Yii::$app->request->post())) {
        $model->status =2;
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
        return $this->renderAjax('_form_day', compact('model', 'partColorsAll', 'partMarksAll', 'customersAll'));
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
        $model->target_date = $model->target_date . '-01';
        if ($model->save(false)) {
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
