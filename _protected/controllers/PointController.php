<?php

namespace app\controllers;

use app\enums\ShipMode;
use Yii;
use app\models\Point;
use app\models\PointSearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * PointController implements the CRUD actions for Point model.
 */
class PointController extends AppController
{


    public function actionIndex()
    {
        $searchModel = new PointSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', array_merge(
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider
            ],
            self::loadDictionaries()
        ));
    }


    public function actionCreate()
    {
        $model = new Point();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect('index');
        }

        return $this->render('create', array_merge(
            [
                'model' => $model,
            ],
            self::loadDictionaries()
        ));
    }

    /**
     * Updates an existing Point model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect('index');
        }

        return $this->render('update', array_merge(
            [
                'model' => $model,
            ],
            self::loadDictionaries()
        ));
    }

    /**
     * Deletes an existing Point model.
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



    public function actionGetPointsByShipMode($shipMode, $isJSON = true)
    {
        if ($isJSON) Yii::$app->response->format = Response::FORMAT_JSON;
        return self::getPointsByShipMode($shipMode);
    }

    public static function getPointsByShipMode($shipMode)
    {
        $data = [];
        $points = Point::find()->where(['ship_mode' => $shipMode])->all();
        foreach ($points as $point) {
            $data[] = [
                'id' => $point->id,
                'name' => $point->name
            ];
        }
        return $data;
        //return ArrayHelper::map(Point::find()->where(['ship_mode' => $shipMode])->all(), 'id', 'name');
    }

    /**
     * Finds the Point model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Point the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Point::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    private static function loadDictionaries()
    {
        $shipModes = ShipMode::list();
        return compact('shipModes');
    }
}
