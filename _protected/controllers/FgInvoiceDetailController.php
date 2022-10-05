<?php
namespace app\controllers;

use app\models\Factory;
use app\models\FgInvoice;
use app\models\FgInvoiceDetail;
use app\models\FgInvoiceDetailSearch;
use app\models\Part;
use app\models\PartPart;
use app\models\ProductionOrder;
use app\models\SalesContract;
use app\models\SalesContractDetail;
use app\models\Stock;
use Yii;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * FgInvoiceDetailController implements the CRUD actions for FgInvoiceDetail model.
 */
class FgInvoiceDetailController extends AppController {

  /**
   * Lists all FgInvoiceDetail models.
   *
   * @return mixed
   */
  public function actionIndex() {
    $searchModel = new FgInvoiceDetailSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
    ]);
  }

  /**
   * Displays a single FgInvoiceDetail model.
   *
   * @param integer $id
   *
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionView($id) {
    return $this->render('view', [
      'model' => $this->findModel($id),
    ]);
  }

  /**
   * Creates a new FgInvoiceDetail model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   *
   * @return mixed
   */
  public function actionCreate($fg_inv_id) {
    $fg_inv_model = FgInvoice::findOne($fg_inv_id);
    $sales_contract_id = SalesContract::findOne(['contract_no' => $fg_inv_model->contract])->id;
    $inv_name_dt = $fg_inv_model->invoice_no."(".$fg_inv_model->invoice_date.")";
    $parts = Part::find()->select(["part.id as id", "CONCAT(part_no, ' - (',part_name,')' ) as part_no"])
                 ->leftJoin('sales_contract_detail', 'part.id = sales_contract_detail.part_id')
                 ->where(['sales_contract_id' => $sales_contract_id])
                 ->orderBy('part_no')
                 ->all();
    $part_items = ArrayHelper::map($parts, 'id', 'part_no');
    $model = new FgInvoiceDetail();
    if($model->load(Yii::$app->request->post())) {
      $model->created_by = Yii::$app->user->id;
      $model->created_at = time();
      $model->fg_invoice_id = $fg_inv_id;
      $model->source = FgInvoiceDetail::SOURCE_WH;
      $transaction = Yii::$app->db->beginTransaction();
      if($model->save()) {
        $fg_warehouse_id = Factory::findOne(['id' => $model->fgInvoice->factory_id])->fg_warehouse_id;
        $parts = Part::findOne(['part_no' => $model->part_no]);
        $part_list[] = [
          'part_id' => $parts->id,
          'qty' => (float)($model->qty)
        ];
        $res = Stock::issueFromShop($fg_warehouse_id, $part_list);
        if($res['success']) {
          $transaction->commit();
          Yii::$app->session->setFlash(
            'success',
            Yii::t('app', 'FG Invoice item add successfully.')
          );
          return $this->redirect(['../fg-invoice/update', 'id' => $fg_inv_id]);
        } else {
          $transaction->rollBack();
          Yii::$app->session->setFlash(
            'warning',
            Yii::t('app', 'Stock-issue error: ').$model->errors
          );
        }
      } else {
        echo "<pre>";
        print_r($model->errors);
        echo "</pre>";
        Yii::$app->session->setFlash(
          'warning',
          Yii::t('app', 'FG Invoice item add error:')
        );
      }
    }
    return $this->render('create', [
      'model' => $model,
      'inv_name_dt' => $inv_name_dt,
      'part_items' => $part_items,
      'fg_inv_id' => $fg_inv_id,
    ]);
  }

  /**
   * Updates an existing FgInvoiceDetail model.
   * If update is successful, the browser will be redirected to the 'view' page.
   *
   * @param integer $id
   *
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionUpdate($id) {
    $model = $this->findModel($id);
    $old_qty = $model->qty;
    if($model->load(Yii::$app->request->post()) && $model->save()) {
      $model->updated_by = Yii::$app->user->id;
      $model->updated_at = time();
      $fg_warehouse_id = Factory::findOne(['id' => $model->fgInvoice->factory_id])->fg_warehouse_id;
      $parts = Part::findOne(['part_no' => $model->part_no]);
      $qty_farq = $old_qty - $model->qty;
      $part_list[] = [
        'part_id' => $parts->id,
        'qty' => abs($qty_farq)
      ];
      $transaction = Yii::$app->db->beginTransaction();
      if(!$model->save()) {
        Yii::$app->session->setFlash(
          'warning',
          Yii::t('app', 'Error! Action not completed.')."<br>".$model->errors
        );
        return $this->redirect(['fg-invoice/update', 'id' => $model->fgInvoice->id]);
      }
      $errText = null;
      if($model->source == FgInvoiceDetail::SOURCE_PRODUCTION) {
        if($qty_farq > 0) {
          $modelPo = new ProductionOrder();
          $modelPo->part_id = $parts->id;
          $modelPo->quantity = $qty_farq;
          $resultCons = Stock::deconsumption($modelPo);
          if($resultCons['success'] != 1) {
            $errText .= "<br>".Yii::t('app', 'Confirmed, Consumption-issue error:').$model->errors;
          }
        } else {
          /*BOMdagi componentlarini ko`paytirib qo`yish */
          $childPartList = [];
          $childPart = PartPart::find()->where(['part_id' => $parts->id])->all();
          foreach($childPart as $childParts) {
            if(!empty($childParts)) {
              $childPartList[] = [
                'part_id' => $childParts['sub_part_id'],
                'qty' => $qty_farq*$childParts['usage_qty']
              ];
              $res = Stock::receipt($childParts['warehouse_id'], $childPartList);
              if($res['success'] != 1) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', Yii::t('app', 'Stock-issue error:').$model->errors);
                return $this->redirect(['fg-invoice/update', 'id' => $model->fgInvoice->id]);
              }
            }
          }
          /*BOMdagi componentlarini ko`paytirib qo`yish */

          $modelPo = new ProductionOrder();
          $modelPo->part_id = $parts->id;
          $modelPo->quantity = abs($qty_farq);
          $modelPo->current_event = ProductionOrder::EVENT_PRODUCED;
          $modelPo->current_seq = $modelPo->getCurrentSeq($modelPo->part_id) + 1;
          $modelPo->created_at = time();
          $modelPo->serial_number = $modelPo->generateSerialNumber();
          $modelPo->is_label = ProductionOrder::LABEL_ACTUAL;
          if($modelPo->save()) {
            $resultCons = Stock::consumption($modelPo);
            if($resultCons['success'] != 1) {
              $errText .= "<br>".Yii::t('app', 'Confirmed, Consumption-issue error:');
            }
          } else {
            $message = 'Production order not created.';
            $errors = '';
            foreach($modelPo->errors as $err) {
              foreach($err as $err_text) {
                $errors .= '<br>'.$err_text;
              }
            }
            $errText .= "<br>".Yii::t('app', $message).'<br>'.$errors;
          }
        }
      } else {
        if($qty_farq > 0) {
          $res = Stock::receipt($fg_warehouse_id, $part_list);
          if($res['success'] != 1) {
            $errText .= "<br>".Yii::t('app', 'Stock-delete error:').$model->errors;
          }
        } else {
          $res = Stock::issueFromShop($fg_warehouse_id, $part_list);
          if($res['success'] != 1) {
            $errText .= "<br>".Yii::t('app', 'Confirmed, Stock-issue error:').$model->errors;
          }
        }
      }
      if(strlen($errText) >= 1) {
        $transaction->rollBack();
        Yii::$app->session->setFlash('error', $errText);
        return $this->render('update', ['model' => $model]);
      } else {
        $transaction->commit();
        Yii::$app->session->setFlash('success', Yii::t('app', 'Action successfully completed.'));
      }
      return $this->redirect(['fg-invoice/update', 'id' => $model->fgInvoice->id]);
    }
    return $this->render('update', ['model' => $model]);
  }

  /**
   * Deletes an existing FgInvoiceDetail model.
   * If deletion is successful, the browser will be redirected to the 'index' page.
   *
   * @param integer $id
   *
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionDelete($id) {
    $model = $this->findModel($id);
    try {
      $model->delete();
      $fg_warehouse_id = Factory::findOne(['id' => $model->fgInvoice->factory_id])->fg_warehouse_id;
      $parts = Part::findOne(['part_no' => $model->part_no]);
      $part_list[] = [
        'part_id' => $parts->id,
        'qty' => (float)($model->qty)
      ];
      $transaction = Yii::$app->db->beginTransaction();
      if($model->source == FgInvoiceDetail::SOURCE_PRODUCTION) {
        $modelPo = new ProductionOrder();
        $modelPo->part_id = $parts->id;
        $modelPo->quantity = (float)($model->qty);
        $resultCons = Stock::deconsumption($modelPo);
        if($resultCons['success'] != 1) {
          $transaction->rollBack();
          Yii::$app->session->setFlash('warning', Yii::t('app', 'Confirmed, Consumption-issue error:').$model->errors);
          goto invoiceUpdate;
        } else {
          /*BOMdagi componentlarini ko`paytirib qo`yish */
          $childPartList = [];
          $childPart = PartPart::find()->where(['part_id' => $parts->id])->all();
          foreach($childPart as $childParts) {
            if(!empty($childParts)) {
              $childPartList[] = [
                'part_id' => $childParts['sub_part_id'],
                'qty' => (float)($model->qty)*$childParts['usage_qty']
              ];
              $res = Stock::receipt($childParts['warehouse_id'], $childPartList);
              if($res['success'] != 1) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', Yii::t('app', 'Stock-issue error:').$model->errors);
                goto invoiceUpdate;
              }
            }
          }
          /*BOMdagi componentlarini ko`paytirib qo`yish */
          $transaction->commit();
          Yii::$app->session->setFlash('success', Yii::t('app', 'Deleted successfully.'));
        }
      } else {
        $res = Stock::receipt($fg_warehouse_id, $part_list);
        if($res['success'] != 1) {
          $transaction->rollBack();
          Yii::$app->session->setFlash('error', Yii::t('app', 'Stock-delete error:').$model->errors);
          goto invoiceUpdate;
        } else {
          $transaction->commit();
          Yii::$app->session->setFlash('success', Yii::t('app', 'Deleted successfully'));
        }
      }
    }
    catch(Exception $e) {
      if($e->errorInfo[1] == 1451) {
        Yii::$app->session->setFlash('error', Yii::t('app', 'error_delete_fr_key'));
      } else {
        throw $e;
      }
    }
    invoiceUpdate:
    return $this->redirect(['fg-invoice/update', 'id' => $model->fgInvoice->id]);
  }

  /**
   * Finds the FgInvoiceDetail model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   *
   * @param integer $id
   *
   * @return FgInvoiceDetail the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id) {
    if(($model = FgInvoiceDetail::findOne($id)) !== null) {
      return $model;
    }
    throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
  }

  public function actionPartData() {
    $post = $_POST;
    $part = Part::findOne(['id' => $post['partid']]);
    $fg_inv = FgInvoice::findOne(['id' => $post['fg_inv_id']]);
    $fg_inv_contract_no = $fg_inv->contract;
    $fg_inv_contract_dt = $fg_inv->contract_date;
    $sales_contract = SalesContract::findOne(['contract_no' => $fg_inv_contract_no, 'contract_date' => $fg_inv_contract_dt]);
    $sales_contract_id = $sales_contract->id;
    $sales_contract_detail = SalesContractDetail::findone(['sales_contract_id' => $sales_contract_id, 'part_id' => $post['partid']]);
    Yii::$app->response->format = Response::FORMAT_JSON;
    $out['part_no'] = $part->part_no;
    $out['part_name'] = $part->part_name;
    $out['price'] = $sales_contract_detail->price;
    $out['unit_id'] = $part->unit->id;
    $out['unit_nm'] = $part->unit->unit_value;
    return $out;
  }

}
