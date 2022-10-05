<?php
namespace app\controllers;

use app\components\Helpers;
use app\models\LineStop;
use app\models\LineStopReason;
use app\models\PartProductionMonitor;
use app\models\ProductionMonitor;
use app\models\Warehouse;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * PartProductionMonitorController implements the CRUD actions for PartProductionMonitor model.
 */
class PartProductionMonitorController extends AppController
{
  /**
   * Lists all PartProductionMonitor models.
   *
   * @return mixed
   */
  public function actionIndex()
  {
    if(isset($_REQUEST["needDate"])){
      $shifts = Yii::$app->params['shifts'];
      if($_REQUEST["needShift"] ==1){
        $shiftTime = $shifts[1][0];
      }else{
        $shiftTime = $shifts[2][0][0];
      }
      $currShift = Helpers::getShift($_REQUEST["needDate"]." ".$shiftTime);
    }else{
      $currShift = Helpers::getShift();
    }

    $selWhId = isset($_REQUEST["whId"]) ? $_REQUEST["whId"] : null;
    $needDate = isset($_REQUEST["needDate"]) ? $_REQUEST["needDate"] : $currShift["productionDate"];
    $needShift = isset($_REQUEST["needShift"]) ? $_REQUEST["needShift"] : $currShift["shift"];

    //    echo "<pre>"; print_r($selWhId);echo "</pre>";
    //    echo "<pre>"; print_r($needDate);echo "</pre>";
    //    echo "<pre>"; print_r($needShift);echo "</pre>";

    $userWarehouseIds = Yii::$app->user->identity->warehouseIds;
    $whCondition = !Yii::$app->user->can("admin")
      ? [
        "and",
        ["in", "id", $userWarehouseIds],
        ["status" => [Warehouse::STATUS_ACTIVE]],
        ["warehouse_type" => [Warehouse::TYPE_SHOP]],
      ]
      : ["and", ["status" => [Warehouse::STATUS_ACTIVE]], ["warehouse_type" => [Warehouse::TYPE_SHOP]]];
    $allWarehouses = ArrayHelper::map(
      Warehouse::find()
        ->where($whCondition)
        ->all(),
      "id",
      "name"
    );
    if ($selWhId === null) {
      if (count($allWarehouses) === 1) {
        foreach ($allWarehouses as $keyWh => $valWh) {
          $selWhId = $keyWh;
        }
      }
    }
    $productionMonitor = null;
    $productionMonitorStatus = null;
    $productionMonitorLineStopsConfirmed = false;
    $prodPartLists = [];
    if ($selWhId !== null) {
      $productionMonitor = ProductionMonitor::findOne([
        "warehouse_id" => $selWhId,
        "production_date" => $needDate,
        "shift" => $needShift,
      ]);
      if ($productionMonitor !== null) {
        $productionMonitorStatus = $productionMonitor->status;
        $productionMonitorLineStopsConfirmed = $productionMonitor->isAllLineStopNotConfirmed();
        $partProductionMonitor = PartProductionMonitor::find()
          ->select([
            "pt.part_color as pt_part_color",
            "pt.part_no as pt_part_no",
            "pt.part_name as pt_part_name",
            "pt.cycle_time as pt_cycle_time",
            "part_production_monitor.id as ppm_id",
            "part_production_monitor.produced_qty as ppm_produced_qty",
            "part_production_monitor.repaired_qty as ppm_repaired_qty",
            "part_production_monitor.broken_qty as ppm_broken_qty",
            "part_production_monitor.start_time as ppm_start_time",
            "part_production_monitor.end_time as ppm_end_time",
            "part_production_monitor.actual_production_time as ppm_actual_production_time",
            "pm.id as pm_id",
            "pm.quality_confirmed_at as pm_quality_confirmed_at",
            "pm.quality_confirmed_by as pm_quality_confirmed_by",
            "pm.production_completed_at as pm_production_completed_at",
            "pm.production_completed_by as pm_production_completed_by",
            "planed_ls.elapsed_minutes as planed_minutes",
            "not_planed_ls.elapsed_minutes as not_planed_minutes",
          ])
          ->innerJoin("production_monitor as pm", "part_production_monitor.production_monitor_id=pm.id")
          ->innerJoin("part as pt", "part_production_monitor.part_id=pt.id")
          ->leftJoin(
            [
              "not_planed_ls" => LineStop::find()
                ->joinWith([
                  "lineStopReason" => function ($ql) {
                    $ql->from(["not_planed_ls" => LineStopReason::tableName()]);
                    $ql->where(["not_planed_ls.type" => LineStopReason::TYPE_NOTPLANNED]);
                  },
                ])
                ->select(["part_production_monitor_id as not_planed_ppm_id", "SUM(elapsed_minutes) AS elapsed_minutes"])
                ->where("start_time>='" . $currShift["start_at"] . "' and end_time<='" . $currShift["end_at"] . "'")
                ->groupBy("part_production_monitor_id"),
            ],
            "not_planed_ppm_id=part_production_monitor.id"
          )
          ->leftJoin(
            [
              "planed_ls" => LineStop::find()
                ->joinWith([
                  "lineStopReason" => function ($ql1) {
                    $ql1->from(["planed_ls" => LineStopReason::tableName()]);
                    $ql1->where(["planed_ls.type" => LineStopReason::TYPE_PLANNED]);
                  },
                ])
                ->select(["part_production_monitor_id as planed_ppm_id", "SUM(elapsed_minutes) AS elapsed_minutes"])
                ->where("start_time>='" . $currShift["start_at"] . "' and end_time<='" . $currShift["end_at"] . "'")
                ->groupBy("part_production_monitor_id"),
            ],
            "planed_ppm_id=part_production_monitor.id"
          )
          ->where([
            "pm.warehouse_id" => $selWhId,
            "pm.production_date" => $needDate,
            "pm.shift" => $needShift,
          ]);
//                echo "<pre>";
//                print_r($partProductionMonitor->createCommand()->rawSql);
//                echo "</pre>";
//                die;
        $prodPartLists = $partProductionMonitor->asArray()->all();
      }
    }
    //    echo "<pre>"; print_r($plannedLineStop);echo "</pre>";
    //    die;
    return $this->render("index", [
      "selWhId" => $selWhId,
      "warehouseLists" => $allWarehouses,
      "productionMonitorStatus" => $productionMonitorStatus,
      "productionMonitorLineStopsConfirmed" => $productionMonitorLineStopsConfirmed,
      "prodPartLists" => $prodPartLists,
      "needDate" => $needDate,
      "needShift" => $needShift,
    ]);
  }

  public function actionEdit()
  {
    $post = $_POST;
    $transaction = Yii::$app->db->beginTransaction();
    if (isset($_POST)) {
      $model = $this->findModel($_POST["id"]);
      switch (true) {
        case $_POST["fieldName"] == "actual_production_time":
          $model->actual_production_time = $_POST["fieldValue"];
          break;
        case $_POST["fieldName"] == "repaired_qty":
          $model->repaired_qty = $_POST["fieldValue"];
          break;
        case $_POST["fieldName"] == "broken_qty":
          $model->broken_qty = $_POST["fieldValue"];
          break;
        case $_POST["fieldName"] == "start_time":
          $model->start_time = $_POST["fieldValue"];
          break;
        case $_POST["fieldName"] == "end_time":
          $model->end_time = $_POST["fieldValue"];
          break;
      }
    }
    $model->updated_by = Yii::$app->user->id;
    $model->updated_at = time();
    //    echo "<pre>"; print_r($model);echo "</pre>";
    Yii::$app->response->format = Response::FORMAT_JSON;
    if (!$model->save()) {
      $transaction->rollBack();
      $result = ["sts" => "ERROR", "sms" => $model->getErrors()];
      return $result;
    }
    $transaction->commit();
    return ["sts" => "OK", "sms" => Yii::t("app", "Done ✓")];
  }

  public function actionConfirm()
  {
    $pm = ProductionMonitor::findOne([
      "warehouse_id" => $_POST["whId"],
      "production_date" => $_POST["needDate"],
      "shift" => $_POST["needShift"],
    ]);
    Yii::$app->response->format = Response::FORMAT_JSON;
    $confirmOk = ProductionMonitor::confirm($pm->id, Yii::$app->user->id, $_POST["allow"]);
    if (!$confirmOk) {
      return ["sts" => "ERROR", "sms" => Yii::t("app", "Confirmed error")];
    } else {
      return ["sts" => "OK", "sms" => Yii::t("app", "Done ✓")];
    }
  }

  public function actionComplete()
  {
    $pm = ProductionMonitor::findOne([
      "warehouse_id" => $_POST["whId"],
      "production_date" => $_POST["needDate"],
      "shift" => $_POST["needShift"],
    ]);
    Yii::$app->response->format = Response::FORMAT_JSON;
    $completeOk = ProductionMonitor::complete($pm->id, Yii::$app->user->id, $_POST["allow"]);
    if (!$completeOk) {
      return ["sts" => "ERROR", "sms" => Yii::t("app", "Completed error")];
    } else {
      return ["sts" => "OK", "sms" => Yii::t("app", "Done ✓")];
    }
  }

  /**
   * Finds the PartProductionMonitor model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   *
   * @param integer $id
   *
   * @return PartProductionMonitor the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id)
  {
    if (($model = PartProductionMonitor::findOne($id)) !== null) {
      return $model;
    }
    throw new NotFoundHttpException(Yii::t("app", "The requested page does not exist."));
  }
}
