<?php
namespace app\controllers;

use app\models\ContainerInvoice;
use app\models\Container;
use app\models\Invoice;
use app\models\Contract;
use app\models\ContractDetail;
use app\models\InvoiceDetail;
use app\models\InvoiceDetailSearch;
use app\models\InvoicePartProblem;
use app\models\Part;
use app\models\PartOrder;
use app\models\PartOrderDetail;
use Yii;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * InvoiceDetailController implements the CRUD actions for InvoiceDetail model.
 */
class InvoiceDetailController extends AppController {

  /**
   * Lists all InvoiceDetail models.
   *
   * @return mixed
   */
  public function actionIndex() {
    $model = new InvoiceDetail();
    $searchModel = new InvoiceDetailSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
      'model' => $model,
    ]);
  }

  /**
   * Creates a new InvoiceDetail model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   *
   * @return mixed
   */
  public function actionCreate($id, $status = 1) {
    $parentModel = $this->findParentModel($id);

    if(!empty($parentModel->document_id)) {
      Yii::$app->session->setFlash('error', Yii::t('app', 'You are not allowed to do this action.'));
      return $this->redirect(['container-invoice/index']);
    }
    $model = new InvoiceDetail();
    $model->scenario = 'scenarioCreateOrUpdate';
    $model->cont_inv_id = $id;
    if($model->load(Yii::$app->request->post())) {
      $model->created_at = time();
      $model->created_by = Yii::$app->user->id;
      if($model->save()) {
        return 2;
        return $this->redirect(["container-invoice/view", "id" => $id]);
      } else {
        $error = $model->errors;
        echo "<pre>";
        print_r($error);
        echo "</pre>";
        die;
      }
    }
    $arr = [];
		if ($status) {
			for ($x = 0; $x < $status; $x++) {
				$arr[$x] = $x + 1;
			  }
		}

    $container_invoice = $this->findContainerInvoice($id);

    $invoice = Invoice::findOne($container_invoice->invoice_id);
    $container = Container::findOne($container_invoice->container_id);

    $container_invoice['supplier'] = $invoice->supplier_id;
    $container_invoice['currency'] = $invoice->currency_id;
    $container_invoice['ship_mode_id'] = $container_invoice->ship_mode_id;
    $container_invoice['container_type'] = $container->container_type;
    $container_invoice['delivery_term_id'] = $container_invoice->delivery_term_id;

    return $this->render('create', [
      'invoice_data' => $container_invoice,
      'model' => $model,
      'status' => $arr,
    ]);
  }

  /**
   * Updates an existing InvoiceDetail model.
   * If update is successful, the browser will be redirected to the 'view' page.
   *
   * @param integer $id
   *
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionUpdate($id) {
    $model = $this->findModel($id);
    $model->scenario = 'scenarioCreateOrUpdate';
    if(!empty($model->contInv->document_id)) {
      Yii::$app->session->setFlash('error', Yii::t('app', 'You are not allowed to do this action.'));
      return $this->redirect(['container-invoice/index']);
    }
    if($model->load(Yii::$app->request->post())) {
      $model->updated_at = time();
      $model->updated_by = Yii::$app->user->id;
      if($model->save()) {
        return $this->redirect(["container-invoice/view", "id" => $model->cont_inv_id]);
      } else {
        $error = $model->errors;
        echo "<pre>";print_r($error);echo "</pre>";die;
      }
    }
    return $this->render('update', [
      'model' => $model,
    ]);
  }

  public function actionFixProblem($id) {
    $model = $this->findModel($id);
    $model->scenario = 'scenarioCreateOrUpdate';
    $data = ContainerInvoice::find()
                            ->select(['container_invoice.id as id', 'concat(invoice.invoice_no,"(",container.container_no,")-",shipped_at) as container_no'])
                            ->leftJoin('container', 'container.id=container_invoice.container_id')
                            ->leftJoin('invoice', 'invoice.id=container_invoice.invoice_id')
                            ->where(['container_invoice.id' => $model->cont_inv_id])
                            ->all();
    $containerInvoice = $data[0]['container_no'];
    $partData = Part::findOne($model->part_id);
    $partNo = $partData->part_no."(".$partData->part_color.")";
    $contractDetail = ContractDetail::find()->select('contract_id')->where(['part_id' => $partData->id]);
    $contracts = Contract::find()->select(['id', 'contract_no'])
                         ->where(['status' => Contract::STATUS_ACTIVE])
                         ->andWhere(['in', 'id', $contractDetail])
                         ->orderBy(['contract_no' => SORT_DESC, 'contract_date' => SORT_DESC])
                         ->all();
    $contractList = ArrayHelper::map($contracts, 'id', 'contract_no');
    if($model->load(Yii::$app->request->post())) {
      $model->updated_at = time();
      $model->updated_by = Yii::$app->user->id;
      $transaction = Yii::$app->db->beginTransaction();
      if($model->save()) {
        $partProblem = InvoicePartProblem::findOne(['inv_detail_id' => $model->id]);
        if($partProblem->delete()) {
          $transaction->commit();
          Yii::$app->session->setFlash('success', Yii::t('app', 'Fixed successfully'));
          return $this->redirect(["invoice-part-problem/"]);
        } else {
          $transaction->rollBack();
          Yii::$app->session->setFlash('danger', Yii::t('app', 'Fixed error')."!!!");
        }
      } else {
        $transaction->rollBack();
        $error = $model->errors;
        echo "<pre>";
        print_r($error);
        echo "</pre>";
        die;
      }
    }
    return $this->render('fix-problem', [
      'model' => $model,
      'containerInvoice' => $containerInvoice,
      'partNo' => $partNo,
      'contractList' => $contractList,
    ]);
  }

  /**
   * Deletes an existing InvoiceDetail model.
   * If deletion is successful, the browser will be redirected to the 'index' page.
   *
   * @param integer $id
   *
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionDelete($id) {
    $model = $this->findModel($id);
    if(!empty($model->contInv->document_id)) {
      Yii::$app->session->setFlash('error', Yii::t('app', 'You are not allowed to do this action.'));
      return $this->redirect(['container-invoice/index']);
    }
    try {
      $model->delete();
      Yii::$app->session->setFlash('success', Yii::t('app', 'Deleted successfully'));
    }
    catch(Exception $e) {
      if($e->errorInfo[1] == 1451) {
        Yii::$app->session->setFlash('error', Yii::t('app', 'error_delete_fr_key'));
      } else {
        throw $e;
      }
    }
    return $this->redirect(["container-invoice/view", "id" => $model->cont_inv_id]);
  }

  /**
   * Finds the InvoiceDetail model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   *
   * @param integer $id
   *
   * @return InvoiceDetail the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id) {
    if(($model = InvoiceDetail::findOne($id)) !== null) {
      return $model;
    }
    throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
  }

  protected function findContainerInvoice($id) {
    if(($model = ContainerInvoice::findOne($id)) !== null) {
      return $model;
    }
    throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
  }

  protected function findParentModel($id) {
    if(($model = ContainerInvoice::findOne(['id' => $id])) !== null) {
      return $model;
    }
    throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
  }

  public function actionOrderListByContract($id) {
    Yii::$app->response->format = Response::FORMAT_JSON;
    $list = PartOrder::find()->where(['contract_id' => $id])->all();
    $data = [];
    foreach($list as $item) {
      $data[] = ['id' => $item->id, 'text' => $item->order_no];
    }
    return $data;
  }

  public function actionOrderListByContractAndPart($id, $partId) {
    Yii::$app->response->format = Response::FORMAT_JSON;
    $partOrderIds = PartOrderDetail::find()
                                   ->select('part_order_id')
                                   ->where(['part_id' => $partId]);
    $list = PartOrder::find()
                     ->where(['contract_id' => $id])
                     ->andWhere(['in', 'id', $partOrderIds])->all();
//      echo "<pre>"; print_r($list->createCommand()->rawSql);echo "</pre>";
//      die;
    $data = [];
    foreach($list as $item) {
      $data[] = ['id' => $item->id, 'text' => $item->order_no];
    }
    return $data;
  }

  public function actionPartListByOrder($id) {
    Yii::$app->response->format = Response::FORMAT_JSON;
    $orderPartList = PartOrderDetail::find()->select('part_id')->where(['part_order_id' => $id]);
    $partQuery = Part::find()->select('id,part_no,part_color')->where(['in', 'id', $orderPartList])->andWhere(['status' => Part::STATUS_ACTIVE]);
    $partListlist = $partQuery->all();
    $data = [];
    foreach($partListlist as $item) {
      $data[] = ['id' => $item->id, 'text' => $item->part_no." ".$item->part_color];
    }
    return $data;
  }

}
