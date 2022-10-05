<?php

namespace app\controllers;

use app\components\Helpers;
use app\console\controllers\CoverageController;
use app\models\ReqDetailWide;
use Yii;
use app\models\Shipment;
use app\models\ShipmentDetail;
use app\models\ShipmentSearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class ShipmentController extends AppController
{
    

    public function actionIndex()
    {
        $searchModel = new ShipmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->sort->defaultOrder = ['id' => SORT_DESC];

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionCreate()
    {
        // Validation 1
        if(!isset(Yii::$app->params['shipment_dates_count']) or Yii::$app->params['shipment_dates_count'] == 0){
            Yii::$app->session->setFlash('error', Yii::t('app', 'Param shipment_dates_count is not set.'));
            return $this->redirect('index');
        }
        // *****
        $reportDate = date('Y-m-d', strtotime('+'.Yii::$app->params['shipment_dates_count'].' days'));
        $columnNumber = array_search($reportDate, Helpers::getPeriodFull()) + 1;
        $query = ReqDetailWide::find()
            ->joinWith('req.part.partPackings')
            //->joinWith('req')
            ->where([
                'and',
                ['req_detail_wide.type' => CoverageController::TYPE_DAILY],
                ['<', 'req_detail_wide.col'.$columnNumber, 0]
            ]);
        $coverage = $query->all();
        
        // Validation 2
        if(!$coverage){
            Yii::$app->session->setFlash('error', Yii::t('app', 'Coverage data not found. Please check coverage page.'));
            return $this->redirect('index');
        }
        // ***

        $isShipmentExist = Shipment::findOne(['created_at' => date('Y-m-d'),'report_date' => $reportDate]);

        // Validation 3
        if($isShipmentExist){
            Yii::$app->session->setFlash('error', Yii::t('app', 'Report has already calculated for this day. Please remove previous one or change settings.'));
            return $this->redirect('index');
        }
        // ***

       

        // echo '<pre>';
        // print_r($coverage);
        // echo '</pre>';
        // die;

        $transaction = Yii::$app->db->beginTransaction();
        $isError = false;
        // Create Shipment 
        $shipmentModel = new Shipment();
        $shipmentModel->report_date = $reportDate;
        $shipmentModel->created_at = date('Y-m-d');
        if($shipmentModel->save()){
            $rows = [];
            foreach ($coverage as $crow) {
                $qty = $needQty = abs($crow['col'.$columnNumber]);
                
                $packSize = null;
                if(count(ArrayHelper::map($crow->req->part->partPackings,'id','pack_qty')) != 0){
                    $packSize = min(ArrayHelper::map($crow->req->part->partPackings,'id','pack_qty'));  
                    if($packSize){
                        $needQty = ceil($qty / $packSize) * $packSize;
                    }
                    
                }
                    
                $actualContract = $crow->req->part->getActualContract();
                $supplier_id = $actualContract->contract->supplier_id ?? null;
                $rows[] = [
                    'id' => null,
                    'shipment_id' => $shipmentModel->id,
                    'part_id' => $crow->req->part_id,
                    'supplier_id' => $supplier_id,
                    'pack_size' => $packSize,
                    'disruption_date' => date('Y-m-d', strtotime('+'.$crow->req->days_count.' days')),
                    'coverage_qty' => $qty,
                    'need_qty' => $needQty,
                    'ready_qty' => 0,
                    'approved_qty' => 0,
                    'comment' => ''
                ];
            }
            $shipmentDetailModel = new ShipmentDetail();
            $exec = Yii::$app->db->createCommand()->batchInsert(ShipmentDetail::tableName(), $shipmentDetailModel->attributes(), $rows)->execute();
            if(!$exec){
                $isError = true;    
            }
        }else{
            $isError = true;
        }

        if (!$isError)
            $transaction->commit();
        else
            $transaction->rollBack();

    

   
        return $this->redirect(['index']);
    }




    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }


    protected function findModel($id)
    {
        if (($model = Shipment::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
