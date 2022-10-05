<?php
namespace app\controllers;

use app\components\Helpers;
use app\enums\FreightInvoicePaymentType;
use app\enums\FreightInvoiceType;
use app\models\Container;
use app\models\ContainerInvoice;
use app\models\FreightInvoice;
use app\models\FreightInvoiceDetail;
use app\models\FreightInvoiceDetailCost;
use app\models\FreightInvoiceDetailInvoice;
use app\models\Invoice;
use Yii;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * FreightInvoiceDetailController implements the CRUD actions for FreightInvoiceDetail model.
 */
class FreightInvoiceDetailController extends Controller {

  private function loadDictionaries() {
    $invoicePaymentType = FreightInvoicePaymentType::list();
    $freightInvoices = ArrayHelper::map(FreightInvoice::find()->select(['id', 'concat((CASE WHEN invoice_no IS NOT NULL THEN invoice_no ELSE "" END)," (",(CASE WHEN invoice_date IS NOT NULL THEN invoice_date ELSE "" END),") ") AS invoice_no'])->all(), 'id', 'invoice_no');
    
    $containers = ArrayHelper::map(
      Container::find()
               ->select(['id, TRIM(TRAILING " - " FROM concat(container_no," - ",container_type)) AS container_no'])
               ->all(),
      'id', 'container_no');
    foreach ($containers as $key => $value) $containers[$key] = str_replace('"', "", str_replace("'", "", $value));
     

    $outInvoices = ArrayHelper::map(FreightInvoice::find()
    ->where(['invoice_type' => FreightInvoiceType::FREIGHT_TYPE_OUTBOUND])
    ->all(), 'id' , 'invoiceInfo') ;
    foreach ($outInvoices as $key => $value) $outInvoices[$key] = str_replace('"', "", str_replace("'", "", $value));
    
    $outContainers = ArrayHelper::map(FreightInvoiceDetail::find()
    ->joinWith('freightInvoice')
    ->where(['freight_invoice.invoice_type' => FreightInvoiceType::FREIGHT_TYPE_OUTBOUND])
    ->all(), 'container_id' , 'containerInfo') ;
    foreach ($outContainers as $key => $value) $outContainers[$key] = str_replace('"', "", str_replace("'", "", $value));

    $invoices = ArrayHelper::map(
      Invoice::find()->select(
        ['id, concat((CASE WHEN invoice_no IS NOT NULL THEN invoice_no ELSE "" END)," (",(CASE WHEN invoice_date IS NOT NULL THEN invoice_date ELSE "" END),") ") AS invoice_no']
      )->all(), 'id', 'invoice_no'
    );
    foreach ($invoices as $key => $value) $invoices[$key] = str_replace('"', "", str_replace("'", "", $value));

    return compact(
      'invoicePaymentType',
      'freightInvoices',
      'containers',
      'outInvoices',
      'outContainers',
      'invoices'
    );
  }

  /**
   * Displays a single FreightInvoiceDetail model.
   *
   * @param integer $id
   *
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionView($id) {
    $model = $this->findModel($id);
    $parentModel = FreightInvoice::findOne(['id' => $model->freight_invoice_id]);
    $freightInvoiceDetailInvoice = FreightInvoiceDetailInvoice::find()
                                                              ->where(['freight_invoice_detail_id' => $id])
                                                              ->all();
    $selectedInvoices = null;
    foreach($freightInvoiceDetailInvoice as $inoice) {
      $selectedInvoices .= $inoice->invoice->invoice_no."-(".$inoice->invoice->invoice_date.")"."<br>";
    }
    $selectedCosts = FreightInvoiceDetailCost::find()->where(['freight_invoice_detail_id' => $id])->all();
    $dictionaries = self::loadDictionaries();
    $costArray = [];
    foreach($dictionaries['invoicePaymentType'] as $key => $value) {
      foreach($selectedCosts as $cost) {
        if($key === $cost['cost_type']) {
          $costArray[] = ['key' => $key, 'title' => $value, 'value' => $cost['value'], 'comment' => $cost['comment']];
        }
      }
    }
    $dictionaries['invoicePaymentType'] = $costArray;
    return $this->render('view',
      array_merge(
        [
          'parentModel' => $parentModel,
          'selectedInvoices' => $selectedInvoices,
          'model' => $this->findModel($id)
        ], $dictionaries
      )
    );
  }

  /**
   * Creates a new FreightInvoiceDetail model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   *
   * @return mixed
   */
  public function actionCreate($id) {

    $errMsg = null;
    $parentModel = FreightInvoice::findOne(['id' => $id]);
    $model = new FreightInvoiceDetail();
    $dictionaries = self::loadDictionaries();
    $costArray = [];

    foreach($dictionaries['invoicePaymentType'] as $key => $value) {
      $costArray[] = ['key' => $key, 'title' => $value, 'inout' => FreightInvoicePaymentType::inOut($key), 'value' => null, 'comment' => null];
    }

    $dictionaries['invoicePaymentType'] = $costArray;

    if($model->load(Yii::$app->request->post())) {

      $postValue = Yii::$app->request->post();
      
      if($parentModel->isInbound){
        $postFreightInvoiceDetail = $postValue['FreightInvoiceDetail'];
         if($postFreightInvoiceDetail['isNeededOutbound'] == 1){
           $model->outbound_id = FreightInvoiceDetail::find()->where([
             'freight_invoice_id' => $postFreightInvoiceDetail['outInvoice'],
             'container_id' => $postFreightInvoiceDetail['container_id'],
             ])->one()->id;
         }else{
           $model->outbound_id = null;
         }
       }

      $childInvoicePOST = ($postValue['childInvoice']) ?? null;
      $childCostPOST = ($postValue['childCost']) ?? null;
      $transaction = Yii::$app->db->beginTransaction();

      try {

        if($model->save()) {

          if(isset($childInvoicePOST)) {
            try {
              $invoiceDetail = [];
              foreach($childInvoicePOST as $key => $detail) {
                $invoiceDetail[] = [$model->id, $detail];
              }
              $insertInvoiceQuery = Yii::$app->db->createCommand()
                                                 ->batchInsert(
                                                   'freight_invoice_detail_invoice',
                                                   ['freight_invoice_detail_id', 'invoice_id'],
                                                   $invoiceDetail
                                                 );
//              echo "<pre>";print_r($insertInvoiceQuery->rawSql);echo "</pre>";die;
              $insertInvoiceQuery->execute();
            }
            catch(Exception $e) {
              throw new HttpException(777, $e->getMessage());
//              $errMsg = "<i><u><strong>".Yii::t('app', 'Insert error(Invoice)').": </strong></u></i>".Helpers::arrayToStringRecursive($e->getMessage());
              goto formCreate;
            }
          }

          if(isset($childCostPOST)) {
            try {
              $costDetail = [];
              foreach($childCostPOST as $key => $detail) {
//              echo "<pre>"; print_r($detail);echo "</pre>";
                if(strlen($detail['costType']) > 0 & strlen($detail['value']) > 0) {
                  $costDetail[] = [$model->id, $detail['costType'], $detail['value'], $detail['comment']];
                }
              }
              $insertCostQuery = Yii::$app->db->createCommand()
                                              ->batchInsert(
                                                'freight_invoice_detail_cost',
                                                ['freight_invoice_detail_id', 'cost_type', 'value', 'comment'],
                                                $costDetail
                                              );
//              echo "<pre>";print_r($insertCostQuery->rawSql);echo "</pre>";die;
              $insertCostQuery->execute();
            }
            catch(Exception $e) {
              throw new HttpException(777, $e->getMessage());
//              $errMsg = "<i><u><strong>".Yii::t('app', 'Insert error(Cost)').": </strong></u></i>".Helpers::arrayToStringRecursive($e->getMessage());
              goto formCreate;
            }
          }

          $transaction->commit();

          return $this->redirect(["freight-invoice/view", "id" => $id]);

        } else {
          $errMsg = "<i><u><strong>FreightInvoice:</strong></u></i>".Helpers::arrayToStringRecursive($model->errors);
        }
      }
      catch(\Exception $e) {
        echo "<pre>";
        print_r($e);
        echo "</pre>";
        die;
      }
    }

    formCreate:
    if($errMsg != null) {
      $transaction->rollBack();
    }

    return $this->render('create',
      array_merge(
        [
          'model' => $model,
          'parentModel' => $parentModel,
          'errMsg' => $errMsg ?? null,
          'selectedInvoices' => $selectedInvoices ?? null,
        ], $dictionaries
      )
    );

  }

  /**
   * Updates an existing FreightInvoiceDetail model.
   * If update is successful, the browser will be redirected to the 'view' page.
   *
   * @param integer $id
   *
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionUpdate($id) {
    $errMsg = null;
    $model = $this->findModel($id);
    $parentModel = FreightInvoice::findOne(['id' => $model->freight_invoice_id]);
    $selectedInvoices = ArrayHelper::map(FreightInvoiceDetailInvoice::find()->where(['freight_invoice_detail_id' => $id])->all(), 'id', 'invoice_id');
    $selectedCosts = FreightInvoiceDetailCost::find()->where(['freight_invoice_detail_id' => $id])->all() ?? null;
    $dictionaries = self::loadDictionaries();
    $costArray = [];
    foreach($dictionaries['invoicePaymentType'] as $key => $value) {
      $found = false;
      foreach($selectedCosts as $cost) {
        if($key === $cost['cost_type']) {
          $costArray[] = ['key' => $key, 'title' => $value, 'inout' => FreightInvoicePaymentType::inOut($key), 'value' => $cost['value'], 'comment' => $cost['comment']];
          $found = true;
        }
      }
      if(!$found) {
        $costArray[] = ['key' => $key, 'title' => $value, 'inout' => FreightInvoicePaymentType::inOut($key), 'value' => null, 'comment' => null];
      }
    }
    $dictionaries['invoicePaymentType'] = $costArray;

    // echo '<pre>';
    // print_r($dictionaries);
    // echo '</pre>';
    // die;

    if(!empty($model->outbound_id)){
      $model->isNeededOutbound = 1;
      $model->outInvoice = $model->outboundInvoiceDetail->freight_invoice_id;
      //$dictionaries['containers'] = $dictionaries['outContainers'];
      $dictionaries['invoices'] = ArrayHelper::map($this->getInvoiceListByContainer($model->container_id), 'id', 'invoice_no');
    }else{
      $model->isNeededOutbound = 0;
    }

    
    
    if($model->load(Yii::$app->request->post())) {
      $postValue = Yii::$app->request->post();
      
      if($parentModel->isInbound){
       $postFreightInvoiceDetail = $postValue['FreightInvoiceDetail'];
        if($postFreightInvoiceDetail['isNeededOutbound'] == 1){
          $model->outbound_id = FreightInvoiceDetail::find()->where([
            'freight_invoice_id' => $postFreightInvoiceDetail['outInvoice'],
            'container_id' => $postFreightInvoiceDetail['container_id'],
            ])->one()->id;
        }else{
          $model->outbound_id = null;
        }
      }       
      

      $childInvoicePOST = ($postValue['childInvoice']) ?? null;
      $childCostPOST = ($postValue['childCost']) ?? null;
      
      $transaction = Yii::$app->db->beginTransaction();
      try {
        if($model->save()) {
          if(isset($childInvoicePOST)) {
            try {
              FreightInvoiceDetailInvoice::deleteAll('freight_invoice_detail_id='.$model->id);
              $invoiceDetail = [];
              foreach($childInvoicePOST as $key => $detail) {
                $invoiceDetail[] = [$model->id, $detail];
              }
              $insertInvoiceQuery = Yii::$app->db->createCommand()
                                                 ->batchInsert(
                                                   'freight_invoice_detail_invoice',
                                                   ['freight_invoice_detail_id', 'invoice_id'],
                                                   $invoiceDetail
                                                 );
//              echo "<pre>";print_r($insertInvoiceQuery->rawSql);echo "</pre>";die;
              $insertInvoiceQuery->execute();
            }
            catch(Exception $e) {
              $errMsg = "<i><u><strong>".Yii::t('app', 'Insert error(Invoice)').": </strong></u></i>".Helpers::arrayToStringRecursive($e->getMessage());
              goto formUpdate;
            }
          }
          if(isset($childCostPOST)) {
            try {
              FreightInvoiceDetailCost::deleteAll('freight_invoice_detail_id='.$model->id);
              $costDetail = [];
              foreach($childCostPOST as $key => $detail) {
//              echo "<pre>"; print_r($detail);echo "</pre>";
                if(strlen($detail['costType']) > 0 & strlen($detail['value']) > 0) {
                  $costDetail[] = [$model->id, $detail['costType'], $detail['value'], $detail['comment']];
                }
              }
              $insertCostQuery = Yii::$app->db->createCommand()
                                              ->batchInsert(
                                                'freight_invoice_detail_cost',
                                                ['freight_invoice_detail_id', 'cost_type', 'value', 'comment'],
                                                $costDetail
                                              );
//              echo "<pre>";print_r($insertCostQuery->rawSql);echo "</pre>";die;
              $insertCostQuery->execute();
            }
            catch(Exception $e) {
              echo '<pre>';
              print_r($e);
              echo '</pre>';
              die;
              $errMsg = "<i><u><strong>".Yii::t('app', 'Insert error(Cost)').": </strong></u></i>".Helpers::arrayToStringRecursive($e->getMessage());
              goto formUpdate;
            }
          }
          $transaction->commit();
          return $this->redirect(["freight-invoice/view", "id" => $parentModel->id]);
        } else {
          $errMsg = "<i><u><strong>FreightInvoice:</strong></u></i>".Helpers::arrayToStringRecursive($model->errors);
        }
      }
      catch(\Exception $e) {
        throw new HttpException(405, 'Error saving model');
        $errMsg .= "<i><u><strong>FreightInvoiceDetailInvoice:</strong></u></i>".Yii::t('app', 'Delete error');
      }
    }
    formUpdate:
    if($errMsg != null) {
      $transaction->rollBack();
    }
    return $this->render('update',
      array_merge(
        [
          'model' => $model,
          'parentModel' => $parentModel,
          'selectedInvoices' => $selectedInvoices ?? null,
          'errMsg' => $errMsg ?? null,
        ], $dictionaries
      )
    );
  }

  /**
   * Deletes an existing FreightInvoiceDetail model.
   * If deletion is successful, the browser will be redirected to the 'freight-invoice/view' page.
   *
   * @param integer $id
   *
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionDelete($id) {
    $model = $this->findModel($id);
    $parentId = $model->freightInvoice->id;
    $model->delete();
    return $this->redirect(["freight-invoice/view", "id" => $parentId]);
  }

  /**
   * Finds the FreightInvoiceDetail model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   *
   * @param integer $id
   *
   * @return FreightInvoiceDetail the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id) {
    if(($model = FreightInvoiceDetail::findOne($id)) !== null) {
      return $model;
    }
    throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
  }

  public function actionInvoiceListByContainer($id) {
    Yii::$app->response->format = Response::FORMAT_JSON;
    $invoiceList = $this->getInvoiceListByContainer($id);$data = [];
    foreach($invoiceList as $item) {
      $data[] = ['id' => $item->id, 'text' => $item->invoice_no];
    }
    return $data;
  }

  protected function getInvoiceListByContainer($id) {
    return ContainerInvoice::find()
                                   ->joinWith('invoice')
                                   ->andFilterWhere(['container_id' => $id])
                                   ->select([
                                     'invoice.id as id',
                                     'concat((CASE WHEN invoice_no IS NOT NULL THEN invoice_no ELSE "" END)," (",(CASE WHEN invoice_date IS NOT NULL THEN invoice_date ELSE "" END),") ") AS invoice_no'
                                   ])
                                   ->where(['container_id' => $id])
                                   ->all();
  }

  public function actionOutContainersListByOutInvoice($id) {
    Yii::$app->response->format = Response::FORMAT_JSON;

    $list = FreightInvoiceDetail::find()
    ->where(['freight_invoice_id' => $id])
    ->all();
    $data = [];
    foreach($list as $item) {
      $data[] = ['id' => $item->container_id, 'text' => str_replace('"', "", str_replace("'", "", $item->containerInfo))];
    }
    return $data;
  }

}
