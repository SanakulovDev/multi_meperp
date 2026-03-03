<?php

/**
 * @link      http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license   http://www.yiiframework.com/license/
 */

namespace app\console\controllers;

use app\components\Helpers;
use app\models\CoverageBalance;
use app\models\Currency;
use app\models\CurrencyRate;
use app\models\Part;
use app\models\ReqDetailWide;
use app\models\Reqt;
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
class CoverageController extends Controller
{


	const TYPE_DAILY       = 'D';
	const TYPE_WEEKLY      = 'W';
	const TYPE_LOCAL_DAILY = 'L';
	const TYPE_STOCK       = 'S';
	const TYPE_LOCAL_CONS = 'C';
	const TYPE_LOCAL_SEMI = 'X'; // Semi parts

	const CNT_COLUMNS       = 220; // Count of columns of table Req.
	/**
	 * This command echoes what you have entered as the message.
	 * @param string $message the message to be echoed.
	 */
	protected
		$stock_result,
		$pstock_result,
		$stock_fg_result,
		$intransit_result,
		$arrive_result,
		$plan_result;

	protected function iniSet()
	{
		//ini_set('mysql.connect_timeout', 300);
		//ini_set('default_socket_timeout', 300);
		error_reporting(E_ALL & ~E_NOTICE);
		return true;
	}



	public function actionDaily()
	{


		$this->iniSet();
		$period = Helpers::getPeriodFull();
		
		$this->clearingData();

		$this->prepareData();
		$this->preparePlan();


		// ---------------------------------------
		// result
		$start_result = microtime(true);
		$partModels = Part::find()->where([
			'status' => Part::STATUS_ACTIVE,
			'state' => Part::STATE_RAW,
			'contract_source_id' => Yii::$app->params['import_contract_source_ids']
		])->all();

		$transaction = Yii::$app->db->beginTransaction();

		$result = [];
		$error = false;
		if (is_array($partModels) and count($partModels) > 0) {
			foreach ($partModels as $pmodel) {
				$modelReq = new Reqt();
				$modelReq->type = self::TYPE_DAILY;
				$modelReq->part_id = $pmodel->id;
				$modelReq->whbal = $this->stock_result[0][$pmodel->id];
				$modelReq->linebal = $this->stock_result[1][$pmodel->id];
				$modelReq->semistock = $this->stock_result[9][$pmodel->id];
				$modelReq->pending = $this->pstock_result[0][$pmodel->id] + $this->pstock_result[1][$pmodel->id];
				$modelReq->outsourcing = $this->stock_result[2][$pmodel->id] + $this->stock_result[3][$pmodel->id];
				$modelReq->arrive = $this->arrive_result[$pmodel->id];
				$modelReq->calc_at = date('Y-m-d H:i:s');
				$bal = $modelReq->whbal + $modelReq->linebal + $modelReq->outsourcing + $modelReq->pending + $modelReq->arrive + $modelReq->semistock;
				// bu fg stock balance ga qo'shilmaydi
				$modelReq->fgstock = $this->stock_fg_result[$pmodel->id];

				// 09.04.2020 da fgstock ham balance ga qo'shilishi kerakligi aytildi
				// $bal += $modelReq->fgstock;

				$order = 0;
				if ($modelReq->save()) {
					$days_count = 0;
					$stop = false;
					$cols = '';
					$values_wide = '';
					$values_plan = '';
					//$hasPlan = false;
					foreach ($period as $pdate) {


						$intransit = $this->getIntransit($modelReq->part_id, $pdate);
						$plan = $this->getPlan($modelReq->part_id, $pdate);
						//if(!$hasPlan) $hasPlan = ($plan != 0);
						$qty = $bal + $intransit - $plan;
						$bal = $qty;

						if (!$stop) if ($qty >= 0) $days_count++;
						else $stop = true;

						$last_day_of_period = date('Y-m-t', strtotime('+6 month'));
						if ($pdate <= $last_day_of_period) {

							$order++;
							$cols .= "`col" . $order . "`";
							$values_wide .= "" . $qty . "";
							$values_plan .= "" . $plan . "";

							if ($pdate < $last_day_of_period) {
								$cols .= ", ";
								$values_wide .= ", ";
								$values_plan .= ", ";
							}
						}

					}


					//if(!$hasPlan){
					//	$modelReq->delete();
					//}else{
						$query = "INSERT INTO `req_detail_wide_t` (`req_id`,`type`," . $cols . ") VALUES (" . $modelReq->id . ", '" . self::TYPE_DAILY . "', " . $values_wide . ")";
						// echo $query;
						// die;
						Yii::$app->db->createCommand($query)->execute();

						$query = "INSERT INTO `req_detail_plan_t` (`req_id`,`type`," . $cols . ") VALUES (" . $modelReq->id . ", '" . self::TYPE_DAILY . "', " . $values_plan . ")";
						Yii::$app->db->createCommand($query)->execute();

						$avg_usage = $modelReq->part->averageUsage;

						$doh = 0;
						if($modelReq->totalstock > 0){
							if($avg_usage > 0){
								$doh = $modelReq->totalstock / $avg_usage;
								if($doh > 307) $doh = 307;
							}else{
								$doh = 307;
							}
						}else{
							$doh = 0;
						}
						

						$query = "UPDATE `req_t` SET `days_count` = " . $days_count . ", `doh` = " . $doh . " WHERE `id` = " . $modelReq->id . ";";
						Yii::$app->db->createCommand($query)->execute();	
					//}

					
				} else {
					$error = true;
				}
			}
		}
		$query = "select days_count from req_t where type = :type group by days_count order by days_count;";
		$union_query = "";
		$day_counts = Yii::$app->db->createCommand($query, [':type' => self::TYPE_DAILY])->queryAll();
		if (is_array($day_counts) and count($day_counts) > 0) {
			$t = 0;
			foreach ($day_counts as $day_count) {
				$t++;
				$order_col_number = ($day_count['days_count'] < self::CNT_COLUMNS) ? $day_count['days_count'] + 1 : self::CNT_COLUMNS;
				$union_query .= "
                    select * from
                    (
                        select r.id rid, r.part_id, r.whbal, r.linebal, r.outsourcing,r.pending, r.arrive, r.days_count, r.calc_at, w.* 
                        from req_t r, req_detail_wide_t w 
                        where r.type = '" . self::TYPE_DAILY . "' and r.id = w.req_id and r.days_count = " . $day_count['days_count'] . "
                        order by  w.col" . $order_col_number . "
                        limit 1000
                    ) t" . $day_count['days_count'] . "                
                ";
				if (is_array($day_counts) and count($day_counts) != $t) {
					$union_query .= " union ";
				}
			}
			$union_result = Yii::$app->db->createCommand($union_query)->queryAll();
			$values = '';
			$i = 0;
			foreach ($union_result as $ures) {
				$i++;
				$values .= "(" . $ures['req_id'] . ", '" . self::TYPE_DAILY . "',";
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
			$query = "INSERT INTO `req_detail_wide_t` (`req_id`,`type`, " . $cols . ") VALUES " . $values . ";";
			Yii::$app->db->createCommand(' delete from `req_detail_wide_t` where  `type` = :type ', [':type' => self::TYPE_DAILY])->execute();
			Yii::$app->db->createCommand($query)->execute();
		}
		if (!$error)
			$transaction->commit();
		else
			$transaction->rollBack();
		$end_result = microtime(true);
		$dur_result = $end_result - $start_result;
		echo "\nResult: " . round($dur_result, 5) . " sec\n";
		// end of result
	}

	public function actionWeekly()
	{
		$this->iniSet();
		$period = Helpers::getPeriodWeek6Month();

		$this->prepareData();

		// ---------------------------------------
		// result
		$start_result = microtime(true);
		$partModels = Part::find()->where([
			'status' => Part::STATUS_ACTIVE,
			'state' => Part::STATE_RAW,
			'contract_source_id' => Yii::$app->params['import_contract_source_ids']
		])->all();
		$transaction = Yii::$app->db->beginTransaction();

		$result = [];
		$error = false;
		if (is_array($partModels) and count($partModels) > 0) {
			foreach ($partModels as $pmodel) {
				$modelReq = new Reqt();
				$modelReq->type = self::TYPE_WEEKLY;
				$modelReq->part_id = $pmodel->id;
				$modelReq->whbal = $this->stock_result[0][$pmodel->id];
				$modelReq->linebal = $this->stock_result[1][$pmodel->id];
				$modelReq->semistock = $this->stock_result[9][$pmodel->id];
				$modelReq->pending = $this->pstock_result[0][$pmodel->id] + $this->pstock_result[1][$pmodel->id];
				$modelReq->outsourcing = $this->stock_result[2][$pmodel->id] + $this->stock_result[3][$pmodel->id];
				$modelReq->arrive = $this->arrive_result[$pmodel->id];
				$modelReq->calc_at = date('Y-m-d H:i:s');
				$bal = $modelReq->whbal + $modelReq->linebal + $modelReq->outsourcing + $modelReq->pending + $modelReq->arrive + $modelReq->semistock;
				// bu fg stock balance ga qo'shilmaydi
				$modelReq->fgstock = $this->stock_fg_result[$pmodel->id];

				// 09.04.2020 da fgstock ham balance ga qo'shilishi kerakligi aytildi
				// $bal += $modelReq->fgstock;
				
				$order = 0;
				if ($modelReq->save()) {

					$cols = '';
					$values_wide = '';
					$values_plan = '';
					//$hasPlan = false;
					foreach ($period as $per) {
						$order++;
						$intransit = $this->getIntransit($modelReq->part_id, $per['from'], $per['to']);
						$plan = $this->getPlan($modelReq->part_id, $per['from'], $per['to']);
						//if(!$hasPlan) $hasPlan = ($plan != 0);
						$qty = $bal + $intransit - $plan;
						$bal = $qty;
						$cols .= "`col" . $order . "`";
						$values_wide .= "" . $qty . "";
						$values_plan .= "" . $plan . "";
						if (is_array($period) and count($period) != $order) {
							$cols .= ", ";
							$values_wide .= ", ";
							$values_plan .= ", ";
						}
					}

					//if(!$hasPlan){
					//	$modelReq->delete();
					//}else{
						$query = "INSERT INTO `req_detail_wide_t` (`req_id`,`type`," . $cols . ") VALUES (" . $modelReq->id . ", '" . self::TYPE_WEEKLY . "', " . $values_wide . ")";
						Yii::$app->db->createCommand($query)->execute();

						$query = "INSERT INTO `req_detail_plan_t` (`req_id`,`type`," . $cols . ") VALUES (" . $modelReq->id . ", '" . self::TYPE_WEEKLY . "', " . $values_plan . ")";
						Yii::$app->db->createCommand($query)->execute();

						$days_count = Reqt::find()->where(['type' => self::TYPE_DAILY, 'part_id' => $modelReq->part_id])->one()->days_count;

						$avg_usage = $modelReq->part->averageUsage;
						$doh = 0;
						if($modelReq->totalstock > 0){
							if($avg_usage > 0){
								$doh = $modelReq->totalstock / $avg_usage;
								if($doh > 307) $doh = 307;
							}else{
								$doh = 307;
							}
						}else{
							$doh = 0;
						}

						$query = "UPDATE `req_t` SET `days_count` = " . ((!empty($days_count)) ? $days_count : 0) . ", `doh` = " . $doh . "  WHERE `id` = " . $modelReq->id . ";";
						Yii::$app->db->createCommand($query)->execute();
					//}
					
				} else {
					$error = true;
				}
			}
		}
		$query = "select days_count from req_t where type = :type group by days_count order by days_count;";
		$union_query = "";
		$day_counts = Yii::$app->db->createCommand($query, [':type' => self::TYPE_WEEKLY])->queryAll();
		if (is_array($day_counts) and count($day_counts) > 0) {
			$t = 0;
			foreach ($day_counts as $day_count) {
				$t++;
				$order_col_number = ($day_count['days_count'] < self::CNT_COLUMNS) ? $day_count['days_count'] + 1 : self::CNT_COLUMNS;
				$union_query .= "
                    select * from
                    (
                        select r.id rid, r.part_id, r.whbal, r.linebal, r.outsourcing,r.pending, r.arrive, r.days_count, r.calc_at, w.* 
                        from req_t r, req_detail_wide_t w 
                        where r.type = '" . self::TYPE_WEEKLY . "' and r.id = w.req_id and r.days_count = " . $day_count['days_count'] . "
                        order by  w.col" . $order_col_number . "
                        limit 1000
                    ) t" . $day_count['days_count'] . "                
                ";
				if (is_array($day_counts) and count($day_counts) != $t) {
					$union_query .= " union ";
				}
			}
			$union_result = Yii::$app->db->createCommand($union_query)->queryAll();
			$values = '';
			$i = 0;
			foreach ($union_result as $ures) {
				$i++;
				$values .= "(" . $ures['req_id'] . ", '" . self::TYPE_WEEKLY . "',";
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
			$query = "INSERT INTO `req_detail_wide_t` (`req_id`,`type`, " . $cols . ") VALUES " . $values . ";";
			Yii::$app->db->createCommand(' delete from `req_detail_wide_t` where  `type` = :type ', [':type' => self::TYPE_WEEKLY])->execute();
			Yii::$app->db->createCommand($query)->execute();
		}
		if (!$error)
			$transaction->commit();
		else
			$transaction->rollBack();
		$end_result = microtime(true);
		$dur_result = $end_result - $start_result;
		echo "\nResult: " . round($dur_result, 5) . " sec\n";
		// end of result
	}

	public function actionLocalDaily()
	{
		$this->iniSet();
		$period = Helpers::getPeriodFull();

		$this->prepareData();


		// ---------------------------------------
		// result
		$start_result = microtime(true);
		$partModels = Part::find()->where([
			'status' => Part::STATUS_ACTIVE,
			'state' => Part::STATE_RAW,
			'contract_source_id' => Yii::$app->params['local_contract_source_ids']
		])->all();
		$transaction = Yii::$app->db->beginTransaction();

		$result = [];
		$error = false;
		if (is_array($partModels) and count($partModels) > 0) {
			foreach ($partModels as $pmodel) {
				$modelReq = new Reqt();
				$modelReq->type = self::TYPE_LOCAL_DAILY;
				$modelReq->part_id = $pmodel->id;
				$modelReq->whbal = $this->stock_result[0][$pmodel->id];
				$modelReq->linebal = $this->stock_result[1][$pmodel->id];
				$modelReq->semistock = $this->stock_result[9][$pmodel->id];
				$modelReq->pending = $this->pstock_result[0][$pmodel->id] + $this->pstock_result[1][$pmodel->id];
				$modelReq->outsourcing = $this->stock_result[2][$pmodel->id] + $this->stock_result[3][$pmodel->id];
				$modelReq->arrive = $this->arrive_result[$pmodel->id];
				$modelReq->calc_at = date('Y-m-d H:i:s');
				$bal = $modelReq->whbal + $modelReq->linebal + $modelReq->outsourcing + $modelReq->pending + $modelReq->arrive + $modelReq->semistock;
				// bu fg stock balance ga qo'shilmaydi
				$modelReq->fgstock = $this->stock_fg_result[$pmodel->id];

				// 09.04.2020 da fgstock ham balance ga qo'shilishi kerakligi aytildi
				// $bal += $modelReq->fgstock;
				
				$order = 0;
				if ($modelReq->save()) {
					$days_count = 0;
					$stop = false;
					$cols = '';
					$values_wide = '';
					$values_plan = '';
					//$hasPlan = false;
					foreach ($period as $pdate) {


						$intransit = $this->getIntransit($modelReq->part_id, $pdate);
						$plan = $this->getPlan($modelReq->part_id, $pdate);
						//if(!$hasPlan) $hasPlan = ($plan != 0);
						$qty = $bal + $intransit - $plan;
						$bal = $qty;

						if (!$stop) if ($qty >= 0) $days_count++;
						else $stop = true;

						$last_day_of_period = date('Y-m-t', strtotime('+6 month'));
						if ($pdate <= $last_day_of_period) {

							$order++;
							$cols .= "`col" . $order . "`";
							$values_wide .= "" . $qty . "";
							$values_plan .= "" . $plan . "";

							if ($pdate < $last_day_of_period) {
								$cols .= ", ";
								$values_wide .= ", ";
								$values_plan .= ", ";
							}
						}
					}
					//if(!$hasPlan){
					//	$modelReq->delete();
					//}else{
						$query = "INSERT INTO `req_detail_wide_t` (`req_id`,`type`," . $cols . ") VALUES (" . $modelReq->id . ", '" . self::TYPE_LOCAL_DAILY . "', " . $values_wide . ")";
						Yii::$app->db->createCommand($query)->execute();
						$query = "INSERT INTO `req_detail_plan_t` (`req_id`,`type`," . $cols . ") VALUES (" . $modelReq->id . ", '" . self::TYPE_LOCAL_DAILY . "', " . $values_plan . ")";
						Yii::$app->db->createCommand($query)->execute();

						$avg_usage = $modelReq->part->averageUsage;
						$doh = 0;
						if($modelReq->totalstock > 0){
							if($avg_usage > 0){
								$doh = $modelReq->totalstock / $avg_usage;
								if($doh > 307) $doh = 307;
							}else{
								$doh = 307;
							}
						}else{
							$doh = 0;
						}

						$query = "UPDATE `req_t` SET `days_count` = " . $days_count . ", `doh` = " . $doh . " WHERE `id` = " . $modelReq->id . ";";
						Yii::$app->db->createCommand($query)->execute();
					//}
					
				} else {
					$error = true;
				}
			}
		}
		$query = "select days_count from req_t where type = :type group by days_count order by days_count;";
		$union_query = "";
		$day_counts = Yii::$app->db->createCommand($query, [':type' => self::TYPE_LOCAL_DAILY])->queryAll();
		if (is_array($day_counts) and count($day_counts) > 0) {
			$t = 0;
			foreach ($day_counts as $day_count) {
				$t++;
				$order_col_number = ($day_count['days_count'] < self::CNT_COLUMNS) ? $day_count['days_count'] + 1 : self::CNT_COLUMNS;
				$union_query .= "
                    select * from
                    (
                        select r.id rid, r.part_id, r.whbal, r.linebal, r.outsourcing,r.pending, r.arrive, r.days_count, r.calc_at, w.* 
                        from req_t r, req_detail_wide_t w 
                        where r.type = '" . self::TYPE_LOCAL_DAILY . "' and r.id = w.req_id and r.days_count = " . $day_count['days_count'] . "
                        order by  w.col" . $order_col_number . "
                        limit 1000
                    ) t" . $day_count['days_count'] . "                
                ";
				if (is_array($day_counts) and count($day_counts) != $t) {
					$union_query .= " union ";
				}
			}
			$union_result = Yii::$app->db->createCommand($union_query)->queryAll();
			$values = '';
			$i = 0;
			foreach ($union_result as $ures) {
				$i++;
				$values .= "(" . $ures['req_id'] . ", '" . self::TYPE_LOCAL_DAILY . "',";
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
			$query = "INSERT INTO `req_detail_wide_t` (`req_id`,`type`, " . $cols . ") VALUES " . $values . ";";
			Yii::$app->db->createCommand(' delete from `req_detail_wide_t` where  `type` = :type ', [':type' => self::TYPE_LOCAL_DAILY])->execute();
			Yii::$app->db->createCommand($query)->execute();
		}
		if (!$error)
			$transaction->commit();
		else
			$transaction->rollBack();
		$end_result = microtime(true);
		$dur_result = $end_result - $start_result;
		echo "\nResult: " . round($dur_result, 5) . " sec\n";
		// end of result
	}

	public function actionCalcAllStock()
	{
		$this->iniSet();

		$this->prepareData();

		// result
		$start_result = microtime(true);
		$partModels = Part::find()->where([
			'status' => Part::STATUS_ACTIVE,
			'state' => Part::STATE_RAW
		])->all();
		$transaction = Yii::$app->db->beginTransaction();

		$result = [];
		$error = false;
		if (is_array($partModels) and count($partModels) > 0) {
			foreach ($partModels as $pmodel) {
				$modelReq = new Reqt();
				$modelReq->type = self::TYPE_STOCK;
				$modelReq->part_id = $pmodel->id;
				$modelReq->whbal = $this->stock_result[0][$pmodel->id];
				$modelReq->linebal = $this->stock_result[1][$pmodel->id];
				$modelReq->semistock = $this->stock_result[9][$pmodel->id];
				$modelReq->pending = $this->pstock_result[0][$pmodel->id] + $this->pstock_result[1][$pmodel->id];
				$modelReq->outsourcing = $this->stock_result[2][$pmodel->id] + $this->stock_result[3][$pmodel->id];
				$modelReq->arrive = $this->arrive_result[$pmodel->id];
				$modelReq->calc_at = date('Y-m-d H:i:s');
				$bal = $modelReq->whbal + $modelReq->linebal + $modelReq->outsourcing + $modelReq->pending + $modelReq->arrive + $modelReq->semistock;
				// bu fg stock balance ga qo'shilmaydi
				$modelReq->fgstock = $this->stock_fg_result[$pmodel->id];
				$order = 0;
				if (!$modelReq->save()) $error = true;
			}
		}
		if (!$error)
			$transaction->commit();
		else
			$transaction->rollBack();
		$end_result = microtime(true);
		$dur_result = $end_result - $start_result;
		echo "\nResult: " . round($dur_result, 5) . " sec\n";
		// end of result
	}

	public function actionConsignmentDaily()
	{
		$this->iniSet();
		$period = Helpers::getPeriodFull();

		$this->prepareData('C');


		// ---------------------------------------
		// result
		$start_result = microtime(true);
		$partModels = Part::find()->where([
			'status' => Part::STATUS_ACTIVE,
			'contract_source_id' => Yii::$app->params['consignment_contract_source_ids']
		])->all();
		$transaction = Yii::$app->db->beginTransaction();

		$result = [];
		$error = false;
		if (is_array($partModels) and count($partModels) > 0) {
			foreach ($partModels as $pmodel) {
				$modelReq = new Reqt();
				$modelReq->type = self::TYPE_LOCAL_CONS;
				$modelReq->part_id = $pmodel->id;
				$modelReq->whbal = $this->stock_result[0][$pmodel->id];
				$modelReq->linebal = $this->stock_result[1][$pmodel->id];
				$modelReq->semistock = $this->stock_result[9][$pmodel->id];
				$modelReq->pending = $this->pstock_result[0][$pmodel->id] + $this->pstock_result[1][$pmodel->id];
				$modelReq->outsourcing = $this->stock_result[2][$pmodel->id] + $this->stock_result[3][$pmodel->id];
				$modelReq->arrive = $this->arrive_result[$pmodel->id];
				$modelReq->calc_at = date('Y-m-d H:i:s');
				$bal = $modelReq->whbal + $modelReq->linebal + $modelReq->outsourcing + $modelReq->pending + $modelReq->arrive + $modelReq->semistock;
				// bu fg stock balance ga qo'shilmaydi
				$modelReq->fgstock = $this->stock_fg_result[$pmodel->id];

				// 09.04.2020 da fgstock ham balance ga qo'shilishi kerakligi aytildi
				// $bal += $modelReq->fgstock;
				
				$order = 0;
				if ($modelReq->save()) {
					$days_count = 0;
					$stop = false;
					$cols = '';
					$values_wide = '';
					$values_plan = '';
					//$hasPlan = false;
					foreach ($period as $pdate) {


						$intransit = $this->getIntransit($modelReq->part_id, $pdate);
						$plan = $this->getPlan($modelReq->part_id, $pdate,null,'C');
						//if(!$hasPlan) $hasPlan = ($plan != 0);
						$qty = $bal + $intransit - $plan;
						$bal = $qty;

						if (!$stop) if ($qty >= 0) $days_count++;
						else $stop = true;

						$last_day_of_period = date('Y-m-t', strtotime('+6 month'));
						if ($pdate <= $last_day_of_period) {

							$order++;
							$cols .= "`col" . $order . "`";
							$values_wide .= "" . $qty . "";
							$values_plan .= "" . $plan . "";

							if ($pdate < $last_day_of_period) {
								$cols .= ", ";
								$values_wide .= ", ";
								$values_plan .= ", ";
							}
						}
					}

					// if (!$hasPlan) {
					// 	$modelReq->delete();
					// }else{
						$query = "INSERT INTO `req_detail_wide_t` (`req_id`,`type`," . $cols . ") VALUES (" . $modelReq->id . ", '" . self::TYPE_LOCAL_CONS . "', " . $values_wide . ")";
						Yii::$app->db->createCommand($query)->execute();
						$query = "INSERT INTO `req_detail_plan_t` (`req_id`,`type`," . $cols . ") VALUES (" . $modelReq->id . ", '" . self::TYPE_LOCAL_CONS . "', " . $values_plan . ")";
						Yii::$app->db->createCommand($query)->execute();
						
						$avg_usage = $modelReq->part->averageUsage;
						$doh = 0;
						if($modelReq->totalstock > 0){
							if($avg_usage > 0){
								$doh = $modelReq->totalstock / $avg_usage;
								if($doh > 307) $doh = 307;
							}else{
								$doh = 307;
							}
						}else{
							$doh = 0;
						}

						$query = "UPDATE `req_t` SET `days_count` = " . $days_count . ", `doh` = " . $doh . " WHERE `id` = " . $modelReq->id . ";";
						Yii::$app->db->createCommand($query)->execute();
					// }

					
				} else {
					$error = true;
				}
			}
		}
		$query = "select days_count from req_t where type = :type group by days_count order by days_count;";
		$union_query = "";
		$day_counts = Yii::$app->db->createCommand($query, [':type' => self::TYPE_LOCAL_CONS])->queryAll();
		if (is_array($day_counts) and count($day_counts) > 0) {
			$t = 0;
			foreach ($day_counts as $day_count) {
				$t++;
				$order_col_number = ($day_count['days_count'] < self::CNT_COLUMNS) ? $day_count['days_count'] + 1 : self::CNT_COLUMNS;
				$union_query .= "
                    select * from
                    (
                        select r.id rid, r.part_id, r.whbal, r.linebal, r.outsourcing,r.pending, r.arrive, r.days_count, r.calc_at, w.* 
                        from req_t r, req_detail_wide_t w 
                        where r.type = '" . self::TYPE_LOCAL_CONS . "' and r.id = w.req_id and r.days_count = " . $day_count['days_count'] . "
                        order by  w.col" . $order_col_number . "
                        limit 1000
                    ) t" . $day_count['days_count'] . "                
                ";
				if (is_array($day_counts) and count($day_counts) != $t) {
					$union_query .= " union ";
				}
			}
			$union_result = Yii::$app->db->createCommand($union_query)->queryAll();
			$values = '';
			$i = 0;
			foreach ($union_result as $ures) {
				$i++;
				$values .= "(" . $ures['req_id'] . ", '" . self::TYPE_LOCAL_CONS . "',";
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
			$query = "INSERT INTO `req_detail_wide_t` (`req_id`,`type`, " . $cols . ") VALUES " . $values . ";";
			Yii::$app->db->createCommand(' delete from `req_detail_wide_t` where  `type` = :type ', [':type' => self::TYPE_LOCAL_CONS])->execute();
			Yii::$app->db->createCommand($query)->execute();
		}
		if (!$error)
			$transaction->commit();
		else
			$transaction->rollBack();
		$end_result = microtime(true);
		$dur_result = $end_result - $start_result;
		echo "\nResult: " . round($dur_result, 5) . " sec\n";
		// end of result
	}

	public function actionSemiDaily()
	{
		$this->iniSet();
		$period = Helpers::getPeriodFull();

		$this->prepareData('S');


		// ---------------------------------------
		// result
		$start_result = microtime(true);
		$partModels = Part::find()->where([
			'status' => Part::STATUS_ACTIVE,
			'contract_source_id' => Yii::$app->params['semi_contract_source_ids']
		])->all();
		$transaction = Yii::$app->db->beginTransaction();

		$result = [];
		$error = false;
		if (is_array($partModels) and count($partModels) > 0) {
			foreach ($partModels as $pmodel) {
				$modelReq = new Reqt();
				$modelReq->type = self::TYPE_LOCAL_SEMI;
				$modelReq->part_id = $pmodel->id;
				$modelReq->whbal = $this->stock_result[0][$pmodel->id];
				$modelReq->linebal = $this->stock_result[1][$pmodel->id];
				$modelReq->semistock = $this->stock_result[9][$pmodel->id];
				$modelReq->pending = $this->pstock_result[0][$pmodel->id] + $this->pstock_result[1][$pmodel->id];
				$modelReq->outsourcing = $this->stock_result[2][$pmodel->id] + $this->stock_result[3][$pmodel->id];
				$modelReq->arrive = $this->arrive_result[$pmodel->id];
				$modelReq->calc_at = date('Y-m-d H:i:s');
				$bal = $modelReq->whbal + $modelReq->linebal + $modelReq->outsourcing + $modelReq->pending + $modelReq->arrive + $modelReq->semistock;
				// bu fg stock balance ga qo'shilmaydi
				$modelReq->fgstock = $this->stock_fg_result[$pmodel->id];

				// 09.04.2020 da fgstock ham balance ga qo'shilishi kerakligi aytildi
				// $bal += $modelReq->fgstock;
				
				$order = 0;
				if ($modelReq->save()) {
					$days_count = 0;
					$stop = false;
					$cols = '';
					$values_wide = '';
					$values_plan = '';
					// $hasPlan = false;
					foreach ($period as $pdate) {


						$intransit = $this->getIntransit($modelReq->part_id, $pdate);
						$plan = $this->getPlan($modelReq->part_id, $pdate,null,'S');
						// if(!$hasPlan) $hasPlan = ($plan != 0);
						$qty = $bal + $intransit - $plan;
						$bal = $qty;

						if (!$stop) if ($qty >= 0) $days_count++;
						else $stop = true;

						$last_day_of_period = date('Y-m-t', strtotime('+6 month'));
						if ($pdate <= $last_day_of_period) {

							$order++;
							$cols .= "`col" . $order . "`";
							$values_wide .= "" . $qty . "";
							$values_plan .= "" . $plan . "";

							if ($pdate < $last_day_of_period) {
								$cols .= ", ";
								$values_wide .= ", ";
								$values_plan .= ", ";
							}
						}
					}

					// if (!$hasPlan) {
					// 	$modelReq->delete();
					// } else {
						$query = "INSERT INTO `req_detail_wide_t` (`req_id`,`type`," . $cols . ") VALUES (" . $modelReq->id . ", '" . self::TYPE_LOCAL_SEMI . "', " . $values_wide . ")";
						Yii::$app->db->createCommand($query)->execute();
						$query = "INSERT INTO `req_detail_plan_t` (`req_id`,`type`," . $cols . ") VALUES (" . $modelReq->id . ", '" . self::TYPE_LOCAL_SEMI . "', " . $values_plan . ")";
						Yii::$app->db->createCommand($query)->execute();
						
						$avg_usage = $modelReq->part->averageUsage;
						$doh = 0;
						if($modelReq->totalstock > 0){
							if($avg_usage > 0){
								$doh = $modelReq->totalstock / $avg_usage;
								if($doh > 307) $doh = 307;
							}else{
								$doh = 307;
							}
						}else{
							$doh = 0;
						}

						$query = "UPDATE `req_t` SET `days_count` = " . $days_count . ", `doh` = " . $doh . " WHERE `id` = " . $modelReq->id . ";";
						Yii::$app->db->createCommand($query)->execute();
					// }
					

					
				} else {
					$error = true;
				}
			}
		}
		$query = "select days_count from req_t where type = :type group by days_count order by days_count;";
		$union_query = "";
		$day_counts = Yii::$app->db->createCommand($query, [':type' => self::TYPE_LOCAL_SEMI])->queryAll();
		if (is_array($day_counts) and count($day_counts) > 0) {
			$t = 0;
			foreach ($day_counts as $day_count) {
				$t++;
				$order_col_number = ($day_count['days_count'] < self::CNT_COLUMNS) ? $day_count['days_count'] + 1 : self::CNT_COLUMNS;
				$union_query .= "
                    select * from
                    (
                        select r.id rid, r.part_id, r.whbal, r.linebal, r.outsourcing,r.pending, r.arrive, r.days_count, r.calc_at, w.* 
                        from req_t r, req_detail_wide_t w 
                        where r.type = '" . self::TYPE_LOCAL_SEMI . "' and r.id = w.req_id and r.days_count = " . $day_count['days_count'] . "
                        order by  w.col" . $order_col_number . "
                        limit 1000
                    ) t" . $day_count['days_count'] . "                
                ";
				if (is_array($day_counts) and count($day_counts) != $t) {
					$union_query .= " union ";
				}
			}
			$union_result = Yii::$app->db->createCommand($union_query)->queryAll();
			$values = '';
			$i = 0;
			foreach ($union_result as $ures) {
				$i++;
				$values .= "(" . $ures['req_id'] . ", '" . self::TYPE_LOCAL_SEMI . "',";
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
			$query = "INSERT INTO `req_detail_wide_t` (`req_id`,`type`, " . $cols . ") VALUES " . $values . ";";
			Yii::$app->db->createCommand(' delete from `req_detail_wide_t` where  `type` = :type ', [':type' => self::TYPE_LOCAL_SEMI])->execute();
			Yii::$app->db->createCommand($query)->execute();
		}
		if (!$error)
			$transaction->commit();
		else
			$transaction->rollBack();
		$end_result = microtime(true);
		$dur_result = $end_result - $start_result;
		echo "\nResult: " . round($dur_result, 5) . " sec\n";
		// end of result
	}


	public function actionBalance()
	{

		// balance
		$start_balance = microtime(true);

		$periodDaily = Helpers::getPeriodFull();
		$periodWeekly = Helpers::getPeriodWeek2();

		$dates = [
			1 => date('Y-m-t'),
			2 => date('Y-m-t', strtotime('+1 month')),
			3 => date('Y-m-t', strtotime('+2 month')),
			4 => date('Y-m-t', strtotime('+3 month')),
		];



		$resultD = $this->getSupplierAmount(self::TYPE_DAILY,array_search($dates[1], $periodDaily)+1,array_search($dates[2], $periodDaily)+1);
		$resultW = $this->getSupplierAmount(self::TYPE_WEEKLY,$this->searchKeyByDate($dates[3], $periodWeekly)+1,$this->searchKeyByDate($dates[4], $periodWeekly)+1);


		$keys = [];
		foreach ($resultD as $row) $keys[] = $row['coun_supp_pterm'];
		foreach ($resultW as $row) $keys[] = $row['coun_supp_pterm'];
		$keys = array_unique($keys);

	
		$data = [];
		foreach ($keys as $key) {
			
			$totalFirstAmount = 0;
			$totalSecondAmount = 0;
			$totalThirdAmount = 0;
			$totalFourthAmount = 0;
			
			foreach ($resultD as $row) {
				if ($key == $row['coun_supp_pterm']) {
					$totalFirstAmount += $row['aAmount'];
					$totalSecondAmount += $row['bAmount'];
				}
			}

			foreach ($resultW as $row) {
				if ($key == $row['coun_supp_pterm']) {
					$totalThirdAmount += $row['aAmount'];
					$totalFourthAmount += $row['bAmount'];
				}
			}

			$totalSecondAmount = $totalSecondAmount - $totalFirstAmount;
			$totalThirdAmount = $totalThirdAmount - $totalSecondAmount;
			$totalFourthAmount = $totalFourthAmount - $totalThirdAmount;

			list($country, $supplier, $supplier_id, $payment_term_id, $currency_id, $deliveryTerm) = explode('|', $key);

			$data[] = [
				'country' => $country,
				'supplier' => $supplier, 
				'supplier_id' => $supplier_id, 
				'payment_term_id' => $payment_term_id, 
				'currency_id' => $currency_id, 
				'deliveryTerm' => $deliveryTerm,
				'debt' => [
					1 => round($totalFirstAmount),
					2 => round($totalSecondAmount),
					3 => round($totalThirdAmount),
					4 => round($totalFourthAmount)
				]
				
			];
		}

		$this->updateCoverageBalance($data, $dates);	

		$end_balance = microtime(true);
		$dur_balance = $end_balance - $start_balance;
		echo "\nBalance: " . round($dur_balance, 5) . " sec\n";
		// ---------------------------------------

		
	}

	protected function updateCoverageBalance($data, $dates){
		
		

		foreach ($data as $row) {
			foreach ($dates as $key => $date) {
				$coverageBalance = CoverageBalance::find()
				->where([
					'and',
					['supplier_id' => $row['supplier_id']],
					['payment_term_id' => $row['payment_term_id']],
					['period' => $date]
				])->one();

				if($coverageBalance){
					// Ma'lumot bor
					$coverageBalance->currency_id = $row['currency_id'];
					$coverageBalance->debt = $row['debt'][$key];
					if(!$coverageBalance->save()){
						echo '<pre>';
						print_r($coverageBalance->errors);
						echo '</pre>';
						die;
					}
				}else{
					// Ma'lumot yo'q, yangi ma'lumot
					$coverageBalance = new CoverageBalance();
					$coverageBalance->supplier_id = $row['supplier_id'];
					$coverageBalance->payment_term_id = $row['payment_term_id'];
					$coverageBalance->currency_id = $row['currency_id'];
					$coverageBalance->period = $date;
					$coverageBalance->debt = $row['debt'][$key];
					if(!$coverageBalance->save()){
						echo '<pre>';
						print_r($coverageBalance->errors);
						echo '</pre>';
						die;
					}

				}
			}
		}

	}	

	protected function getSupplierAmount($type,$firstDate,$secondDate){

		$rateUSD = CurrencyRate::currentRate(Currency::findOneCurrencyCode('USD')->id);
		$rateEUR = CurrencyRate::currentRate(Currency::findOneCurrencyCode('EUR')->id);
		$rateRUB = CurrencyRate::currentRate(Currency::findOneCurrencyCode('RUB')->id);
		
		$query = ReqDetailWide::find()
			->where([
				'and',
				['type' => $type]
			]);
		$dailyCovDetail = $query->all();
		

		
		$result = [];
		foreach ($dailyCovDetail as $crow) {

			if($crow->{'col' . $firstDate} >= 0 and $crow->{'col' . $secondDate} >= 0) continue;
			//if($crow->{'col' . $secondDate} >= 0) continue;
			
			$actualContract = $crow->req->part->getActualContract();
			$supplierModel = $actualContract->contract->supplier ?? '';
			$supplier_id = $actualContract->contract->supplier_id ?? '';

			if(empty($supplier_id)) continue;

			$countryCode = $supplierModel->countryCode ?? '';
			$paymentTerm = $actualContract->contract->paymentTerm->name ?? '';
			$payment_term_id = $actualContract->contract->payment_term_id ?? null;

			if(empty($payment_term_id)) continue;

			

			// Agar postavshik UZ bo'lsa hisobga olmaymiz
			if (($countryCode->alpha_2 ?? '') == 'UZ') continue;

			$price = $actualContract->price ?? 0;
			$currency_id = $actualContract->contract->currency_id ?? '';
			$currency = $actualContract->contract->currency->code ?? '';
			$priceUZS = 0;
			switch ($currency) {
				case 'EUR':
					$priceUZS = $price * $rateEUR;
					break;
				case 'RUB':
					$priceUZS = $price * $rateRUB;
					break;
				case 'USD':
						$priceUZS = $price * $rateUSD;
						break;
				case 'UZS':
					$priceUZS = $price;
					break;
			}
			$supplier = $supplierModel->name ?? '';
			$country = $countryCode->name ?? '';
			
			$aAmount = 0;
			$bAmount = 0;

			if ($crow->{'col' . $firstDate} < 0) $aAmount = abs($crow->{'col' . $firstDate}) * $priceUZS;
			if ($crow->{'col' . $secondDate} < 0) $bAmount = abs($crow->{'col' . $secondDate}) * $priceUZS;
			
			// mln.sum
			$aAmount = $aAmount / 1000000;
			$bAmount = $bAmount / 1000000;

			$result[] = [
				'coun_supp_pterm' => $country . '|' . $supplier . '|' . $supplier_id . '|' . $payment_term_id . '|' . $currency_id . '|' . $paymentTerm,
				'country' => $country,
				'supplier' => $supplier,
				'supplier_id' => $supplier_id,
				'payment_term_id' => $payment_term_id,
				'currency_id' => $currency_id,
				'payment_term' => $paymentTerm,
				'part' => $crow->req->part->part_no,
				'col_number' => $firstDate,
				'qty' => abs($crow->{'col' . $firstDate}),
				'price' => $priceUZS,
				'aAmount' => $aAmount ?? 0,
				'bAmount' => $bAmount ?? 0
			];

			
		}
		return $result;

	}

	protected function searchKeyByDate($date, $array)
	{
		foreach ($array as $key => $val) {
			if ($val['to'] === $date) {
				return $key;
			}
		}
		return null;
	}

	protected function prepareData($wtype = 'R')
	{

		// stock
		$start_stock = microtime(true);
		$query = "
            
        select 
         part_id, warehouse_type, sum(qty) qty
         from
        (
        select 
            st.part_id,
          CASE st.is_semi
            WHEN '1' THEN '9'
            WHEN '0' THEN wh.warehouse_type
            END as 'warehouse_type',
          st.qty
        from 
            (
                select 
                  b.sub_part_id part_id, a.warehouse_id, '1' is_semi, (a.qty * b.usage_qty) qty 
                from 
                  stock a, 
                  (select part_id, sub_part_id, sum(usage_qty) usage_qty, warehouse_id from part_part_wide where type = '" . $wtype . "' group by part_id, sub_part_id,  warehouse_id) b
                where 
                  a.part_id = b.part_id



                union all 

                select part_id , warehouse_id, '0' is_semi, qty
                from stock
                where part_id not in (select part_id from part_part_wide where type = '" . $wtype . "')

            ) st, 
            (select * from warehouse where id not in (" . implode(',', array_merge(Yii::$app->params['fg_wh_ids'], Yii::$app->params['damage_wh_ids'])) . ")) wh,
            (select * from part where status = 1) pt
        where
            st.warehouse_id = wh.id and
            st.part_id = pt.id

        ) xx
        group by
            part_id, warehouse_type
      ";
		$stock = Yii::$app->db->createCommand($query)->queryAll();
		$part = [];
		$whtype = [];
		$stock_result = [];
		foreach ($stock as $st) {
			$part[] = $st['part_id'];
			$whtype[] = $st['warehouse_type'];
		}
		$part = array_unique($part);
		$whtype = array_unique($whtype);
		foreach ($whtype as $t)
			foreach ($part as $p)
				foreach ($stock as $st)
					if ($st['part_id'] == $p and $st['warehouse_type'] == $t) {
						$stock_result[$t][$p] = $st['qty'];
						break;
					}

		$this->stock_result = $stock_result;

		$end_stock = microtime(true);
		$dur_stock = $end_stock - $start_stock;
		echo "\nStock: " . round($dur_stock, 5) . " sec\n";
		// end of stock

		// pending stock
		$start_pstock = microtime(true);
		$query = "
            
        select
              part_id, is_semi, sum(qty) qty
              from
             (
             select
                 st.part_id, st.is_semi, st.qty
             from
                 (
                     select
                             b.sub_part_id part_id, '1' is_semi, (a.qty * b.usage_qty) qty
                     from
                             (
                               select dt.part_id,dt.qty from document dc, document_detail dt 
                   where dc.id = dt.document_id and dc.status = 0
                 ) a,
                             (select part_id, sub_part_id, sum(usage_qty) usage_qty from part_part_wide where type = '" . $wtype . "' group by part_id, sub_part_id) b
                     where
                             a.part_id = b.part_id

                     union all

                     select part_id , '0' is_semi, qty
                     from (
                       select dt.part_id,dt.qty from document dc, document_detail dt 
               where dc.id = dt.document_id and dc.status = 0
             ) a
                     where part_id not in (select part_id from part_part_wide where type = '" . $wtype . "' )

                 ) st,
                 (select * from part where status = 1) pt
             where
                 st.part_id = pt.id

             ) xx
             group by
                 part_id, is_semi
      ";
		$pstock = Yii::$app->db->createCommand($query)->queryAll();
		$part = [];
		$semi = [];
		$pstock_result = [];
		foreach ($pstock as $st) {
			$part[] = $st['part_id'];
			$semi[] = $st['is_semi'];
		}
		$part = array_unique($part);
		$semi = array_unique($semi);
		foreach ($semi as $s)
			foreach ($part as $p)
				foreach ($pstock as $st)
					if ($st['part_id'] == $p and $st['is_semi'] == $s) {
						$pstock_result[$s][$p] = $st['qty'];
						break;
					}
		$this->pstock_result = $pstock_result;
		$end_pstock = microtime(true);
		$dur_pstock = $end_pstock - $start_pstock;
		echo "\nStock pending: " . round($dur_pstock, 5) . " sec\n";
		// end of pstock

		// stock_fg
		// bu qism finished good wh lardagi tayyor detallarni componentlarga parchalab chiqadi
		// lekin bu keyinchalik ostatkaga qoshilmaydi, alohida ustunda info sifatida turadi
		$start_stock_fg = microtime(true);
		$query = "
                select
                        b.sub_part_id part_id, sum((a.qty * b.usage_qty)) qty
                from
                        stock a,
                        (select part_id, sub_part_id, sum(usage_qty) usage_qty, warehouse_id from part_part_wide  where type = '" . $wtype . "' group by part_id, sub_part_id,  warehouse_id) b
                where
                        a.part_id = b.part_id
                        and a.warehouse_id in (" . implode(',', Yii::$app->params['fg_wh_ids']) . ")
                group by         
                		b.sub_part_id

                union all

                select part_id , sum(qty) qty
                from stock
                where 
                	part_id not in (select part_id from part_part_wide where type = '" . $wtype . "' )
                	and warehouse_id in (" . implode(',', Yii::$app->params['fg_wh_ids']) . ")
            	group by         
            		part_id
                    ";
		$stock_fg = Yii::$app->db->createCommand($query)->queryAll();
		$part = [];
		$stock_fg_result = [];
		foreach ($stock_fg as $st) {
			$part[] = $st['part_id'];
		}
		$part = array_unique($part);
		foreach ($part as $p)
			foreach ($stock_fg as $st)
				if ($st['part_id'] == $p) {
					$stock_fg_result[$p] = $st['qty'];
					break;
				}
		$this->stock_fg_result = $stock_fg_result;
		$end_stock_fg = microtime(true);
		$dur_stock_fg = $end_stock_fg - $start_stock_fg;
		echo "\nStock FG: " . round($dur_stock_fg, 5) . " sec\n";
		// end of stock_fg

		// intransit
		$start_intransit = microtime(true);
		$query = "
            select app_arr_at estdate, part_id, sum(qty) qty from 
                    (
                    select * from container_invoice 
                    where 
                            app_arr_at is not null and
                            app_arr_at >= CURDATE() and
                            shipped_at is not null and
                            arrived_at is null
                    ) ci,
                    (select * from invoice_detail) ind,
                    (select * from part where status = 1) pt
            where 
                    ci.id = ind.cont_inv_id and
                    ind.part_id = pt.id
            group by app_arr_at, part_id
        ";
		$intransit = Yii::$app->db->createCommand($query)->queryAll();
		$part = [];
		$estdate = [];
		$intransit_result = [];
		foreach ($intransit as $tr) {
			$part[] = $tr['part_id'];
			$estdate[] = $tr['estdate'];
		}
		$part = array_unique($part);
		$estdate = array_unique($estdate);
		foreach ($estdate as $d)
			foreach ($part as $p)
				foreach ($intransit as $tr)
					if ($tr['part_id'] == $p and $tr['estdate'] == $d) {
						$intransit_result[$d][$p] = $tr['qty'];
						break;
					}
		$this->intransit_result = $intransit_result;
		$end_intransit = microtime(true);
		$dur_intransit = $end_intransit - $start_intransit;
		echo "\nIntransit: " . round($dur_intransit, 5) . " sec\n";
		// end of intransit

		// arrive
		$start_arrive = microtime(true);
		$query = "
            select part_id, sum(qty) qty from 
                    (
                    select * from container_invoice 
                    where 
                            arrived_at <= CURDATE() and
                            received_at is null
                    ) ci,
                    (select * from invoice_detail) ind,
                    (select * from part where status = 1) pt
            where 
                    ci.id = ind.cont_inv_id and 
                    ind.part_id = pt.id
            group by part_id
        ";
		$arrive = Yii::$app->db->createCommand($query)->queryAll();
		$part = [];
		$arrive_result = [];
		foreach ($arrive as $ar) {
			$part[] = $ar['part_id'];
		}
		$part = array_unique($part);
		foreach ($part as $p)
			foreach ($arrive as $ar)
				if ($ar['part_id'] == $p) {
					$arrive_result[$p] = $ar['qty'];
					break;
				}
		$this->arrive_result = $arrive_result;
		$end_arrive = microtime(true);
		$dur_arrive = $end_arrive - $start_arrive;
		echo "\nArrive: " . round($dur_arrive, 5) . " sec\n";
		// end of arrive



	}

	protected function clearingData()
	{

		// clearing
		$start_clearing = microtime(true);

		Yii::$app->db->createCommand('SET foreign_key_checks = 0')->execute();
		Yii::$app->db->createCommand()->truncateTable('req_t')->execute();
		Yii::$app->db->createCommand()->truncateTable('req_detail_wide_t')->execute();
		Yii::$app->db->createCommand()->truncateTable('req_detail_plan_t')->execute();
		Yii::$app->db->createCommand('SET foreign_key_checks = 1')->execute();



		$end_clearing = microtime(true);
		$dur_clearing = $end_clearing - $start_clearing;
		echo "\nClearing: " . round($dur_clearing, 5) . " sec\n";
		// ---------------------------------------
	}

	public function actionCopyingData()
	{

		// copying
		$start_clearing = microtime(true);

		Yii::$app->db->createCommand('SET foreign_key_checks = 0')->execute();
		Yii::$app->db->createCommand()->truncateTable('req')->execute();
		Yii::$app->db->createCommand()->truncateTable('req_detail_wide')->execute();
		Yii::$app->db->createCommand()->truncateTable('req_detail_plan')->execute();
		Yii::$app->db->createCommand('SET foreign_key_checks = 1')->execute();

		Yii::$app->db->createCommand('insert into req select * from req_t')->execute();
		Yii::$app->db->createCommand('insert into req_detail_wide select * from req_detail_wide_t')->execute();
		Yii::$app->db->createCommand('insert into req_detail_plan select * from req_detail_plan_t')->execute();



		$end_clearing = microtime(true);
		$dur_clearing = $end_clearing - $start_clearing;
		echo "\nCopying: " . round($dur_clearing, 5) . " sec\n";
		// ---------------------------------------
	}

	protected function preparePlan()
	{

		$from = date('Y-m-d');
		$to = date('Y-m-t', strtotime('+10 month'));

		// plan
		$start_plan = microtime(true);

		Yii::$app->db->createCommand('SET foreign_key_checks = 0')->execute();
		Yii::$app->db->createCommand()->truncateTable('production_plan_sub')->execute();
		Yii::$app->db->createCommand('SET foreign_key_checks = 1')->execute();

		$query = "
            insert into production_plan_sub 
            
            select 
                production_date plandate, sub_part_id part_id, type, sum(pl.target_qty * pp.usage_qty) qty  
            from
            (
                select production_date, part_id, sum(target_qty) target_qty  
                from production_plan 
                where production_date between '" . $from . "' and '" . $to . "'  and target_qty <> 0
                group by production_date, part_id
            ) pl,
            (
							select part_id, sub_part_id, type, sum(usage_qty) usage_qty
							from part_part_wide
							group by part_id, sub_part_id, type
            ) pp,
            (
                select id from part where status = " . Part::STATUS_ACTIVE . " and (state = " . Part::STATE_FINISHED . ") 
            ) pt
            where 
                pl.part_id = pp.part_id and
                pp.part_id = pt.id
                
								group by production_date, sub_part_id, type
								order by production_date, sub_part_id, type
				";
				

		Yii::$app->db->createCommand($query)->execute();

		$end_plan = microtime(true);
		$dur_plan = $end_plan - $start_plan;
		echo "\nPlan: " . round($dur_plan, 5) . " sec\n";
		// ---------------------------------------
	}

	protected function getIntransit($part_id, $from, $to = null)
	{
		$to = (empty($to)) ? $from : $to;
		$begin = new DateTime($from);
		$end = new DateTime($to);
		$end = $end->modify('+1 day');
		$daterange = new DatePeriod($begin, new DateInterval('P1D'), $end);
		$qty = 0;
		foreach ($daterange as $date) {
			$qty += $this->intransit_result[$date->format("Y-m-d")][$part_id];
		}
		return $qty;
	}

	protected function getPlan($part_id, $from, $to = null,$wtype = 'R')
	{

		$query = "
        	select part_id, sum(qty) qty from production_plan_sub
            where 
              plandate between :from and :to and
							part_id = :part_id 
							and type = '" . $wtype . "'
            group by part_id
        ";
		$plan = Yii::$app->db->createCommand($query, [':from' => $from, ':to' => (empty($to)) ? $from : $to, ':part_id' => $part_id])->queryOne();
		return ($plan) ? $plan['qty'] : 0;
	}
}
