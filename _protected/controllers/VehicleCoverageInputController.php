<?php
namespace app\controllers;

use app\components\Helpers;
use app\models\ProductModel;
use app\models\VehicleCoverageInput;
use app\models\VehicleCoverageInputSearch;
use Yii;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * VehicleCoverageInputController implements the CRUD actions for VehicleCoverageInput model.
 */
class VehicleCoverageInputController extends AppController {

  private function loadDictionaries() {
    $modelDescriptionName = new VehicleCoverageInput();
    $descriptionList = $modelDescriptionName->descriptionName;
    $productModel = ProductModel::getModelVehicles();

    return compact('productModel', 'descriptionList');
  }

  public function loadStockEtaPaid($modelId, $desc) {
    switch ($desc) {
      case ($desc < 3 or $desc == 4):
        $result = VehicleCoverageInput::findOne(['model_id' => $modelId, 'description' => $desc]);
        break;
      case 3:
        $result = ArrayHelper::map(
          VehicleCoverageInput::find()->where(['model_id' => $modelId, 'description' => $desc])->all(),
          'for_date', 'quantity'
        );
        break;
    }

    return $result;
  }

  /**
   * Lists all VehicleCoverageInput models.
   *
   * @return mixed
   */
  public function actionIndex() {
    
    $vehicleCoverage = VehicleCoverageInput::find()
                                           ->where(['description' => VehicleCoverageInput::CURRENT_STOCK])
                                           ->all();
    $rowCurrent = [];
    foreach ($vehicleCoverage as $key => $item) {
      $rowCurrent[$item['model_id']][$item['coverage_date']] = $item['quantity'];
    }


    $vehicleCoverage = VehicleCoverageInput::find()
                                           ->where(['description' => VehicleCoverageInput::UAM_STOCK])
                                           ->all();
    $rowUam = [];
    foreach ($vehicleCoverage as $key => $item) {
      $rowUam[$item['model_id']][$item['coverage_date']] = $item['quantity'];
    }

    $vehicleCoverage = VehicleCoverageInput::find()
                                           ->where(['description' => VehicleCoverageInput::PAID_NOT_SHIPPED_ORDER])
                                           ->all();
    $rowPaid = [];
    foreach ($vehicleCoverage as $key => $item) {
      $rowPaid[$item['model_id']][$item['for_date']] = $item['quantity'];
    }

    $vehicleCoverage = VehicleCoverageInput::find()
                                           ->where(['description' => VehicleCoverageInput::INTRANSIT_ETA])
                                           ->orderBy('for_date, model_id')
                                           ->all();
    //      ->createCommand()->rawSql;
    //    echo "<pre>"; print_r($vehicleCoverage);echo "</pre>";
    //    die;
    $rowETA = [];
    foreach ($vehicleCoverage as $key => $item) {
      $rowETA[$item['for_date']][$item['model_id']] = $item['quantity'];
    }

    ksort($rowCurrent, 1);
    ksort($rowUam, 1);
    ksort($rowPaid, 1);
    ksort($rowETA, 1);

    $downloadFileName = Helpers::downloadFileName('vehicle-set-coverage', '1');
    $downloadFileName = rtrim($downloadFileName, '.1');

    return $this->render('index', array_merge(
        [
          'rowCurrent' => $rowCurrent,
          'rowUam' => $rowUam,
          'rowPaid' => $rowPaid,
          'rowETA' => $rowETA,
          'downloadFileName' => $downloadFileName
        ], self::loadDictionaries()
      )
    );
  }

  public function actionRefresh($modelId) {

    $model = new VehicleCoverageInput();
    $model->model_id = $modelId;
    $curStockObj = self::loadStockEtaPaid($modelId, VehicleCoverageInput::CURRENT_STOCK);
    $uamStockObj = self::loadStockEtaPaid($modelId, VehicleCoverageInput::UAM_STOCK);
    $intransitETA = self::loadStockEtaPaid($modelId, VehicleCoverageInput::INTRANSIT_ETA);
    $paidNotShippedOrderObj = self::loadStockEtaPaid($modelId, VehicleCoverageInput::PAID_NOT_SHIPPED_ORDER);
    
   
    $currCoverageDate = $curStockObj->coverage_date ?? null;
    $curStock = ($curStockObj) ? $curStockObj->quantity : 0;
    $uamStock = ($uamStockObj) ? $uamStockObj->quantity : 0;
    $paidNotShippedOrder = ($paidNotShippedOrderObj) ? $paidNotShippedOrderObj->quantity : 0;

    $model->model_id = $modelId;
    if (isset($_POST) && count($_POST) > 0) {
      $transaction = Yii::$app->db->beginTransaction();
      /** Avval shu madelga tegishli barcha ma`lumotni o`chirvolamiz */
      try {
        VehicleCoverageInput::deleteAll(['model_id' => $modelId]);
      }
      catch (Exception $e) {
        $err_msg = $e->errorInfo;
        Yii::$app->session->setFlash('error', $err_msg[2]);

        return $this->render(
          'refresh', array_merge(
            [
              'model' => $model,
              'curStock' => Helpers::numberFormatRemoveZero($curStock, 4, ".", ""),
              'paidNotShippedOrder' => Helpers::numberFormatRemoveZero($paidNotShippedOrder, 4, ".", ""),
              'intransitETA' => $intransitETA,
            ], self::loadDictionaries()
          )
        );
      }
      $err = 0;
      $err_msg = '';
      /** 1-CURRENT_STOCK KIRITSH*/
      $modelStock = new VehicleCoverageInput();
      $modelStock->model_id = $modelId;
      $modelStock->description = VehicleCoverageInput::CURRENT_STOCK;
      $modelStock->quantity = $_POST['curStock'];
      $modelStock->for_date = date("Y-m-d", time());
      $modelStock->coverage_date = $currCoverageDate;
      $modelStock->created_by = Yii::$app->user->identity->id;
      $modelStock->created_at = time();
      if (!$modelStock->save()) {
        $err = 1;
        $err_msg .= "<i><u><strong>CURRENT_STOCK:</strong></u></i>".Helpers::arrayToStringRecursive($modelStock->errors);
      }

      /** 4-CURRENT_STOCK KIRITSH*/
      $modelUamStock = new VehicleCoverageInput();
      $modelUamStock->model_id = $modelId;
      $modelUamStock->description = VehicleCoverageInput::UAM_STOCK;
      $modelUamStock->quantity = $_POST['uamStock'];
      $modelUamStock->for_date = date("Y-m-d", time());
      $modelUamStock->coverage_date = $currCoverageDate;
      $modelUamStock->created_by = Yii::$app->user->identity->id;
      $modelUamStock->created_at = time();
      if (!$modelUamStock->save()) {
        $err = 1;
        $err_msg .= "<i><u><strong>UAM_STOCK:</strong></u></i>".Helpers::arrayToStringRecursive($modelUamStock->errors);
      }

      /** 2-PAID_NOT_SHIPPED_ORDER  KIRITSH*/
      if ($err == 0) {
        $modelPaid = new VehicleCoverageInput();
        $modelPaid->model_id = $modelId;
        $modelPaid->description = VehicleCoverageInput::PAID_NOT_SHIPPED_ORDER;
        $modelPaid->quantity = $_POST['paidNotShippedOrder'];
        $modelPaid->for_date = date("Y-m-d", time());
        $modelPaid->created_by = Yii::$app->user->identity->id;
        $modelPaid->created_at = time();
        if (!$modelPaid->save()) {
          $err = 1;
          $err_msg .= "<i><u><strong>PAID_NOT_SHIPPED_ORDER:</strong></u></i>".Helpers::arrayToStringRecursive($modelPaid->errors);
          Yii::$app->session->setFlash('error', $err_msg);
        }
      }
      /** 3-INTRANSIT_ETA KIRITSH*/
      $hasWrongEta = false;
      if ($err == 0) {
        if (isset($_POST['eta']) > 0) {
          foreach ($_POST['eta']['qty'] as $key => $val) {
            if (($_POST['eta']['qty'][$key]) > 0) {
              $modelEta = new VehicleCoverageInput();
              $modelEta->model_id = $modelId;
              $modelEta->description = VehicleCoverageInput::INTRANSIT_ETA;
              $modelEta->quantity = $_POST['eta']['qty'][$key];
              $modelEta->for_date = $_POST['eta']['date'][$key];
              $modelEta->created_by = Yii::$app->user->identity->id;
              $modelEta->created_at = time();
              if (!$modelEta->save()) {
                $err = 1;
                $err_msg .= "<i><u><strong>INTRANSIT_ETA:</strong></u></i>".Helpers::arrayToStringRecursive($modelEta->errors);
              }else{
                  if(!$hasWrongEta)
                    $hasWrongEta = ($modelEta->for_date < date('Y-m-d'));
              }
            }
          }
        }
      }

      if ($err == 0) {
          // Agar Stock Successfully save bo'lsa, yani o'zgartirilsa
          //  Agar barcha modellar bo'yicha olinganda joriy kundan kichik bolgan ETA lar bo'lmasa 
          //  coverage_date ga bugungi sanani yozamiz

          // old stock $curStock; 
          // new stock $modelStock->quantity;
          if($curStock != $modelStock->quantity){
            // Demak stock o'zgartirilgan
            // Endi barcha detallar bo'yicha ETA larni ichida bugundan kichigi bor yo'qligini aniqlaymiz
            if(count(VehicleCoverageInput::getExpiredData()) == 0){
              $modelStock->coverage_date = date('Y-m-d');
              if (!$modelStock->save()) {
                $err = 1;
                $err_msg .= "<i><u><strong>UPDATE_COVERAGE_DATE:</strong></u></i>".Helpers::arrayToStringRecursive($modelStock->errors);
              }
            }

          }
      }
      
      if ($err == 0) {
        $transaction->commit();
        Yii::$app->session->setFlash('success', Yii::t('app', 'Successfully'." !!!"));

        return $this->redirect(['index']);
      } else {
        $transaction->rollback();
        Yii::$app->session->setFlash('error', $err_msg);
      }
    }

    return $this->render(
      'refresh', array_merge(
        [
          'model' => $model,
          'curStock' => Helpers::numberFormatRemoveZero($curStock, 4, ".", ""),
          'uamStock' => Helpers::numberFormatRemoveZero($uamStock, 4, ".", ""),
          'paidNotShippedOrder' => Helpers::numberFormatRemoveZero($paidNotShippedOrder, 4, ".", ""),
          'intransitETA' => $intransitETA,
        ], self::loadDictionaries()
      )
    );
  }

  /**
   * Deletes an existing VehicleCoverageInput model.
   * If deletion is successful, the browser will be redirected to the 'index' page.
   *
   * @param int $id
   *
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionDelete($id) {
    $this->findModel($id)->delete();

    return $this->redirect(['index']);
  }

  /**
   * Finds the VehicleCoverageInput model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   *
   * @param int $id
   *
   * @return VehicleCoverageInput the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id) {
    if (($model = VehicleCoverageInput::findOne($id)) !== null) {
      return $model;
    }
    throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
  }

}
