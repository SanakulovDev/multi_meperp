<?php

namespace app\controllers;

use Yii;
use app\models\CurrencyRate;
use app\models\CurrencyRateSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CurrencyRateController implements the CRUD actions for CurrencyRate model.
 */
class CurrencyRateController extends AppController
{
   

    public function actionIndex()
    {
        $searchModel = new CurrencyRateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->sort->defaultOrder = ['updated_at' => SORT_DESC];

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    

    /**
     * Creates a new CurrencyRate model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
      
        $model = new CurrencyRate();
        $model->rate_date = date('Y-m-d');
        
        if ($model->load(Yii::$app->request->post())) {
            $model->created_by = Yii::$app->user->identity->id;
            $model->created_at = time();
            $model->updated_by = Yii::$app->user->identity->id;
            $model->updated_at = time();
            if ($model->save()) {
                return $this->redirect('index');
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing CurrencyRate model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->uzs_value = \app\components\Helpers::formatRemoveDecimal($model->uzs_value);

        if ($model->load(Yii::$app->request->post())) {
            $model->updated_by = Yii::$app->user->identity->id;
            $model->updated_at = time();
            if ($model->save()) {
                return $this->redirect('index');
            }else{
              echo "<pre>";
              print_r($model->errors);
              echo "</pre>";
              die;
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing CurrencyRate model.
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
     * Finds the CurrencyRate model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CurrencyRate the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CurrencyRate::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
