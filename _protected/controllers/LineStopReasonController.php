<?php

namespace app\controllers;

use Yii;
use app\models\LineStopReason;
use app\models\LineStopReasonSearch;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * LineStopReasonController implements the CRUD actions for LineStopReason model.
 */
class LineStopReasonController extends AppController
{
    /**
     * Lists all LineStopReason models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LineStopReasonSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new LineStopReason model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new LineStopReason();

        if (Yii::$app->getRequest()->isAjax){
          if($model->load(Yii::$app->request->post())){
            if($model->save()){
              $data['status'] = 1;
            }else{
              $data['status'] = 0;
              $data['errors'] = $model->getErrors();
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $data;
          }else{
            return $this->renderAjax('_form', compact('model'));
          }
        }else{
          return $this->redirect(['index']);
        }
    }

    /**
     * Updates an existing LineStopReason model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
      $model = $this->findModel($id);
      if(Yii::$app->getRequest()->isAjax){
        if($model->load(Yii::$app->request->post())){
          if($model->save()){
            $data['status'] = 1;
          }else{
            $data['status'] = 0;
            $data['errors'] = $model->getErrors();
          }
          Yii::$app->response->format = Response::FORMAT_JSON;
          return $data;
        } else {
          return $this->renderAjax('_form', compact('model'));
        }
      }else{
        return $this->redirect(['index']);
      }
    }

    /**
     * Deletes an existing LineStopReason model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
      Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
      $model = LineStopReason::find()->where(['id' => $id])->one();
      if($model && $model->delete()){
        return [
          "status" => 1
        ];
      }
      return [
        "status" => 0
      ];
    }

    /**
     * Finds the LineStopReason model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return LineStopReason the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = LineStopReason::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionValidate($id = null){
      $model = $id === null ? new LineStopReason() : LineStopReason::findOne($id);
      if(Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())){
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ActiveForm::validate($model);
      }
    }
}
