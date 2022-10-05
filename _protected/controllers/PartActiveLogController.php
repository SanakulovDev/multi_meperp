<?php
namespace app\controllers;

use app\models\PartActiveLog;
use app\models\PartActiveLogSearch;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * PartActiveLogController implements the CRUD actions for PartActiveLog model.
 */
class PartActiveLogController extends AppController {

  /**
   * Lists all PartActiveLog models.
   *
   * @return mixed
   */
  public function actionIndex() {
    $searchModel = new PartActiveLogSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
    ]);
  }

  /**
   * Finds the PartActiveLog model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   *
   * @param integer $id
   *
   * @return PartActiveLog the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id) {
    if(($model = PartActiveLog::findOne($id)) !== null) {
      return $model;
    }
    throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
  }

}
