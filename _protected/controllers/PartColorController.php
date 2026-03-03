<?php

namespace app\controllers;

use Yii;
use app\models\PartColor;
use app\models\PartColorSearch;
use yii\base\BaseObject;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * PartColorController implements the CRUD actions for PartColor model.
 */
class PartColorController extends AppController
{
  /**
   * Lists all PartColor models.
   * @return mixed
   */
  public function actionIndex()
  {
    $searchModel = new PartColorSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
    ]);
  }

  public function actionValidate($id = null)
  {
    $model = $id === null ? new PartColor() : PartColor::findOne($id);
    if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      return ActiveForm::validate($model);
    }
  }

  /**
   * Creates a new PartColor model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   * @return mixed
   */
  public function actionCreate()
  {
    $model = new PartColor();
    if (Yii::$app->getRequest()->isAjax) {
      if ($model->load(Yii::$app->request->post())) {
        if ($model->save()) {
          $data['status'] = 1;
          $this->clearCache();
        } else {
          $data['status'] = 0;
          $data['errors'] = $model->getErrors();
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $data;
      } else {
        return $this->renderAjax('_form', compact('model'));
      }
    } else {
      return $this->redirect(['index']);
    }
  }

  /**
   * Updates an existing PartColor model.
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
        if ($model->save()) {
          $data['status'] = 1;
          $this->clearCache();
        } else {
          $data['status'] = 0;
          $data['errors'] = $model->getErrors();
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $data;
      } else {
        return $this->renderAjax('_form', compact('model'));
      }
    } else {
      return $this->redirect(['index']);
    }
  }

  /**
   * Deletes an existing PartColor model.
   * If deletion is successful, the browser will be redirected to the 'index' page.
   * @param integer $id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionDelete($id)
  {
    Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
    $model = PartColor::find()
      ->where(['id' => $id])
      ->one();
    if ($model && $model->delete()) {
      $this->clearCache();
      return [
        'status' => 1,
      ];
    }
    return [
      'status' => 0,
    ];
  }

  /**
   * Finds the PartColor model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param integer $id
   * @return PartColor the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id)
  {
    if (($model = PartColor::findOne($id)) !== null) {
      return $model;
    }

    throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
  }

  private function clearCache()
  {
      Yii::$app->cache->delete('partColorsAll');
  }
}
