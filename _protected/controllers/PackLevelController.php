<?php

namespace app\controllers;

use app\models\Pack;
use app\models\Part;
use Yii;
use app\models\PackLevel;
use app\models\PackLevelSearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\widgets\ActiveForm;
use yii\web\Response;

/**
 * PackLevelController implements the CRUD actions for PackLevel model.
 */
class PackLevelController extends AppController
{
    /**
     * Lists all PackLevel models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PackLevelSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', array_merge([
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ], self::loadDictionaries()));
    }

    /**
     * Creates a new PackLevel model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PackLevel();

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
                return $this->renderAjax('_form', ['model' => $model]);
            }
        }else{
            return $this->redirect(['index']);
        }
    }

    /**
     * Updates an existing PackLevel model.
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
            }else{
                return $this->renderAjax('_form', array_merge([
                    'model' => $model,
                ], self::loadDictionaries()));
            }
        }else{
            return $this->redirect(['index']);
        }
    }

    /**
     * Deletes an existing PackLevel model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id){
        Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
        $model = PackLevel::find()->where(['id' => $id])->one();
        if($model && $model->delete()){
            return [
                "status" => 1
            ];
        }
        return [
            "status" => 0
        ];
    }

    private function loadDictionaries(){
        $packs = ArrayHelper::map(Pack::find()->all(), 'id', 'code');
        $parts = ArrayHelper::map(Part::find()->where(['status'=>Part::STATUS_ACTIVE])->all(), 'id', 'partinfo');
        return compact('packs','parts');
    }
    /**
     * Finds the PackLevel model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PackLevel the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PackLevel::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionValidate($id = null){
        $model = $id === null ? new PackLevel() : PackLevel::findOne($id);
        if(Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
    }
}
