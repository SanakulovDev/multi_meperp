<?php
namespace app\controllers;

use app\components\Helpers;
use app\models\Part;
use app\models\ProductModel;
use app\models\Shipment;
use app\models\ShipmentDetail;
use app\models\ShipmentDetailSearch;
use app\models\Supplier;
use app\models\Unit;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * ShipmentDetailController implements the CRUD actions for ShipmentDetail model.
 */
class ShipmentDetailController extends AppController {

  public function actionIndex()
  {
    $searchModel = new ShipmentDetailSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    $parts = ArrayHelper::map(Part::find()->where([
      'status' => Part::STATUS_ACTIVE,
      'state' => Part::STATE_RAW,
      'contract_source_id' => Yii::$app->params['import_contract_source_ids']
    ])->all(), 'id','partinfo');
    $suppliers = ArrayHelper::map(Supplier::find()->all(), 'id','name');
    $models = ArrayHelper::map(ProductModel::find()->all(), 'id','modelname');
    $units = ArrayHelper::map(Unit::find()->all(), 'id','unit_value');

    // dates
    $queryParams = Yii::$app->request->queryParams;

    $query = 'SELECT disruption_date FROM shipment_detail WHERE shipment_id= :shipmend_id GROUP BY disruption_date';
    $resultDates =  Yii::$app->db->createCommand($query, [':shipmend_id' => $queryParams['ShipmentDetailSearch']['shipment_id']])->queryAll();

    foreach ($resultDates as $rdate) {
      $dates[$rdate['disruption_date']] = date('d M', strtotime($rdate['disruption_date']));
    }
    // ****

    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
      'shipment' => (isset($_REQUEST['ShipmentDetailSearch']['shipment_id'])) ? Shipment::findOne($_REQUEST['ShipmentDetailSearch']['shipment_id']) : '',
      'parts' => $parts,
      'suppliers' => $suppliers,
      'models' => $models,
      'units' => $units,
      'dates' => $dates,
    ]);
  }

  public function actionValidate($id = null) {
    $model = $id === null ? new ShipmentDetail() : ShipmentDetail::findOne($id);
    if(Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
      Yii::$app->response->format = Response::FORMAT_JSON;

      return ActiveForm::validate($model);
    }
  }

  public function actionUpdate($id) {
    $model = $this->findModel($id);
    if(Yii::$app->getRequest()->isAjax) {
      if($model->load(Yii::$app->request->post())) {
        if($model->save()) {
          $data['status'] = 1;
        } else {
          $data['status'] = 0;
          $data['errors'] = $model->getErrors();
        }
        Yii::$app->response->format = Response::FORMAT_JSON;

        return $data;
      } else {
        return $this->renderAjax('_form', ['model' => $model]);
      }
    } else {
      return $this->redirect(['shipment-detail/index', 'ShipmentDetailSearch[shipment_id]' => $model->shipment->id]);
    }
  }

  public function actionRecalculate($id) {
    $model = $this->findModel($id);
    $qty = $needQty = $model->coverage_qty;
    $packSize = null;
    if(count(ArrayHelper::map($model->part->partPackings, 'id', 'pack_qty')) != 0) {
      $packSize = min(ArrayHelper::map($model->part->partPackings, 'id', 'pack_qty'));
      if($packSize) {
        $needQty = ceil($qty/$packSize)*$packSize;
      }
    }
    $model->pack_size = $packSize;
    $model->need_qty = $needQty;
    $model->save();

    return $this->redirect(['shipment-detail/index', 'ShipmentDetailSearch[shipment_id]' => $model->shipment->id]);
  }

  public function actionXls() {
    ini_set('memory_limit', '-1');
    ini_set('max_execution_time', '-1');
    $searchModel = new ShipmentDetailSearch();
    $xsl_file = $searchModel->search(Yii::$app->request->queryParams, 'excel');
    $xsl_file->send(Helpers::downloadFileName('shipment_control'));
    die;
  }

  protected function findModel($id) {
    if(($model = ShipmentDetail::findOne($id)) !== null) {
      return $model;
    }
    throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
  }

}
