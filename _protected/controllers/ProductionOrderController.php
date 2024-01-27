<?php
namespace app\controllers;

use app\components\Helpers;
use app\models\Machine;
use app\models\Part;
use app\models\PartProductionMonitor;
use app\models\ProduceForm;
use app\models\ProductionMonitor;
use app\services\TelegramService;
use app\models\ProductionOrder;
use app\models\ProductionOrderSearch;
use app\models\ProductionOrderUploadForm;
use app\models\ProductModel;
use app\models\ProductSpecification;
use app\models\Stock;
use app\models\StockInfoWrapper;
use app\models\User;
use app\models\Warehouse;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * ProductionOrderController implements the CRUD actions for ProductionOrder model.
 */
class ProductionOrderController extends AppController
{
  public function actionIndex()
  {
    $searchModel = new ProductionOrderSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    if (!Yii::$app->user->can("admin")) {
      if (Yii::$app->user->can("counter") || Yii::$app->user->can("mrpc")) {
        $dataProvider->query->andWhere(["part.warehouse_id" => Yii::$app->user->identity->warehouseIds]);
      }
    }
    $dataProvider->sort->defaultOrder = ["id" => SORT_DESC];
    return $this->render(
      "index",
      array_merge(
        [
          "searchModel" => $searchModel,
          "dataProvider" => $dataProvider,
        ],
        self::loadDictionaries()
      )
    );
  }

  /**
   * Finds the ProductionOrder model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   *
   * @param int $id
   *
   * @return ProductionOrder the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id)
  {
    if (($model = ProductionOrder::findOne($id)) !== null) {
      return $model;
    }
    throw new NotFoundHttpException(Yii::t("app", "The requested page does not exist."));
  }

  public function actionCreateProductionOrders()
  {
    Yii::$app->response->format = Response::FORMAT_JSON;
    $post = $_POST;
    $product_line_model = Machine::findOne($post["machine_id"]);
    $product_line_model->last_count = $product_line_model->last_count + $post["cnt"];
    $transaction = Yii::$app->db->beginTransaction();
    $post_params["ProductionOrder"] = "";
    $post_params["produced"] = 1;
    if (!$product_line_model->save()) {
      $message = Yii::t("app", "Production order not updated. Something is wrong.");
      $transaction->rollBack();
      $errors = implode(PHP_EOL, $product_line_model->errors);
      $result = ["sts" => "ERROR", "sms" => $message . PHP_EOL . $errors];
      return $result;
    }
    $part_info = $product_line_model->mold->moldParts;
    $post_params["produced"] = 1;
    foreach ($part_info as $part_item) {
      $post_params["ProductionOrder"] = [
        "part_id" => $part_item->part_id,
        "quantity" => $part_item->quantity * $post["cnt"],
        "quantity_of_copy" => 1,
      ];
      $crtResult = ProductionOrder::createProdOrders($post_params, time());
      if ($crtResult["success"] != true) {
        $message = Yii::t("app", "Production order not created. Something is wrong.");
        $errors = $crtResult["errorlist"];
        $errors = str_replace("<br>", PHP_EOL, $errors);
        $transaction->rollBack();
        $result = ["sts" => "ERROR", "sms" => $message . PHP_EOL . $errors];
        return $result;
      }
    }

    $transaction->commit();
    $message = Yii::t("app", "Done ✓");
    return ["sts" => "OK", "sms" => $message];
  }

  /**
   * Creates a new ProductionOrder model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   *
   * @return mixed
   */
  public function actionCreate()
  {

    $stock_info_wrapper_list = StockInfoWrapper::all();
    $post_params = Yii::$app->request->post();
    $model = new ProductionOrder(["scenario" => "create"]);
    $model->quantity_of_copy = 1;
    $xozir_time = date("H:i");
    $shift = Yii::$app->params["shifts"];
    $shift_1 = $shift["1"]["0"];
    $shift_2 = $shift["2"]["0"]["0"];
    $shift_1_p1 = date("H:i", strtotime($shift_1) + 60 * 60);
    $shift_1_m1 = date("H:i", strtotime($shift_1) - 1);
    $shift_2_p1 = date("H:i", strtotime($shift_2) + 60 * 60);
    $shift_2_m1 = date("H:i", strtotime($shift_2) - 1);
    $prev_shift = 0;
    $shift_crt_at = time();
    if ($xozir_time >= $shift_1 && $xozir_time < $shift_1_p1) {
      $prev_shift = 1;
      $shift_crt_at = strtotime(date("Ymd") . $shift_1_m1);
    } elseif ($xozir_time >= $shift_2 && $xozir_time < $shift_2_p1) {
      $prev_shift = 1;
      $shift_crt_at = strtotime(date("Ymd") . $shift_2_m1);
    }
    if (isset($post_params["ProductionOrder"])) {
      $model->load($post_params);
      // if fact attach spec id
      $transaction = Yii::$app->db->beginTransaction();
      $spec = ProductSpecification::find()
        ->where(["part_id" => $model->part_id, "status" => ProductSpecification::STATUS_ACTIVE])
        ->one();
      $post_params["ProductionOrder"]["product_specification_id"] = $spec ? $spec->id : null;
      $new_ids = [];
      for ($q = 1; $q <= $post_params["ProductionOrder"]["quantity_of_copy"]; $q++) {
        $crtResult = [];
        $crtResult = ProductionOrder::createProdOrders($post_params, $shift_crt_at);
        if ($crtResult["success"] != true) {
          $err = 1;
          $message = "Production order not created. Something is wrong.";
          $errors = $crtResult["errorlist"];
          $err_sms = Yii::t("app", $message . "<br>" . $errors);
          $transaction->rollBack();
          Yii::$app->session->setFlash("error", $err_sms);
          return $this->render(
            "create",
            array_merge(["model" => $model, "prev_shift" => $prev_shift, 'stock_info_wrapper_list' => $stock_info_wrapper_list], self::loadDictionaries())
          );
        }
      }

      $floc = Warehouse::find()
        ->where(["id" => $post_params["floc"]])
        ->one();

      $tg["quantity_of_copy"] = $model->quantity_of_copy;
      $tg["quantity"] = $model->quantity;
      $tg["code"] = $spec->code;
      $tg["floc"] = $floc["name"];
      $tg["side"] = $post_params["side"];
      TelegramService::productionOrder($tg);

      $transaction->commit();
      Yii::$app->session->setFlash("success", Yii::t("app", "Production order created successfully."));
      return $this->redirect(["create", "ProductionOrderSearch" => ["ids" => $new_ids]]);
    }
    $queryParams = Yii::$app->request->queryParams;
    if (isset($queryParams["ProductionOrderSearch"]["ids"])) {
      $searchModel = new ProductionOrderSearch();
      $modelsToPrint = $searchModel->search(Yii::$app->request->queryParams)->query->all();
    }
    return $this->render(
      "create",
      array_merge(
        [
          "model" => $model,
          "prev_shift" => $prev_shift,
          "modelsToPrint" => $modelsToPrint ?? null,
          'stock_info_wrapper_list' => $stock_info_wrapper_list
        ],
        self::loadDictionaries()
      )
    );
  }

//  private function writeToMonitor($model, $copyCount, $production_date, $shift)
//  {
//    // Dastlab header tablega ma'lumot yozamiz
//    $result = ProductionMonitor::write($model->part->warehouse_id, $production_date, $shift);
//    if ($result["status"] === 0) {
//      Yii::$app->session->setFlash("error", Helpers::arrayToStringRecursive($result["errors"]));
//      return false;
//    }
//    PartProductionMonitor::setProduced(
//      $result["data"]->id,
//      $model->part_id,
//      $model->quantity * $copyCount,
//      Yii::$app->user->identity->id
//    );
//    return true;
//  }

  private function deleteToMonitor($model, $production_date, $shift)
  {
    $productionMonitor = ProductionMonitor::find()
      ->where(["warehouse_id" => $model->part->warehouse_id, "production_date" => $production_date, "shift" => $shift])
      ->one();
    if ($productionMonitor) {
      PartProductionMonitor::setProduced(
        $productionMonitor->id,
        $model->part_id,
        $model->quantity,
        Yii::$app->user->identity->id,
        "delete"
      );
      return true;
    }
    return false;
  }

  public function actionUpload()
  {
    $model = new ProductionOrderUploadForm();
    if ($model->load(Yii::$app->request->post())) {
      $uploadedFile = UploadedFile::getInstance($model, "file");
      $data = $model->readExcel($uploadedFile->tempName);
      $emptyCells = [];
      $notExistsParts = [];
      $modelErrors = [];
      foreach ($data["values"] as $key => $row) {
        if (empty($row["part_no"]) or empty($row["qty"])) {
          $emptyCells[] = Yii::t("app", "Row") . " " . ($key + 2) . ". " . Yii::t("app", "Empty data.");
        } else {
          if (!in_array(trim($row["part_no"]), Part::getPartNumbers())) {
            $notExistsParts[] =
              Yii::t("app", "Row") .
              " " .
              ($key + 2) .
              ". " .
              Yii::t("app", "Part not found.") .
              "  (" .
              $row["part_no"] .
              ")";
          }
        }
      }
      if (count($emptyCells) > 0) {
        $message = "Empty cells found in uploaded file.";
        $errors = implode("<br>", $emptyCells);
        Yii::$app->session->setFlash("error", "<b>" . Yii::t("app", $message) . "</b>" . "<br>" . $errors);
        return $this->render("upload", ["model" => $model]);
      }
      if (count($notExistsParts) > 0) {
        $message = "These parts are not found in active parts list.";
        $errors = implode("<br>", $notExistsParts);
        Yii::$app->session->setFlash("error", "<b>" . Yii::t("app", $message) . "</b>" . "<br>" . $errors);
        return $this->render("upload", ["model" => $model]);
      }
      $transaction = Yii::$app->db->beginTransaction();
      foreach ($data["values"] as $key => $row) {
        $new_ids = [];
        $modelPo = new ProductionOrder();
        $modelPo->part_id = Part::findOneByPartNumber($row["part_no"])->id;
        $spec = ProductSpecification::find()
          ->where(["part_id" => $modelPo->part_id, "status" => ProductSpecification::STATUS_ACTIVE])
          ->one();
        $modelPo->product_specification_id = $spec ? $spec->id : null;
        $modelPo->quantity = $row["qty"];
        $modelPo->current_event = ProductionOrder::EVENT_PRODUCED;
        $modelPo->current_seq = $modelPo->getCurrentSeq($modelPo->part_id) + 1;
        $modelPo->created_at = time();
        $modelPo->serial_number = $modelPo->generateSerialNumber();
        $modelPo->is_label = $modelPo->quantity > 0 ? ProductionOrder::LABEL_ACTUAL : ProductionOrder::LABEL_INDIVIDUAL;
        if ($modelPo->save()) {
          $resultCons = Stock::consumption($modelPo);
          if ($resultCons["success"] != 1) {
            $transaction->rollBack();
            $message = "Production order not created. Something is wrong.";
            $errors = implode("<br>", $resultCons["errorlist"]);
            Yii::$app->session->setFlash("error", Yii::t("app", $message . "<br>" . $errors));
            return $this->render("upload", ["model" => $model]);
          }
          $new_ids[] = $modelPo->id;

          $resultToMonitor = ProductionOrder::writeToMonitor(
            $modelPo,
            $modelPo->quantity
          );
          if (!$resultToMonitor) {
            $transaction->rollBack();
            $message = "writeToMonitor not created. Something is wrong.";
            $errors = implode("<br>", $resultCons["errorlist"]);
            Yii::$app->session->setFlash("error", Yii::t("app", $message . "<br>" . $errors));
            return $this->render("upload", ["model" => $model]);
          }
        } else {
          $transaction->rollBack();
          $message = "Production order not created";
          $errors = "";
          foreach ($modelPo->errors as $field_errors) {
            foreach ($field_errors as $error) {
              $errors .= "<br>" . $error;
            }
          }
          Yii::$app->session->setFlash("error", Yii::t("app", $message) . "<br>" . $errors);
          return $this->render("upload", ["model" => $model]);
        }
      }
      $transaction->commit();
      Yii::$app->session->setFlash("success", Yii::t("app", "Production order created successfully."));
      return $this->redirect("index");
    }
    return $this->render("upload", [
      "model" => $model,
    ]);
  }

  public function actionCreateIsbulk()
  {
    $post_params = Yii::$app->request->post();
    $model = new ProductionOrder(["scenario" => "create-isbulk"]);
    $model->quantity_of_copy = 1;
    if (isset($post_params["ProductionOrder"])) {
      $model->load($post_params);
      $transaction = Yii::$app->db->beginTransaction();
      $spec = ProductSpecification::find()
        ->where(["part_id" => $model->part_id, "status" => ProductSpecification::STATUS_ACTIVE])
        ->one();
      $post_params["ProductionOrder"]["product_specification_id"] = $spec ? $spec->id : null;
      $new_ids = [];
      for ($q = 1; $q <= $post_params["ProductionOrder"]["quantity_of_copy"]; $q++) {
        $modelPo = new ProductionOrder();
        //        $modelPo->scenario = 'create-isbulk';
        $modelPo->load($post_params);
        $qty = $post_params["ProductionOrder"]["quantity"] ?? 0;
        $is_label = $post_params["ProductionOrder"]["is_label"] ?? 0;
        $modelPo->quantity = $qty;
        $modelPo->current_seq = $modelPo->getCurrentSeq($modelPo->part_id) + 1;
        if ($is_label == 1) {
          if ($qty > 0) {
            $modelPo->current_event = "600";
            $modelPo->quantity = $qty;
          } else {
            $message = "Quantity must be between 1 and 999999";
            Yii::$app->session->setFlash("error", Yii::t("app", $message));
            return $this->render("create-isbulk", array_merge(["model" => $model], self::loadDictionaries()));
          }
        } else {
          $modelPo->current_event = isset($post_params["produced"])
            ? ProductionOrder::EVENT_PRODUCED
            : ProductionOrder::EVENT_INITIAL;
          $modelPo->quantity = 0;
        }
        $modelPo->created_at = time();
        $modelPo->serial_number = $modelPo->generateSerialNumber();
        if ($modelPo->save()) {
          $new_ids[] = $modelPo->id;
        } else {
          $transaction->rollBack();
          $message = "Production order not created.";
          $errors = $message . "<br>" . print_r($modelPo->errors);
          Yii::$app->session->setFlash("error", Yii::t("app", $message . "<br>" . $errors));
          return $this->render("create-isbulk", array_merge(["model" => $model], self::loadDictionaries()));
        }
      }
      $transaction->commit();
      Yii::$app->session->setFlash("success", Yii::t("app", "Production order created successfully."));
      return $this->redirect(["create-isbulk", "ProductionOrderSearch" => ["ids" => $new_ids]]);
    }
    $queryParams = Yii::$app->request->queryParams;
    if (isset($queryParams["ProductionOrderSearch"]["ids"])) {
      $searchModel = new ProductionOrderSearch();
      $modelsToPrint = $searchModel->search(Yii::$app->request->queryParams)->query->all();
    }
    return $this->render(
      "create-isbulk",
      array_merge(["model" => $model, "modelsToPrint" => $modelsToPrint ?? null], self::loadDictionaries())
    );
  }

  public function actionSelectedPrint()
  {
    $searchModel = new ProductionOrderSearch();
    $model = $searchModel->search(Yii::$app->request->queryParams)->query->all();
    return $this->render("print", compact("model", "action"));
  }

  public function actionXls()
  {
    ini_set("memory_limit", "-1");
    $searchModel = new ProductionOrderSearch();
    $xls_file = $searchModel->search(Yii::$app->request->queryParams, "excel");
    if (
      is_array($xls_file->sheets["ProductionOrder"]["data"] ?? null) and
      count($xls_file->sheets["ProductionOrder"]["data"]) == 0
    ) {
      return $this->redirect(["index"]);
    }
    $xls_file->send(Helpers::downloadFileName("production-order"));
  }

  public function actionXlsx()
  {
    ini_set("memory_limit", "-1");
    $searchModel = new ProductionOrderSearch();
    $xls_file = $searchModel->search(Yii::$app->request->queryParams, "xlsx");
    if (
      is_array($xls_file->sheets["ProductionOrder"]["data"] ?? null) and
      count($xls_file->sheets["ProductionOrder"]["data"]) == 0
    ) {
      return $this->redirect(["index"]);
    }
    $xls_file->send(Helpers::downloadFileName("production-order"));
  }

  public function actionProduce()
  {
    $model = new ProduceForm();
    $last_produced_orders = ProductionOrder::find()
      ->where(["current_event" => ProductionOrder::EVENT_PRODUCED])
      ->orderBy(["id" => SORT_DESC])
      ->limit(10)
      ->all();
    if ($model->load(Yii::$app->request->post())) {
      $post = Yii::$app->request->post();
      $serial_number = $post["ProduceForm"]["serial_number"];
      $production_order = ProductionOrder::getOrderBySerial($serial_number);
      if (empty($production_order)) {
        Yii::$app->session->setFlash("error", Yii::t("app", "Serial number not found."));
        return $this->redirect("produce");
      }
      if ($production_order->current_event != ProductionOrder::EVENT_INITIAL) {
        Yii::$app->session->setFlash("error", Yii::t("app", "This production order is already produced."));
        return $this->redirect("produce");
      }
      $transaction = Yii::$app->db->beginTransaction();
      // Ostatkada ayirish
      $resultCons = Stock::consumption($production_order);
      if ($resultCons["success"] != 1) {
        $transaction->rollBack();
        Yii::$app->session->setFlash("error", Yii::t("app", implode(", ", $resultCons["errorlist"])));
        return $this->redirect("produce");
      }
      // change current_event
      $production_order->current_event = ProductionOrder::EVENT_PRODUCED;
      if ($production_order->save()) {
        $transaction->commit();
        Yii::$app->session->setFlash("success", Yii::t("app", "Production order succesfully produced."));
        return $this->redirect("produce");
      } else {
        $transaction->rollBack();
        Yii::$app->session->setFlash("error", Yii::t("app", "Production order not produced. Something wrong."));
        return $this->redirect("produce");
      }
    }
    return $this->render("produce", [
      "model" => $model,
      "last_produced_orders" => $last_produced_orders,
    ]);
  }

  /**
   * Deletes an existing ProductionOrder model.
   * If deletion is successful, the browser will be redirected to the 'index' page.
   *
   * @param int $id
   *
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionDelete($id)
  {
    $model = $this->findModel($id);
    if (!Yii::$app->user->can("admin") && !Yii::$app->user->can("superadmin")) {
      if ($model->created_by != Yii::$app->user->id) {
        throw new ForbiddenHttpException(Yii::t("app", "You are not allowed to edit this record."));
      }
    }
    if ($model->current_event != ProductionOrder::EVENT_PRODUCED) {
      if ($model->current_event == ProductionOrder::EVENT_SHIPPED) {
        throw new ForbiddenHttpException(Yii::t("app", "This package is already shipped."));
      }
      if ($model->current_event == ProductionOrder::EVENT_ARRIVED) {
        throw new ForbiddenHttpException(Yii::t("app", "This package is already arrived."));
      }
    }
    $errorlist = [];
    $transaction = Yii::$app->db->beginTransaction();
    if ($model->is_label == ProductionOrder::LABEL_ACTUAL) {
      // Komponent ostatkasiga qo'shish, part ostatkasidan kamaytirish
      $resultCons = Stock::deconsumption($model, 0);
      if ($resultCons["success"] != 1) {
        $errorlist = $resultCons["errorlist"];
      }
    } else {
      $resultCons["success"] = 1;
    }

    /**  ProductionMonitor edit begin */
    $needDt = date("Y-m-d H:i", $model->created_at);
    $prodDt = date("Y-m-d", $model->created_at);
    $productionMonitor = ProductionMonitor::find()
                                          ->where([
                                            "warehouse_id" => $model->part->warehouse_id,
                                            "production_date" => $prodDt,
                                            "shift" => Helpers::getShift($needDt)["shift"],
                                          ])
                                          ->one();
//    echo "<pre>"; print_r($needDt);echo "</pre>";
//    echo "<pre>"; print_r($model->part->warehouse_id);echo "</pre>";
//    echo "<pre>"; print_r(Helpers::getShift($needDt)["shift"]);echo "</pre>";
//    echo "<pre>"; print_r($productionMonitor);echo "</pre>";
//    die;

    if ($productionMonitor) {
      $result = PartProductionMonitor::setProduced(
        $productionMonitor->id,
        $model->part_id,
        $model->quantity,
        Yii::$app->user->identity->id,
        "delete"
      );
      $deleteError = false;
      if (!$result) {
        $deleteError = true;
      }
    /**  ProductionMonitor edit end*/

    if ($model->delete() and $resultCons["success"] == 1 && $model->is_label === ProductionOrder::LABEL_ACTUAL){
        if ($deleteError == true) {
          $transaction->rollBack();
          Yii::$app->session->setFlash("error", Yii::t("app", "Production order not removed. Something is wrong."));
        } else {
          $transaction->commit();
          Yii::$app->session->setFlash("success", Yii::t("app", "Production order successfully removed."));
        }
      }
    } else {
      $transaction->rollBack();
      Yii::$app->session->setFlash("error", Yii::t("app", "Production order not removed. Something is wrong."));
    }
    return $this->redirect([
      "index",
      "errorlist" => $errorlist ?? null,
      "ProductionOrderSearch" => $_GET["searchUrl"] ?? null,
    ]);
  }

  /**
   * Finds the ProductionOrder model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   *
   * @param int $id
   *
   * @return ProductionOrder the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  private function loadDictionaries()
  {
    $queryPart = Part::find()->where("status = 1 and state <> 0");
    if (Yii::$app->user->identity->rolename == "counter" || Yii::$app->user->identity->rolename == "mrpc") {
      $queryPart->andWhere(["warehouse_id" => Yii::$app->user->identity->warehouseIds]);
    }
    $parts = [];
    $parts_withptnm = [];
    $options = [];
    foreach ($queryPart->all() as $part) {
      $parts[$part->id] = $part->part_no . " (" . $part->part_color . ")";
      if ($part->warehouse->warehouse_type ?? null != Warehouse::TYPE_OUTSOURCING) {
        $parts_withptnm[$part->id] = $part->part_no . " (" . $part->part_color . ") " . $part->part_name. " " .$part->remark;
      }
      $options[$part->id] = ["data-pack-size" => $part->pack_size ?? "0"];
    }
    $users = ArrayHelper::map(
      User::find()
        ->where(["status" => User::STATUS_ACTIVE])
        ->all(),
      "id",
      "fullname"
    );
    $models = ArrayHelper::map(ProductModel::find()->all(), "id", "modelinfo");
    $flocs = ArrayHelper::map(
      Warehouse::find()
        ->where(["warehouse_type" => [Warehouse::TYPE_SHOP, Warehouse::TYPE_OUTSOURCING, Warehouse::TYPE_STOCKINFO]])
        ->all(),
      "id",
      "name"
    );
    return compact("parts", "parts_withptnm", "users", "options", "models", "flocs");
  }
}
