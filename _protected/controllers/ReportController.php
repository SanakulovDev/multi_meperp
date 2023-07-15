<?php
namespace app\controllers;

use app\components\Helpers;
use app\console\controllers\CoverageController;
use app\console\controllers\CoverageVehicleController;
use app\models\CoverageBalance;
use app\models\CoverageVehicleDetail;
use app\models\Currency;
use app\models\CurrencyRate;
use app\models\Defect;
use app\models\FactByHourForm;
use app\models\Part;
use app\models\PartActiveLog;
use app\models\ProductionOrder;
use app\models\ProductionPlan;
use app\models\ProductLine;
use app\models\ProductModel;
use app\models\Report;
use app\models\ReportForm;
use app\models\ReportGroup;
use app\models\ReportSearch;
use app\models\Req;
use app\models\ReqDetailPlan;
use app\models\ReqDetailWide;
use app\models\VehicleCoverageInput;
use app\models\Visitor;
use app\models\Customer;
use app\models\VisitorSearch;
use app\rbac\models\Role;
use app\services\ReportService;
use app\models\ReportFaktProdajMonth;

use yii\helpers\Url;
use DateInterval;
use DatePeriod;
use DateTime;
use Yii;
use yii\base\DynamicModel;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * ReportController implements the CRUD actions for Report model.
 */
class ReportController extends AppController {

  /**
   * Lists all Report models.
   *
   * @return mixed
   */
  protected $intransit_result;

  private $_reportService;

  public function init() {
    $this->_reportService = new ReportService();
    parent::init();
  }

  public function checkReportAccess($action = null) {
    if(!isset($action)) {
      $action = $this->action->id;
    }
    $report = Report::find()
                    ->where(["action" => $action])
                    ->one();
    if(!in_array(Yii::$app->user->identity->id, ArrayHelper::map($report->users, "id", "id"))) {
      throw new ForbiddenHttpException(Yii::t("app", "You don`t have access to view this report."));
      return $this->redirect(["index"]);
    }
  }

  public function actionIndex() {
    $identiy = Yii::$app->user->identity;
    $query = ReportGroup::find();
    $query->joinWith([
      "reports" => function($query) {
        $query->onCondition(["report.action" => ArrayHelper::map(Yii::$app->user->identity->reports, "id", "action")]);
      },
    ]);
    $reportGroupsT = $query->orderBy(["order" => SORT_ASC])->all();
    $reportGroups = [];
    foreach($reportGroupsT as $key => $rg) {
      if($rg->id == 6) {
        continue;
      } // Visitors
      if(count($rg->reports) == 0) {
        continue;
      }
      $reportGroups[] = $rg;
    }
    //      echo '<pre>';
    //  print_r($reportGroups);
    //  echo '</pre>';
    //  die;
    return $this->render("index", [
      "reportGroups" => $reportGroups,
    ]);
  }

  public function actionIndexBoxes() {
    $searchModel = new ReportSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    if(!in_array(Yii::$app->user->identity->rolename, ["admin", "superadmin"])) {
      $dataProvider->query->andWhere(["not in", "action", ["visitors", "all-visitors"]]);
    }
    $dataProvider->query->andWhere([
      "in",
      "action",
      ArrayHelper::map(Yii::$app->user->identity->reports, "id", "action"),
    ]);
    $dataProvider->sort->defaultOrder = ["list_order" => SORT_ASC];
    return $this->render("index-boxes", [
      "reports" => $dataProvider->getModels(),
    ]);
  }

  public function actionIndexCards() {
    return $this->render("index-cards");
  }

  /**
   * Finds the Report model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   *
   * @param int $id
   *
   * @return Report the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id) {
    if(($model = Report::findOne($id)) !== null) {
      return $model;
    }
    throw new NotFoundHttpException(Yii::t("app", "The requested page does not exist."));
  }

  public function actionProductionPlan() {
    return $this->redirect(["production-fact"]);
    $this->checkReportAccess();
    $this->layout = "req";
    $downloadFileNameJV = rtrim(Helpers::downloadFileName("production-plan", "1"), ".1");
    $downloadFileNameOEM = rtrim(Helpers::downloadFileName("OEM-plan", "1"), ".1");
    $this->checkReportAccess();
    ini_set("memory_limit", "1024M");
    $model = new ProductionPlan();
    $need_month = isset($_POST["need_month"]) ? ($need_month = $_POST["need_month"]) : date("Y-m");
    $need_monthOEM = isset($_POST["need_monthOEM"]) ? ($need_monthOEM = $_POST["need_monthOEM"]) : date("Y-m");
    if(isset($_POST["btnJv_isActive"])) {
      $btnJv_isActive = $_POST["btnJv_isActive"] > 0 ? $_POST["btnJv_isActive"] : 0;
    }
    if(isset($_POST["btnOem_1_isActive"])) {
      $btnOem_1_isActive = $_POST["btnOem_1_isActive"] > 0 ? $_POST["btnOem_1_isActive"] : 0;
    }
    if(isset($_POST["btnOem_2_isActive"])) {
      $btnOem_2_isActive = $_POST["btnOem_2_isActive"] > 0 ? $_POST["btnOem_2_isActive"] : 0;
    }
    $month_end = date("Y-m-t", strtotime($need_month));
    $month_end_date = date("t", strtotime($month_end));
    $where =
      "production_plan.production_date between '".
      $need_month.
      "-01' and '". 
      $need_month.
      "-".
      $month_end_date.
      "' ";
    $part_id = isset($_POST["ProductionPlan"]["part_id"]) ? $_POST["ProductionPlan"]["part_id"] : null;
    if($part_id > 0) {
      $where .= " and production_plan.part_id=".$part_id;
    }
    $warehouse_id = isset($_POST["ProductionPlan"]["warehouse_id"]) ? $_POST["ProductionPlan"]["warehouse_id"] : null;
    if($warehouse_id > 0) {
      $where .= " and production_plan.warehouse_id=".$warehouse_id;
    }
    $product_model = isset($_POST["product_model"]) ? $_POST["product_model"] : null;

    $dateCond = "
    
    and
    (
      (part_active_log.begin_date >= '".$need_month."-01' and part_active_log.end_date >= '".$need_month."-".$month_end_date."') or
      
      (part_active_log.begin_date <= '".$need_month."-01' and part_active_log.end_date >= '".$need_month."-01') or
  
      (part_active_log.begin_date <= '".$need_month."-01' and part_active_log.end_date >= '".$need_month."-".$month_end_date."') or
  
      (part_active_log.begin_date >= '".$need_month."-01' and part_active_log.end_date <= '".$need_month."-".$month_end_date."') 
      
   )
    
    ";

    $where_DB_part_list = 'part_active_log.status='.Part::STATUS_ACTIVE.
      $dateCond.
      " and production_plan.part_id is not null";
    $wherePo = "";
    if($product_model > 0) {
      $wherePo = "product_model.id=".$product_model;
    }
    $dbPartListQuery = PartActiveLog::find()
                                    ->joinWith([
                                      "part" => function($query) {
                                        $query->from(["all_parts" => Part::tableName()]);
                                      },
                                      "part.productionPlans" => function($query) use ($where) {
                                        $query->onCondition($where);
                                      },
                                      "part.productionPlans.warehouse",
                                    ])
                                    ->where($where_DB_part_list)
                                    ->orderBy("production_plan.warehouse_id, production_plan.shift");
      //  $dbPartList = $dbPartListQuery->createCommand()->rawSql;
      //  echo "<pre>"; print_r($dbPartList);echo "</pre>"; die;
    $dbPartList = $dbPartListQuery->all();
    $rec = 1;
    $has_target_qty = false;
    $table_array = [];
    foreach($dbPartList as $prod_list) {
      if($has_target_qty === true) {
        $rec++;
        $has_target_qty = false;
      }
      $prod_plan_list = count($prod_list->part->productionPlans) == 0 ? [] : $prod_list->part->productionPlans;
      $r = 0;
      foreach($prod_plan_list as $items) {
        $arr_prod_plan[$r]["pl_dt"] = $items["production_date"];
        $arr_prod_plan[$r]["shift"] = $items["shift"];
        $arr_prod_plan[$r]["target_qty"] = $items["target_qty"];
        $arr_prod_plan[$r]["wh_name"] = $items->warehouse->name;
        $r++;
      }
      $prod_plan_bydt = Helpers::groupbyKeyFromArray("pl_dt", $arr_prod_plan);
      for($i = 0; $i < $month_end_date; $i++) {
        $prod_sana = date("Y-m-d", strtotime($need_month."-01 +".$i." day"));
        if(count($prod_plan_bydt)) {
          for($j = 0; $j < 2; $j++) {
            if(isset($prod_plan_bydt[$prod_sana]) && isset($prod_plan_bydt[$prod_sana][$j]["pl_dt"])) {
              if($prod_plan_bydt[$prod_sana][$j]["pl_dt"] == $prod_sana) {
                if($prod_plan_bydt[$prod_sana][$j]["target_qty"] > 0) {
                  $has_target_qty = true;
                  $table_array[$rec]["part_no"] = $prod_list->part->part_no ?? null;
                  $table_array[$rec]["wh_nm"] = $prod_plan_bydt[$prod_sana][$j]["wh_name"] ?? null;
    //                  $table_array[$rec]["product_model"] = $prod_list->part->productModel->modelname ?? null;
                  $table_array[$rec][$prod_sana][$j] = $prod_plan_bydt[$prod_sana][$j]["target_qty"] ?? null;
                }
              }
            } //end if sana va smena bo`yisha shart
          } //end for 1,2 smena
        } //end if $prod_plan_bydt exist
      } //oy bo`yicha tsikl
    } //$dbPartList bo`yicha sikl
    /** OEM Monthly bo`yicha */
    $oemMonth = isset($_POST["oemMonth"]) ? ($oemMonth = $_POST["oemMonth"]) : ($oemMonth = date("Y", time()));
    $query =
      "SELECT pm_desc model, LEFT(target_date,7) oy, SUM(quantity) qty FROM
               (
                 SELECT * FROM oem_plan oem left join 
                 (SELECT id pm_id, description pm_desc FROM product_model WHERE is_vehicle=1) pm
                 ON oem.model_id = pm.pm_id
                 WHERE LEFT(oem.target_date,4) = '".
      $oemMonth.
      "' AND pm_id IS NOT NULL
               ) jami               
              GROUP BY pm_desc,LEFT(target_date,7)
              ";
    $result = Yii::$app->db->createCommand($query)->queryAll();
    $oemMonthlyData = [];
    foreach($result as $key => $item) {
      $oemMonthlyData[$item["model"]][$item["oy"]] = $item["qty"];
    }
    /** OEM Daily bo`yicha */
    $query =
      "SELECT pm_desc model, target_date sana, SUM(quantity) qty FROM
             (
               SELECT * FROM oem_plan oem left join 
               (SELECT id pm_id, description pm_desc FROM product_model WHERE is_vehicle = 1) pm
               ON oem.model_id = pm.pm_id
               WHERE LEFT(oem.target_date,7) = '".
      $need_monthOEM.
      "' AND pm_id IS NOT NULL
             ) jami               
            GROUP BY pm_desc, target_date";
    $result = Yii::$app->db->createCommand($query)->queryAll();
    $oemDailyData = [];
    foreach($result as $key => $item) {
      $oemDailyData[$item["model"]][$item["sana"]] = $item["qty"];
    }
    //    echo "<pre>"; print_r($need_monthOEM);echo "</pre>";
    //    die;
    return $this->render("production-plan", [
      "oemDailyData" => $oemDailyData,
      "oemMonthlyData" => $oemMonthlyData,
      "oemMonth" => $oemMonth,
      "table_array" => $table_array,
      "need_month" => $need_month,
      "need_monthOEM" => $need_monthOEM,
      "warehouse_id" => $warehouse_id,
      "btnJv_isActive" => $btnJv_isActive ?? 0,
      "btnOem_1_isActive" => $btnOem_1_isActive ?? 0,
      "btnOem_2_isActive" => $btnOem_2_isActive ?? 0,
      "part_id" => $part_id,
      "model" => $model,
      "downloadFileNameJV" => $downloadFileNameJV,
      "downloadFileNameOEM" => $downloadFileNameOEM,
    ]);
  }

  public function actionProductionPlanActual() {
    $downloadFileName = Helpers::downloadFileName("production-plan-actual", "1");
    $downloadFileName = rtrim($downloadFileName, ".1");
    $this->checkReportAccess();
    $model = new ProductionPlan();
    $need_date = isset($_POST["need_date"]) ? ($need_date = $_POST["need_date"]) : date("Y-m-d");
    $part_id = isset($_POST["ProductionPlan"]["part_id"]) ? $_POST["ProductionPlan"]["part_id"] : null;
    $part_where = "";
    //    echo "<pre>1: "; print_r(substr($need_date,0,7));echo "</pre>";
    $all_part_where = "";
    if($part_id > 0) {
      $part_where = " and id=".$part_id;
      $all_part_where = " and part_id=".$part_id;
    }
    $warehouse_id = isset($_POST["ProductionPlan"]["warehouse_id"]) ? $_POST["ProductionPlan"]["warehouse_id"] : null;
    $warehouse_where = "";
    if($warehouse_id > 0) {
      $warehouse_where = " and warehouse_id=".$warehouse_id;
    }
    $product_line_id = isset($_POST["product_line_id"]) ? $_POST["product_line_id"] : null;
    $product_line_where = "";
    if($product_line_id > 0) {
      $product_line_where = " and product_line_id=".$product_line_id;
    }
    $shift = Yii::$app->params["shifts"];
    $shift1_from = str_replace(":", "", $shift["1"]["0"])."00";
    $shift1_to = str_replace(":", "", $shift["1"]["1"])."00";
    $shift2_from = str_replace(":", "", $shift["2"]["0"]["0"])."00";
    $shift2_to = str_replace(":", "", $shift["2"]["1"]["1"])."00";
    $query =
      "
		SELECT
		part.id part_id, part.part_no, left(part.part_name,20) part_name, wh.id wh_id, wh.name warehouse_nm,
		IFNULL(d_plan1.d_plan1_qty,0) d_plan1_qty,
		d_plan1.plan_comment d_plan1_comment,
		 
		IFNULL(d_actual1.d_actual1_qty,0) d_actual1_qty, 
		IFNULL(d_actual1.d_actual1_qty,0) - IFNULL(d_plan1.d_plan1_qty,0) d_balance1,
		IFNULL(d_plan2.d_plan2_qty,0) d_plan2_qty,
		d_plan2.plan_comment d_plan2_comment,
		
		IFNULL(d_actual2.d_actual2_qty,0) d_actual2_qty, 
		IFNULL(d_actual2.d_actual2_qty,0) - IFNULL(d_plan2.d_plan2_qty,0) d_balance2,
		IFNULL(d_plan2.d_plan2_qty,0) + IFNULL(d_plan1.d_plan1_qty,0) d_all_plan,
		IFNULL(d_actual1.d_actual1_qty,0) + IFNULL(d_actual2.d_actual2_qty,0) d_all_actual,
		IFNULL(d_actual1.d_actual1_qty,0) + IFNULL(d_actual2.d_actual2_qty,0) - (IFNULL(d_plan2.d_plan2_qty,0) + IFNULL(d_plan1.d_plan1_qty,0)) d_all_balance,
		IFNULL(m_plan.m_plan_qty,0) m_plan_qty,
		IFNULL(m_actual.m_actual_qty,0) m_actual_qty, 
		IFNULL(m_actual.m_actual_qty,0) - IFNULL(m_plan.m_plan_qty,0) m_balance
	    FROM
		(
		    SELECT id, part_no, part_name, warehouse_id 
		    FROM part 
		    WHERE status=1 AND state>0 AND warehouse_id IN (SELECT id FROM warehouse WHERE warehouse_type=1) ".
      $product_line_where.
      $part_where.
      $warehouse_where.
      "
		) part
		LEFT JOIN
		(SELECT id, name FROM warehouse WHERE warehouse_type=1) wh 
		ON part.warehouse_id = wh.id
		LEFT JOIN
		(
			SELECT pp.part_id, IFNULL(SUM(pp.target_qty),0) d_plan1_qty, c.comment plan_comment 
			FROM production_plan pp 
			LEFT JOIN 
			(
				SELECT pc.production_plan_id p_id, pc.comment comment  
				FROM 
				(
					SELECT production_plan_id, MAX(created_at) crt_at
					FROM production_plan_comment
					GROUP BY production_plan_id
				) grp_comment
				LEFT JOIN production_plan_comment pc
				ON pc.production_plan_id = grp_comment.production_plan_id AND pc.created_at=grp_comment.crt_at
			) c			
			ON pp.id = c.p_id 
			WHERE pp.production_date = '$need_date' AND pp.shift = 1 
			GROUP BY pp.part_id, c.comment
		) d_plan1 ON part.id = d_plan1.part_id
		LEFT JOIN 
		(
			SELECT pp.part_id, IFNULL(SUM(pp.target_qty),0) d_plan2_qty, c.comment plan_comment 
			FROM production_plan pp 
			LEFT JOIN 
			(
				SELECT pc.production_plan_id p_id, pc.comment comment  
				FROM 
				(
					SELECT production_plan_id, MAX(created_at) crt_at
					FROM production_plan_comment
					GROUP BY production_plan_id
				) grp_comment
				LEFT JOIN production_plan_comment pc
				ON pc.production_plan_id = grp_comment.production_plan_id AND pc.created_at=grp_comment.crt_at
			) c			
			ON pp.id = c.p_id 
			WHERE pp.production_date = '$need_date' AND pp.shift = 2 
			GROUP BY pp.part_id,c.comment
		) d_plan2 ON part.id = d_plan2.part_id
			LEFT JOIN
		( SELECT part_id, IFNULL(SUM(quantity),0) d_actual1_qty 
		    FROM production_order 
            WHERE 
            from_unixtime(created_at, '%Y-%m-%d %H%i%s') >= CONCAT('$need_date',' ', '$shift1_from') AND 
            from_unixtime(created_at, '%Y-%m-%d %H%i%s') <= CONCAT('$need_date',' ', '$shift1_to') AND
            is_label = 2 
            GROUP BY part_id 
		) d_actual1	ON part.id = d_actual1.part_id
		LEFT JOIN
		( SELECT part_id, IFNULL(SUM(quantity),0) d_actual2_qty 
		    FROM production_order 
            WHERE 
            from_unixtime(created_at, '%Y-%m-%d %H%i%s') >= CONCAT('$need_date',' ', '$shift2_from') AND 
            from_unixtime(created_at, '%Y-%m-%d %H%i%s') <= CONCAT(DATE_ADD('$need_date', INTERVAL 1 DAY),' ', '$shift2_to') AND
            is_label = 2
            GROUP BY part_id 
		) d_actual2	ON part.id = d_actual2.part_id
		LEFT JOIN		
		(
		    SELECT part_id, IFNULL(SUM(target_qty),0) m_plan_qty 
		    FROM production_plan	
		    WHERE production_date between '".
      substr($need_date, 0, 7).
      "-01' and '".
      $need_date.
      "' 
		    GROUP BY part_id
		) m_plan ON part.id = m_plan.part_id		
		LEFT JOIN
		(SELECT part_id, IFNULL(SUM(quantity),0) m_actual_qty 
		FROM production_order 
		  WHERE from_unixtime(created_at, '%Y-%m-%d') between '".
      substr($need_date, 0, 7).
      "-01' and 
		  '".
      $need_date.
      "' AND is_label = 2
		  GROUP BY part_id 
		) m_actual	ON part.id = m_actual.part_id
		WHERE m_plan.m_plan_qty>0 OR  m_actual.m_actual_qty>0
		";
//            $dbPartList_sql = Yii::$app->db->createCommand($query)->rawSql;
//            echo "<pre>"; print_r($dbPartList_sql);echo "</pre>";
//            die;
    $dbPartList = Yii::$app->db->createCommand($query)->queryAll();
    return $this->render("production-plan-actual", [
      "DB_part_list" => $dbPartList,
      "need_date" => $need_date,
      "warehouse_id" => $warehouse_id,
      "product_line_id" => $product_line_id,
      "part_id" => $part_id,
      "model" => $model,
      "downloadFileName" => $downloadFileName,
    ]);
  }

  public function actionProductionCountLine() {
    $downloadFileName = Helpers::downloadFileName("production-count-line", "1");
    $downloadFileName = rtrim($downloadFileName, ".1");
    $this->checkReportAccess();
    $model = new ProductionPlan();
    $part_id = isset($_POST["ProductionPlan"]["part_id"]) ? $_POST["ProductionPlan"]["part_id"] : null;
    $part_where = "";
    if($part_id > 0) {
      $part_where = " and id=".$part_id;
    }
    $warehouse_id = isset($_POST["ProductionPlan"]["warehouse_id"]) ? $_POST["ProductionPlan"]["warehouse_id"] : null;
    $warehouse_where = "";
    if($warehouse_id > 0) {
      $warehouse_where = " and warehouse_id=".$warehouse_id;
    }
    $product_line_id = isset($_POST["product_line_id"]) ? $_POST["product_line_id"] : null;
    $product_line_where = "";
    if($product_line_id > 0) {
      $product_line_where = " and product_line_id=".$product_line_id;
    }
    $d_query =
      "
			SELECT
				part.id part_id, wh.id wh_id, wh.name warehouse_nm, IFNULL(pl.id,0) pl_id, IFNULL(pl.linename,0) line_nm,				
				IFNULL(d_plan_qty,0) d_plan_qty, IFNULL(d_actual_qty,0) d_actual_qty, (IFNULL(d_actual_qty,0) - IFNULL(d_plan_qty,0)) d_balance,
				'0' m_plan_qty, '0' m_actual_qty, '0' m_balance
			FROM
			(SELECT id, part_no, part_name, warehouse_id, product_line_id FROM part WHERE status=1 AND state>0 AND warehouse_id IN (SELECT id FROM warehouse WHERE warehouse_type=1) ".
      $product_line_where.
      $part_where.
      $warehouse_where.
      ") part
			LEFT JOIN
			(SELECT id, name FROM warehouse WHERE warehouse_type=1) wh
			ON part.warehouse_id = wh.id
			LEFT JOIN
			(SELECT id, linename FROM product_line ) pl
			ON part.product_line_id = pl.id
			LEFT JOIN
			(SELECT part_id, IFNULL(SUM(target_qty),0) d_plan_qty FROM production_plan WHERE production_date = CURDATE() GROUP BY part_id) d_plan
			ON part.id = d_plan.part_id
			LEFT JOIN
			( SELECT part_id, IFNULL(SUM(quantity),0) d_actual_qty FROM production_order 
			    WHERE from_unixtime(created_at, '%Y-%m-%d') = CURDATE() AND is_label = 2
			    GROUP BY part_id 
		  ) d_actual
			ON part.id = d_actual.part_id
			WHERE  d_plan_qty>0 or d_actual_qty>0
			";
    $m_query =
      "
			SELECT
				part.id part_id, wh.id wh_id, wh.name warehouse_nm, IFNULL(pl.id,0) pl_id, IFNULL(pl.linename,'-') line_nm,				
				'0' d_plan_qty, '0' d_actual_qty, '0' d_balance,
				IFNULL(m_plan_qty,0) m_plan_qty, IFNULL(m_actual_qty,0) m_actual_qty, (IFNULL(m_actual_qty,0) - IFNULL(m_plan_qty,0)) m_balance
			FROM
			(SELECT id, part_no, part_name, warehouse_id, product_line_id FROM part WHERE status=1 AND state>0 AND warehouse_id IN (SELECT id FROM warehouse WHERE warehouse_type=1) ".
      $product_line_where.
      $part_where.
      $warehouse_where.
      ") part
			LEFT JOIN
			(SELECT id, name FROM warehouse WHERE warehouse_type=1) wh
			ON part.warehouse_id = wh.id
			LEFT JOIN
			(SELECT id, linename FROM product_line ) pl
			ON part.product_line_id = pl.id
			LEFT JOIN
			(SELECT part_id, SUM(target_qty) m_plan_qty FROM production_plan WHERE DATE_FORMAT(production_date, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m') GROUP BY part_id) m_plan
			ON part.id = m_plan.part_id
			LEFT JOIN
			(SELECT part_id, SUM(quantity) m_actual_qty FROM production_order 
			WHERE from_unixtime(created_at, '%Y-%m') =  DATE_FORMAT(NOW(), '%Y-%m') AND is_label = 2
			GROUP BY part_id ) m_actual
			ON part.id = m_actual.part_id
			WHERE  m_plan_qty>0 or m_actual_qty>0
			";
    $res_d_performace = Yii::$app->db->createCommand($d_query)->queryAll();
    $res_m_performace = Yii::$app->db->createCommand($m_query)->queryAll();
    $d_grouped_list = Helpers::groupArrayByField($res_d_performace, "warehouse_nm", "line_nm");
    $m_grouped_list = Helpers::groupArrayByField($res_m_performace, "warehouse_nm", "line_nm");
    $grouped_list = array_merge_recursive($d_grouped_list, $m_grouped_list);
    $wh_cnt = 0;
    $prod_list = [];
    $k = 0;
    foreach($grouped_list as $wh_key => $wh_value) {
      $wh_cnt++;
      $line_cnt = 0;
      foreach($wh_value as $line_key => $line_value) {
        $line_cnt++;
        $all_d_plan = 0;
        $equal_d_balance = 0;
        $short_d_balance = 0;
        $over_d_balance = 0;
        $all_m_plan = 0;
        $equal_m_balance = 0;
        $short_m_balance = 0;
        $over_m_balance = 0;
        foreach($line_value as $res_key => $res_value) {
          if($res_value["d_plan_qty"] > 0 || $res_value["d_actual_qty"] > 0) {
            $all_d_plan++;
          }
          if($res_value["d_plan_qty"] != 0 && $res_value["d_actual_qty"] != 0 && $res_value["d_balance"] == 0) {
            $equal_d_balance++;
          }
          if($res_value["d_balance"] < 0) {
            $short_d_balance++;
          }
          if($res_value["d_balance"] > 0) {
            $over_d_balance++;
          }
          if($res_value["m_plan_qty"] > 0 || $res_value["m_actual_qty"] > 0) {
            $all_m_plan++;
          }
          if($res_value["m_plan_qty"] != 0 && $res_value["m_actual_qty"] != 0 && $res_value["m_balance"] == 0) {
            $equal_m_balance++;
          }
          if($res_value["m_balance"] < 0) {
            $short_m_balance++;
          }
          if($res_value["m_balance"] > 0) {
            $over_m_balance++;
          }
        }
        $k++;
        $prod_list[$k]["warehouse_nm"] = $wh_key;
        $prod_list[$k]["line_nm"] = $line_key;
        $prod_list[$k]["all_d_plan"] = $all_d_plan;
        $prod_list[$k]["equal_d_balance"] = $equal_d_balance;
        $prod_list[$k]["short_d_balance"] = $short_d_balance;
        $prod_list[$k]["over_d_balance"] = $over_d_balance;
        $prod_list[$k]["all_m_plan"] = $all_m_plan;
        $prod_list[$k]["equal_m_balance"] = $equal_m_balance;
        $prod_list[$k]["short_m_balance"] = $short_m_balance;
        $prod_list[$k]["over_m_balance"] = $over_m_balance;
      }
    }
    return $this->render("production-count-line", [
      "performance_list" => $prod_list,
      "warehouse_id" => $warehouse_id,
      "product_line_id" => $product_line_id,
      "part_id" => $part_id,
      "model" => $model,
      "downloadFileName" => $downloadFileName,
    ]);
  }

  public function actionStock() {
    $downloadFileName = Helpers::downloadFileName("stock", "1");
    $downloadFileName = rtrim($downloadFileName, ".1");
    return $this->render(
      "stock",
      array_merge($this->_reportService->stock(), ["downloadFileName" => $downloadFileName])
    );
  }

  public function actionFactByHour() {
    $this->checkReportAccess();
    $modelForm = new FactByHourForm();
    $shifts = Yii::$app->params["shifts"];
    if(!$modelForm->load(Yii::$app->request->post())) {
      $modelForm->flocOrLine = "floc";
      $modelForm->todayOrYesterday = "today";
      if(date("H:i") >= $shifts[1][0] and date("H:i") < $shifts[1][1]) {
        $modelForm->shift = 1;
      } else {
        $modelForm->shift = 2;
      }
    }
    $flocId = $lineId = null;
    if($modelForm->flocOrLine == "line") {
      $lineId = $modelForm->line;
    } else {
      $flocId = $modelForm->floc;
    }
    if($modelForm->todayOrYesterday == "today") {
      if($modelForm->shift == 1) {
        $from = date("Y-m-d ".$shifts[1][0]);
        $to = date("Y-m-d ".$shifts[2][0][0]);
      } else {
        $from = date("Y-m-d ".$shifts[2][0][0]);
        $to = date("Y-m-d ".$shifts[2][1][1], strtotime("+1 days"));
      }
    } else {
      if($modelForm->shift == 1) {
        $from = date("Y-m-d ".$shifts[1][0], strtotime("-1 days"));
        $to = date("Y-m-d ".$shifts[2][0][0], strtotime("-1 days"));
      } else {
        $from = date("Y-m-d ".$shifts[2][0][0], strtotime("-1 days"));
        $to = date("Y-m-d ".$shifts[2][1][1]);
      }
    }
    // test
    // $from = '2020-02-26 08:00';
    // $to = '2020-02-26 20:00';
    $from = strtotime($from);
    $to = strtotime($to);
    $data = $this->_reportService->factByHour($from, $to, $flocId, $lineId);
    // echo '<pre>';
    // print_r($data);
    // echo '</pre>';
    // die;
    return $this->render(
      "fact-by-hour",
      array_merge($data, [
        "downloadFileName" => rtrim(Helpers::downloadFileName("fact-by-hour", "1"), ".1"),
        "modelForm" => $modelForm,
      ])
    );
  }

  public function actionSp($sp_id = null, $supplier_id = null) {
    $this->checkReportAccess();
    if($sp_id and $supplier_id) {
      $data = $this->_reportService->spDetailed($sp_id, $supplier_id);
    } else {
      $data = $this->_reportService->sp($sp_id);
    }
    return $this->render(
      "sp/main",
      array_merge($data, ["downloadFileName" => rtrim(Helpers::downloadFileName("sp", "1"), ".1")])
    );
  }

  public function actionBomCost() {
    $this->checkReportAccess();
    $downloadFileName = Helpers::downloadFileName("bom-cost", "1");
    $downloadFileName = rtrim($downloadFileName, ".1");
    $downloadFileNameDetail = Helpers::downloadFileName("bom-cost-detail", "1");
    $downloadFileNameDetail = rtrim($downloadFileNameDetail, ".1");
    return $this->render(
      "bom-cost",
      array_merge($this->_reportService->bomCost(), [
        "downloadFileName" => $downloadFileName,
        "downloadFileNameDetail" => $downloadFileNameDetail,
      ])
    );
  }

  public function actionBomCostDetail($part_id) {
    $this->checkReportAccess("bom-cost");
    Yii::$app->response->format = Response::FORMAT_JSON;
    $data = $this->_reportService->bomCostDetail($part_id);
    $totalUZS = $totalUSD = $totalEUR = $totalRUB = $totalTUZS = 0;
    foreach($data["items"] as $key => $row) {
      $data["items"][$key]["comp"]["usage_qty"] = Helpers::numberFormatRemoveZero(
        $row["comp"]["usage_qty"],
        7,
        ".",
        " ",
        true,
        true
      );
      $data["items"][$key]["uzs"] = Helpers::numberFormatRemoveZero($row["uzs"], 2, ".", " ", true, true);
      $data["items"][$key]["usd"] = Helpers::numberFormatRemoveZero($row["usd"], 2, ".", " ", true, true);
      $data["items"][$key]["eur"] = Helpers::numberFormatRemoveZero($row["eur"], 2, ".", " ", true, true);
      $data["items"][$key]["rub"] = Helpers::numberFormatRemoveZero($row["rub"], 2, ".", " ", true, true);
      $data["items"][$key]["tuzs"] =
        $row["tuzs"] != "N/A" ? Helpers::numberFormatRemoveZero($row["tuzs"], 2, ".", " ", true, true) : $row["tuzs"];
      $totalUZS += $row["uzs"];
      $totalUSD += $row["usd"];
      $totalEUR += $row["eur"];
      $totalRUB += $row["rub"];
      $totalTUZS += $row["tuzs"] != "N/A" ? Helpers::numberFormatRemoveZero($row["tuzs"], 2, ".", " ", true, true) : 0;
    }
    $data["total"] = [
      "uzs" => Helpers::numberFormatRemoveZero($totalUZS, 2, ".", " ", true, true),
      "usd" => Helpers::numberFormatRemoveZero($totalUSD, 2, ".", " ", true, true),
      "eur" => Helpers::numberFormatRemoveZero($totalEUR, 2, ".", " ", true, true),
      "rub" => Helpers::numberFormatRemoveZero($totalRUB, 2, ".", " ", true, true),
      "tuzs" => Helpers::numberFormatRemoveZero($totalTUZS, 2, ".", " ", true, true),
    ];
    return $data;
  }

  public function actionBomCost2() {
    $this->_reportService->bomCost2();
  }

  public function actionCoverage($filter=null) {
    $this->checkReportAccess();
    $this->layout = "req";
    //vd($this->semiCoverage);
    return $this->render("coverage/coverage", [
      "data_weekly" => self::getWeeklyCoverage($filter),
      "data_daily" => self::getDailyCoverage($filter),
      "data_local" => self::getLocalCoverage($filter),
      "data_cons" => self::getConsCoverage($filter),
      "data_semi" => self::getSemiCoverage($filter),
    ]);
  }

  public function actionVehicleSet() {
    $this->checkReportAccess();
    $this->layout = "req";
    return $this->render(
      "vehicle-set/vehicle-set",
      array_merge($this->_reportService->coverageByVehicleSet(), [
        "data_vehicle_weekly" => $this->weeklyCoverageVehicle,
        "data_vehicle_daily" => $this->dailyCoverageVehicle,
        "data_vehicle_oem" => $this->getPlanVehicle(VehicleCoverageInput::getLastCoverageDate()),
        "data_vehicle_intransit" => $this->intransitVehicle,
        "models" => $this->models,
      ])
    );
  }

  protected function getModels() {
    return ProductModel::find()
                       ->where([
                         "is_vehicle" => ProductModel::IS_VEHICLE,
                       ])
                       ->all();
  }

  public static function getDailyCoverageVehicle() {
    $query = "
                            select a.model_id,pm.modelname,pm.description model_desc, a.*  from
                            (
                                    select r.id rid, r.model_id,r.stock,r.uamstock,r.intransit,r.orders, r.stock_out, 
                                    (ifnull(r.stock,0)+ifnull(r.intransit,0)+ifnull(r.orders,0)) totalpl,
                                    r.doh, r.calc_at, w.* 
                                    from 
                                      coverage_vehicle_detail w 
                                      left join coverage_vehicle r on w.coverage_vehicle_id = r.id 
                                    where w.type = :type
                            ) a
                            left join product_model pm on a.model_id = pm.id
                        ";
    return Yii::$app->db->createCommand($query, [":type" => CoverageVehicleController::TYPE_DAILY])->queryAll();
  }

  public static function getWeeklyCoverageVehicle() {
    $query = "
                        select a.model_id,pm.modelname,pm.description model_desc, a.*  from
                        (
                                select r.id rid, r.model_id,r.stock,r.uamstock,r.intransit,r.orders, r.stock_out, 
                                (ifnull(r.stock,0)+ifnull(r.intransit,0)+ifnull(r.orders,0)) totalpl,
                                r.doh, r.calc_at, w.* 
                                from 
                                  coverage_vehicle_detail w 
                                  left join coverage_vehicle r on w.coverage_vehicle_id = r.id 
                                where w.type = :type
                        ) a
                        left join product_model pm on a.model_id = pm.id
                        ";
    return Yii::$app->db->createCommand($query, [":type" => CoverageVehicleController::TYPE_WEEKLY])->queryAll();
  }

  public static function getDailyCoverage($filter=null) {
    $query = "
                            select a.part_id,p.part_no,p.part_name,p.part_color, p.remark, p.comment, cs.name csourse,un.unit_value unit,un.id unit_id, a.*  from
                            (
                                    select r.id rid,r.part_id,r.whbal,r.linebal,r.semistock,r.fgstock,r.outsourcing,r.pending,
                                    (ifnull(r.whbal,0)+ifnull(r.linebal,0)+ifnull(r.semistock,0)+ifnull(r.outsourcing,0)+ifnull(r.pending,0)+ifnull(r.arrive,0)) totalstock,
                                    r.arrive,r.days_count, r.doh, r.calc_at, w.* from req_detail_wide w left join req r on w.req_id = r.id 
                                    where w.type = :type
                            ) a
                            left join part p on a.part_id = p.id
                            left join contract_source cs on p.contract_source_id = cs.id
                            left join unit un on p.unit_id = un.id
                        ";
    if ($filter != null)
    {
      $query = "
                            select a.part_id,p.part_no,p.part_name,p.part_color, p.remark, p.comment, cs.name csourse,un.unit_value unit,un.id unit_id, a.*  from
                            (
                                    select r.id rid,r.part_id,r.whbal,r.linebal,r.semistock,r.fgstock,r.outsourcing,r.pending,
                                    (ifnull(r.whbal,0)+ifnull(r.linebal,0)+ifnull(r.semistock,0)+ifnull(r.outsourcing,0)+ifnull(r.pending,0)+ifnull(r.arrive,0)) totalstock,
                                    r.arrive,r.days_count, r.doh, r.calc_at, w.* from req_detail_wide w left join req r on w.req_id = r.id 
                                    where w.type = :type AND r.whbal!=0
                            ) a
                            left join part p on a.part_id = p.id
                            left join contract_source cs on p.contract_source_id = cs.id
                            left join unit un on p.unit_id = un.id
                        ";

    }
    return Yii::$app->db->createCommand($query, [":type" => CoverageController::TYPE_DAILY])->queryAll();
  }

  public static function getWeeklyCoverage($filter=null) {
    $query = "
                            select a.part_id,p.part_no,p.part_name,p.part_color, p.remark, p.comment, cs.name csourse,un.unit_value unit,un.id unit_id, a.*  from
                            (
                                    select r.id rid,r.part_id,r.whbal,r.linebal,r.semistock,r.fgstock,r.outsourcing,r.pending,
                                    (ifnull(r.whbal,0)+ifnull(r.linebal,0)+ifnull(r.semistock,0)+ifnull(r.outsourcing,0)+ifnull(r.pending,0)+ifnull(r.arrive,0)) totalstock,
                                    r.arrive,r.days_count, r.doh, r.calc_at, w.* from req_detail_wide w left join req r on w.req_id = r.id 
                                    where w.type = :type
                            ) a
                            left join part p on a.part_id = p.id
                            left join contract_source cs on p.contract_source_id = cs.id
                            left join unit un on p.unit_id = un.id
                        ";
    if ($filter != null)
      $query = "
                            select a.part_id,p.part_no,p.part_name,p.part_color, p.remark, p.comment, cs.name csourse,un.unit_value unit,un.id unit_id, a.*  from
                            (
                                    select r.id rid,r.part_id,r.whbal,r.linebal,r.semistock,r.fgstock,r.outsourcing,r.pending,
                                    (ifnull(r.whbal,0)+ifnull(r.linebal,0)+ifnull(r.semistock,0)+ifnull(r.outsourcing,0)+ifnull(r.pending,0)+ifnull(r.arrive,0)) totalstock,
                                    r.arrive,r.days_count, r.doh, r.calc_at, w.* from req_detail_wide w left join req r on w.req_id = r.id 
                                    where w.type = :type AND r.whbal!=0
                            ) a
                            left join part p on a.part_id = p.id
                            left join contract_source cs on p.contract_source_id = cs.id
                            left join unit un on p.unit_id = un.id
                        ";
    //vd($query);
    return Yii::$app->db->createCommand($query, [":type" => CoverageController::TYPE_WEEKLY])->queryAll();
  }

  public static function getLocalCoverage($filter=null) {
    $query = "
                            select p.part_no,p.part_name,p.part_color, p.remark, p.comment, cs.name csourse, un.unit_value unit,un.id unit_id, a.*  from
                            (
                                    select r.id rid,r.part_id,r.whbal,r.linebal,r.semistock,r.fgstock,r.outsourcing,r.pending,
                                    (ifnull(r.whbal,0)+ifnull(r.linebal,0)+ifnull(r.semistock,0)+ifnull(r.outsourcing,0)+ifnull(r.pending,0)+ifnull(r.arrive,0)) totalstock,
                                    r.arrive,r.days_count, r.doh, r.calc_at, w.* from req_detail_wide w left join req r on w.req_id = r.id 
                                    where w.type = :type
                            ) a
                            left join part p on a.part_id = p.id
                            left join contract_source cs on p.contract_source_id = cs.id
                            left join unit un on p.unit_id = un.id
                        ";
    if ($filter != null)
    {
      $query = "
                            select p.part_no,p.part_name,p.part_color, p.remark, p.comment, cs.name csourse, un.unit_value unit,un.id unit_id, a.*  from
                            (
                                    select r.id rid,r.part_id,r.whbal,r.linebal,r.semistock,r.fgstock,r.outsourcing,r.pending,
                                    (ifnull(r.whbal,0)+ifnull(r.linebal,0)+ifnull(r.semistock,0)+ifnull(r.outsourcing,0)+ifnull(r.pending,0)+ifnull(r.arrive,0)) totalstock,
                                    r.arrive,r.days_count, r.doh, r.calc_at, w.* from req_detail_wide w left join req r on w.req_id = r.id 
                                    where w.type = :type AND r.whbal!=0 
                            ) a
                            left join part p on a.part_id = p.id
                            left join contract_source cs on p.contract_source_id = cs.id
                            left join unit un on p.unit_id = un.id
                        ";
    }
    //vd($query);
    return Yii::$app->db->createCommand($query, [":type" => CoverageController::TYPE_LOCAL_DAILY])->queryAll();
  }

  public static function getConsCoverage($filter=null) {
    $query = "
    select p.part_no,p.part_name,p.part_color, p.remark, p.comment, cs.name csourse,un.unit_value unit,un.id unit_id, a.*  from
    (
            select r.id rid,r.part_id,r.whbal,r.linebal,r.semistock,r.fgstock,r.outsourcing,r.pending,
            (ifnull(r.whbal,0)+ifnull(r.linebal,0)+ifnull(r.semistock,0)+ifnull(r.outsourcing,0)+ifnull(r.pending,0)+ifnull(r.arrive,0)) totalstock,
            r.arrive,r.days_count, r.doh, r.calc_at, w.* from req_detail_wide w left join req r on w.req_id = r.id 
            where w.type = :type
    ) a
    left join part p on a.part_id = p.id
    left join contract_source cs on p.contract_source_id = cs.id
    left join unit un on p.unit_id = un.id
";

    if ($filter != null)
    {
      $query = "
    select p.part_no,p.part_name,p.part_color, p.remark, p.comment, cs.name csourse,un.unit_value unit,un.id unit_id, a.*  from
    (
            select r.id rid,r.part_id,r.whbal,r.linebal,r.semistock,r.fgstock,r.outsourcing,r.pending,
            (ifnull(r.whbal,0)+ifnull(r.linebal,0)+ifnull(r.semistock,0)+ifnull(r.outsourcing,0)+ifnull(r.pending,0)+ifnull(r.arrive,0)) totalstock,
            r.arrive,r.days_count, r.doh, r.calc_at, w.* from req_detail_wide w left join req r on w.req_id = r.id 
            where w.type = :type AND r.whbal!=0
    ) a
    left join part p on a.part_id = p.id
    left join contract_source cs on p.contract_source_id = cs.id
    left join unit un on p.unit_id = un.id
";
    }
    return Yii::$app->db->createCommand($query, [":type" => CoverageController::TYPE_LOCAL_CONS])->queryAll();
  }

  public static function getSemiCoverage($filter=null) {
    $query = "
    select p.part_no,p.part_name,p.part_color, p.remark, p.comment, cs.name csourse,un.unit_value unit,un.id unit_id, a.*  from
    (
            select r.id rid,r.part_id,r.whbal,r.linebal,r.semistock,r.fgstock,r.outsourcing,r.pending,
            (ifnull(r.whbal,0)+ifnull(r.linebal,0)+ifnull(r.semistock,0)+ifnull(r.outsourcing,0)+ifnull(r.pending,0)+ifnull(r.arrive,0)) totalstock,
            r.arrive,r.days_count, r.doh, r.calc_at, w.* from req_detail_wide w left join req r on w.req_id = r.id 
            where w.type = :type
    ) a
    left join part p on a.part_id = p.id
    left join contract_source cs on p.contract_source_id = cs.id
    left join unit un on p.unit_id = un.id
";

    if ($filter != null)
    {
      $query = "
    select p.part_no,p.part_name,p.part_color, p.remark, p.comment, cs.name csourse,un.unit_value unit,un.id unit_id, a.*  from
    (
            select r.id rid,r.part_id,r.whbal,r.linebal,r.semistock,r.fgstock,r.outsourcing,r.pending,
            (ifnull(r.whbal,0)+ifnull(r.linebal,0)+ifnull(r.semistock,0)+ifnull(r.outsourcing,0)+ifnull(r.pending,0)+ifnull(r.arrive,0)) totalstock,
            r.arrive,r.days_count, r.doh, r.calc_at, w.* from req_detail_wide w left join req r on w.req_id = r.id 
            where w.type = :type AND r.whbal!=0
    ) a
    left join part p on a.part_id = p.id
    left join contract_source cs on p.contract_source_id = cs.id
    left join unit un on p.unit_id = un.id
";
    }
    return Yii::$app->db->createCommand($query, [":type" => CoverageController::TYPE_LOCAL_SEMI])->queryAll();
  }

  public function actionLocalCoverage() {
    $this->checkReportAccess();
    $this->layout = "req";
    return $this->render("local-coverage", [
      "data_daily" => $this->localCoverage,
    ]);
  }

  public function actionConsCoverage() {
    $this->checkReportAccess();
    $this->layout = "req";
    return $this->render("cons-coverage", [
      "data_daily" => $this->consCoverage,
    ]);
  }

  public function actionSemiCoverage() {
    $this->checkReportAccess();
    $this->layout = "req";
    return $this->render("semi-coverage", [
      "data_daily" => $this->semiCoverage,
    ]);
  }

  public function actionRequirement($filter=null) {
    $this->checkReportAccess();
    $this->layout = "req";
    $query = "
                            select p.part_no,p.part_name,p.part_color, p.remark, cs.name csourse, a.*  from
                            (
                                    select r.id rid,r.part_id,r.calc_at, w.* from req_detail_plan w left join req r on w.req_id = r.id
                                    where w.type = :type
                            ) a
                            left join part p on a.part_id = p.id
                            left join contract_source cs on p.contract_source_id = cs.id
                            order by p.part_no
                        ";
    $data_weekly = Yii::$app->db->createCommand($query, [":type" => CoverageController::TYPE_WEEKLY])->queryAll();
    $query = "
                            select p.part_no,p.part_name,p.part_color, p.remark, cs.name csourse, a.*  from
                            (
                                    select r.id rid,r.part_id,r.calc_at, w.* from req_detail_plan w left join req r on w.req_id = r.id
                                    where w.type = :type_d or w.type = :type_l or w.type = :type_c or w.type = :type_s
                            ) a
                            left join part p on a.part_id = p.id
                            left join contract_source cs on p.contract_source_id = cs.id
                            order by p.part_no

                        ";
    $data_daily = Yii::$app->db
      ->createCommand($query, [
        ":type_d" => CoverageController::TYPE_DAILY,
        ":type_l" => CoverageController::TYPE_LOCAL_DAILY,
        ":type_c" => CoverageController::TYPE_LOCAL_CONS,
        ":type_s" => CoverageController::TYPE_LOCAL_SEMI,
      ])
      ->queryAll();
    return $this->render("requirement", [
      "data_weekly" => $data_weekly,
      "data_daily" => $data_daily,
      "filter" => $filter,
    ]);
  }

  public function actionInTransit() {
    $this->checkReportAccess();
    $this->layout = "req";
    return $this->render("in-transit", [
      "data" => $this->getInTransitData(),
    ]);
  }

  public function actionImport() {
    $this->checkReportAccess();
    $this->layout = "req";
    $need_month = isset($_POST["need_month"])
      ? ($need_month = $_POST["need_month"])
      : date("Y-m", strtotime("-1 month"));
    return $this->render("import", [
      "need_month" => $need_month,
      "data" => $this->getImportData($need_month),
    ]);
  }

  public function actionDoh($r = null) {
    $this->checkReportAccess();
    $data = [
      "less60" => $this->getDohData60(),
      "greater120" => $this->getDohData120(),
    ];
    $keys = [];
    foreach($data["less60"] as $row) {
      $keys[] = $row["key"];
    }
    foreach($data["greater120"] as $row) {
      $keys[] = $row["key"];
    }
    $keys = array_unique($keys);
    asort($keys);
    $result = [];
    $totalLess60 = 0;
    $totalGreater120 = 0;
    $totalLess60Amount = 0;
    $totalGreater120Amount = 0;
    $countries = [];
    $suppliers = [];
    foreach($keys as $rKey) {
      [$country, $supplier, $country_id, $supplier_id] = explode("|", $rKey);
      $tmpRes = [];
      $tmpRes["unknown"] = empty($country) || empty($supplier) ? "1" : "";
      if(!empty($country)) {
        $tmpRes["country"] = $country;
        $tmpRes["country_id"] = $country_id;
        $countries[] = $tmpRes["country"];
      } else {
        $tmpRes["country"] = Yii::t("app", "Unknown country");
        $tmpRes["country_id"] = "na";
      }
      if(!empty($supplier)) {
        $tmpRes["supplier"] = $supplier;
        $tmpRes["supplier_id"] = $supplier_id;
        $suppliers[] = $tmpRes["supplier"];
      } else {
        $tmpRes["supplier"] = Yii::t("app", "Unknown supplier");
        $tmpRes["supplier_id"] = "na";
      }
      $less60 = 0;
      $greater120 = 0;
      $less60Amount = 0;
      $greater120Amount = 0;
      foreach($data["less60"] as $row) {
        [$country60, $supplier60, $country_id60, $supplier_id60] = explode("|", $row["key"]);
        if($country60 == $country and $supplier60 == $supplier) {
          $less60 = !empty($row["total_count"]) ? $row["total_count"] : 0;
          $less60Amount = !empty($row["total_amount"]) ? $row["total_amount"] : 0;
          break;
        }
      }
      foreach($data["greater120"] as $row) {
        [$country120, $supplier120, $country_id120, $supplier_id120] = explode("|", $row["key"]);
        if($country120 == $country and $supplier120 == $supplier) {
          $greater120 = !empty($row["total_count"]) ? $row["total_count"] : 0;
          $greater120Amount = !empty($row["total_amount"]) ? $row["total_amount"] : 0;
          break;
        }
      }
      $tmpRes["less60"] = $less60;
      $tmpRes["greater120"] = $greater120;
      $tmpRes["less60Amount"] = $less60Amount;
      $tmpRes["greater120Amount"] = $greater120Amount;
      $totalLess60 += $less60;
      $totalGreater120 += $greater120;
      $totalLess60Amount += $less60Amount;
      $totalGreater120Amount += $greater120Amount;
      $result[] = $tmpRes;
    }
    // echo '<pre>';
    // print_r($result);
    // echo '</pre>';
    // die;
    $total = [
      "countries" => count(array_unique($countries)),
      "suppliers" => count(array_unique($suppliers)),
      "less60" => $totalLess60,
      "greater120" => $totalGreater120,
      "less60Amount" => $totalLess60Amount,
      "greater120Amount" => $totalGreater120Amount,
    ];
    $downloadFileName = Helpers::downloadFileName("doh", "1");
    $downloadFileName = rtrim($downloadFileName, ".1");
    if(!$r) {
      return $this->render("doh", [
        "total" => $total,
        "data" => $result,
        "downloadFileName" => $downloadFileName,
      ]);
    } else {
      return $total;
    }
  }

  public function actionDohDetail($type, $id = null) {
    $this->checkReportAccess("doh");
    if($type == "l60") {
      $result = $this->getDetailedDohData60(true);
    } elseif($type == "g120") {
      $result = $this->getDetailedDohData120(true);
    } elseif($type == "country" or $type == "supplier") {
      $result60 = $this->getDetailedDohData60(true, $type, $id);
      $result120 = $this->getDetailedDohData120(true, $type, $id);
      $result = [];
      foreach($result60 as $row) {
        $result[] = $row;
      }
      foreach($result120 as $row) {
        $result[] = $row;
      }
    }
    switch($type) {
      case "l60":
        $title = Yii::t("app", "DOH < {cnt} days", ["cnt" => Yii::$app->params["less_dates_count"]]);
        break;
      case "g120":
        $title = Yii::t("app", "DOH > {cnt} days", ["cnt" => Yii::$app->params["greater_dates_count"]]);
        break;
      case "country":
        $title = Yii::t("app", "Country");
        break;
      case "supplier":
        $title = Yii::t("app", "Supplier");
        break;
    }
    $downloadFileName = Helpers::downloadFileName("doh_detail_".$type, "1");
    $downloadFileName = rtrim($downloadFileName, ".1");
    return $this->render("doh-detail", [
      "type" => $type,
      "title" => $title,
      "result" => $result,
      "downloadFileName" => $downloadFileName,
    ]);
  }

  public function actionCoverageBalanceOld() {
    $this->checkReportAccess();
    $periods = [
      date("Y-m-t"),
      date("Y-m-t", strtotime("+1 month")),
      date("Y-m-t", strtotime("+2 month")),
      date("Y-m-t", strtotime("+3 month")),
    ];
    // echo '<pre>';
    // print_r($periods);
    // echo '</pre>';
    $suppliers = CoverageBalance::find()
                                ->select(["supplier_id", "payment_term_id", "currency_id"])
                                ->where(["period" => $periods])
                                ->groupBy(["supplier_id", "payment_term_id", "currency_id"])
                                ->all();
    $coverageBalances = CoverageBalance::find()
                                       ->where(["period" => $periods])
                                       ->asArray()
                                       ->all();
    // echo '<pre>';
    // print_r($suppliers);
    // echo '</pre>';
    $keys = [];
    foreach($coverageBalances as $row) {
      $keys[] = $row["supplier_id"]."|".$row["payment_term_id"];
    }
    $keys = array_unique($keys);
    $data = [];
    foreach($keys as $key) {
      [$supplier_id, $payment_term_id] = explode("|", $key);
      $suppData = [];
      foreach($coverageBalances as $row) {
        if($supplier_id == $row["supplier_id"] and $payment_term_id == $row["payment_term_id"]) {
          $suppData[$row["period"]] = [
            "debt" => $row["debt"] ?? 0,
            "paid" => $row["paid"] ?? 0,
            "diff" => $row["debt"] - $row["paid"] ?? 0,
          ];
        }
      }
      $data[$key] = $suppData;
    }
    foreach($data as $key1 => $val1) {
      $tmpDates = [];
      foreach($val1 as $key2 => $val2) {
        $tmpDates[] = $key2;
      }
      foreach($periods as $rDate) {
        if(!in_array($rDate, $tmpDates)) {
          $val1[$rDate] = [
            "debt" => 0,
            "paid" => 0,
            "diff" => 0,
          ];
        }
      }
      $data[$key1] = $val1;
    }
    $downloadFileName = Helpers::downloadFileName("coverage-balance", "1");
    $downloadFileName = rtrim($downloadFileName, ".1");
    return $this->render("coverage-balance", [
      "suppliers" => $suppliers,
      "data" => $data,
      "downloadFileName" => $downloadFileName,
    ]);
  }

  public function actionSpl() {
    $this->checkReportAccess();
    $rateUSD = CurrencyRate::currentRate(Currency::findOneCurrencyCode("USD")->id);
    $rateEUR = CurrencyRate::currentRate(Currency::findOneCurrencyCode("EUR")->id);
    $rateRUB = CurrencyRate::currentRate(Currency::findOneCurrencyCode("RUB")->id);
    $part = Part::find()
                ->with("unit")
                ->where(["state" => [Part::STATE_RAW, Part::STATE_FINISHED]])
                ->all();
    $raws = [];
    $products = [];
    foreach($part as $p) {
      if($p->state == Part::STATE_RAW) {
        $raws[] = $p;
      }
      if($p->state == Part::STATE_FINISHED) {
        $products[] = $p;
      }
    }
    $query = "
      select part_id product_id, sub_part_id raw_id, sum(usage_qty) usage_qty from part_part_wide group by part_id, sub_part_id
    ";
    $bom = Yii::$app->db->createCommand($query)->queryAll() ?? [];
    // group by product_id, raw_id
    $keys = [];
    foreach($bom as $b) {
      $keys[] = $b["product_id"]."|".$b["raw_id"];
    }
    $keys = array_unique($keys);
    // ******
    // bom ni oson o'qiladigan holatga o'tkazish
    $bom2 = [];
    foreach($keys as $key) {
      [$productId, $rawId] = explode("|", $key);
      foreach($bom as $b) {
        if($productId == $b["product_id"] and $rawId == $b["raw_id"]) {
          $bom2[$productId][$rawId] = $b["usage_qty"];
          break;
        }
      }
    }
    // *********
    $data = [];
    $total = [];
    foreach($raws as $raw) {
      $tmp = [];
      $tmp["part_no"] = $raw->partinfo;
      $tmp["part_name"] = $raw->part_name;
      $tmp["unit"] = $raw->unit->unit_value;
      // contract ma'lumotlarini chaqirish
      $actualContract = $raw->getActualContract();
      $contractPrice = $actualContract->price ?? 0;
      $currency = $actualContract->contract->currency->code ?? "";
      $priceUZS = 0;
      switch($currency) {
        case "USD":
          $priceUZS = $contractPrice*$rateUSD;
          break;
        case "EUR":
          $priceUZS = $contractPrice*$rateEUR;
          break;
        case "RUB":
          $priceUZS = $contractPrice*$rateRUB;
          break;
        case "UZS":
          $priceUZS = $contractPrice;
          break;
      }
      // ***
      $tmp["contract_price"] = $contractPrice;
      $tmp["currency"] = $currency;
      $tmp["price_uzs"] = $priceUZS;
      // product columns
      foreach($products as $product) {
        $qty = $bom2[$product->id][$raw->id] ?? 0;
        if($qty) {
          $amount = $qty*$tmp["price_uzs"];
          $tmp[$product->id] = [
            "qty" => $qty,
            "amount" => $amount,
          ];
          $total[$product->id] = ($total[$product->id] ?? 0) + $amount;
        }
      }
      // ***
      $data[] = $tmp;
    }
    // download uchun tayyorgarlik
    $arrFile = [];
    // total larni qo'shish
    unset($tmpArray);
    $tmpArray["part_no"] = "";
    $tmpArray["part_name"] = "";
    $tmpArray["unit"] = "";
    $tmpArray["contract_price"] = "";
    $tmpArray["currency"] = "";
    $tmpArray["price_uzs"] = "";
    foreach($products as $product) {
      $tmpArray[$product->id] = $product->part_name;
    }
    $arrFile[] = $tmpArray;
    // ***
    // header
    unset($tmpArray);
    $tmpArray["part_no"] = Yii::t("app", "Part number");
    $tmpArray["part_name"] = Yii::t("app", "Part name");
    $tmpArray["unit"] = Yii::t("app", "Unit");
    $tmpArray["contract_price"] = Yii::t("app", "Contract price");
    $tmpArray["currency"] = Yii::t("app", "Currency");
    $tmpArray["price_uzs"] = Yii::t("app", "Price ({currency})", ["currency" => "UZS"]);
    foreach($products as $product) {
      $tmpArray[$product->id] = $product->part_no;
    }
    $arrFile[] = $tmpArray;
    // ***
    // asosiy ma'lumotlar
    foreach($data as $row) {
      unset($tmpArray);
      $tmpArray["part_no"] = $row["part_no"];
      $tmpArray["part_name"] = $row["part_name"];
      $tmpArray["unit"] = $row["unit"];
      $tmpArray["contract_price"] = $row["contract_price"];
      $tmpArray["currency"] = $row["currency"];
      $tmpArray["price_uzs"] = $row["price_uzs"];
      foreach($products as $product) {
        $tmpArray[$product->id] = $row[$product->id]["qty"] ?? 0;
      }
      $arrFile[] = $tmpArray;
    }
    // ***
    // total larni qo'shish
    unset($tmpArray);
    $tmpArray["part_no"] = Yii::t("app", "Total");
    $tmpArray["part_name"] = "";
    $tmpArray["unit"] = "";
    $tmpArray["contract_price"] = "";
    $tmpArray["currency"] = "";
    $tmpArray["price_uzs"] = "";
    foreach($products as $product) {
      $tmpArray[$product->id] = $total[$product->id] ?? 0;
    }
    $arrFile[] = $tmpArray;
    // ***
    // Excelga ko'chirib olish
    $file = Yii::createObject([
      "class" => 'codemix\excelexport\ExcelFile',
      "sheets" => [
        "spl" => [
          "data" => $arrFile,
        ],
      ],
    ]);
    $file->send(Helpers::downloadFileName("spl"));
    // ***
  }

  protected function getInTransitData() {
    $period = Helpers::getPeriod60Days();
    $query = "select app_arr_at estdate, part_id, sum(qty) qty from 
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
    foreach($intransit as $tr) {
      $part[] = $tr["part_id"];
      $estdate[] = $tr["estdate"];
    }
    $part = array_unique($part);
    $estdate = array_unique($estdate);
    foreach($estdate as $d) {
      foreach($part as $p) {
        foreach($intransit as $tr) {
          if($tr["part_id"] == $p and $tr["estdate"] == $d) {
            $intransit_result[$d][$p] = $tr["qty"];
            break;
          }
        }
      }
    }
    $this->intransit_result = $intransit_result;
    $partModels = Part::find()
                      ->where([
                        "status" => Part::STATUS_ACTIVE,
                        "state" => Part::STATE_RAW,
                        "contract_source_id" => Yii::$app->params["import_contract_source_ids"],
                      ])
                      ->all();
    $data = [];
    if(is_array($partModels) and count($partModels) > 0) {
      foreach($partModels as $pmodel) {
        unset($row);
        $row["part"] = $pmodel;
        foreach($period as $per) {
          $row[$per["plandate"]] = $this->getIntransit($pmodel->id, $per["from"], $per["to"]);
        }
        $data[] = $row;
      }
    }
    return $data;
  }

  protected function getImportData($need_month) {
    $query =
      "
    select p.part_no, p.part_color, p.part_name, u.unit_value uom, a.*, 
    s.name supplier,cc.name_ru country,cn.cnfea tnved,  cu.code currency
    from
(
        select id.part_id, id.price, sum(id.qty) qty, sum(id.qty * id.price) amount, id.contract_id 
        from 
                container_invoice ci, invoice_detail id
        where 
                ci.id = id.cont_inv_id and 
                ci.invoice_id in
                (
                        select invoice_id 
                        from gtd g, gtd_invoice gd
                        where g.id = gd.gtd_id and g.gtd_dt between '".
      $need_month.
      "-01' and '".
      $need_month.
      "-31'
                )
                        group by id.part_id, id.price, id.contract_id
                        order by id.part_id
                ) a
                left join part p on a.part_id = p.id
                left join unit u on p.unit_id = u.id
                left join contract c on a.contract_id = c.id
                left join supplier s on c.supplier_id = s.id
                left join currency cu on c.currency_id = cu.id
                left join country_code cc on s.country_code_id = cc.id
                left join (select contract_id,part_id,max(cnfea) cnfea from contract_detail group by contract_id,part_id) cn 
                on p.id = cn.part_id and cn.contract_id = a.contract_id

                            ";
    $data = Yii::$app->db->createCommand($query)->queryAll();
    return $data;
  }

  protected function getDohData60() {
    $result = $this->getDetailedDohData60();
    $keys = [];
    foreach($result as $row) {
      $keys[] = $row["coun_supp"];
    }
    $keys = array_unique($keys);
    $data = [];
    foreach($keys as $key) {
      $totalCount = 0;
      $totalAmount = 0;
      foreach($result as $row) {
        if($key == $row["coun_supp"]) {
          $totalCount++;
          $totalAmount += $row["amount"];
        }
      }
      $data[] = [
        "key" => $key,
        "total_count" => $totalCount,
        "total_amount" => $totalAmount,
      ];
    }
    return $data;
  }

  protected function getDetailedDohData60($isDetailed = false, $type = false, $detailModelId = null) {
    $columnNumber =
      array_search(
        date("Y-m-d", strtotime("+".Yii::$app->params["less_dates_count"]." days")),
        Helpers::getPeriodFull()
      ) + 1;
    $rateUSD = CurrencyRate::currentRate(Currency::findOneCurrencyCode("USD")->id);
    $rateEUR = CurrencyRate::currentRate(Currency::findOneCurrencyCode("EUR")->id);
    $rateRUB = CurrencyRate::currentRate(Currency::findOneCurrencyCode("RUB")->id);
    $query = ReqDetailWide::find()
                          ->joinWith("req")
                          ->where([
                            "and",
                            ["req_detail_wide.type" => CoverageController::TYPE_DAILY],
                            ["<", "req_detail_wide.col".$columnNumber, 0],
                          ]);
    $coverage = $query->all();
    $result = [];
    $tmpArr = [];
    foreach($coverage as $crow) {
      unset($tmpArr);
      $actualContract = $crow->req->part->getActualContract();
      $supplierModel = $actualContract->contract->supplier ?? "";
      $countryCode = $supplierModel->countryCode ?? "";
      $supplier = $supplierModel->name ?? "";
      $country = $countryCode->name ?? "";
      $supplier_id = $supplierModel->id ?? "";
      $country_id = $countryCode->id ?? "";
      if($type == "country") {
        if($detailModelId != "na") {
          if($country_id != $detailModelId) {
            continue;
          }
        } else {
          if($country_id != "") {
            continue;
          }
        }
      } elseif($type == "supplier") {
        if($detailModelId != "na") {
          if($supplier_id != $detailModelId) {
            continue;
          }
        } else {
          if($supplier_id != "") {
            continue;
          }
        }
      }
      // Agar postavshik UZ bo'lsa hisobga olmaymiz
      if(($countryCode->alpha_2 ?? "") == "UZ") {
        continue;
      }
      $price = $actualContract->price ?? 0;
      $currency = $actualContract->contract->currency->code ?? "";
      $priceUSD = 0;
      switch($currency) {
        case "EUR":
          $priceUSD = ($price*$rateEUR)/$rateUSD;
          break;
        case "RUB":
          $priceUSD = ($price*$rateRUB)/$rateUSD;
          break;
        case "UZS":
          $priceUSD = $price/$rateUSD;
          break;
        case "USD":
          $priceUSD = $price;
          break;
      }
      $totalRequiredAmount = abs($crow["col".$columnNumber])*$priceUSD;
      $tmpArr = [
        "coun_supp" => $country."|".$supplier."|".$country_id."|".$supplier_id,
        "supplier" => $supplier,
        "country" => $country,
        "amount" => $totalRequiredAmount,
      ];
      if($isDetailed) {
        $tmpArr["lg"] = "l60";
        $tmpArr["coverage"] = $crow->req;
        $tmpArr["price"] = $priceUSD;
        $tmpArr["needQty"] = abs($crow["col".$columnNumber]);
        $tmpArr["daysQty"] = 0; // ???;
      }
      $result[] = $tmpArr;
    }
    return $result;
  }

  protected function getDohData120() {
    $result = $this->getDetailedDohData120();
    $keys = [];
    foreach($result as $row) {
      $keys[] = $row["coun_supp"];
    }
    $keys = array_unique($keys);
    $data = [];
    foreach($keys as $key) {
      $totalCount = 0;
      $totalAmount = 0;
      foreach($result as $row) {
        if($key == $row["coun_supp"]) {
          $totalCount++;
          $totalAmount += $row["amount"];
        }
      }
      $data[] = [
        "key" => $key,
        "total_count" => $totalCount,
        "total_amount" => $totalAmount,
      ];
    }
    return $data;
  }

  protected function getDetailedDohData120($isDetailed = false, $type = false, $detailModelId = null) {
    $rateUSD = CurrencyRate::currentRate(Currency::findOneCurrencyCode("USD")->id);
    $rateEUR = CurrencyRate::currentRate(Currency::findOneCurrencyCode("EUR")->id);
    $rateRUB = CurrencyRate::currentRate(Currency::findOneCurrencyCode("RUB")->id);
    $query = Req::find()->where([
      "and",
      ["type" => CoverageController::TYPE_DAILY],
      [">", "doh", Yii::$app->params["greater_dates_count"]],
    ]);
    $coverage = $query->all();
    $result = [];
    $tmpArr = [];
    foreach($coverage as $crow) {
      unset($tmpArr);
      $actualContract = $crow->part->getActualContract();
      $supplierModel = $actualContract->contract->supplier ?? "";
      $countryCode = $supplierModel->countryCode ?? "";
      $supplier = $supplierModel->name ?? "";
      $country = $countryCode->name ?? "";
      $supplier_id = $supplierModel->id ?? "";
      $country_id = $countryCode->id ?? "";
      if($type == "country") {
        if($detailModelId != "na") {
          if($country_id != $detailModelId) {
            continue;
          }
        } else {
          if($country_id != "") {
            continue;
          }
        }
      } elseif($type == "supplier") {
        if($detailModelId != "na") {
          if($supplier_id != $detailModelId) {
            continue;
          }
        } else {
          if($supplier_id != "") {
            continue;
          }
        }
      }
      // Agar postavshik UZ bo'lsa hisobga olmaymiz
      if(($countryCode->alpha_2 ?? "") == "UZ") {
        continue;
      }
      $price = $actualContract->price ?? 0;
      $currency = $actualContract->contract->currency->code ?? "";
      $priceUSD = 0;
      switch($currency) {
        case "EUR":
          $priceUSD = ($price*$rateEUR)/$rateUSD;
          break;
        case "RUB":
          $priceUSD = ($price*$rateRUB)/$rateUSD;
          break;
        case "UZS":
          $priceUSD = $price/$rateUSD;
          break;
        case "USD":
          $priceUSD = $price;
          break;
      }
      // 120 kunga o'rtacha qancha kerakligi
      $daysQty = Yii::$app->params["greater_dates_count"]*round($crow->part->averageUsage);
      // 120 kundan ortiq qty
      $needQty = $crow->totalstock > 0 ? $crow->totalstock - $daysQty : $daysQty;
      // 120 kunga yetish uchun yoki 120 kundan ortiq summa
      $totalRequiredAmount = $needQty*$priceUSD;
      $tmpArr = [
        "coun_supp" => $country."|".$supplier."|".$country_id."|".$supplier_id,
        "supplier" => $supplier,
        "country" => $country,
        "amount" => $totalRequiredAmount,
      ];
      if($isDetailed) {
        $tmpArr["lg"] = "g120";
        $tmpArr["coverage"] = $crow;
        $tmpArr["price"] = $priceUSD;
        $tmpArr["needQty"] = $needQty;
        $tmpArr["daysQty"] = $daysQty;
      }
      $result[] = $tmpArr;
    }
    return $result;
  }

  public function actionDownloadWeeklyCoverage() {
    $this->checkReportAccess("coverage");
    ini_set("memory_limit", "-1");
    $reqDetailsWide = ReqDetailWide::find()
                                   ->where(["type" => CoverageController::TYPE_WEEKLY])
                                   ->all();
    $period = Helpers::getPeriodWeek2();
    $rateUSD = CurrencyRate::currentRate(Currency::findOneCurrencyCode("USD")->id);
    $rateEUR = CurrencyRate::currentRate(Currency::findOneCurrencyCode("EUR")->id);
    $rateRUB = CurrencyRate::currentRate(Currency::findOneCurrencyCode("RUB")->id);
    $arrFile = [];
    foreach($reqDetailsWide as $detailWide) {
      unset($tmpArray);
      $tmpArray["part_no"] = $detailWide->req->part->part_no;
      $tmpArray["part_color"] = $detailWide->req->part->part_color;
      $tmpArray["partname"] = $detailWide->req->part->part_name;
      $tmpArray["csource"] = $detailWide->req->part->contractSource->name;
      $tmpArray["pmodel"] = $detailWide->req->part->productModel->modelname ?? "";
      $tmpArray["unit"] = $detailWide->req->part->unit->unit_value ?? "";
      $tmpArray["whbal"] = $detailWide->req->whbal;
      $tmpArray["linebal"] = $detailWide->req->linebal;
      $tmpArray["semistock"] = $detailWide->req->semistock;
      $tmpArray["pending"] = $detailWide->req->pending;
      $tmpArray["outsourcing"] = $detailWide->req->outsourcing;
      $tmpArray["arrive"] = $detailWide->req->arrive;
      $tmpArray["totalstock"] =
        $tmpArray["whbal"] +
        $tmpArray["linebal"] +
        $tmpArray["semistock"] +
        $tmpArray["pending"] +
        $tmpArray["outsourcing"] +
        $tmpArray["arrive"];
      $tmpArray["fgstock"] = $detailWide->req->fgstock;
      $tmpArray["days_count"] = $detailWide->req->doh;
      $actualContract = $detailWide->req->part->getActualContract();
      $price = $actualContract->price ?? 0;
      $currency = $actualContract->contract->currency->code ?? "";
      $priceUSD = 0;
      switch($currency) {
        case "EUR":
          $priceUSD = ($price*$rateEUR)/$rateUSD;
          break;
        case "RUB":
          $priceUSD = ($price*$rateRUB)/$rateUSD;
          break;
        case "UZS":
          $priceUSD = $price/$rateUSD;
          break;
        case "USD":
          $priceUSD = $price;
          break;
      }
      $tmpArray["days_count_sum"] = $tmpArray["totalstock"] > 0 ? round($tmpArray["totalstock"]*$priceUSD, 2) : 0;
      foreach($period as $col => $per) {
        $tmpArray["'".$per["plandate"]."'"] = $detailWide->{"col".($col + 1)};
      }
      $arrFile[] = $tmpArray;
    }
    $header_titles = [
      0 => Yii::t("app", "Part number"),
      1 => Yii::t("app", "Part color"),
      2 => Yii::t("app", "Part name"),
      3 => Yii::t("app", "Type"),
      4 => Yii::t("app", "Model"),
      5 => Yii::t("app", "Unit"),
      6 => Yii::t("app", "WH balance"),
      7 => Yii::t("app", "Line balance"),
      8 => Yii::t("app", "Semi balance"),
      9 => Yii::t("app", "Pending balance"),
      10 => Yii::t("app", "Outsourcing balance"),
      11 => Yii::t("app", "Arrived"),
      12 => Yii::t("app", "Total balance"),
      13 => Yii::t("app", "FG balance"),
      14 => Yii::t("app", "DOH (days)"),
      15 => Yii::t("app", 'DOH ($)'),
    ];
    $detail_titles = [];
    $i = 14;
    foreach($period as $per) {
      $detail_titles[$i + 1] =
        strlen(trim($per["plandate"])) > 7
          ? date("d.m", strtotime($per["from"]))."-".date("d.m", strtotime($per["to"]))
          : date("m.Y", strtotime($per["plandate"]));
      $i++;
    }
    $titles = array_merge($header_titles, $detail_titles);
    //        echo "<pre>";
    //        print_r($titles);
    //        echo "</pre>";
    //        die;
    $file = Yii::createObject([
      "class" => 'codemix\excelexport\ExcelFile',
      "sheets" => [
        "coverage" => [
          "data" => $arrFile,
          "titles" => $titles,
        ],
      ],
    ]);
    $file->send(Helpers::downloadFileName("weekly-coverage"));
  }

  public function actionDownloadWeeklyCoverageVehicle() {
    $this->checkReportAccess("coverage");
    ini_set("memory_limit", "-1");
    $covVehicleDetail = CoverageVehicleDetail::find()
                                             ->where(["type" => CoverageVehicleController::TYPE_WEEKLY])
                                             ->all();
    $period = Helpers::getPeriodWeek2();
    $arrFile = [];
    foreach($covVehicleDetail as $det) {
      unset($tmpArray);
      $tmpArray["model"] = $det->coverageVehicle->model->description;
      $tmpArray["stock"] = $det->coverageVehicle->stock;
      $tmpArray["intransit"] = $det->coverageVehicle->intransit;
      $tmpArray["orders"] = $det->coverageVehicle->orders;
      $tmpArray["totalstock"] = $tmpArray["stock"] + $tmpArray["intransit"] + $tmpArray["orders"];
      $tmpArray["doh"] = $det->coverageVehicle->doh;
      $tmpArray["stock_out"] = $det->coverageVehicle->stock_out;
      foreach($period as $col => $per) {
        $tmpArray["'".$per["plandate"]."'"] = $det->{"col".($col + 1)};
      }
      $arrFile[] = $tmpArray;
    }
    $header_titles = [
      0 => Yii::t("app", "Model"),
      1 => Yii::t("app", "Vehicle set stock"),
      2 => Yii::t("app", "TTL Intransit"),
      3 => Yii::t("app", "Paid but not shipped orders"),
      4 => Yii::t("app", "Total pipeline"),
      5 => Yii::t("app", "DOH"),
      6 => Yii::t("app", "Stock out"),
    ];
    $detail_titles = [];
    $i = count($header_titles);
    foreach($period as $per) {
      $detail_titles[$i + 1] =
        strlen(trim($per["plandate"])) > 7
          ? date("d.m", strtotime($per["from"]))."-".date("d.m", strtotime($per["to"]))
          : date("m.Y", strtotime($per["plandate"]));
      $i++;
    }
    $titles = array_merge($header_titles, $detail_titles);
    $file = Yii::createObject([
      "class" => 'codemix\excelexport\ExcelFile',
      "sheets" => [
        "coverage" => [
          "data" => $arrFile,
          "titles" => $titles,
        ],
      ],
    ]);
    $file->send(Helpers::downloadFileName("weekly-coverage-vehicle"));
  }

  protected function getIntransitVehicle() {
    // intransit
    $query = "
			select  
				for_date estdate, model_id , sum(quantity) qty 
			from 
				vehicle_coverage_input 
			where 
				description = :type group by for_date, model_id 
        ";
    $intransit = Yii::$app->db->createCommand($query, [":type" => VehicleCoverageInput::INTRANSIT_ETA])->queryAll();
    $model = [];
    $estdate = [];
    $intransit_result = [];
    foreach($intransit as $tr) {
      $model[] = $tr["model_id"];
      $estdate[] = $tr["estdate"];
    }
    $model = array_unique($model);
    $estdate = array_unique($estdate);
    foreach($estdate as $d) {
      foreach($model as $p) {
        foreach($intransit as $tr) {
          if($tr["model_id"] == $p and $tr["estdate"] == $d) {
            $intransit_result[$d][$p] = (int)$tr["qty"];
            break;
          }
        }
      }
    }
    return $intransit_result;
    // end of intransit
  }

  protected function getPlanVehicle($reportDate = null) {
    $reportDate = $reportDate ? $reportDate : date("Y-m-d");
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
    $plan = Yii::$app->db->createCommand($query, [":report_date" => $reportDate])->queryAll();
    $model = [];
    $target_date = [];
    $plan_result = [];
    foreach($plan as $tr) {
      $model[] = $tr["model_id"];
      $target_date[] = $tr["target_date"];
    }
    $model = array_unique($model);
    $target_date = array_unique($target_date);
    foreach($target_date as $d) {
      foreach($model as $p) {
        foreach($plan as $tr) {
          if($tr["model_id"] == $p and $tr["target_date"] == $d) {
            $plan_result[$d][$p] = (int)$tr["qty"];
            break;
          }
        }
      }
    }
    return $plan_result;
    // end of plan
  }

  // Bu funksiya berilgan model va sana boyicha, ma'lunotlarni ichidan kerakli ma'lumot qayatradi
  // OEM va Intransit uchun ishlaydi (Coverage vehicle)
  public function getVehicleDataByDate($data, $model_id, $from, $to = null) {
    $to = empty($to) ? $from : $to;
    $begin = new DateTime($from);
    $end = new DateTime($to);
    $end = $end->modify("+1 day");
    $daterange = new DatePeriod($begin, new DateInterval("P1D"), $end);
    $qty = 0;
    foreach($daterange as $date) {
      $qty += $data[$date->format("Y-m-d")][$model_id] ?? 0;
    }
    return $qty;
  }

  public function actionDownloadWeeklyCoverageMore() {
    $this->checkReportAccess("coverage");
    ini_set("memory_limit", "-1");
    $reqDetailsWide = ReqDetailWide::find()
                                   ->where(["type" => CoverageController::TYPE_WEEKLY])
                                   ->all();
    $period = Helpers::getPeriodWeek2();
    $rateUSD = CurrencyRate::currentRate(Currency::findOneCurrencyCode("USD")->id);
    $rateEUR = CurrencyRate::currentRate(Currency::findOneCurrencyCode("EUR")->id);
    $rateRUB = CurrencyRate::currentRate(Currency::findOneCurrencyCode("RUB")->id);
    $arrFile = [];
    foreach($reqDetailsWide as $detailWide) {
      unset($tmpArray);
      $tmpArray["part_no"] = $detailWide->req->part->part_no;
      $tmpArray["part_color"] = $detailWide->req->part->part_color;
      $tmpArray["partname"] = $detailWide->req->part->part_name;
      $tmpArray["csource"] = $detailWide->req->part->contractSource->name;
      $tmpArray["pmodel"] = $detailWide->req->part->productModel->modelname ?? "";
      $tmpArray["unit"] = $detailWide->req->part->unit->unit_value ?? "";
      $tmpArray["whbal"] = $detailWide->req->whbal;
      $tmpArray["linebal"] = $detailWide->req->linebal;
      $tmpArray["semistock"] = $detailWide->req->semistock;
      $tmpArray["pending"] = $detailWide->req->pending;
      $tmpArray["outsourcing"] = $detailWide->req->outsourcing;
      $tmpArray["arrive"] = $detailWide->req->arrive;
      $tmpArray["totalstock"] =
        $tmpArray["whbal"] +
        $tmpArray["linebal"] +
        $tmpArray["semistock"] +
        $tmpArray["pending"] +
        $tmpArray["outsourcing"] +
        $tmpArray["arrive"];
      $tmpArray["fgstock"] = $detailWide->req->fgstock;
      $tmpArray["days_count"] = $detailWide->req->doh;
      $actualContract = $detailWide->req->part->getActualContract();
      $price = $actualContract->price ?? 0;
      $currency = $actualContract->contract->currency->code ?? "";
      $priceUSD = 0;
      switch($currency) {
        case "EUR":
          $priceUSD = ($price*$rateEUR)/$rateUSD;
          break;
        case "RUB":
          $priceUSD = ($price*$rateRUB)/$rateUSD;
          break;
        case "UZS":
          $priceUSD = $price/$rateUSD;
          break;
        case "USD":
          $priceUSD = $price;
          break;
      }
      // more data
      $tmpArray["price"] = $price ?? "";
      $tmpArray["currency"] = $currency ?? "";
      $tmpArray["priceUSD"] = $priceUSD ?? "";
      $tmpArray["supplier"] = $actualContract->contract->supplier->name ?? "";
      $tmpArray["country"] = $actualContract->contract->supplier->countryCode->name ?? "";
      $tmpArray["avg_usage"] = round($detailWide->req->part->averageUsage) ?? "";
      $tmpArray["days_count_sum"] = $tmpArray["totalstock"] > 0 ? round($tmpArray["totalstock"]*$priceUSD, 2) : 0;
      foreach($period as $col => $per) {
        $tmpArray["'".$per["plandate"]."'"] = $detailWide->{"col".($col + 1)};
      }
      $arrFile[] = $tmpArray;
    }
    $header_titles = [
      0 => Yii::t("app", "Part number"),
      1 => Yii::t("app", "Part color"),
      2 => Yii::t("app", "Part name"),
      3 => Yii::t("app", "Type"),
      4 => Yii::t("app", "Model"),
      5 => Yii::t("app", "Unit"),
      6 => Yii::t("app", "WH balance"),
      7 => Yii::t("app", "Line balance"),
      8 => Yii::t("app", "Semi balance"),
      9 => Yii::t("app", "Pending balance"),
      10 => Yii::t("app", "Outsourcing balance"),
      11 => Yii::t("app", "Arrived"),
      12 => Yii::t("app", "Total balance"),
      13 => Yii::t("app", "FG balance"),
      14 => Yii::t("app", "DOH (days)"),
      15 => Yii::t("app", "price"),
      16 => Yii::t("app", "currency"),
      17 => Yii::t("app", "usd"),
      18 => Yii::t("app", "supplier"),
      19 => Yii::t("app", "country"),
      20 => Yii::t("app", "avg_usage"),
      21 => Yii::t("app", 'DOH ($)'),
    ];
    $detail_titles = [];
    $i = 22;
    foreach($period as $per) {
      $detail_titles[$i + 1] =
        strlen(trim($per["plandate"])) > 7
          ? date("d.m", strtotime($per["from"]))."-".date("d.m", strtotime($per["to"]))
          : date("m.Y", strtotime($per["plandate"]));
      $i++;
    }
    $titles = array_merge($header_titles, $detail_titles);
    //        echo "<pre>";
    //        print_r($titles);
    //        echo "</pre>";
    //        die;
    $file = Yii::createObject([
      "class" => 'codemix\excelexport\ExcelFile',
      "sheets" => [
        "coverage" => [
          "data" => $arrFile,
          "titles" => $titles,
        ],
      ],
    ]);
    $file->send(Helpers::downloadFileName("weekly-coverage"));
  }

  public function actionDownloadDailyCoverage() {
    $this->checkReportAccess("coverage");
    ini_set("memory_limit", "-1");
    $reqDetailsWide = ReqDetailWide::find()
                                   ->where(["type" => CoverageController::TYPE_DAILY])
                                   ->all();
    $rateUSD = CurrencyRate::currentRate(Currency::findOneCurrencyCode("USD")->id);
    $rateEUR = CurrencyRate::currentRate(Currency::findOneCurrencyCode("EUR")->id);
    $rateRUB = CurrencyRate::currentRate(Currency::findOneCurrencyCode("RUB")->id);
    $period = [];
    foreach(Helpers::getPeriodFull() as $pdate) {
      if($pdate > date("Y-m-d", strtotime("+2 month"))) {
        break;
      }
      $period[] = $pdate;
    }
    $arrFile = [];
    foreach($reqDetailsWide as $detailWide) {
      unset($tmpArray);
      $tmpArray["part_no"] = $detailWide->req->part->part_no;
      $tmpArray["part_color"] = $detailWide->req->part->part_color;
      $tmpArray["partname"] = $detailWide->req->part->part_name;
      $tmpArray["csource"] = $detailWide->req->part->contractSource->name;
      $tmpArray["pmodel"] = $detailWide->req->part->productModel->modelname ?? "";
      $tmpArray["unit"] = $detailWide->req->part->unit->unit_value ?? "";
      $tmpArray["whbal"] = $detailWide->req->whbal;
      $tmpArray["linebal"] = $detailWide->req->linebal;
      $tmpArray["semistock"] = $detailWide->req->semistock;
      $tmpArray["pending"] = $detailWide->req->pending;
      $tmpArray["outsourcing"] = $detailWide->req->outsourcing;
      $tmpArray["arrive"] = $detailWide->req->arrive;
      $tmpArray["totalstock"] =
        $tmpArray["whbal"] +
        $tmpArray["linebal"] +
        $tmpArray["semistock"] +
        $tmpArray["pending"] +
        $tmpArray["outsourcing"] +
        $tmpArray["arrive"];
      $tmpArray["fgstock"] = $detailWide->req->fgstock;
      $tmpArray["days_count"] = $detailWide->req->doh;
      $actualContract = $detailWide->req->part->getActualContract();
      $price = $actualContract->price ?? 0;
      $currency = $actualContract->contract->currency->code ?? "";
      $priceUSD = 0;
      switch($currency) {
        case "EUR":
          $priceUSD = ($price*$rateEUR)/$rateUSD;
          break;
        case "RUB":
          $priceUSD = ($price*$rateRUB)/$rateUSD;
          break;
        case "UZS":
          $priceUSD = $price/$rateUSD;
          break;
        case "USD":
          $priceUSD = $price;
          break;
      }
      $tmpArray["days_count_sum"] = $tmpArray["totalstock"] > 0 ? round($tmpArray["totalstock"]*$priceUSD, 2) : 0;
      foreach($period as $col => $pdate) {
        $tmpArray["'".$pdate."'"] = $detailWide->{"col".($col + 1)};
      }
      $arrFile[] = $tmpArray;
    }
    $header_titles = [
      0 => Yii::t("app", "Part number"),
      1 => Yii::t("app", "Part color"),
      2 => Yii::t("app", "Part name"),
      3 => Yii::t("app", "Type"),
      4 => Yii::t("app", "Model"),
      5 => Yii::t("app", "Unit"),
      6 => Yii::t("app", "WH balance"),
      7 => Yii::t("app", "Line balance"),
      8 => Yii::t("app", "Semi balance"),
      9 => Yii::t("app", "Pending balance"),
      10 => Yii::t("app", "Outsourcing balance"),
      11 => Yii::t("app", "Arrived"),
      12 => Yii::t("app", "Total balance"),
      13 => Yii::t("app", "FG balance"),
      14 => Yii::t("app", "Days count"),
      15 => Yii::t("app", 'DOH ($)'),
    ];
    $detail_titles = [];
    $i = 14;
    foreach($period as $pdate) {
      $detail_titles[$i + 1] = date("d.m.Y", strtotime($pdate));
      $i++;
    }
    $titles = array_merge($header_titles, $detail_titles);
    $file = Yii::createObject([
      "class" => 'codemix\excelexport\ExcelFile',
      "sheets" => [
        "coverage" => [
          "data" => $arrFile,
          "titles" => $titles,
        ],
      ],
    ]);
    $file->send(Helpers::downloadFileName("daily-coverage"));
  }

  public function actionDownloadDailyCoverageVehicle() {
    $this->checkReportAccess("coverage");
    ini_set("memory_limit", "-1");
    $covVehDetail = CoverageVehicleDetail::find()
                                         ->where(["type" => CoverageVehicleController::TYPE_DAILY])
                                         ->all();
    $period = [];
    foreach(Helpers::getPeriodFull() as $pdate) {
      if($pdate > date("Y-m-d", strtotime("+2 month"))) {
        break;
      }
      $period[] = $pdate;
    }
    $arrFile = [];
    foreach($covVehDetail as $det) {
      unset($tmpArray);
      $tmpArray["model"] = $det->coverageVehicle->model->description;
      $tmpArray["stock"] = $det->coverageVehicle->stock;
      $tmpArray["intransit"] = $det->coverageVehicle->intransit;
      $tmpArray["orders"] = $det->coverageVehicle->orders;
      $tmpArray["totalstock"] = $tmpArray["stock"] + $tmpArray["intransit"] + $tmpArray["orders"];
      $tmpArray["doh"] = $det->coverageVehicle->doh;
      $tmpArray["stock_out"] = $det->coverageVehicle->stock_out;
      foreach($period as $col => $pdate) {
        $tmpArray["'".$pdate."'"] = $det->{"col".($col + 1)};
      }
      $arrFile[] = $tmpArray;
    }
    $header_titles = [
      0 => Yii::t("app", "Model"),
      1 => Yii::t("app", "Vehicle set stock"),
      2 => Yii::t("app", "TTL Intransit"),
      3 => Yii::t("app", "Paid but not shipped orders"),
      4 => Yii::t("app", "Total pipeline"),
      5 => Yii::t("app", "DOH"),
      6 => Yii::t("app", "Stock out"),
    ];
    $detail_titles = [];
    $i = count($header_titles);
    foreach($period as $pdate) {
      $detail_titles[$i + 1] = date("d.m.Y", strtotime($pdate));
      $i++;
    }
    $titles = array_merge($header_titles, $detail_titles);
    $file = Yii::createObject([
      "class" => 'codemix\excelexport\ExcelFile',
      "sheets" => [
        "coverage" => [
          "data" => $arrFile,
          "titles" => $titles,
        ],
      ],
    ]);
    $file->send(Helpers::downloadFileName("daily-coverage-vehicle"));
  }

  public function actionDownloadDailyCoverageMore() {
    $this->checkReportAccess("coverage");
    ini_set("memory_limit", "-1");
    $reqDetailsWide = ReqDetailWide::find()
                                   ->where(["type" => CoverageController::TYPE_DAILY])
                                   ->all();
    $rateUSD = CurrencyRate::currentRate(Currency::findOneCurrencyCode("USD")->id);
    $rateEUR = CurrencyRate::currentRate(Currency::findOneCurrencyCode("EUR")->id);
    $rateRUB = CurrencyRate::currentRate(Currency::findOneCurrencyCode("RUB")->id);
    $period = [];
    foreach(Helpers::getPeriodFull() as $pdate) {
      if($pdate > date("Y-m-d", strtotime("+2 month"))) {
        break;
      }
      $period[] = $pdate;
    }
    $arrFile = [];
    foreach($reqDetailsWide as $detailWide) {
      unset($tmpArray);
      $tmpArray["part_no"] = $detailWide->req->part->part_no;
      $tmpArray["part_color"] = $detailWide->req->part->part_color;
      $tmpArray["partname"] = $detailWide->req->part->part_name;
      $tmpArray["csource"] = $detailWide->req->part->contractSource->name;
      $tmpArray["pmodel"] = $detailWide->req->part->productModel->modelname ?? "";
      $tmpArray["unit"] = $detailWide->req->part->unit->unit_value ?? "";
      $tmpArray["whbal"] = $detailWide->req->whbal;
      $tmpArray["linebal"] = $detailWide->req->linebal;
      $tmpArray["semistock"] = $detailWide->req->semistock;
      $tmpArray["pending"] = $detailWide->req->pending;
      $tmpArray["outsourcing"] = $detailWide->req->outsourcing;
      $tmpArray["arrive"] = $detailWide->req->arrive;
      $tmpArray["totalstock"] =
        $tmpArray["whbal"] +
        $tmpArray["linebal"] +
        $tmpArray["semistock"] +
        $tmpArray["pending"] +
        $tmpArray["outsourcing"] +
        $tmpArray["arrive"];
      $tmpArray["fgstock"] = $detailWide->req->fgstock;
      $tmpArray["days_count"] = $detailWide->req->doh;
      $actualContract = $detailWide->req->part->getActualContract();
      $price = $actualContract->price ?? 0;
      $currency = $actualContract->contract->currency->code ?? "";
      $priceUSD = 0;
      switch($currency) {
        case "EUR":
          $priceUSD = ($price*$rateEUR)/$rateUSD;
          break;
        case "RUB":
          $priceUSD = ($price*$rateRUB)/$rateUSD;
          break;
        case "UZS":
          $priceUSD = $price/$rateUSD;
          break;
        case "USD":
          $priceUSD = $price;
          break;
      }
      // more data
      $tmpArray["price"] = $price ?? "";
      $tmpArray["currency"] = $currency ?? "";
      $tmpArray["priceUSD"] = $priceUSD ?? "";
      $tmpArray["supplier"] = $actualContract->contract->supplier->name ?? "";
      $tmpArray["country"] = $actualContract->contract->supplier->countryCode->name ?? "";
      $tmpArray["avg_usage"] = round($detailWide->req->part->averageUsage) ?? "";
      $tmpArray["days_count_sum"] = $tmpArray["totalstock"] > 0 ? round($tmpArray["totalstock"]*$priceUSD, 2) : 0;
      foreach($period as $col => $pdate) {
        $tmpArray["'".$pdate."'"] = $detailWide->{"col".($col + 1)};
      }
      $arrFile[] = $tmpArray;
    }
    $header_titles = [
      0 => Yii::t("app", "Part number"),
      1 => Yii::t("app", "Part color"),
      2 => Yii::t("app", "Part name"),
      3 => Yii::t("app", "Type"),
      4 => Yii::t("app", "Model"),
      5 => Yii::t("app", "Unit"),
      6 => Yii::t("app", "WH balance"),
      7 => Yii::t("app", "Line balance"),
      8 => Yii::t("app", "Semi balance"),
      9 => Yii::t("app", "Pending balance"),
      10 => Yii::t("app", "Outsourcing balance"),
      11 => Yii::t("app", "Arrived"),
      12 => Yii::t("app", "Total balance"),
      13 => Yii::t("app", "FG balance"),
      14 => Yii::t("app", "Days count"),
      15 => Yii::t("app", "price"),
      16 => Yii::t("app", "currency"),
      17 => Yii::t("app", "usd"),
      18 => Yii::t("app", "supplier"),
      19 => Yii::t("app", "country"),
      20 => Yii::t("app", "avg_usage"),
      21 => Yii::t("app", 'DOH ($)'),
    ];
    $detail_titles = [];
    $i = 22;
    foreach($period as $pdate) {
      $detail_titles[$i + 1] = date("d.m.Y", strtotime($pdate));
      $i++;
    }
    $titles = array_merge($header_titles, $detail_titles);
    $file = Yii::createObject([
      "class" => 'codemix\excelexport\ExcelFile',
      "sheets" => [
        "coverage" => [
          "data" => $arrFile,
          "titles" => $titles,
        ],
      ],
    ]);
    $file->send(Helpers::downloadFileName("daily-coverage"));
  }

  public function actionDownloadLocalCoverage() {
    $this->checkReportAccess("coverage");
    ini_set("memory_limit", "-1");
    $reqDetailsWide = ReqDetailWide::find()
                                   ->where(["type" => CoverageController::TYPE_LOCAL_DAILY])
                                   ->all();
    $rateUSD = CurrencyRate::currentRate(Currency::findOneCurrencyCode("USD")->id);
    $rateEUR = CurrencyRate::currentRate(Currency::findOneCurrencyCode("EUR")->id);
    $rateRUB = CurrencyRate::currentRate(Currency::findOneCurrencyCode("RUB")->id);
    $period = [];
    foreach(Helpers::getPeriodFull() as $pdate) {
      if($pdate > date("Y-m-d", strtotime("+2 month"))) {
        break;
      }
      $period[] = $pdate;
    }
    $arrFile = [];
    foreach($reqDetailsWide as $detailWide) {
      unset($tmpArray);
      $tmpArray["part_no"] = $detailWide->req->part->part_no;
      $tmpArray["part_color"] = $detailWide->req->part->part_color;
      $tmpArray["partname"] = $detailWide->req->part->part_name;
      $tmpArray["csource"] = $detailWide->req->part->contractSource->name;
      $tmpArray["pmodel"] = $detailWide->req->part->productModel->modelname ?? "";
      $tmpArray["unit"] = $detailWide->req->part->unit->unit_value ?? "";
      $tmpArray["whbal"] = $detailWide->req->whbal;
      $tmpArray["linebal"] = $detailWide->req->linebal;
      $tmpArray["semistock"] = $detailWide->req->semistock;
      $tmpArray["pending"] = $detailWide->req->pending;
      $tmpArray["outsourcing"] = $detailWide->req->outsourcing;
      $tmpArray["arrive"] = $detailWide->req->arrive;
      $tmpArray["totalstock"] =
        $tmpArray["whbal"] +
        $tmpArray["linebal"] +
        $tmpArray["semistock"] +
        $tmpArray["pending"] +
        $tmpArray["outsourcing"] +
        $tmpArray["arrive"];
      $tmpArray["fgstock"] = $detailWide->req->fgstock;
      $tmpArray["days_count"] = $detailWide->req->doh;
      $actualContract = $detailWide->req->part->getActualContract();
      $price = $actualContract->price ?? 0;
      $currency = $actualContract->contract->currency->code ?? "";
      $convPrice = 0;
      switch($currency) {
        case "EUR":
          $convPrice = ($price*$rateEUR)/$rateUSD;
          break;
        case "RUB":
          $convPrice = ($price*$rateRUB)/$rateUSD;
          break;
        case "USD":
          $convPrice = $price;
          break;
        case "UZS":
          $convPrice = $price/$rateUSD;
          break;
      }
      $tmpArray["days_count_sum"] = $tmpArray["totalstock"] > 0 ? round($tmpArray["totalstock"]*$convPrice, 2) : 0;
      foreach($period as $col => $pdate) {
        if(strlen(trim($pdate)) == 7) {
          continue;
        }
        $tmpArray["'".$pdate."'"] = $detailWide->{"col".($col + 1)};
      }
      $arrFile[] = $tmpArray;
    }
    $header_titles = [
      0 => Yii::t("app", "Part number"),
      1 => Yii::t("app", "Part color"),
      2 => Yii::t("app", "Part name"),
      3 => Yii::t("app", "Type"),
      4 => Yii::t("app", "Model"),
      5 => Yii::t("app", "Unit"),
      6 => Yii::t("app", "WH balance"),
      7 => Yii::t("app", "Line balance"),
      8 => Yii::t("app", "Semi balance"),
      9 => Yii::t("app", "Pending balance"),
      10 => Yii::t("app", "Outsourcing balance"),
      11 => Yii::t("app", "Arrived"),
      12 => Yii::t("app", "Total balance"),
      13 => Yii::t("app", "FG balance"),
      14 => Yii::t("app", "Days count"),
      15 => Yii::t("app", 'DOH ($)'),
    ];
    $detail_titles = [];
    $i = 14;
    foreach($period as $pdate) {
      if(strlen(trim($pdate)) == 7) {
        continue;
      }
      $detail_titles[$i + 1] = date("d.m.Y", strtotime($pdate));
      $i++;
    }
    $titles = array_merge($header_titles, $detail_titles);
    $file = Yii::createObject([
      "class" => 'codemix\excelexport\ExcelFile',
      "sheets" => [
        "coverage" => [
          "data" => $arrFile,
          "titles" => $titles,
        ],
      ],
    ]);
    $file->send(Helpers::downloadFileName("local-coverage"));
  }

  public function actionDownloadConsCoverage() {
    $this->checkReportAccess("coverage");
    ini_set("memory_limit", "-1");
    $reqDetailsWide = ReqDetailWide::find()
                                   ->where(["type" => CoverageController::TYPE_LOCAL_CONS])
                                   ->all();
    $rateUSD = CurrencyRate::currentRate(Currency::findOneCurrencyCode("USD")->id);
    $rateEUR = CurrencyRate::currentRate(Currency::findOneCurrencyCode("EUR")->id);
    $rateRUB = CurrencyRate::currentRate(Currency::findOneCurrencyCode("RUB")->id);
    $period = [];
    foreach(Helpers::getPeriodFull() as $pdate) {
      if($pdate > date("Y-m-d", strtotime("+2 month"))) {
        break;
      }
      $period[] = $pdate;
    }
    $arrFile = [];
    foreach($reqDetailsWide as $detailWide) {
      unset($tmpArray);
      $tmpArray["part_no"] = $detailWide->req->part->part_no;
      $tmpArray["part_color"] = $detailWide->req->part->part_color;
      $tmpArray["partname"] = $detailWide->req->part->part_name;
      $tmpArray["csource"] = $detailWide->req->part->contractSource->name;
      $tmpArray["pmodel"] = $detailWide->req->part->productModel->modelname ?? "";
      $tmpArray["unit"] = $detailWide->req->part->unit->unit_value ?? "";
      $tmpArray["whbal"] = $detailWide->req->whbal;
      $tmpArray["linebal"] = $detailWide->req->linebal;
      $tmpArray["semistock"] = $detailWide->req->semistock;
      $tmpArray["pending"] = $detailWide->req->pending;
      $tmpArray["outsourcing"] = $detailWide->req->outsourcing;
      $tmpArray["arrive"] = $detailWide->req->arrive;
      $tmpArray["totalstock"] =
        $tmpArray["whbal"] +
        $tmpArray["linebal"] +
        $tmpArray["semistock"] +
        $tmpArray["pending"] +
        $tmpArray["outsourcing"] +
        $tmpArray["arrive"];
      $tmpArray["fgstock"] = $detailWide->req->fgstock;
      $tmpArray["days_count"] = $detailWide->req->doh;
      $price = $detailWide->req->part->actualContract->price ?? 0;
      $currency = $detailWide->req->part->actualContract->contract->currency->code ?? "";
      $convPrice = 0;
      switch($currency) {
        case "EUR":
          $convPrice = ($price*$rateEUR)/$rateUSD;
          break;
        case "RUB":
          $convPrice = ($price*$rateRUB)/$rateUSD;
          break;
        case "USD":
          $convPrice = $price;
          break;
        case "UZS":
          $convPrice = $price/$rateUSD;
          break;
      }
      $tmpArray["days_count_sum"] = $tmpArray["totalstock"] > 0 ? round($tmpArray["totalstock"]*$convPrice, 2) : 0;
      foreach($period as $col => $pdate) {
        if(strlen(trim($pdate)) == 7) {
          continue;
        }
        $tmpArray["'".$pdate."'"] = $detailWide->{"col".($col + 1)};
      }
      $arrFile[] = $tmpArray;
    }
    $header_titles = [
      0 => Yii::t("app", "Part number"),
      1 => Yii::t("app", "Part color"),
      2 => Yii::t("app", "Part name"),
      3 => Yii::t("app", "Type"),
      4 => Yii::t("app", "Model"),
      5 => Yii::t("app", "Unit"),
      6 => Yii::t("app", "WH balance"),
      7 => Yii::t("app", "Line balance"),
      8 => Yii::t("app", "Semi balance"),
      9 => Yii::t("app", "Pending balance"),
      10 => Yii::t("app", "Outsourcing balance"),
      11 => Yii::t("app", "Arrived"),
      12 => Yii::t("app", "Total balance"),
      13 => Yii::t("app", "FG balance"),
      14 => Yii::t("app", "Days count"),
      15 => Yii::t("app", 'DOH ($)'),
    ];
    $detail_titles = [];
    $i = 14;
    foreach($period as $pdate) {
      if(strlen(trim($pdate)) == 7) {
        continue;
      }
      $detail_titles[$i + 1] = date("d.m.Y", strtotime($pdate));
      $i++;
    }
    $titles = array_merge($header_titles, $detail_titles);
    $file = Yii::createObject([
      "class" => 'codemix\excelexport\ExcelFile',
      "sheets" => [
        "coverage" => [
          "data" => $arrFile,
          "titles" => $titles,
        ],
      ],
    ]);
    $file->send(Helpers::downloadFileName("cons-coverage"));
  }

  public function actionDownloadSemiCoverage() {
    $this->checkReportAccess("coverage");
    ini_set("memory_limit", "-1");
    $reqDetailsWide = ReqDetailWide::find()
                                   ->where(["type" => CoverageController::TYPE_LOCAL_SEMI])
                                   ->all();
    $rateUSD = CurrencyRate::currentRate(Currency::findOneCurrencyCode("USD")->id);
    $rateEUR = CurrencyRate::currentRate(Currency::findOneCurrencyCode("EUR")->id);
    $rateRUB = CurrencyRate::currentRate(Currency::findOneCurrencyCode("RUB")->id);
    $period = [];
    foreach(Helpers::getPeriodFull() as $pdate) {
      if($pdate > date("Y-m-d", strtotime("+2 month"))) {
        break;
      }
      $period[] = $pdate;
    }
    $arrFile = [];
    foreach($reqDetailsWide as $detailWide) {
      unset($tmpArray);
      $tmpArray["part_no"] = $detailWide->req->part->part_no;
      $tmpArray["part_color"] = $detailWide->req->part->part_color;
      $tmpArray["partname"] = $detailWide->req->part->part_name;
      $tmpArray["csource"] = $detailWide->req->part->contractSource->name;
      $tmpArray["pmodel"] = $detailWide->req->part->productModel->modelname ?? "";
      $tmpArray["unit"] = $detailWide->req->part->unit->unit_value ?? "";
      $tmpArray["whbal"] = $detailWide->req->whbal;
      $tmpArray["linebal"] = $detailWide->req->linebal;
      $tmpArray["semistock"] = $detailWide->req->semistock;
      $tmpArray["pending"] = $detailWide->req->pending;
      $tmpArray["outsourcing"] = $detailWide->req->outsourcing;
      $tmpArray["arrive"] = $detailWide->req->arrive;
      $tmpArray["totalstock"] =
        $tmpArray["whbal"] +
        $tmpArray["linebal"] +
        $tmpArray["semistock"] +
        $tmpArray["pending"] +
        $tmpArray["outsourcing"] +
        $tmpArray["arrive"];
      $tmpArray["fgstock"] = $detailWide->req->fgstock;
      $tmpArray["days_count"] = $detailWide->req->doh;
      $price = $detailWide->req->part->actualContract->price ?? 0;
      $currency = $detailWide->req->part->actualContract->contract->currency->code ?? "";
      $convPrice = 0;
      switch($currency) {
        case "EUR":
          $convPrice = ($price*$rateEUR)/$rateUSD;
          break;
        case "RUB":
          $convPrice = ($price*$rateRUB)/$rateUSD;
          break;
        case "USD":
          $convPrice = $price;
          break;
        case "UZS":
          $convPrice = $price/$rateUSD;
          break;
      }
      $tmpArray["days_count_sum"] = $tmpArray["totalstock"] > 0 ? round($tmpArray["totalstock"]*$convPrice, 2) : 0;
      foreach($period as $col => $pdate) {
        if(strlen(trim($pdate)) == 7) {
          continue;
        }
        $tmpArray["'".$pdate."'"] = $detailWide->{"col".($col + 1)};
      }
      $arrFile[] = $tmpArray;
    }
    $header_titles = [
      0 => Yii::t("app", "Part number"),
      1 => Yii::t("app", "Part color"),
      2 => Yii::t("app", "Part name"),
      3 => Yii::t("app", "Type"),
      4 => Yii::t("app", "Model"),
      5 => Yii::t("app", "Unit"),
      6 => Yii::t("app", "WH balance"),
      7 => Yii::t("app", "Line balance"),
      8 => Yii::t("app", "Semi balance"),
      9 => Yii::t("app", "Pending balance"),
      10 => Yii::t("app", "Outsourcing balance"),
      11 => Yii::t("app", "Arrived"),
      12 => Yii::t("app", "Total balance"),
      13 => Yii::t("app", "FG balance"),
      14 => Yii::t("app", "Days count"),
      15 => Yii::t("app", 'DOH ($)'),
    ];
    $detail_titles = [];
    $i = 14;
    foreach($period as $pdate) {
      if(strlen(trim($pdate)) == 7) {
        continue;
      }
      $detail_titles[$i + 1] = date("d.m.Y", strtotime($pdate));
      $i++;
    }
    $titles = array_merge($header_titles, $detail_titles);
    $file = Yii::createObject([
      "class" => 'codemix\excelexport\ExcelFile',
      "sheets" => [
        "coverage" => [
          "data" => $arrFile,
          "titles" => $titles,
        ],
      ],
    ]);
    $file->send(Helpers::downloadFileName("semi-coverage"));
  }

  public function actionDownloadWeeklyRequirement() {
    $this->checkReportAccess("requirement");
    ini_set("memory_limit", "-1");
    $reqDetailsWide = ReqDetailPlan::find()
                                   ->where(["type" => CoverageController::TYPE_WEEKLY])
                                   ->all();
    $period = Helpers::getPeriodWeek2();
    $arrFile = [];
    foreach($reqDetailsWide as $detailWide) {
      unset($tmpArray);
      $tmpArray["part_no"] = $detailWide->req->part->part_no;
      $tmpArray["part_color"] = $detailWide->req->part->part_color;
      $tmpArray["csource"] = $detailWide->req->part->contractSource->name;
      $tmpArray["pmodel"] = $detailWide->req->part->productModel->modelname ?? "";
      $tmpArray["partname"] = $detailWide->req->part->part_name;
      $tmpArray["unit"] = $detailWide->req->part->unit->unit_value;
      $tmpArray["avg_usage"] = round($detailWide->req->part->averageUsage);
      foreach($period as $col => $per) {
        $tmpArray["'".$per["plandate"]."'"] = $detailWide->{"col".($col + 1)};
      }
      $arrFile[] = $tmpArray;
    }
    $header_titles = [
      0 => Yii::t("app", "Part number"),
      1 => Yii::t("app", "Part color"),
      2 => Yii::t("app", "Type"),
      3 => Yii::t("app", "Model"),
      4 => Yii::t("app", "Part name"),
      5 => Yii::t("app", "Unit"),
      6 => Yii::t("app", "Average usage"),
    ];
    $detail_titles = [];
    $i = 6;
    foreach($period as $per) {
      $detail_titles[$i + 1] =
        strlen(trim($per["plandate"])) > 7
          ? date("d.m", strtotime($per["from"]))."-".date("d.m", strtotime($per["to"]))
          : date("m.Y", strtotime($per["plandate"]));
      $i++;
    }
    $titles = array_merge($header_titles, $detail_titles);
    $file = Yii::createObject([
      "class" => 'codemix\excelexport\ExcelFile',
      "sheets" => [
        "coverage" => [
          "data" => $arrFile,
          "titles" => $titles,
        ],
      ],
    ]);
    $file->send(Helpers::downloadFileName("weekly-requirement"));
  }

  public function actionDownloadDailyRequirement() {
    $this->checkReportAccess("requirement");
    ini_set("memory_limit", "-1");
    $reqDetailsWide = ReqDetailPlan::find()
                                   ->where([
                                     "or",
                                     ["type" => CoverageController::TYPE_DAILY],
                                     ["type" => CoverageController::TYPE_LOCAL_DAILY],
                                     ["type" => CoverageController::TYPE_LOCAL_CONS],
                                     ["type" => CoverageController::TYPE_LOCAL_SEMI],
                                   ])
                                   ->all();
    $period = [];
    foreach(Helpers::getPeriodFull() as $pdate) {
      if($pdate > date("Y-m-d", strtotime("+2 month"))) {
        break;
      }
      $period[] = $pdate;
    }
    $arrFile = [];
    foreach($reqDetailsWide as $detailWide) {
      unset($tmpArray);
      $tmpArray["part_no"] = $detailWide->req->part->part_no;
      $tmpArray["part_color"] = $detailWide->req->part->part_color;
      $tmpArray["csource"] = $detailWide->req->part->contractSource->name;
      $tmpArray["pmodel"] = $detailWide->req->part->productModel->modelname ?? "";
      $tmpArray["partname"] = $detailWide->req->part->part_name;
      $tmpArray["unit"] = $detailWide->req->part->unit->unit_value;
      $tmpArray["avg_usage"] = round($detailWide->req->part->averageUsage);
      foreach($period as $col => $pdate) {
        if(strlen(trim($pdate)) == 7) {
          continue;
        }
        $tmpArray["'".$pdate."'"] = $detailWide->{"col".($col + 1)};
      }
      $arrFile[] = $tmpArray;
    }
    $header_titles = [
      0 => Yii::t("app", "Part number"),
      1 => Yii::t("app", "Part color"),
      2 => Yii::t("app", "Type"),
      3 => Yii::t("app", "Model"),
      4 => Yii::t("app", "Part name"),
      5 => Yii::t("app", "Unit"),
      6 => Yii::t("app", "Average usage"),
    ];
    $detail_titles = [];
    $i = 6;
    foreach($period as $pdate) {
      if(strlen(trim($pdate)) == 7) {
        continue;
      }
      $detail_titles[$i + 1] = date("d.m.Y", strtotime($pdate));
      $i++;
    }
    $titles = array_merge($header_titles, $detail_titles);
    $file = Yii::createObject([
      "class" => 'codemix\excelexport\ExcelFile',
      "sheets" => [
        "coverage" => [
          "data" => $arrFile,
          "titles" => $titles,
        ],
      ],
    ]);
    $file->send(Helpers::downloadFileName("daily-requirement"));
  }

  public function actionDownloadInTransit() {
    $this->checkReportAccess("in-transit");
    ini_set("memory_limit", "-1");
    $data = $this->getInTransitData();
    $period = Helpers::getPeriod60Days();
    $arrFile = [];
    foreach($data as $row) {
      unset($tmpArray);
      $tmpArray["part_no"] = $row["part"]->part_no;
      $tmpArray["part_color"] = $row["part"]->part_color;
      $tmpArray["partname"] = $row["part"]->part_name;
      $tmpArray["csource"] = $row["part"]->contractSource->name;
      $tmpArray["pmodel"] = $row["part"]->productModel->modelname ?? "";
      $tmpArray["unit"] = $row["part"]->unit->unit_value;
      foreach($period as $col => $per) {
        $tmpArray["'".date("d.m", strtotime($per["plandate"]))."'"] = $row[$per["plandate"]];
      }
      $arrFile[] = $tmpArray;
    }
    $header_titles = [
      0 => Yii::t("app", "Part number"),
      1 => Yii::t("app", "Part color"),
      2 => Yii::t("app", "Part name"),
      3 => Yii::t("app", "Type"),
      4 => Yii::t("app", "Model"),
      5 => Yii::t("app", "Unit"),
    ];
    $detail_titles = [];
    $i = 5;
    foreach($period as $per) {
      $detail_titles[$i + 1] = date("d.m", strtotime($per["plandate"]));
      $i++;
    }
    $titles = array_merge($header_titles, $detail_titles);
    $file = Yii::createObject([
      "class" => 'codemix\excelexport\ExcelFile',
      "sheets" => [
        "coverage" => [
          "data" => $arrFile,
          "titles" => $titles,
        ],
      ],
    ]);
    $file->send(Helpers::downloadFileName("in-transite"));
  }

  public function actionDownloadImport($need_month) {
    $this->checkReportAccess("import");
    ini_set("memory_limit", "-1");
    $data = $this->getImportData($need_month);
    $arrFile = [];
    foreach($data as $row) {
      unset($tmpArray);
      $tmpArray["part_no"] = $row["part_no"];
      $tmpArray["part_color"] = $row["part_color"];
      $tmpArray["partname"] = $row["part_name"];
      $tmpArray["supplier"] = $row["supplier"];
      $tmpArray["country"] = $row["country"];
      $tmpArray["tnved"] = $row["tnved"];
      $tmpArray["price"] = $row["price"];
      $tmpArray["qty"] = $row["qty"];
      $tmpArray["uom"] = $row["uom"];
      $tmpArray["amount"] = $row["amount"];
      $tmpArray["currency"] = $row["currency"];
      $arrFile[] = $tmpArray;
    }
    $titles = [
      0 => Yii::t("app", "Part number"),
      1 => Yii::t("app", "Part color"),
      2 => Yii::t("app", "Part name"),
      3 => Yii::t("app", "Supplier"),
      4 => Yii::t("app", "Country"),
      5 => Yii::t("app", "CNFEA Code"),
      6 => Yii::t("app", "Price"),
      7 => Yii::t("app", "Qty"),
      8 => Yii::t("app", "UOM"),
      9 => Yii::t("app", "Amount"),
      10 => Yii::t("app", "Currency"),
    ];
    $file = Yii::createObject([
      "class" => 'codemix\excelexport\ExcelFile',
      "sheets" => [
        "coverage" => [
          "data" => $arrFile,
          "titles" => $titles,
        ],
      ],
    ]);
    $file->send(Helpers::downloadFileName("import"));
  }

  protected function getIntransit($part_id, $from, $to) {
    $begin = new DateTime($from);
    $end = new DateTime($to);
    $end = $end->modify("+1 day");
    $daterange = new DatePeriod($begin, new DateInterval("P1D"), $end);
    $qty = 0;
    foreach($daterange as $date) {
      if(!empty($this->intransit_result[$date->format("Y-m-d")][$part_id])) {
        $qty += $this->intransit_result[$date->format("Y-m-d")][$part_id];
      }
    }
    return $qty;
  }

  public function actionFtqByLine() {
    $downloadFileName = Helpers::downloadFileName("ftq-by-line", "1");
    $downloadFileName = rtrim($downloadFileName, ".1");
    $this->checkReportAccess();
    $shift = Helpers::getPeriod();
    $filter_from = $shift["start_at"];
    $filter_to = $shift["end_at"];
    if(isset($_POST["filter_from"])) {
      $filter_from = Helpers::getPeriod($_POST["filter_from"])["start_at"];
    }
    if(isset($_POST["filter_to"])) {
      $filter_to = Helpers::getPeriod($_POST["filter_to"])["end_at"];
    }
    // product_line
    $product_line = null;
    if(isset($_POST["product_line"])) {
      $product_line = $_POST["product_line"];
    }
    $query =
      "SELECT 
					p.part_no, 
					pl.linename, 
					pm.modelname, 
					T1.produced, 
					IFNULL(T2.defects,0) as defects 
				FROM (SELECT part_id, SUM(quantity) as produced FROM `production_order` WHERE created_at>=UNIX_TIMESTAMP('$filter_from') AND created_at<=UNIX_TIMESTAMP('$filter_to') AND is_label = 0 GROUP BY part_id) T1 
				INNER JOIN part p ON T1.part_id=p.id
				LEFT JOIN product_line pl on p.product_line_id=pl.id
				LEFT JOIN product_model pm on p.product_model_id=pm.id
				LEFT JOIN (SELECT po.part_id, sum(qty) as defects FROM `production_order_defect` pod INNER JOIN production_order po on pod.production_order_id=po.id WHERE  pod.created_at>=UNIX_TIMESTAMP('$filter_from') AND pod.created_at<=UNIX_TIMESTAMP('$filter_to') AND po.is_label = 0 GROUP BY po.part_id)T2
					ON T1.part_id=T2.part_id".
      ($product_line ? " WHERE pl.id=".$product_line : "").
      " ORDER BY pl.linename, defects DESC, p.part_no";
    $data = Yii::$app->db->createCommand($query)->queryAll();
    return $this->render(
      "ftq-by-line",
      compact("data", "filter_from", "filter_to", "product_line", "downloadFileName")
    );
  }

  public function actionFtqSummary() {
    $downloadFileName = Helpers::downloadFileName("ftq-summary", "1");
    $downloadFileName = rtrim($downloadFileName, ".1");
    $this->checkReportAccess();
    $this->layout = "req";
    //filterMonth
    $filterMonth = isset($_POST["filterMonth"]) ? $_POST["filterMonth"] : date("Y-m", time());
    $month_end = date("Y-m-t", strtotime($filterMonth));
    $month_end_date = date("t", strtotime($month_end));
    $fromDT = $filterMonth."-01 ".Yii::$app->params["shifts"]["1"]["0"];
    $toDT = $filterMonth."-".$month_end_date." ".Yii::$app->params["shifts"]["2"]["1"]["1"];
    // filterLine
    $filterLine = null;
    if(isset($_POST["filterLine"]) && strlen($_POST["filterLine"]) > 0) {
      $filterLine = $_POST["filterLine"];
      $whereFilterLinePO = "AND part_id IN (SELECT id FROM part WHERE product_line_id=".$filterLine.")";
    } else {
      $whereFilterLinePO = "AND part_id IN (SELECT id FROM part WHERE product_line_id IS NOT NULL)";
    }
    $whereFilterMonth = " prod_dt between '".$fromDT."' AND '".$toDT."'";
    $whereFilterShift = null;
    if(isset($_POST["filterShift"]) && strlen($_POST["filterShift"]) > 0) {
      $filterShift = $_POST["filterShift"];
      $whereFilterShift = "AND shift=".$filterShift;
    }
    $shift0000 = Yii::$app->params["shifts"]["2"]["1"]["0"];
    $shift0759 = Yii::$app->params["shifts"]["2"]["1"]["1"];
    $shift0800 = Yii::$app->params["shifts"]["1"]["0"];
    $shift2000 = Yii::$app->params["shifts"]["1"]["1"];
    $prodDtShift =
      "
         CASE
            WHEN (FROM_UNIXTIME(created_at, '%Y-%m-%d %H%i') BETWEEN (CONCAT(FROM_UNIXTIME(created_at, '%Y-%m-%d'), ' ', '".
      $shift0000.
      "')) 
                AND(CONCAT(FROM_UNIXTIME(created_at, '%Y-%m-%d'), ' ', '".
      $shift0759.
      "'))) 
            THEN FROM_UNIXTIME((created_at - 86400), '%Y-%m-%d')
            ELSE FROM_UNIXTIME(created_at, '%Y-%m-%d')
         END AS prod_dt
       , CASE
            WHEN (FROM_UNIXTIME(created_at, '%Y-%m-%d %H%i') BETWEEN (CONCAT(FROM_UNIXTIME(created_at, '%Y-%m-%d'), ' ', '".
      $shift0800.
      "')) 
                AND(CONCAT(FROM_UNIXTIME(created_at, '%Y-%m-%d'), ' ', '".
      $shift2000.
      "'))) 
            THEN 1
            ELSE 2
         END AS shift";
    $queryPO =
      "SELECT prod_dt, SUM(quantity) qty 
    FROM (SELECT  part_id, quantity, ".
      $prodDtShift.
      "
        FROM production_order
        WHERE is_label=".
      ProductionOrder::LABEL_ACTUAL.
      " 
          AND current_event=".
      ProductionOrder::EVENT_PRODUCED.
      "
          ".
      $whereFilterLinePO.
      "
       ) po
    WHERE ".
      $whereFilterMonth.
      " ".
      $whereFilterShift.
      " 
    GROUP BY prod_dt";
    $resPO = Yii::$app->db->createCommand($queryPO)->queryAll() ?? null;
    $queryDef =
      "SELECT prod_dt, defect_id, SUM(qty) qty
    FROM (SELECT defect_id, qty, ".
      $prodDtShift.
      "
        FROM production_order_defect) def
    WHERE  ".
      $whereFilterMonth.
      " ".
      $whereFilterShift.
      " 
    GROUP BY prod_dt, defect_id";
    //    echo "<pre>"; print_r($queryDef);echo "</pre>";  die;
    $resDef = Yii::$app->db->createCommand($queryDef)->queryAll() ?? null;
    /**  ********************************************************************* */
    $begin = new DateTime($fromDT);
    $end = new DateTime($toDT);
    $end = $end->modify("+1 day");
    $beginSana = $begin->format("Y-m-d");
    $daterange = new DatePeriod($begin, new DateInterval("P1D"), $end);
    $arrFile = $tmpArr = [];
    /** Summary ma`lumotlarni bir oyga aylantirish*/
    $arrFile["fakt"]["title"] = "summFakt";
    $arrFile["defect"]["title"] = "summDefect";
    $arrFile["foiz"]["title"] = "summFoiz";
    foreach($daterange as $date) {
      $prodSana = $date->format("Y-m-d");
      $kun = $date->format("d");
      if(!empty($resPO)) {
        /** fakt summary qilish*/
        $arrFile["fakt"][$kun] = 0;
        foreach($resPO as $prodOrder) {
          if(isset($prodOrder["prod_dt"])) {
            if($prodSana == $prodOrder["prod_dt"]) {
              $arrFile["fakt"][$kun] = $prodOrder["qty"];
            } // shu kunga prod_dt bormi?
          } //prod_dt bormi?
        } //fakt bo`yicha tsikl
      } // PO bormi?
      /** defect summary qilish*/
      $arrFile["defect"][$kun] = 0;
      if(!empty($resDef)) {
        foreach($resDef as $prodDefect) {
          if(isset($prodDefect["prod_dt"])) {
            if($prodSana == $prodDefect["prod_dt"]) {
              if(isset($arrFile["defect"][$kun])) {
                $arrFile["defect"][$kun] += $prodDefect["qty"];
              } else {
                $arrFile["defect"][$kun] = $prodDefect["qty"];
              }
            } // shu kunga prod_dt bormi?
          } //prod_dt bormi?
        } //defect bo`yicha tsikl
      } // defect bormi?
      $arrFile["foiz"][$kun] = 0;
      if(isset($arrFile["fakt"][$kun])) {
        if($arrFile["fakt"][$kun] > 0) {
          $arrFile["foiz"][$kun] = 100 - ($arrFile["defect"][$kun]*100)/$arrFile["fakt"][$kun];
        }
      }
    } // beginDT-toDT tsikl
    $arrResult[] = $arrFile["fakt"] ?? null;
    $arrResult[] = $arrFile["defect"] ?? null;
    $arrResult[] = $arrFile["foiz"] ?? null;
    $arr = $arrFile = $tmpArr = $tmpArray = [];
    if(!empty($resDef)) {
      foreach($resDef as $resDefs) {
        $uResDefs[] = $resDefs["defect_id"]."|".$resDefs["prod_dt"];
        $uDefLists[] = $resDefs["defect_id"];
      }
    }
    $uResDef = [];
    if(isset($uResDefs)) {
      $uResDef = array_unique($uResDefs);
      $uDefList = array_unique($uDefLists);
      foreach($uDefList as $uDefectId) {
        $defect = Defect::findOne(["id" => $uDefectId]);
        $arr["title"] = $defect->code;
        foreach($daterange as $date) {
          $arr[$date->format("d")] = 0;
        }
        foreach($uResDef as $uResDefItem) {
          [$arrDefectId, $prodDT] = explode("|", $uResDefItem);
          $kun = date("d", strtotime($prodDT));
          if($uDefectId == $arrDefectId) {
            foreach($resDef as $ResDefItem) {
              if($arrDefectId == $ResDefItem["defect_id"] && $prodDT == $ResDefItem["prod_dt"]) {
                $arr[$kun] += $ResDefItem["qty"];
              }
            }
          }
        }
        $arrResult[] = $arr;
      }
    }
    $arr = [];
    foreach($arrResult as $arrKey => $arr) {
      foreach($arr as $key => $val) {
        if($key != "title") {
          $arrResult[$arrKey][$key] = $val > 0 ? Helpers::numberFormatRemoveZero($val) : null;
        }
      }
    }
    //    $data = isset($arrResult)? json_encode($arrResult) : null;
    $data = isset($arrResult) ? $arrResult : null;
    /**  ********************************************************************* */
    $lineList = ArrayHelper::map(ProductLine::find()->all(), "id", "linename");
    return $this->render("ftq-summary", [
      "data" => $data,
      "lineList" => $lineList ?? null,
      "filterLine" => $filterLine,
      "filterMonth" => $filterMonth,
      "filterShift" => $filterShift ?? null,
      "downloadFileName" => $downloadFileName ?? null,
    ]);
  }

  public function actionPipeline() {
    $downloadFileName = Helpers::downloadFileName("pipeline", "1");
    $downloadFileName = rtrim($downloadFileName, ".1");
    $this->checkReportAccess();
    $query = "SELECT ci.current_locate, ci.app_arr_at, count(ci.id) as total 
								FROM container_invoice ci INNER JOIN ship_mode sm ON ci.ship_mode_id=sm.id 
								where sm.name='Container' 
								GROUP BY ci.current_locate, ci.app_arr_at ORDER BY ci.app_arr_at";
    $data = Yii::$app->db->createCommand($query)->queryAll();
    return $this->render("pipeline", compact("data", "downloadFileName"));
  }

  public function actionFgInvoice() {
    $downloadFileName = Helpers::downloadFileName("fg-invoice", "1");
    $downloadFileName = rtrim($downloadFileName, ".1");
    $this->checkReportAccess();
    $factory = $_POST["factory"] ?? null;
    $part = $_POST["part"] ?? null;
    $from = date("Y-m-d"); //$shift['start_at'];
    $to = date("Y-m-d");   //$shift['end_at'];
    if(isset($_POST["from"])) {
      $from = $_POST["from"]; // Helpers::getPeriod($_POST['from'])['start_at'];
    }
    if(isset($_POST["to"])) {
      $to = $_POST["to"]; //Helpers::getPeriod($_POST['to'])['end_at'];
    }
    $query =
      "SELECT 
                f.contract, 
                f.invoice_no, 
                f.invoice_date, 
                f.name,
                fd.part_no, 
                SUM(fd.qty) as qty, 
                SUM(fd.qty*fd.price) as price, 
                sum(fd.qty*fd.price*IFNULL(f.vat,0)/100) as VAT 
              FROM `fg_invoice_detail` fd 
              INNER JOIN (
                SELECT fi.*, fa.name FROM `fg_invoice` fi 
                INNER JOIN `factory` fa ON fi.factory_id=fa.id
						    WHERE fi.`invoice_date`>='$from' 
							      AND fi.`invoice_date`<='$to'".
      ($factory ? " AND fi.`factory_id`=".$factory : "").
      " 
					    ) f	ON fd.fg_invoice_id=f.id ".
      ($part ? "WHERE fd.`part_no`='$part' " : "").
      " GROUP BY f.name, f.contract, f.invoice_no, f.invoice_date, fd.part_no
                ORDER BY f.name, f.contract, f.invoice_no, f.invoice_date, fd.part_no";
    $data = Yii::$app->db->createCommand($query)->queryAll();
    return $this->render("fg-invoice", compact("data", "factory", "part", "from", "to", "downloadFileName"));
  }

  public function actionOpenOrder() {
    $downloadFileName = Helpers::downloadFileName("open-order", "1");
    $downloadFileName = rtrim($downloadFileName, ".1");
    $this->checkReportAccess();
    ini_set("memory_limit", "1024M");
    $query = "SELECT orders.*, 
      part.part_color, part.part_no, part.part_name, supplier.supplier_name, supplier.supplier_duns,
      unit.unit_value, currency.currency_code AS currency_code,
      last_ship.last_ship_dt, ifnull(contract_detail.cont_price,0) as cont_price 
    FROM
    (
    SELECT inv_order_id, inv_qty_sum, order_id, order_no, delivery_term_id, order_type, order_dt, mr_dt, contract_id, IFNULL(order_qty,0) AS order_qty, IFNULL(inv_part_id, part_id) AS part_id FROM
      (
      SELECT * FROM
        (SELECT part_order_id AS inv_order_id, part_id AS inv_part_id, SUM(qty)  AS inv_qty_sum
              FROM invoice_detail 
              GROUP BY part_order_id, part_id) l_inv_sum
        LEFT JOIN 
        (SELECT part_order_id AS order_id, order_no, delivery_term_id, order_type, iss_dt AS order_dt, mr_dt, contract_id, part_id, qty AS order_qty 
              FROM 
                (SELECT id, order_no, delivery_term_id,
                    CASE order_type
                      WHEN 1 THEN 'Regular'
                      WHEN 2 THEN 'Urgent'
                      WHEN 3 THEN 'Urgent'
                      ELSE NULL
                    END as 'order_type', iss_dt, mr_dt, contract_id FROM part_order
                ) ordrs
              LEFT JOIN 
                (SELECT part_order_id, part_id, qty FROM part_order_detail) order_dtl
              ON ordrs.id = order_dtl.part_order_id) l_orders
        ON l_inv_sum.inv_order_id = l_orders.order_id AND l_inv_sum.inv_part_id = l_orders.part_id
        UNION 
        SELECT * FROM 
        (SELECT part_order_id AS inv_order_id, part_id AS inv_part_id, SUM(qty)  AS inv_qty_sum
              FROM invoice_detail 
              GROUP BY part_order_id, part_id) r_inv_sum
        RIGHT JOIN 
        (SELECT part_order_id AS order_id, order_no, delivery_term_id, order_type, iss_dt AS order_dt, mr_dt, contract_id, part_id, qty AS order_qty 
              FROM 
                (SELECT id, order_no, delivery_term_id,
                    CASE order_type
                      WHEN 1 THEN 'Regular'
                      WHEN 2 THEN 'Urgent'
                      WHEN 3 THEN 'Urgent'
                      ELSE NULL
                    END as 'order_type', iss_dt, mr_dt, contract_id FROM part_order
                ) ordrs
              LEFT JOIN 
                (SELECT part_order_id, part_id, qty FROM part_order_detail) order_dtl
              ON ordrs.id = order_dtl.part_order_id) r_orders
        ON r_inv_sum.inv_order_id = r_orders.order_id AND r_inv_sum.inv_part_id = r_orders.part_id
      ) a
    ) orders
    LEFT JOIN 
    (SELECT part_order_id, MAX(shipped_at) last_ship_dt from 
        (
          SELECT part_order_id,contract_id,cont_inv_id FROM  invoice_detail 	
          GROUP BY part_order_id,contract_id,cont_inv_id
        ) cont_inv
        LEFT JOIN container_invoice ON cont_inv.cont_inv_id = container_invoice.id
        GROUP BY part_order_id) last_ship
    ON orders.inv_order_id = last_ship.part_order_id				
    LEFT JOIN (SELECT id AS pt_id, part_color, part_no, part_name, unit_id FROM part) part  ON orders.part_id = part.pt_id
    LEFT JOIN (SELECT id AS unit_id, unit_value FROM unit )unit ON part.unit_id = unit.unit_id  
    LEFT JOIN (SELECT id, supplier_id, currency_id FROM contract) AS contract ON orders.contract_id = contract.id
    LEFT JOIN (SELECT id AS supplier_id, name AS supplier_name, duns AS supplier_duns FROM supplier) AS supplier ON contract.supplier_id = supplier.supplier_id
    LEFT JOIN (SELECT id AS currency_id, code AS currency_code FROM currency) AS currency ON contract.currency_id = currency.currency_id
    LEFT JOIN (SELECT contract_id AS cont_id, part_id AS cont_part_id, delivery_term_id AS cont_del_term_id, price AS cont_price FROM contract_detail) AS contract_detail 
    ON orders.contract_id = contract_detail.cont_id AND orders.delivery_term_id = contract_detail.cont_del_term_id AND orders.part_id = contract_detail.cont_part_id 
    ORDER BY order_no
      ";
    //    echo "<pre>"; print_r($query);echo "</pre>"; die;
    $data = Yii::$app->db->createCommand($query)->queryAll() ? Yii::$app->db->createCommand($query)->queryAll() : null;
    return $this->render("open-order", compact("data", "downloadFileName"));
  }

  public function actionXlsVisitor() {
    ini_set("memory_limit", "-1");
    $searchModel = new VisitorSearch();
    $xsl_file = $searchModel->search(Yii::$app->request->queryParams, "excel");
    $xsl_file->send(Helpers::downloadFileName("visitors"));
  }

  public function actionVisitors() {
    $this->checkReportAccess();
    $model = new ReportForm();
    $hhmm = date("Hi");
    //$hhmm = "2000";
    if($hhmm >= "0800" and $hhmm <= "1959") {
      $model->from1 = date("Y-m-d 08:00");
      $model->to1 = date("Y-m-d 19:59");
      $model->from2 = date("Y-m-d 08:00", strtotime("-30 days"));
      $model->to2 = date("Y-m-d 19:59");
    } else {
      $model->from1 = date("Y-m-d 20:00", strtotime("-1 days"));
      $model->to1 = date("Y-m-d 07:59");
      $model->from2 = date("Y-m-01 20:00", strtotime("-30 days"));
      $model->to2 = date("Y-m-d 07:59");
    }
    $model->load(Yii::$app->request->post());
    //chart1
    $data["chart1"] = $this->generatePageData($model->from1, $model->to1);
    //chart2
    $data["chart2"] = $this->generateUserData($model->from1, $model->to1);
    //chart3
    $data["chart3"] = $this->generateRoleData($model->from1, $model->to1);
    //    //chart4
    //    $data['chart4'] = $this->generateBrowserData($model->from1, $model->to1);
    //chart5
    //    $data['chart5'] = $this->generateDeviceTypeData($model->from1, $model->to1);
    //chart6
    $data_chart6 = $this->generateDateData($model->from2, $model->to2);
    $data["chart6_categories"] = $data_chart6["categories"];
    $data["chart6_series"] = $data_chart6["series"];
    return $this->render("visitors", [
      "model" => $model,
      "data" => $data,
    ]);
  }

  public function actionAllVisitors() {
    $this->checkReportAccess();
    $searchModel = new VisitorSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    $dataProvider->sort->defaultOrder = ["id" => SORT_DESC];
    return $this->render("all-visitors", [
      "searchModel" => $searchModel,
      "dataProvider" => $dataProvider,
    ]);
  }

  public function actionViewVisitor($id) {
    return $this->render("view-visitor", [
      "model" => $this->findVisitor($id),
    ]);
  }

  protected function findVisitor($id) {
    if(($model = Visitor::findOne($id)) !== null) {
      return $model;
    }
    throw new NotFoundHttpException(Yii::t("app", "The requested page does not exist."));
  }

  protected $actions_outside_report = [
    "site/index",
    "site/login",
    "site/logout",
    "part/get-partname",
    "part/get-parts-by-floc",
  ];

  protected function getAdminids() {
    return ArrayHelper::map(
      Role::find()
          ->where(["item_name" => "admin"])
          ->all(),
      "user_id",
      "user_id"
    );
  }

  protected function getCountsByPage($from, $to) {
    $sql =
      "
            select concat(`controller`,'/',`action`) page_title, count(*) as  count from visitor 
            where visited_at between '".
      $from.
      "' and '".
      $to.
      "' 
            
            and user_id not in (".
      implode(",", $this->adminids).
      ")
            and concat(`controller`,'/',`action`) not in ('".
      implode('\',\'', $this->actions_outside_report).
      "')
                
            group by concat(`controller`,'/',`action`)
            order by count desc       
        ";
    return Yii::$app->db->createCommand($sql)->queryAll();
  }

  protected function getCountsByUser($from, $to) {
    $sql =
      "
                select user_id ,ifnull(fullname, 'Гость') fullname, count(*) as  count from 
                visitor left join user on visitor.user_id = user.id 
                where visited_at between '".
      $from.
      "' and '".
      $to.
      "'  
                
                and user_id not in (".
      implode(",", $this->adminids).
      ")
                and concat(`controller`,'/',`action`) not in ('".
      implode('\',\'', $this->actions_outside_report).
      "')
                
                group by user_id
                order by count desc  
        ";
    //        echo $sql;
    //        die;
    return Yii::$app->db->createCommand($sql)->queryAll();
  }

  protected function getCountsByRole($from, $to) {
    $sql =
      "
                select item_name, count(*) as  count from 
                visitor 
                left join auth_assignment on visitor.user_id = auth_assignment.user_id
                where visited_at between '".
      $from.
      "' and '".
      $to.
      "' 
                    
                and item_name is not null 
                    
                and visitor.user_id not in (".
      implode(",", $this->adminids).
      ")
                and concat(`controller`,'/',`action`) not in ('".
      implode('\',\'', $this->actions_outside_report).
      "')
                
                group by item_name
                order by count desc  
        ";
    //        echo $sql;
    //        die;
    return Yii::$app->db->createCommand($sql)->queryAll();
  }

  protected function getCountsByBrowser($from, $to) {
    $sql =
      "
            select ifnull(user_browser, 'Другие') user_browser, count(*) as  count from visitor 
            where visited_at between '".
      $from.
      "' and '".
      $to.
      "' 
                
            and user_id not in (".
      implode(",", $this->adminids).
      ")
            and concat(`controller`,'/',`action`) not in ('".
      implode('\',\'', $this->actions_outside_report).
      "')
                
            group by user_browser
            order by count desc       
        ";
    //        echo $sql;
    //        die;
    return Yii::$app->db->createCommand($sql)->queryAll();
  }

  protected function getCountsByDeviceType($from, $to) {
    $sql =
      "
            select ifnull(user_device_type, 'Другие') user_device_type, count(*) as  count from visitor 
            where visited_at between '".
      $from.
      "' and '".
      $to.
      "' 
                
            and user_id not in (".
      implode(",", $this->adminids).
      ")
            and concat(`controller`,'/',`action`) not in ('".
      implode('\',\'', $this->actions_outside_report).
      "')
                
            group by user_device_type
            order by count desc       
        ";
    //        echo $sql;
    //        die;
    return Yii::$app->db->createCommand($sql)->queryAll();
  }

  protected function getCountsByDate($from, $to) {
    $sql =
      "
            select date(visited_at) visited_at, count(*) count from visitor 
            where visited_at between '".
      $from.
      "' and '".
      $to.
      "'  
                
            and user_id not in (".
      implode(",", $this->adminids).
      ")
            and concat(`controller`,'/',`action`) not in ('".
      implode('\',\'', $this->actions_outside_report).
      "')
                
            group by date(visited_at) 
            order by date(visited_at)      
        ";
    //        echo $sql;
    //        die;
    return Yii::$app->db->createCommand($sql)->queryAll();
  }

  protected function generatePageData($from, $to) {
    $data = $this->getCountsByPage($from, $to);
    $result = "[";
    foreach($data as $key => $item) {
      $result .= "['".Yii::t("app", str_replace("'", "`", $item["page_title"]))."', ".$item["count"]."]";
      if($key + 1 != count($data)) {
        $result .= ", ";
      }
    }
    $result .= "]";
    return $result;
  }

  protected function generateCompanyData($from, $to) {
    $data = $this->getCountsByCompany($from, $to);
    $result = "[";
    foreach($data as $key => $item) {
      $result .= "['".Yii::t("app", str_replace("'", "`", $item["company_title"]))."', ".$item["count"]."]";
      if($key + 1 != count($data)) {
        $result .= ", ";
      }
    }
    $result .= "]";
    return $result;
  }

  protected function generateUserData($from, $to) {
    $data = $this->getCountsByUser($from, $to);
    $result = "[";
    foreach($data as $key => $item) {
      $result .= "['".str_replace("'", "`", $item["fullname"])."', ".$item["count"]."]";
      if($key + 1 != count($data)) {
        $result .= ", ";
      }
    }
    $result .= "]";
    return $result;
  }

  protected function generateRoleData($from, $to) {
    $roles = $this->getCountsByRole($from, $to);
    $result = "[";
    $i = 0;
    foreach($roles as $role) {
      $i++;
      $result .= '{"name": "'.$role["item_name"].'","y": '.$role["count"]."},";
    }
    $result .= "]";
    return $result;
  }

  protected function generateBrowserData($from, $to) {
    $roles = $this->getCountsByBrowser($from, $to);
    $result = "[";
    $i = 0;
    foreach($roles as $role) {
      $i++;
      $result .= '{"name": "'.$role["user_browser"].'","y": '.$role["count"]."},";
    }
    $result .= "]";
    return $result;
  }

  protected function generateDeviceTypeData($from, $to) {
    $roles = $this->getCountsByDeviceType($from, $to);
    $result = "[";
    $i = 0;
    foreach($roles as $role) {
      $i++;
      $result .= '{"name": "'.ucfirst($role["user_device_type"]).'","y": '.$role["count"]."},";
    }
    $result .= "]";
    return $result;
  }

  protected function generateDateData($from, $to) {
    $data = $this->getCountsByDate($from, $to);
    $categories = "[";
    $i = 0;
    foreach($data as $row) {
      $i++;
      $categories .= "'".$row["visited_at"]."'";
      if(count($data) != $i) {
        $categories .= ",";
      }
    }
    $categories .= "]";
    $series = "[{name: 'Посещаемость',data: [";
    $i = 0;
    foreach($data as $row) {
      $i++;
      $series .= $row["count"];
      if(count($data) != $i) {
        $series .= ",";
      }
    }
    $series .= "]}]";
    //$categories = "['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']";
    //$series = "[{name: 'Tokyo111',data: [7.0, 6.9, 9.5, 14.5, 18.4, 21.5, 25.2, 26.5, 23.3, 18.3, 13.9, 9.6]}]";
    return [
      "categories" => $categories,
      "series" => $series,
    ];
  }

  // public function actionHealthCheck() {
  public function actionHc() {
    //$this->checkReportAccess();
    if(!in_array(Yii::$app->user->identity->roleName, ["superadmin"])) {
      return $this->redirect(["site/index"]);
    }
    $downloadFileName = Helpers::downloadFileName("HealthCheck", "1");
    $downloadFileName = rtrim($downloadFileName, ".1");
    return $this->render(
      "health-check",
      array_merge($this->_reportService->healthCheck(), ["downloadFileName" => $downloadFileName])
    );
  }

  public function actionPfepParts() {
    $downloadFileName = Helpers::downloadFileName("pack", "1");
    $downloadFileName = rtrim($downloadFileName, ".1");
    $filters = [
      1 => Yii::t("app", "All"),
      "import_contract_source_ids" => "IMPORT",
      "local_contract_source_ids" => "LOCAL",
    ];
    $model = new DynamicModel(["type"]);
    $model->addRule(["type"], "string");
    $selectedFilter = "1";
    if($model->load(Yii::$app->request->post())) {
      $selectedFilter = $model->type;
    }
    $data = $this->_reportService->pfepParts($selectedFilter);
    return $this->render(
      "pfep-parts",
      array_merge($data, [
        "downloadFileName" => $downloadFileName,
        "filters" => $filters,
        "model" => $model,
      ])
    );
  }

  public function actionCcu() {
    $this->checkReportAccess();
    $data = $this->_reportService->ccu();
    return $this->render("ccu/ccu", [
      "data" => $data,
      "downloadFileName" => rtrim(Helpers::downloadFileName("ccu", "1"), ".1"),
    ]);
  }

  public function actionCcum($month = null) {
    $this->checkReportAccess();
    if(!$month) {
      $month = date("Y-m");
    }
    $data = $this->_reportService->ccum($month);
    return $this->render("ccu/ccum", [
      "month" => $month,
      "data" => $data,
      "downloadFileName" => rtrim(Helpers::downloadFileName("ccum", "1"), ".1"),
    ]);
  }

  public function actionCcuc($detail_id) {
    $this->checkReportAccess("ccum");
    $data = $this->_reportService->ccuc($detail_id);
    return $this->render("ccu/ccuc", [
      "data" => $data,
      "downloadFileName" => rtrim(Helpers::downloadFileName("ccuc", "1"), ".1"),
    ]);
  }

  public function actionDohCalc($type = false) {
    $this->checkReportAccess();
    if($type == "download") {
      $data = $this->_reportService->dohCalc(false);
      $downloadFileName = rtrim(Helpers::downloadFileName("doh-calc", "1"), ".1");
      return $this->downloadDohCalc($data, $downloadFileName);
    }
    $data = $this->_reportService->dohCalc();
    return $this->render("doh-calc", [
      "data" => $data,
    ]);
  }

  private function downloadDohCalc($data, $downloadFileName) {
    ini_set("memory_limit", "-1");
    $titles = [
      Yii::t("app", "Part number"),
      Yii::t("app", "Part name"),
      Yii::t("app", "Supplier"),
      Yii::t("app", "Contract subject"),
      Yii::t("app", "Country"),
      Yii::t("app", "Unit"),
      Yii::t("app", "Price"),
      Yii::t("app", "Currency"),
      Yii::t("app", "Average daily usage"),
      Yii::t("app", "DOH WH, Bank stock"),
      Yii::t("app", "Transit time"),
      Yii::t("app", "MOQ (Pieces)"),
      Yii::t("app", "MOQ (Days)"),
      Yii::t("app", "Total Days on Hand with MOQ (Days)"),
      Yii::t("app", "Total Days on Hand with MOQ (Qty)"),
      Yii::t("app", "Total Days on Hand with MOQ (Amount)"),
      Yii::t("app", "Total Days On Hand without MOQ (Days)"),
      Yii::t("app", "Total Days On Hand without MOQ (Qty)"),
      Yii::t("app", "Total Days On Hand without MOQ (Amount)"),
      Yii::t("app", "DIFF. Amount"),
    ];
    $file = Yii::createObject([
      "class" => 'codemix\excelexport\ExcelFile',
      "sheets" => [
        "coverage" => [
          "data" => $data,
          "titles" => $titles,
        ],
      ],
    ]);
    $file->send($downloadFileName.".xlsx");
  }

  public function actionCashRequirement() {
    $this->checkReportAccess();
    $this->layout = "req";
    $data = $this->_reportService->cashRequirement();
    return $this->render("cash-requirement", [
      "data" => $data,
      "downloadFileName" => rtrim(Helpers::downloadFileName("cash-requirement", "1"), ".1"),
    ]);
  }

  public function actionImos() {
    $this->checkReportAccess();
    $this->layout = "req";
    $data = $this->_reportService->imos();
    return $this->render("imos", [
      "data" => $data,
      "downloadFileName" => rtrim(Helpers::downloadFileName("imos", "1"), ".1"),
    ]);
  }

  public function actionSumImos() {
    $this->checkReportAccess();
    $this->layout = "req";
    $data = $this->_reportService->sumImos();
    return $this->render("sum-imos", [
      "data" => $data,
      "downloadFileName" => rtrim(Helpers::downloadFileName("sum-imos", "1"), ".1"),
    ]);
  }

  public function actionProductionMonitor() {
    $fromDate = (isset($_GET["from_date"]) && !empty($_GET["from_date"])) ? $_GET["from_date"] : date("Y-m-d");
    $toDate = (isset($_GET["to_date"]) && !empty($_GET["to_date"])) ? $_GET["to_date"] : date("Y-m-d");
    $needWarehouseId = (isset($_GET["warehouse_id"]) && $_GET["warehouse_id"] != 0) ? $_GET["warehouse_id"] : null;
    $data = $this->_reportService->productionMonitor($fromDate, $toDate, $needWarehouseId);
    return $this->render("production-monitor", [
      "data" => $data,
      "fromDate" => $fromDate,
      "toDate" => $toDate,
      "needWarehouseId" => $needWarehouseId,
      "downloadFileName" => rtrim(Helpers::downloadFileName("production-monitor", "1"), ".1"),
    ]);
  }

  public function actionSalesSummary(){
    $reportName = 'sales-summary';
    $downloadFileName = rtrim(Helpers::downloadFileName($reportName, "1"), ".1");
    $data = $this->_reportService->salesSummary();
    return $this->render('sales/' . $reportName, [
      'data' => $data,
      'downloadFileName' => $downloadFileName
      ]);
  }

  public function actionSalesSummaryDomestic(){
    $reportName = 'sales-summary-domestic';
    $downloadFileName = rtrim(Helpers::downloadFileName($reportName, "1"), ".1");
    $data = $this->_reportService->salesSummaryDomestic();
    return $this->render('sales/' . $reportName, [
      'data' => $data,
      'downloadFileName' => $downloadFileName
      ]);
  }

  public function actionSalesSummaryCustomer($customer_id){
    $reportName = 'sales-summary-customer';
    $downloadFileName = rtrim(Helpers::downloadFileName($reportName, "1"), ".1");
    $data = $this->_reportService->salesSummaryByPartType($customer_id);
    return $this->render('sales/' . $reportName, [
      'data' => $data,
      'downloadFileName' => $downloadFileName
      ]);
  }

  public function actionSalesSummaryExport(){
    $reportName = 'sales-summary-export';
    $downloadFileName = rtrim(Helpers::downloadFileName($reportName, "1"), ".1");
    $data = $this->_reportService->salesSummaryExport();
    return $this->render('sales/' . $reportName, [
      'data' => $data,
      'downloadFileName' => $downloadFileName
      ]);
  }

  public function actionImplementationPlan(){
    $reportName = 'implementation-plan';
    $downloadFileName = rtrim(Helpers::downloadFileName($reportName, "1"), ".1");
    $data = $this->_reportService->salesImplementationPlan();
    return $this->render('sales/' . $reportName, [
      'data' => $data,
      'downloadFileName' => $downloadFileName
      ]);
  }


  
  public function actionImplementationPlanFact($q = null){
    $reportName = 'implementation-plan-fact';
    $downloadFileName = rtrim(Helpers::downloadFileName($reportName, "1"), ".1");
    $data = $this->_reportService->salesImplementationPlanFact();
    $q = ($q) ? $q : $data['q'];
    return $this->render('sales/' . $reportName, [
      'data' => $data,
      'q' => $q,
      'downloadFileName' => $downloadFileName
      ]);
  }

  public function actionSalesPaymentStatus(){
    $reportName = 'sales-payment-status';
    $downloadFileName = rtrim(Helpers::downloadFileName($reportName, "1"), ".1");
    $data = $this->_reportService->salesPaymentStatus();
    return $this->render('sales/' . $reportName, [
      'data' => $data,
      'downloadFileName' => $downloadFileName
      ]);
  }

  public function actionSalesPaymentStatusCustomer($customer_id){
    if(!$customer_id) return 'Error :(';
    $reportName = 'sales-payment-status-customer';
    $downloadFileName = rtrim(Helpers::downloadFileName($reportName, "1"), ".1");
    $data = $this->_reportService->SalesPaymentStatusCustomer($customer_id);
    return $this->render('sales/' . $reportName, [
      'data' => $data,
      'downloadFileName' => $downloadFileName
      ]);
  }
  // qarzlari va qilingan oplatalari
  public function actionSalesPaymentInfo($year = null)
  {
    if(empty($year)){
      $year = date('Y');
    }
    $years = [
      2020 => 2020,
      2021 => 2021,
      2022 => 2022,
      2023 => 2023,
      2024 => 2024,
      2025 => 2025,
      2026 => 2026,
      2027 => 2027,
      2028 => 2028,
      2029 => 2029,
      2030 => 2030,
    ];
    $reportName = 'sales-payment-info';
    $downloadFileName = rtrim(Helpers::downloadFileName($reportName, "1"), ".1");
    $data = Customer::find()->where(['status' => Customer::STATUS_ACTIVE])->all();
    return $this->render('sales/' . $reportName, [
      'data' => $data,
      'downloadFileName' => $downloadFileName,
      'year' => $year,
      'years' => $years
      ]);
  }
  public function actionMaterialStock($state = Part::STATE_RAW){
    $reportName = 'material-stock';
    $downloadFileName = rtrim(Helpers::downloadFileName($reportName, "1"), ".1");
    $data = $this->_reportService->materialStock($state);
    return $this->render('stock/' . $reportName, [
      'data' => $data,
      'downloadFileName' => $downloadFileName
      ]);
  }

  public function actionMaterialStockByType($state = Part::STATE_RAW, $type){
    $reportName = 'material-stock-by-type';
    $downloadFileName = rtrim(Helpers::downloadFileName($reportName, "1"), ".1");
    $data = $this->_reportService->materialStockByType($state, $type);
    return $this->render('stock/' . $reportName, [
      'data' => $data,
      'downloadFileName' => $downloadFileName
      ]);
  }


  // requirement-short
  public function actionRequirementShort($filter=null)
  {
    // $this->checkReportAccess();
    $this->layout = "req";
    $query = "
        select p.id as part_id, p.part_no,p.part_name,p.part_color, p.remark, cs.name csourse, a.*  from
        (
                select r.id rid,r.part_id,r.calc_at, w.* from req_detail_plan w left join req r on w.req_id = r.id
                where w.type in(:type_d, :type_l, :type_c, :type_s)
        ) a
        left join part p on a.part_id = p.id
        left join contract_source cs on p.contract_source_id = cs.id
        

    ";
    // $query = "
    //     select p.id as part_id, p.part_no,p.part_name,p.part_color, p.remark, cs.name csourse from part p
      
    //     left join contract_source cs on p.contract_source_id = cs.id
    //     order by p.part_no
    // ";
    $data_daily = Yii::$app->db
      ->createCommand($query, [
          ":type_d" => CoverageController::TYPE_DAILY,
          ":type_l" => CoverageController::TYPE_LOCAL_DAILY,
          ":type_c" => CoverageController::TYPE_LOCAL_CONS,
          ":type_s" => CoverageController::TYPE_LOCAL_SEMI,
      ])
    ->queryAll();
    return $this->render("requirement-short", [
      "data_daily" => $data_daily,
      'filter'   => $filter
    ]);      

  }
  // excel import
  public function actionDownloadRequirementShort()
  {
    // $this->checkReportAccess("requirement-short");
    ini_set("memory_limit", "-1");
    $query = "
        select p.part_no,p.part_name,p.part_color, p.remark, cs.name csourse, a.*  from
        (
                select r.id rid,r.part_id,r.calc_at, w.* from req_detail_plan w left join req r on w.req_id = r.id
                where w.type = :type_d or w.type = :type_l or w.type = :type_c or w.type = :type_s
        ) a
        left join part p on a.part_id = p.id
        left join contract_source cs on p.contract_source_id = cs.id
        order by p.part_no

      ";
      $data_daily = Yii::$app->db
        ->createCommand($query, [
          ":type_d" => CoverageController::TYPE_DAILY,
          ":type_l" => CoverageController::TYPE_LOCAL_DAILY,
          ":type_c" => CoverageController::TYPE_LOCAL_CONS,
          ":type_s" => CoverageController::TYPE_LOCAL_SEMI,
        ])
        ->queryAll();
    $period_daily = [];
    foreach (\app\components\Helpers::getPeriodFull() as $pdate) {
      if($pdate > date('Y-m-t', strtotime('+6 month'))) break;
      $period_daily[] = $pdate;
    }
    $arrFile = [];
    // vd($data_daily[0]);
    $arr = [[], []];
    $month = [];

     foreach($period_daily as $col => $pdate){ 
			 
				if (count($arr[1]) == 0 and !$nextWeek)  {
					if (date('w', strtotime($pdate)) == 0) {
						$nextWeek = true;
					}
					array_push($arr[0], $col);
				} else if ($nextWeek and count($arr[1]) <= 6) {
					array_push($arr[1], $col);
				}
				if (!$indexMonth) {
					$indexMonth = date("m", strtotime($pdate));
					array_push($month, $col);
				} else if ($indexMonth == date("m", strtotime($pdate))) {
					array_push($month, $col);
				}
      }
    foreach($data_daily as $key => $detailWide) {
      unset($tmpArray);
      $tmpArray['id'] = $key+1;
      $tmpArray["part_no"] = $detailWide['part_no'];
      $tmpArray["part_color"] = $detailWide['part_color'];
      $tmpArray["part_name"] = $detailWide['part_name'];
      $tmpArray["csourse"] = $detailWide['csourse'];
      $avg = round(Part::findOne($detailWide['part_id'])->averageUsage);
      $c_week = 0;
      foreach($arr[0] as $col){
          $c_week = $c_week + Helpers::formatRemoveDecimal($detailWide['col'.($col + 1)]);
      }
      $next_week = 0;
      foreach($arr[1] as $col){
          $next_week = $next_week + Helpers::formatRemoveDecimal($detailWide['col'.($col + 1)]);
      }
      $month_total = 0;
      foreach($month as $col){
          $month_total = $month_total + Helpers::formatRemoveDecimal($detailWide['col'.($col + 1)]);
      }
      $tmpArray["avg_usage"] = $avg;
      $tmpArray['qty'] = round($detailWide['qty']);
      $tmpArray["week"] = $c_week;
      $tmpArray['balance1'] = $tmpArray['qty'] - $tmpArray['week'];
      $tmpArray["next_week"] = $next_week;
      $tmpArray['balance2'] = $tmpArray['qty'] - $tmpArray['next_week'];
      $tmpArray["month"] = $month_total;
      $tmpArray['balance3'] = $tmpArray['qty'] - $tmpArray['month'];
      $arrFile[] = $tmpArray;
      
    }
    $header_titles = [
      0   => 'ID',
      1   => 'Материал',
      2   => 'Цвет',
      3   => 'МАРКА',
      4   => 'Тип',
      5   => 'Среднее исполь.',
      6   => 'Количество остатка',
      7   => '1 нед',
      8   => 'Баланс',
      9   => 'след нед',
      10  => 'Баланс',
      11  => '1месяц',
      12  => 'Баланс',

    ];
    $detail_titles = [];
    $titles = array_merge($header_titles, $detail_titles);
    $file = Yii::createObject([
      "class" => 'codemix\excelexport\ExcelFile',
      "sheets" => [
        "coverage" => [
          "data" => $arrFile,
          "titles" => $titles,
        ],
      ],
    ]);
    $file->send(Helpers::downloadFileName("daily-requirement"));
  }

  // calculator
  public function actionCalculate()
  {
    return $this->redirect(['calculate-product/index']);
  }
  //material report
  public function actionMaterialReport()
  {
    return $this->redirect(['dashboard/index']);
  }
  // stock-dahboard action
  public function actionStockDashboard()
  {
    return $this->redirect(['stock/dashboard']);
  }

  //  plan dashboard-analiz action
  public function actionDashboardAnaliz()
  {
    return $this->redirect(['dashboard/analiz']);
  }

  // plan-prodaj
  public function actionPlanProdaj()
  {
    return $this->redirect(['dashboard/plan-prodaj']);
  }

  // plan-prodaj-month
  public function actionPlanProdajMonth()
  {
    return $this->redirect(['dashboard/plan-prodaj-new']);
  }
 // rezultat prodaj oylik 2023
    // ============================================
    // =============Sanakulov Anvar================
    // ============================================
    // 2023-06-25  Sanakulov Anvar
    public function actionReportPlanMonth($month = null, $year = null)
    {
        if($month == null){
            $month = date('m');
        }
        if($year == null){
            $year = date('Y');
        }
        
        $monthList = [
            '01' => 'Январь',
            '02' => 'Февраль',
            '03' => 'Март',
            '04' => 'Апрель',
            '05' => 'Май',
            '06' => 'Июнь',
            '07' => 'Июль',
            '08' => 'Август',
            '09' => 'Сентябрь',
            '10' => 'Октябрь',
            '11' => 'Ноябрь',
            '12' => 'Декабрь',
        ];
        // $year = '2021';
        // $month = '07';
        $monthName = $monthList[$month];
        $models = ReportFaktProdajMonth::resultReport($month, $year);
        // vd($models);
        $headers = '';
        // $models = Dashboard::getMonthResult($month, $year);
        // $headers = Dashboard::getMonthResultTableHeaders();
        return $this->render('../dashboard/report-plan-month', [
            'models'        => $models,
            'headers'       => $headers,
            'month'         => $month,
            'year'          => $year,
            'monthName'     => $monthName,
            'monthList'     => $monthList,
        ]);
    }

    // customer types DOM or EXP Report
    // ============================================
    // =============Sanakulov Anvar================
    // ============================================
    // 2023-07-04  Sanakulov Anvar

    public function actionCustomerTypesPlan($customer_type_id=1, $month=null, $year=null)
    {
      if(empty($month)){
        $month = date('m');
      }

      if(empty($year)){
        $year = date('Y');
      }

      $models = ReportFaktProdajMonth::customerTypesPlan($customer_type_id, $month, $year);
      // vd($models);
      $monthList = [
          '01' => 'Январь',                                                                                                                                                        
          '02' => 'Февраль',
          '03' => 'Март',
          '04' => 'Апрель',
          '05' => 'Май',
          '06' => 'Июнь',
          '07' => 'Июль',
          '08' => 'Август',
          '09' => 'Сентябрь',
          '10' => 'Октябрь',
          '11' => 'Ноябрь',
          '12' => 'Декабрь',
      ];
      $monthName = $monthList[$month];
      // vd($month);
      return $this->render('dashboard/customer-types-plan', [
        'models'              => $models,
        'monthName'           => $monthName,
        'customer_type_id'    => $customer_type_id,
        'month'               => $month,
        'year'                => $year,
      ]);
    }



    // report production plan new
    public function actionProductionFact() {
      // $this->checkReportAccess();
      $this->layout = "req";
      $model = new ProductionOrder();

      $need_month = isset($_POST["need_month"]) ? ($need_month = $_POST["need_month"]) : date("Y-m");
      $month_end = date("Y-m-t", strtotime($need_month));
      $date = date("Y-m");
      $warehouse_id = null;
      $part_id = null;

      $lines = ProductionOrder::getLines();
      $lines[0] = 'Все линии';
      $type = 1;
      ksort($lines);
      if($post = Yii::$app->request->post()) {
        $date         = isset($post['need_month'])? $post['need_month'] : date('Y-m');
        $warehouse_id = isset($post['ProductionOrder']['line'])? $post['ProductionOrder']['line'] : null;
        $part_id      = isset($post['ProductionOrder']['part_id'])? $post['ProductionOrder']['part_id'] : null;
        $type         = isset($post['report-type'])? $post['report-type'] : 1;
        // vd($post);
      }
      $countMontDays = date('t', strtotime($date));
      $todayDay = date('d');
      
      $data = $this->_reportService->getProductionFakt($date, $warehouse_id, $part_id, $todayDay, $type);


      // vd($data);
      return $this->render('production-fact', [
        'model' => $model,
        'need_month' => $need_month,
        'month_end' => $countMontDays,
        'type' => $type,
        'data' => $data,
        'todayDay' => $todayDay,  
        'lines' => $lines,
      ]);
    } 


    // 2023-07-14 Sanakulov Anvar


    public function actionProductionPlanFactDaily()
    {
      $date = date('d.m.Y', strtotime('-1 day'));
      $date2 = date('Y-m-d', strtotime('-1 day'));
      $beginDate = date("Y-m-d");
      $endDate  = date("Y-m-d", strtotime($date.'+3 day'));
      $dateList = [];

      while (strtotime($beginDate) <= strtotime($endDate)) {
        $dateList[] = date('d-M', strtotime($beginDate));
        $beginDate = date ("Y-m-d", strtotime("+1 day", strtotime($beginDate)));
      }
      $data = $this->_reportService->productionPlanFactDaily($date2);
      // vd($data);  
      return $this->render('production-plan-fact-daily', [
        'date'    => $date,
        'data'    => $data,
        'endDate' => $endDate,  

        'dateList' => $dateList,
      ]);
    }

}
