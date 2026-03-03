<?php

namespace app\controllers;

use app\enums\ShipMode;
use app\models\Point;
use Yii;
use app\models\Route;
use app\models\RouteSearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * RouteController implements the CRUD actions for Route model.
 */
class RouteController extends AppController
{


    /**
     * Lists all Route models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RouteSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', array_merge(
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider
            ],
            self::loadDictionaries()
        ));
    }



    /**
     * Creates a new Route model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Route();

        if ($model->load(Yii::$app->request->post())) {
            $model->name = $model->fromPoint->name . ' - ' . $model->toPoint->name;
            if ($model->save()) {
                return $this->redirect('index');
            }
        }

        return $this->render('create', array_merge(
            [
                'model' => $model,
                'points' => [],
            ],
            self::loadDictionaries(['shipModes'])
        ));
    }

    /**
     * Updates an existing Route model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->name = $model->fromPoint->name . ' - ' . $model->toPoint->name;
            if ($model->save()) {
                return $this->redirect('index');
            }
        }

        return $this->render('create', array_merge(
            [
                'model' => $model,
                'points' => yii\helpers\ArrayHelper::map(PointController::getPointsByShipMode($model->ship_mode), 'id', 'name'),
            ],
            self::loadDictionaries(['shipModes'])
        ));
    }

    /**
     * Deletes an existing Route model.
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
     * Finds the Route model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Route the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Route::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    private static function loadDictionaries($dataList = [])
    {
        if (empty($dataList)) {
            $dataList = ['shipModes', 'points'];
        }

        foreach ($dataList as $data) {
            switch ($data) {
                case 'shipModes':
                    $shipModes = ShipMode::list();
                    break;
                case 'points':
                    $points = ArrayHelper::map(Point::find()->all(), 'id', 'name');
                    break;
            }
        }

        return compact($dataList);
    }
}
