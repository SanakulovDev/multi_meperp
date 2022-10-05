<?php
namespace app\console\controllers;

use app\models\Contract;
use app\models\HealthCheck;
use app\models\HealthCheckDetail;
use app\models\InvoicePartProblem;
use app\models\Part;
use app\models\SalesContract;
use app\models\VehicleCoverageInput;
use app\models\Warehouse;
use Exception;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

class HcController extends Controller {

  /** Хамма деталлар шартномалари борми? Шартномаси йук деталлар нечта? */
  public const SAVOL_DIFFERENCE_PART_CONTRACT = 'difference_part_contract';
  /** Хамма деталлар сотув шартномалари борми? Шартномаси йук деталлар нечта? */
  public const SAVOL_DIFFERENCE_PART_SALES_CONTRACT = 'difference_part_sales_contract';
  /** Заказларнинг хаммаси тизимга киритилганми? Order Status хисоботи тугри курсатяптими? */
  public const SAVOL_ORDER_STATUS = 'order_status';
  /** Олинган Инвойслар тизимга киритилганми? Intransit хисоботи тугри курсатяптими? */
  public const SAVOL_INTRANSIT_ETA = 'intransit_eta';
  /** Тизимга киритилган заказларнинг Местонахождение груза каби маълумотлари тулдирилганми? */
  public const SAVOL_ORDER_LOC_NOT_INPUT = 'order_loc_not_input';
  /** Деталнинг ТН-ВЭД код лари тизимга киритилганми? */
  public const SAVOL_DIFFERENCE_PART_TNVED = 'difference_part_tnved';
  /** Тизимга киритилган инвойс контейнерларнинг MRD санаси маълумотлари тулдирилганми? */
  public const SAVOL_MRD_STATUS = 'mrd_status';
  /** Импорт шартномадаги деталларнинг асосий нархи танланганми? */
  public const SAVOL_PRIMARY_PRICE_STATUS = 'primary_price_status';
  /** Тизимда ишлаб чикариш фактлари  режалаштиришга нисбатан тўғрими? */
  public const SAVOL_PLAN_FACT_STATUS = 'plan_fact_status';
  /** Контракты компонентов ойнасида "Условия поставки" тулдирилганми (Импорт деталлар учун)? */
  public const SAVOL_DELIVERY_TERM_FILLED = 'delivery_term_filled';
  /** Махаллий ва Давал асосидаги (агар булса) деталлар кирим-чикимлари тизим оркали тулик юритиляптими? Манфий детал колдиклари борми? */
  public const SAVOL_OUTSOURCE_STOCK = 'outsource_stock';
  /** Асосий омбордан И/ч ва колган омборларга деталлар чикими тизим оркали тулик юритиляптими? Манфий детал колдиклари борми? */
  public const SAVOL_WH_LINE_STOCK = 'wh_line_stock';
  /** Тайёр махсулот омбори кирим-чикимлари тулик юритиляптими?  */
  public const SAVOL_FG_DOC_STATUS = 'fg_doc_status';
  /** Счёт фактура уз вактида киритиябдими? */
  public const SAVOL_FG_INVOICE_STATUS = 'fg_invoice_status';
  /** Йўлдаги машинакомплектларнинг ETA муддати ўтиб кетганлар муддати янгиланябдими?  */
  public const SAVOL_VEHICLE_ETA_STATUS = 'vehicle_eta_status';
  /**   */
  public const SAVOL_X = 'xx';

  public static function getToday() {
    return date('Y-m-d', time());
  }

  public static function getQuessionStatus($minusCount, $redBoundry, $greenBoundry) {
    if ($minusCount >= $redBoundry) {
      $sts = HealthCheckDetail::STATUS_RED;
    } elseif ($greenBoundry < $minusCount && $minusCount < $redBoundry) {
      $sts = HealthCheckDetail::STATUS_YELLOW;
    } else {
      $sts = HealthCheckDetail::STATUS_GREEN;
    }

    return $sts;
  }

  public function createHealthCek($quesionTitle, $sts, $minusCount) {
    $crtError = false;
    $healthCheck = HealthCheck::find()->where(['title' => $quesionTitle])->one();
    $transaction = Yii::$app->db->beginTransaction();
    try {
      Yii::$app->db->createCommand(
        "REPLACE INTO health_check_detail (health_check_id, check_date, status, description) 
             VALUES (".$healthCheck->id.", '".self::getToday()."', '$sts', '$minusCount')
            "
      )->execute();
      $transaction->commit();
      //      $vaqt = Console::ansiFormat(date("Y-m-d H:i:s"), [Console::FG_YELLOW]);
      //      $status = Console::ansiFormat("Status=".$sts."(".$minusCount.")", [Console::FG_GREEN]);
      //      $title = Console::ansiFormat($quesionTitle, [Console::FG_BLUE]);
      //      return $vaqt." ".$status." ".$title.PHP_EOL;
      return date("Y-m-d H:i:s")." Status=".$sts."(".$minusCount.")".$quesionTitle.PHP_EOL;
    }
    catch (Exception $e) {
      $crtError = true;
      $transaction->rollBack();
      throw $e;
    }

    return $crtError;
  }

  public function actionIndex() {
    $res = '';
    $checkStep = 0;
    //    $allStep = 12;
    //    Console::startProgress(0, $allStep);
    //    Console::updateProgress(++$checkStep, $allStep);
    //    Console::endProgress();
    Console::output("------------ BEGIN ------------");
    $res .= str_pad(++$checkStep, 2, 0, STR_PAD_LEFT).". ".self::differencePartContract();
    $res .= str_pad(++$checkStep, 2, 0, STR_PAD_LEFT).". ".self::differencePartSalesContract();
    $res .= str_pad(++$checkStep, 2, 0, STR_PAD_LEFT).". ".self::orderStatus();
    $res .= str_pad(++$checkStep, 2, 0, STR_PAD_LEFT).". ".self::intransitEta();
    $res .= str_pad(++$checkStep, 2, 0, STR_PAD_LEFT).". ".self::orderLocNotInput();
    $res .= str_pad(++$checkStep, 2, 0, STR_PAD_LEFT).". ".self::differencePartTnved();
    $res .= str_pad(++$checkStep, 2, 0, STR_PAD_LEFT).". ".self::mrdStatus();
    $res .= str_pad(++$checkStep, 2, 0, STR_PAD_LEFT).". ".self::primaryPriceStatus();
    $res .= str_pad(++$checkStep, 2, 0, STR_PAD_LEFT).". ".self::planFactStatus();
    $res .= str_pad(++$checkStep, 2, 0, STR_PAD_LEFT).". ".self::deliveryTermFilled();
    $res .= str_pad(++$checkStep, 2, 0, STR_PAD_LEFT).". ".self::outsourceStock();
    $res .= str_pad(++$checkStep, 2, 0, STR_PAD_LEFT).". ".self::whLineStock();
    $res .= str_pad(++$checkStep, 2, 0, STR_PAD_LEFT).". ".self::fgDocStatus();
    $res .= str_pad(++$checkStep, 2, 0, STR_PAD_LEFT).". ".self::fgInvoiceStatus();
    $res .= str_pad(++$checkStep, 2, 0, STR_PAD_LEFT).". ".self::vehicleEtaStatus();
    Console::stdout("{$res}"."------------ DONE ------------");
  }

  // SAVOL_DIFFERENCE_PART_CONTRACT
  public function actionDifferencePartContract() {
    self::differencePartContract();
  }

  // SAVOL_DIFFERENCE_PART_SALES_CONTRACT
  public function actionDifferencePartSalesContract() {
    self::differencePartSalesContract();
  }

  // SAVOL_ORDER_STATUS
  public function actionOrderStatus() {
    self::orderStatus();
  }

  // SAVOL_INTRANSIT_ETA
  public function actionIntransitEta() {
    self::intransitEta();
  }

  // SAVOL_ORDER_LOC_NOT_INPUT
  public function actionOrderLocNotInput() {
    self::orderLocNotInput();
  }

  // SAVOL_DIFFERENCE_PART_TNVED
  public function actionDifferencePartTnved() {
    self::differencePartTnved();
  }

  // SAVOL_MRD_STATUS
  public function actionMrdStatus() {
    self::mrdStatus();
  }

  // SAVOL_PRIMARY_PRICE_STATUS
  public function actionPrimaryPriceStatus() {
    self::primaryPriceStatus();
  }

  // SAVOL_PLAN_FACT_STATUS
  public function actionPlanFactStatus() {
    self::planFactStatus();
  }

  // SAVOL_DELIVERY_TERM_FILLED
  public function actionDeliveryTermFilled() {
    self::deliveryTermFilled();
  }

  // SAVOL_OUTSOURCE_STOCK
  public function actionOutsourceStock() {
    self::outsourceStock();
  }

  // SAVOL_WH_LINE_STOCK
  public function actionWhLineStock() {
    self::whLineStock();
  }

  // SAVOL_FG_DOC_STATUS
  public function actionFgDocStatus() {
    self::fgDocStatus();
  }

  // SAVOL_FG_INVOICE_STATUS
  public function actionFgInvoiceStatus() {
    self::fgInvoiceStatus();
  }

  // SAVOL_VEHICLE_ETA_STATUS
  public function actionVehicleEtaStatus() {
    self::vehicleEtaStatus();
  }

  private function differencePartContract() {
    $query = "SELECT COUNT(*) cnt FROM 
              (
                SELECT id FROM part WHERE state=".Part::STATE_RAW." UNION
                SELECT id FROM part WHERE state=".Part::STATE_SEMI." AND warehouse_id IN (SELECT id FROM warehouse WHERE warehouse_type=".Warehouse::TYPE_OUTSOURCING.") 
              ) all_parts
              LEFT JOIN
              ( SELECT part_id c_part_id FROM contract c LEFT JOIN contract_detail cd ON c.id=cd.contract_id WHERE c.status=".Contract::STATUS_ACTIVE." AND c.expiry_date>='".self::getToday()."') contr_parts
              ON all_parts.id= contr_parts.c_part_id
              WHERE contr_parts.c_part_id IS NULL";
    $minusCount = Yii::$app->db->createCommand($query)->queryScalar();
    $greenBoundry = 0;
    $redBoundry = 4;
    $quesionTitle = self::SAVOL_DIFFERENCE_PART_CONTRACT;
    $sts = self::getQuessionStatus($minusCount, $redBoundry, $greenBoundry);

    return self::createHealthCek($quesionTitle, $sts, $minusCount);
  }

  private function differencePartSalesContract() {
    $query = "SELECT COUNT(*) cnt FROM 
              (
                SELECT id FROM part WHERE state=".Part::STATE_RAW." UNION
                SELECT id FROM part WHERE state=".Part::STATE_SEMI." AND warehouse_id IN (SELECT id FROM warehouse WHERE warehouse_type=".Warehouse::TYPE_OUTSOURCING.") 
              ) all_parts
              LEFT JOIN
              ( SELECT part_id c_part_id FROM sales_contract c LEFT JOIN sales_contract_detail cd ON c.id=cd.sales_contract_id WHERE c.status=".SalesContract::STATUS_ACTIVE." AND c.expiry_date>='".self::getToday()."') contr_parts
              ON all_parts.id= contr_parts.c_part_id
              WHERE contr_parts.c_part_id IS NULL";
    $minusCount = Yii::$app->db->createCommand($query)->queryScalar();
    $greenBoundry = 0;
    $redBoundry = 4;
    $quesionTitle = self::SAVOL_DIFFERENCE_PART_SALES_CONTRACT;
    $sts = self::getQuessionStatus($minusCount, $redBoundry, $greenBoundry);

    return self::createHealthCek($quesionTitle, $sts, $minusCount);
  }

  private function orderStatus() {
    $greenBoundry = 0;
    $redBoundry = 4;
    $minusCount = InvoicePartProblem::find()->count();
    $quesionTitle = self::SAVOL_ORDER_STATUS;
    $sts = self::getQuessionStatus($minusCount, $redBoundry, $greenBoundry);

    return self::createHealthCek($quesionTitle, $sts, $minusCount);
  }

  private function intransitEta() {
    $query = "SELECT DATEDIFF('".self::getToday()."', app_arr_at) farq  FROM container_invoice WHERE 
              ( arrived_at IS NULL OR LENGTH(TRIM(arrived_at))=0 )
              AND app_arr_at < '".self::getToday()."'
              ORDER BY farq DESC
              LIMIT 1";
    $greenBoundry = 0;
    $redBoundry = 1;
    $minusCount = Yii::$app->db->createCommand($query)->queryScalar();
    $quesionTitle = self::SAVOL_INTRANSIT_ETA;
    $sts = self::getQuessionStatus($minusCount, $redBoundry, $greenBoundry);

    return self::createHealthCek($quesionTitle, $sts, $minusCount);
  }

  private function orderLocNotInput() {
    $query = "SELECT COUNT(*) loc_not_inpt_cnt FROM container_invoice WHERE 
               ( received_at IS NULL OR LENGTH(TRIM(received_at))=0 ) AND 
               ( current_locate IS NULL OR LENGTH(TRIM(current_locate)) =0)";
    $greenBoundry = 0;
    $redBoundry = 4;
    $minusCount = Yii::$app->db->createCommand($query)->queryScalar();
    $quesionTitle = self::SAVOL_ORDER_LOC_NOT_INPUT;
    $sts = self::getQuessionStatus($minusCount, $redBoundry, $greenBoundry);

    return self::createHealthCek($quesionTitle, $sts, $minusCount);
  }

  private function differencePartTnved() {
    $query = "SELECT COUNT(*) cnt  FROM contract_detail 
              WHERE ( cnfea IS NULL OR LENGTH(TRIM(cnfea))=0 ) AND 
              contract_id IN 
              (
                select id from contract 
                WHERE status = ".Contract::STATUS_ACTIVE." AND 
                expiry_date>='".self::getToday()."' AND 
                contract_source_id IN (select id cs_id from contract_source WHERE name IN ('IKD','IDB'))
              )";
    $greenBoundry = 0;
    $redBoundry = 4;
    $minusCount = Yii::$app->db->createCommand($query)->queryScalar();
    $quesionTitle = self::SAVOL_DIFFERENCE_PART_TNVED;
    $sts = self::getQuessionStatus($minusCount, $redBoundry, $greenBoundry);

    return self::createHealthCek($quesionTitle, $sts, $minusCount);
  }

  private function mrdStatus() {
    $query = "SELECT count(*) cnt FROM container_invoice WHERE 
               ( received_at IS NULL OR LENGTH(TRIM(received_at))=0 ) AND 
               ( need_at IS NULL OR LENGTH(TRIM(need_at)) =0)";
    $minusCount = Yii::$app->db->createCommand($query)->queryScalar();
    $greenBoundry = 0;
    $redBoundry = 4;
    $quesionTitle = self::SAVOL_MRD_STATUS;
    $sts = self::getQuessionStatus($minusCount, $redBoundry, $greenBoundry);

    return self::createHealthCek($quesionTitle, $sts, $minusCount);
  }

  private function primaryPriceStatus() {
    $query = "SELECT COUNT(*) cnt FROM (
                SELECT part_id, is_primary_price FROM contract_detail WHERE contract_id IN (
                   select id from contract  WHERE 
                   status = ".Contract::STATUS_ACTIVE." AND 
                   expiry_date > '".self::getToday()."' AND 
                   contract_source_id IN (select id cs_id from contract_source WHERE name IN ('IKD','IDB'))
                  )
                GROUP BY part_id, is_primary_price
              )jami 
              WHERE jami.part_id NOT IN (
                SELECT part_id FROM contract_detail WHERE contract_id IN (
                   select id from contract  
                   WHERE status = ".Contract::STATUS_ACTIVE." AND 
                   expiry_date > '".self::getToday()."' AND 
                   contract_source_id IN (select id cs_id from contract_source WHERE name IN ('IKD','IDB'))
                ) AND is_primary_price=1
                GROUP BY part_id
              )";
    $minusCount = Yii::$app->db->createCommand($query)->queryScalar();
    $greenBoundry = 0;
    $redBoundry = 4;
    $quesionTitle = self::SAVOL_PRIMARY_PRICE_STATUS;
    $sts = self::getQuessionStatus($minusCount, $redBoundry, $greenBoundry);

    return self::createHealthCek($quesionTitle, $sts, $minusCount);
  }

  private function planFactStatus() {
    $kecha = date('Y-m-d', strtotime('-1 day', time()));
    $partIdQuery = "SELECT id pt_id FROM part WHERE is_plan = 1 AND status=".Part::STATUS_ACTIVE;
    $planIdQuery = "SELECT id FROM production_plan WHERE production_date='".$kecha."'";
    $query = "SELECT COUNT(*) cnt FROM 
               (
                 SELECT pt_id, IFNULL(pl_qty,0) plan, IFNULL(po_qty,0) fact, (IFNULL(pl_qty,0)-IFNULL(po_qty,0)) farq FROM 
                  ($partIdQuery) pt
                  LEFT JOIN 
                  (
                    SELECT part_id pl_part_id, IFNULL(SUM(target_qty), 0) pl_qty  FROM production_plan  WHERE production_date='".$kecha."'
                    AND id NOT IN ( SELECT production_plan_id FROM  production_plan_comment WHERE production_plan_id IN ($planIdQuery) )
                    GROUP BY part_id
                  ) plan
                  ON pt.pt_id = plan.pl_part_id
                  LEFT JOIN
                  (
                    SELECT part_id po_part_id, IFNULL(SUM(quantity), 0) po_qty FROM production_order
                    WHERE part_id IN ($partIdQuery) 
                    AND from_unixtime(created_at, '%Y-%m-%d %H%i') BETWEEN '".$kecha." 0800' AND '".self::getToday()." 0759'
                    GROUP BY part_id
                  ) fact
                  ON pt.pt_id = fact.po_part_id
                ) pl_fact
                WHERE pl_fact.farq <> 0";
    $minusCount = Yii::$app->db->createCommand($query)->queryScalar();
    $greenBoundry = 0;
    $redBoundry = 4;
    $quesionTitle = self::SAVOL_PLAN_FACT_STATUS;
    $sts = self::getQuessionStatus($minusCount, $redBoundry, $greenBoundry);

    return self::createHealthCek($quesionTitle, $sts, $minusCount);
  }

  private function deliveryTermFilled() {
    $query = "SELECT COUNT(*) cnt FROM contract_detail WHERE contract_id IN 
              (
               SELECT id FROM contract WHERE 
               status = ".Contract::STATUS_ACTIVE." AND 
               expiry_date > '".self::getToday()."'  AND 
               contract_source_id IN (select id cs_id from contract_source WHERE name IN ('IKD','IDB'))
              ) and ( delivery_term_id is null OR LENGTH(TRIM(delivery_term_id))=0 )";
    $minusCount = Yii::$app->db->createCommand($query)->queryScalar();
    $greenBoundry = 0;
    $redBoundry = 5;
    $quesionTitle = self::SAVOL_DELIVERY_TERM_FILLED;
    $sts = self::getQuessionStatus($minusCount, $redBoundry, $greenBoundry);

    return self::createHealthCek($quesionTitle, $sts, $minusCount);
  }

  private function outsourceStock() {
    $query = "SELECT COUNT(*) minus_count FROM stock 
              WHERE  warehouse_id IN (
               SELECT id FROM warehouse WHERE warehouse_type IN (".Warehouse::TYPE_OUTSOURCING.")
              ) AND qty<0";
    $minusCount = Yii::$app->db->createCommand($query)->queryScalar();
    $greenBoundry = 0;
    $redBoundry = 5;
    $quesionTitle = self::SAVOL_OUTSOURCE_STOCK;
    $sts = self::getQuessionStatus($minusCount, $redBoundry, $greenBoundry);

    return self::createHealthCek($quesionTitle, $sts, $minusCount);
  }

  private function whLineStock() {
    $query = "SELECT COUNT(*) minus_count FROM stock 
              WHERE  warehouse_id IN (
               SELECT id FROM warehouse WHERE warehouse_type IN (".Warehouse::TYPE_PHYSICAL.",".Warehouse::TYPE_SHOP.")
              ) AND qty<0";
    $minusCount = Yii::$app->db->createCommand($query)->queryScalar();
    $greenBoundry = 10;
    $redBoundry = 20;
    $quesionTitle = self::SAVOL_WH_LINE_STOCK;
    $sts = self::getQuessionStatus($minusCount, $redBoundry, $greenBoundry);

    return self::createHealthCek($quesionTitle, $sts, $minusCount);
  }

  private function fgDocStatus() {
    $query = "SELECT  DATEDIFF(CURRENT_DATE, MAX(docdate)) farq FROM document WHERE 
               to_warehouse_id IN (
                     SELECT  fg_warehouse_id FROM part WHERE fg_warehouse_id IS NOT NULL GROUP BY fg_warehouse_id
                  )";
    $minusCount = Yii::$app->db->createCommand($query)->queryScalar();
    $greenBoundry = 0;
    $redBoundry = 2;
    $quesionTitle = self::SAVOL_FG_DOC_STATUS;
    $sts = self::getQuessionStatus($minusCount, $redBoundry, $greenBoundry);

    return self::createHealthCek($quesionTitle, $sts, $minusCount);
  }

  private function fgInvoiceStatus() {
    $query = "SELECT DATEDIFF(CURRENT_DATE, MAX(invoice_date)) farq  FROM fg_invoice";
    $minusCount = Yii::$app->db->createCommand($query)->queryScalar();
    $greenBoundry = 0;
    $redBoundry = 3;
    $quesionTitle = self::SAVOL_FG_INVOICE_STATUS;
    $sts = self::getQuessionStatus($minusCount, $redBoundry, $greenBoundry);

    return self::createHealthCek($quesionTitle, $sts, $minusCount);
  }

  private function vehicleEtaStatus() {
    $pastEtaDatesV = VehicleCoverageInput::getExpiredData();
    $minusCount = count($pastEtaDatesV);
    $greenBoundry = 0;
    $redBoundry = 2;
    $quesionTitle = self::SAVOL_VEHICLE_ETA_STATUS;
    $sts = self::getQuessionStatus($minusCount, $redBoundry, $greenBoundry);

    return self::createHealthCek($quesionTitle, $sts, $minusCount);
  }

}