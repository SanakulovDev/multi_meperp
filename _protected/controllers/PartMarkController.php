<?php

namespace app\controllers;

use Yii;
use app\models\PartMark;
use app\models\PartMarkSearch;
use yii\base\BaseObject;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * PartMarkController implements the CRUD actions for PartMark model.
 */
class PartMarkController extends AppController
{
  /**
   * Lists all PartMark models.
   * @return mixed
   */
  public function actionIndex()
  {
    $searchModel = new PartMarkSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
    ]);
  }

  public function actionValidate($id = null)
  {
    $model = $id === null ? new PartMark() : PartMark::findOne($id);
    if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      return ActiveForm::validate($model);
    }
  }

  /**
   * Displays a single PartMark model.
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
   * Creates a new PartMark model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   * @return mixed
   */
  public function actionCreate()
  {
    $model = new PartMark();

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
   * Updates an existing PartMark model.
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
          $this->clearCache();
          $data['status'] = 1;
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
   * Deletes an existing PartMark model.
   * If deletion is successful, the browser will be redirected to the 'index' page.
   * @param integer $id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionDelete($id)
  {
    Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
    $model = PartMark::find()
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

  private function clearCache()
  {
      Yii::$app->cache->delete('partMarksAll');
  }

  /**
   * Finds the PartMark model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param integer $id
   * @return PartMark the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id)
  {
    if (($model = PartMark::findOne($id)) !== null) {
      return $model;
    }

    throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
  }
}
