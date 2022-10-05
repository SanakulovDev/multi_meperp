<?php

namespace app\controllers;

use Yii;
use app\models\Location;
use app\models\LocationSearch;
use app\models\LocationType;
use yii\bootstrap\ActiveForm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Response;

/**
 * LocationController implements the CRUD actions for Location model.
 */
class LocationController extends AppController
{
    /**
     * Lists all Location models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LocationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $locationtypes = ArrayHelper::map(LocationType::find()->all(), 'id', 'name');
       
        return $this->render('index', compact('searchModel', 'dataProvider', 'locationtypes'));
    }

    /**
     * Displays a single Location model.
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
     * Creates a new Location model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Location();

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
            }else{
                return $this->renderAjax('_form', ['model' => $model,'create_visible' => 1]);
            }
        }else{
            return $this->redirect(['index']);
        }
    }

    /**
     * Creates a new Location model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionPlant()
    {
        $model = new Location();

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
            }else{
                return $this->renderAjax('plant', ['model' => $model]);
            }
        }else{
            return $this->redirect(['index']);
        }
    }
    
    public function actionShop()
    {
        $model = new Location();

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
            }else{
                return $this->renderAjax('shop', ['model' => $model]);
            }
        }else{
            return $this->redirect(['index']);
        }
    }
    
    public function actionLine()
    {
        $model = new Location();

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
            }else{
                return $this->renderAjax('line', ['model' => $model]);
            }
        }else{
            return $this->redirect(['index']);
        }
    }
    
    public function actionSector()
    {
        $model = new Location();

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
            }else{
                return $this->renderAjax('sector', ['model' => $model]);
            }
        }else{
            return $this->redirect(['index']);
        }
    }

    /**
     * Updates an existing Location model.
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
     * Deletes an existing Location model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Location model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Location the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Location::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionValidate($id = null){
        $model = $id === null ? new Location() : Location::findOne($id);
        if(Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
    }
}
