<?php

namespace app\console\controllers;

use app\components\Helpers;
use app\models\InvoiceDetail;
use app\models\Part;
use app\models\PartPartWide;
use app\models\Req;
use app\models\ReqDetailWide;
use app\models\ShipmentPerformance;
use app\models\ShipmentPerformanceDetail;
use Yii;
use yii\console\Controller;

class SpController extends Controller
{
	public function actionIndex()
	{

		$start_sp = microtime(true);

		
		// DOH malumotini yozish
		$thisMonday = date('Y-m-d', strtotime('monday this week'));

		$columnNumber = array_search(date('Y-m-d', strtotime('+' . Yii::$app->params['less_dates_count'] . ' days')), Helpers::getPeriodFull()) + 1;
	
		// DOH < 60 
		$query = ReqDetailWide::find()
		->joinWith('req')
		->where([
			'and',
			['req_detail_wide.type' => CoverageController::TYPE_DAILY],
			['<', 'req_detail_wide.col' . $columnNumber, 0],
		]);
		$coverage = $query->all();
		// ***

		// DOH > 120
    $query120 = Req::find()
                ->where([
                  'and',
                  ['type' => CoverageController::TYPE_DAILY],
                  ['>', 'doh', Yii::$app->params['greater_dates_count']]
                ]);
    $coverage120 = $query120->all();
    $doh120 = [];
    foreach($coverage120 as $crow) {
      $daysQty = Yii::$app->params['greater_dates_count'] * round($crow->part->averageUsage);
      $needQty = ($crow->totalstock > 0) ? $crow->totalstock - $daysQty : $daysQty;
      $doh120[$crow->part_id] = $needQty;
		}
		// ***


		$err = false;
		$transaction = Yii::$app->db->beginTransaction();

		$deleted = ShipmentPerformance::deleteAll(['report_date' => $thisMonday]);

		$sp = new ShipmentPerformance();
		$sp->report_date = $thisMonday;
		$sp->created_at = date('Y-m-d H:i:s');
		if($sp->save()){
			foreach ($coverage as $crow) {
				$spd = new ShipmentPerformanceDetail();
				$spd->shipment_performance_id = $sp->id;	
				$spd->part_id = $crow->req->part->id;	
				$spd->doh = $crow->req->doh;	
				$spd->less_doh_qty = abs($crow['col'.$columnNumber]);	
				if(isset($doh120[$spd->part_id])) $spd->over_doh_qty = $doh120[$spd->part_id];
				if(!$spd->save()) $err = true;
			}
		}else $err = true;

		if($err) $transaction->rollBack(); else $transaction->commit();

		// ***********
		
		
		// Shipment ma'lumotlarini o'tgan haftaga yozib qo'yish
		$prevMonday = date('Y-m-d', strtotime('monday last week'));
		$prevSunday = date('Y-m-d', strtotime('sunday last week'));
		$spds = ShipmentPerformanceDetail::find()
			->joinWith('shipmentPerformance')
			->where(['shipment_performance.report_date' => $prevMonday])
			->all();

		foreach ($spds as $spd) {

			$inv = InvoiceDetail::find()
			->select(['part_id', 'cont_inv_id', 'qty' => 'sum(qty)'])
			->joinWith('contInv','part')
			->where([
				'and',
				['part_id' => $spd->part_id],
				['between', 'container_invoice.shipped_at', $prevMonday, $prevSunday]
			])
			->groupBy('part_id')
			->one();

			if($inv){
				$spd->shipped_qty = $inv->qty;
				$spd->shipped_at = $inv->contInv->shipped_at;
				$spd->save();
			}
		}
		
		// ******************

		$end_sp = microtime(true);
		$dur_sp = $end_sp - $start_sp;
		echo "\nShipment performance: " . round($dur_sp, 5) . " sec\n";
	}
}
