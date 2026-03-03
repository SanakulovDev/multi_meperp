<?php
	namespace app\controllers;

	use app\models\ProductLine;
	use Yii;
	use yii\data\Pagination;

	/**
		* ReportController implements the CRUD actions for Report model.
		*/
	class MonitorController extends AppController{
		/**
			* Lists all Report models.
			* @return mixed
			*/
		public function actionShop($shop){
			$this->layout = 'req';
			$res = Yii::$app->helpers->getPeriod();
			$connection = Yii::$app->getDb();
			$productLine = ProductLine::find()->where(['id' => $shop])->one();
			$sql = "SELECT 
                p.id, 
                p.part_no, 
                p.part_color, 
                p.part_name, 
                IFNULL(pp.target_qty,0) as target_qty, 
                IFNULL(po.actual,0) as actual, 
                target_qty-actual as diff 
             FROM part p 
						 LEFT JOIN (SELECT * FROM `production_plan` WHERE production_date='".$res['productionDate']."' AND shift=".$res['shift'].")pp ON p.id=pp.part_id
						 LEFT JOIN (SELECT part_id, sum(quantity) as actual FROM `production_order` WHERE is_label=2 AND created_at>=UNIX_TIMESTAMP('".$res['start_at']."') AND created_at<UNIX_TIMESTAMP('".$res['end_at']."') group by part_id)po ON p.id=po.part_id  
						 WHERE (target_qty>0 OR actual>0) AND p.product_line_id=".$shop." ORDER BY diff DESC";
			$sqlCount = "SELECT count(*) FROM ($sql)t";
			$pages = new Pagination(['totalCount' => $connection->createCommand($sqlCount)->queryScalar(), 'pageSize' => 4]);
			$models = $connection->createCommand($sql." LIMIT ".$pages->limit." OFFSET ".$pages->offset)->queryAll();
			return $this->render('shop', compact('productLine', 'models', 'pages'));
		}

		public function actionLine($shop){
			$this->layout = 'req';
			$res = Yii::$app->helpers->getPeriod();
			$connection = Yii::$app->getDb();
			$productLine = ProductLine::find()->where(['id' => $shop])->one();
			$sql = "SELECT p.id, p.part_no, p.part_color, p.part_name, IFNULL(pp.target_qty,0) as target_qty, IFNULL(po.actual,0) as actual, target_qty-actual as diff FROM part p 
						LEFT JOIN (SELECT * FROM `production_plan` WHERE production_date='".$res['productionDate']."' AND shift=".$res['shift'].")pp ON p.id=pp.part_id
						LEFT JOIN (SELECT part_id, sum(quantity) as actual FROM `production_order` WHERE is_label=2 AND created_at>=UNIX_TIMESTAMP('".$res['start_at']."') AND created_at<UNIX_TIMESTAMP('".$res['end_at']."') group by part_id)po ON p.id=po.part_id  
						WHERE (target_qty>0 OR actual>0) AND p.product_line_id=".$shop." ORDER BY diff DESC";
			$sqlCount = "SELECT count(*) FROM ($sql)t";
			$pages = new Pagination(['totalCount' => $connection->createCommand($sqlCount)->queryScalar(), 'pageSize' => 10]);
			$models = $connection->createCommand($sql." LIMIT ".$pages->limit." OFFSET ".$pages->offset)->queryAll();
			return $this->render('line', compact('productLine', 'models', 'pages'));
		}

		public function actionDaily($line){
			$this->layout = 'req';
			$connection = Yii::$app->getDb();
			$prodDays = Yii::$app->helpers->getPeriod();
			$need_date = $prodDays['productionDate'];
			$need_date_1 = date('Y-m-d', strtotime($need_date.' + 1 days'));
			$productLine = ProductLine::find()->where(['id' => $line])->one();
      $sql = "SELECT part.id AS part_id, part.part_no, left(part.part_name,20) AS part_name,
								IFNULL(d_plan1.d_plan_qty,'-') AS d_plan1_qty, 
								IFNULL(d_actual1.quantity,'-') as actual1, 
								IFNULL(d_actual1.quantity,0) - IFNULL(d_plan1.d_plan_qty,0) AS d_balance1,
								IFNULL(d_plan2.d_plan_qty,'-') AS d_plan2_qty, 
								IFNULL(d_actual2.quantity,'-') as actual2, 
								IFNULL(d_actual2.quantity,0) - IFNULL(d_plan2.d_plan_qty,0) AS d_balance2,
								IFNULL(d_plan2.d_plan_qty,0) + IFNULL(d_plan1.d_plan_qty,0) AS d_all_plan,
								IFNULL(d_actual1.quantity,0) + IFNULL(d_actual2.quantity,0) AS d_all_actual,
								IFNULL(d_actual1.quantity,0) + IFNULL(d_actual2.quantity,0) - (IFNULL(d_plan2.d_plan_qty,0) + IFNULL(d_plan1.d_plan_qty,0)) AS d_all_balance
							FROM part 
							LEFT JOIN (SELECT part_id, SUM(target_qty) as d_plan_qty FROM production_plan WHERE production_date = '$need_date' AND shift = 1 GROUP BY part_id) d_plan1 ON part.id = d_plan1.part_id
							LEFT JOIN (SELECT part_id, SUM(target_qty) as d_plan_qty FROM production_plan WHERE production_date = '$need_date' AND shift = 2 GROUP BY part_id) d_plan2 ON part.id = d_plan2.part_id
							LEFT JOIN (SELECT part_id, SUM(quantity) as quantity FROM production_order 
										WHERE is_label=2 AND
											created_at >= UNIX_TIMESTAMP('$need_date 08:00:00') AND 
											created_at < UNIX_TIMESTAMP('$need_date 20:00:00') GROUP BY part_id 
										) d_actual1 ON part.id = d_actual1.part_id
							LEFT JOIN (SELECT part_id, SUM(quantity) as quantity  FROM production_order 
										WHERE is_label=2 AND
										created_at >= UNIX_TIMESTAMP('$need_date 20:00:00') AND 
										created_at <  UNIX_TIMESTAMP('$need_date_1 08:00:00') 
										GROUP BY part_id 
									) d_actual2	ON part.id = d_actual2.part_id 
							WHERE (d_plan1.d_plan_qty>0 OR d_plan2.d_plan_qty>0 OR d_actual1.quantity>0 OR d_actual2.quantity>0) AND part.status=1 AND part.state>0 AND part.product_line_id=$line";
			$sqlCount = "SELECT count(*) FROM ($sql)t";
			$pages = new Pagination(['totalCount' => Yii::$app->db->createCommand($sqlCount)->queryScalar(), 'pageSize' => 10]);
			$data = $connection->createCommand($sql." LIMIT ".$pages->limit." OFFSET ".$pages->offset)->queryAll();
			return $this->render('daily', compact('productLine', 'data', 'pages', 'need_date'));
		}
	}
