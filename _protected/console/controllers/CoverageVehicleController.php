<?php

/**
 * @link      http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license   http://www.yiiframework.com/license/
 */

namespace app\console\controllers;

use app\components\Helpers;
use app\models\ProductModel;
use app\models\CoverageVehicleT;
use app\models\VehicleCoverageInput;
use DateInterval;
use DatePeriod;
use DateTime;
use Yii;
use yii\console\Controller;

/**
 * This command echoes the first argument that you have entered.
 * This command is provided as an example for you to learn how to create console commands.
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since  2.0
 */
class CoverageVehicleController extends Controller
{


	const TYPE_DAILY       	 = 'D'; // Stock + Intransit
	const TYPE_DAILY_S       = 'DS'; // Stock
	const TYPE_DAILY_O       = 'DO'; // Stock + Intransit + Orders
	const TYPE_WEEKLY        = 'W'; // Stock + Intransit
	const TYPE_WEEKLY_S      = 'WS'; // Stock
	const TYPE_WEEKLY_O      = 'WO'; // Stock + Intransit + Orders

	const CNT_COLUMNS       = 71; // Count of columns of table Req.
	/**
	 * This command echoes what you have entered as the message.
	 * @param string $message the message to be echoed.
	 */
	protected
		$report_date,
		$stock_result,
		$uamstock_result,
		$intransit_result,
		$orders_result,
		$plan_result;

	protected function iniSet()
	{
		error_reporting(E_ALL & ~E_NOTICE);
	}



	public function actionDaily()
	{


		$this->iniSet();

		$this->setReportDate();
		$period = Helpers::getPeriodFull($this->report_date);

		$this->clearingData();

		$this->prepareData();

	
		// ---------------------------------------
		// result
		$start_result = microtime(true);
		
		$modelModels = ProductModel::find()->where([
			'is_vehicle' => ProductModel::IS_VEHICLE
		])->all();
		
			
		$this->calcDaily(self::TYPE_DAILY_S, $modelModels, $period);
		$this->calcDaily(self::TYPE_DAILY, $modelModels, $period);	
		$this->calcDaily(self::TYPE_DAILY_O, $modelModels, $period);
	
		$end_result = microtime(true);
		$dur_result = $end_result - $start_result;
		echo "\nResult: " . round($dur_result, 5) . " sec\n";
		// end of result
	}

	public function setReportDate(){
		
		$this->report_date =  VehicleCoverageInput::getLastCoverageDate();

	}

	protected function calcDaily($type, $modelModels, $period)
	{


		$transaction = Yii::$app->db->beginTransaction();
		
		$error = false;
		if (is_array($modelModels) and count($modelModels) > 0) {
			foreach ($modelModels as $pmodel) {
				$modelCovVeh = new CoverageVehicleT();
				$modelCovVeh->type = $type;
				$modelCovVeh->model_id = $pmodel->id;
				$modelCovVeh->stock = $this->stock_result[$pmodel->id] ?? 0;
				$modelCovVeh->uamstock = $this->uamstock_result[$pmodel->id] ?? 0;
				$modelCovVeh->orders = $this->orders_result[$pmodel->id] ?? 0;
				$modelCovVeh->calc_at = date('Y-m-d H:i');
				switch ($type) {
					case self::TYPE_DAILY_S : $bal = $modelCovVeh->stock + $modelCovVeh->uamstock; break;
					case self::TYPE_DAILY : $bal = $modelCovVeh->stock + $modelCovVeh->uamstock; break;
					case self::TYPE_DAILY_O : $bal = $modelCovVeh->stock + $modelCovVeh->uamstock + $modelCovVeh->orders; break;
				}
				

				$order = 0;
				if ($modelCovVeh->save()) {

					$doh = 0;
					$stop = false;
					$cols = '';
					$values_wide = '';
					$totalIntransit = 0;
					$stockOut = null;

					foreach ($period as $pdate) {

						$intransit = $this->getIntransit($modelCovVeh->model_id, $pdate);
						$totalIntransit += $intransit;
						$intransit = ($type != self::TYPE_DAILY_S) ? $intransit : 0;

						$plan = $this->getPlan($modelCovVeh->model_id, $pdate);
						
						$qty = $bal + $intransit - $plan;
						$bal = $qty;

						if (!$stop) if ($qty >= 0) { $doh++; $stockOut = $pdate;}
						else $stop = true;

						$last_day_of_period = date('Y-m-d', strtotime("+2 months", strtotime($this->report_date)));
						if ($pdate <= $last_day_of_period) {

							$order++;
							$cols .= "`col" . $order . "`";
							$values_wide .= "" . $qty . "";

							if ($pdate < $last_day_of_period) {
								$cols .= ", ";
								$values_wide .= ", ";
							}
						}
					}
				
					$query = "INSERT INTO `coverage_vehicle_detail_t` (`coverage_vehicle_id`,`type`," . $cols . ") VALUES (" . $modelCovVeh->id . ", '" . $type . "', " . $values_wide . ")";
					Yii::$app->db->createCommand($query)->execute();
					$sqlStockOut = ($stockOut) ? ", `stock_out` = '" . $stockOut . "'" : '';
					$query = "UPDATE `coverage_vehicle_t` SET  `doh` = " . $doh . $sqlStockOut . ", `intransit` = " . $totalIntransit . " WHERE `id` = " . $modelCovVeh->id . ";";
					Yii::$app->db->createCommand($query)->execute();	
					

					
				} else {
					$error = true;
				}
			}
		}

		
		

		$query = "select doh from coverage_vehicle_t where type = :type group by doh order by doh;";
		$dohs = Yii::$app->db->createCommand($query, [':type' => $type])->queryAll();
		
		if (is_array($dohs) and count($dohs) > 0) {
			$t = 0;

			foreach ($dohs as $rowDOH) {
				$t++;
				$order_col_number = ($rowDOH['doh'] < self::CNT_COLUMNS) ? $rowDOH['doh'] + 1 : self::CNT_COLUMNS;
				$union_query .= "
                    select * from
                    (
                        select r.id rid, r.model_id, r.stock, r.doh, r.calc_at, w.* 
                        from coverage_vehicle_t r, coverage_vehicle_detail_t w 
                        where r.type = '" . $type . "' and r.id = w.coverage_vehicle_id and r.doh = " . $rowDOH['doh'] . "
                        order by  w.col" . $order_col_number . "
                        limit 1000
                    ) t" . $rowDOH['doh'] . "                
                ";
				if (is_array($dohs) and count($dohs) != $t) {
					$union_query .= " union ";
				}
			}

			$union_result = Yii::$app->db->createCommand($union_query)->queryAll();
			$values = '';
			$i = 0;

			foreach ($union_result as $ures) {
				$i++;
				$values .= "(" . $ures['coverage_vehicle_id'] . ", '" . $type . "',";
				$cols = '';
				$colvals = '';
				for ($c = 1; $c <= (self::CNT_COLUMNS - 1); $c++) {
					$cols .= " `col" . $c . "`";
					$colvals .= (!empty($ures['col' . $c])) ? $ures['col' . $c] : 0;
					if ($c < (self::CNT_COLUMNS - 1)) {
						$cols .= ", ";
						$colvals .= ", ";
					}
				}
				$values .= $colvals . " ) ";
				if (is_array($union_result) and count($union_result) != $i) {
					$values .= ", ";
				}
			}

			$query = "INSERT INTO `coverage_vehicle_detail_t` (`coverage_vehicle_id`,`type`, " . $cols . ") VALUES " . $values . ";";
			Yii::$app->db->createCommand(' delete from `coverage_vehicle_detail_t` where  `type` = :type ', [':type' => $type])->execute();
			Yii::$app->db->createCommand($query)->execute();

		}
		// end calculation


		if (!$error)
			$transaction->commit();
		else
			$transaction->rollBack();
		
	}
	

	public function actionWeekly()
	{
		$this->iniSet();
		$this->setReportDate();
		$period = Helpers::getPeriodWeek2($this->report_date);
		
		$this->prepareData();

		// ---------------------------------------
		// result
		$start_result = microtime(true);

		$modelModels = ProductModel::find()->where([
			'is_vehicle' => ProductModel::IS_VEHICLE
		])->all();

		
		$this->calcWeekly(self::TYPE_WEEKLY_S, $modelModels, $period);
		$this->calcWeekly(self::TYPE_WEEKLY, $modelModels, $period);	
		$this->calcWeekly(self::TYPE_WEEKLY_O, $modelModels, $period);

		$end_result = microtime(true);
		$dur_result = $end_result - $start_result;
		echo "\nResult: " . round($dur_result, 5) . " sec\n";
		// end of result
	}

	public function calcWeekly($type, $modelModels, $period)
	{
		

		$transaction = Yii::$app->db->beginTransaction();

		$error = false;
		if (is_array($modelModels) and count($modelModels) > 0) {
			foreach ($modelModels as $pmodel) {
				$modelCovVeh = new CoverageVehicleT();
				$modelCovVeh->type = $type;
				$modelCovVeh->model_id = $pmodel->id;
				$modelCovVeh->stock = $this->stock_result[$pmodel->id] ?? 0;
				$modelCovVeh->uamstock = $this->uamstock_result[$pmodel->id] ?? 0;
				$modelCovVeh->orders = $this->orders_result[$pmodel->id] ?? 0;
				$modelCovVeh->calc_at = date('Y-m-d H:i');
				switch ($type) {
					case self::TYPE_WEEKLY_S : $bal = $modelCovVeh->stock + $modelCovVeh->uamstock; break;
					case self::TYPE_WEEKLY : $bal = $modelCovVeh->stock + $modelCovVeh->uamstock; break;
					case self::TYPE_WEEKLY_O : $bal = $modelCovVeh->stock + $modelCovVeh->uamstock + $modelCovVeh->orders; break;
				}
				
				$order = 0;
				if ($modelCovVeh->save()) {

					$cols = '';
					$values_wide = '';
					$totalIntransit = 0;
					foreach ($period as $per) {
						$order++;
						
						$intransit = $this->getIntransit($modelCovVeh->model_id, $per['from'], $per['to']);
						$totalIntransit += $intransit;
						$intransit = ($type != self::TYPE_WEEKLY_S) ? $intransit : 0;

						$plan = $this->getPlan($modelCovVeh->model_id, $per['from'], $per['to']);
						$qty = $bal + $intransit - $plan;
						$bal = $qty;
						$cols .= "`col" . $order . "`";
						$values_wide .= "" . $qty . "";
						$values_plan .= "" . $plan . "";
						if (is_array($period) and count($period) != $order) {
							$cols .= ", ";
							$values_wide .= ", ";
						}
					}

					
					$query = "INSERT INTO `coverage_vehicle_detail_t` (`coverage_vehicle_id`,`type`," . $cols . ") VALUES (" . $modelCovVeh->id . ", '" . $type . "', " . $values_wide . ")";
					Yii::$app->db->createCommand($query)->execute();

					switch ($type) {
						case self::TYPE_WEEKLY_S : $dailyType = self::TYPE_DAILY_S; break;
						case self::TYPE_WEEKLY : $dailyType = self::TYPE_DAILY; break;
						case self::TYPE_WEEKLY_O : $dailyType = self::TYPE_DAILY_O; break;
					}

					$covVehT = CoverageVehicleT::find()->where(['type' => $dailyType, 'model_id' => $modelCovVeh->model_id])->one();
					$sqlStockOut = ($covVehT->stock_out) ? ", `stock_out` = '" . $covVehT->stock_out . "'" : '';
					$query = "UPDATE `coverage_vehicle_t` SET `doh` = " . $covVehT->doh . $sqlStockOut . ", `intransit` = " . $totalIntransit . "   WHERE `id` = " . $modelCovVeh->id . ";";
					Yii::$app->db->createCommand($query)->execute();
					
					
				} else {
					$error = true;
				}
			}
		}
		$query = "select doh from coverage_vehicle_t where type = :type group by doh order by doh;";
		$dohs = Yii::$app->db->createCommand($query, [':type' => $type])->queryAll();
		if (is_array($dohs) and count($dohs) > 0) {
			$t = 0;
			foreach ($dohs as $rowDOH) {
				$t++;
				$order_col_number = ($rowDOH['doh'] < self::CNT_COLUMNS) ? $rowDOH['doh'] + 1 : self::CNT_COLUMNS;
				$union_query .= "
                    select * from
                    (
                        select r.id rid, r.model_id, r.stock, r.doh, r.calc_at, w.* 
                        from coverage_vehicle_t r, coverage_vehicle_detail_t w 
                        where r.type = '" . $type . "' and r.id = w.coverage_vehicle_id and r.doh = " . $rowDOH['doh'] . "
                        order by  w.col" . $order_col_number . "
                        limit 1000
                    ) t" . $rowDOH['doh'] . "                
                ";
				if (is_array($dohs) and count($dohs) != $t) {
					$union_query .= " union ";
				}
			}
			$union_result = Yii::$app->db->createCommand($union_query)->queryAll();
			$values = '';
			$i = 0;
			foreach ($union_result as $ures) {
				$i++;
				$values .= "(" . $ures['coverage_vehicle_id'] . ", '" . $type . "',";
				$cols = '';
				$colvals = '';
				for ($c = 1; $c <= (self::CNT_COLUMNS - 1); $c++) {
					$cols .= " `col" . $c . "`";
					$colvals .= (!empty($ures['col' . $c])) ? $ures['col' . $c] : 0;
					if ($c < (self::CNT_COLUMNS - 1)) {
						$cols .= ", ";
						$colvals .= ", ";
					}
				}
				$values .= $colvals . " ) ";
				if (is_array($union_result) and count($union_result) != $i) {
					$values .= ", ";
				}
			}
			$query = "INSERT INTO `coverage_vehicle_detail_t` (`coverage_vehicle_id`,`type`, " . $cols . ") VALUES " . $values . ";";
			Yii::$app->db->createCommand(' delete from `coverage_vehicle_detail_t` where  `type` = :type ', [':type' => $type])->execute();
			Yii::$app->db->createCommand($query)->execute();
		}
		if (!$error)
			$transaction->commit();
		else
			$transaction->rollBack();

	}

	

	protected function prepareData()
	{

		
		// stock
		$start_stock = microtime(true);

		$query = "select model_id , sum(quantity) qty from vehicle_coverage_input where description = :type group by model_id";
		$stock = Yii::$app->db->createCommand($query,[':type' => VehicleCoverageInput::CURRENT_STOCK])->queryAll();
		$this->stock_result = [];
		foreach ($stock as $st) $this->stock_result[$st['model_id']] = (int)$st['qty'];

		$end_stock = microtime(true);
		$dur_stock = $end_stock - $start_stock;
		echo "\nStock: " . round($dur_stock, 5) . " sec\n";
		// end of stock

		// uamstock
		$start_uamstock = microtime(true);

		$query = "select model_id , sum(quantity) qty from vehicle_coverage_input where description = :type group by model_id";
		$uamstock = Yii::$app->db->createCommand($query,[':type' => VehicleCoverageInput::UAM_STOCK])->queryAll();
		$this->uamstock_result = [];
		foreach ($uamstock as $st) $this->uamstock_result[$st['model_id']] = (int)$st['qty'];

		$end_uamstock = microtime(true);
		$dur_uamstock = $end_uamstock - $start_uamstock;
		echo "\nStock UzAutoMotors: " . round($dur_uamstock, 5) . " sec\n";
		// end of uamstock

		// orders
		$start_orders = microtime(true);

		$query = "select model_id , sum(quantity) qty from vehicle_coverage_input where description = :type group by model_id";
		$orders = Yii::$app->db->createCommand($query,[':type' => VehicleCoverageInput::PAID_NOT_SHIPPED_ORDER])->queryAll();
		$this->orders_result = [];
		foreach ($orders as $st) $this->orders_result[$st['model_id']] = (int)$st['qty'];

		$end_orders = microtime(true);
		$dur_orders = $end_orders - $start_orders;
		echo "\nOrders: " . round($dur_orders, 5) . " sec\n";
		// end of orders

		

		// intransit
		$start_intransit = microtime(true);
		$query = "
		select  
			for_date estdate, model_id , sum(quantity) qty 
		from 
			vehicle_coverage_input 
		where 
			description = :type and for_date >= :repport_date
		group by for_date, model_id 
        ";
		$intransit = Yii::$app->db->createCommand($query,[':type' => VehicleCoverageInput::INTRANSIT_ETA, ':repport_date' => $this->report_date])->queryAll();
		$model = [];
		$estdate = [];
		$intransit_result = [];
		foreach ($intransit as $tr) {
			$model[] = $tr['model_id'];
			$estdate[] = $tr['estdate'];
		}
		$model = array_unique($model);
		$estdate = array_unique($estdate);
		foreach ($estdate as $d)
			foreach ($model as $p)
				foreach ($intransit as $tr)
					if ($tr['model_id'] == $p and $tr['estdate'] == $d) {
						$intransit_result[$d][$p] = (int)$tr['qty'];
						break;
					}
		$this->intransit_result = $intransit_result;
		$end_intransit = microtime(true);
		$dur_intransit = $end_intransit - $start_intransit;
		echo "\nIntransit: " . round($dur_intransit, 5) . " sec\n";
		// end of intransit


		// plan
		$start_plan = microtime(true);
		$query = "
			select  
				target_date, model_id , sum(quantity) qty 
			from 
				oem_plan 
			where 
				target_date >= :report_date 
			group by 
				target_date, model_id 
        ";
		$plan = Yii::$app->db->createCommand($query,[':report_date' => $this->report_date])->queryAll();
		$model = [];
		$target_date = [];
		$plan_result = [];
		foreach ($plan as $tr) {
			$model[] = $tr['model_id'];
			$target_date[] = $tr['target_date'];
		}
		$model = array_unique($model);
		$target_date = array_unique($target_date);
		foreach ($target_date as $d)
			foreach ($model as $p)
				foreach ($plan as $tr)
					if ($tr['model_id'] == $p and $tr['target_date'] == $d) {
						$plan_result[$d][$p] = (int)$tr['qty'];
						break;
					}
		$this->plan_result = $plan_result;
		$end_plan = microtime(true);
		$dur_plan = $end_plan - $start_plan;
		echo "\nplan: " . round($dur_plan, 5) . " sec\n";
		// end of plan
		
	}

	protected function clearingData()
	{

		// clearing
		$start_clearing = microtime(true);

		Yii::$app->db->createCommand('SET foreign_key_checks = 0')->execute();
		Yii::$app->db->createCommand()->truncateTable('coverage_vehicle_t')->execute();
		Yii::$app->db->createCommand()->truncateTable('coverage_vehicle_detail_t')->execute();
		Yii::$app->db->createCommand('SET foreign_key_checks = 1')->execute();

		$end_clearing = microtime(true);
		$dur_clearing = $end_clearing - $start_clearing;
		echo "\nClearing: " . round($dur_clearing, 5) . " sec\n";
		// ---------------------------------------

	}

	public function actionCopyingData()
	{

		// copying
		$start_copying = microtime(true);

		Yii::$app->db->createCommand('SET foreign_key_checks = 0')->execute();
		Yii::$app->db->createCommand()->truncateTable('coverage_vehicle')->execute();
		Yii::$app->db->createCommand()->truncateTable('coverage_vehicle_detail')->execute();
		Yii::$app->db->createCommand('SET foreign_key_checks = 1')->execute();

		Yii::$app->db->createCommand('insert into coverage_vehicle select * from coverage_vehicle_t')->execute();
		Yii::$app->db->createCommand('insert into coverage_vehicle_detail select * from coverage_vehicle_detail_t')->execute();

		$end_copying = microtime(true);
		$dur_copying = $end_copying - $start_copying;
		echo "\nCopying: " . round($dur_copying, 5) . " sec\n";
		// ---------------------------------------
	}

	protected function getIntransit($model_id, $from, $to = null)
	{
		$to = (empty($to)) ? $from : $to;
		$begin = new DateTime($from);
		$end = new DateTime($to);
		$end = $end->modify('+1 day');
		$daterange = new DatePeriod($begin, new DateInterval('P1D'), $end);
		$qty = 0;
		foreach ($daterange as $date) {
			$qty += $this->intransit_result[$date->format("Y-m-d")][$model_id];
		}
		return $qty;
	}

	protected function getPlan($model_id, $from, $to = null)
	{

		$to = (empty($to)) ? $from : $to;
		$begin = new DateTime($from);
		$end = new DateTime($to);
		$end = $end->modify('+1 day');
		$daterange = new DatePeriod($begin, new DateInterval('P1D'), $end);
		$qty = 0;
		foreach ($daterange as $date) {
			$qty += $this->plan_result[$date->format("Y-m-d")][$model_id];
		}
		return $qty;

	}
	
}
