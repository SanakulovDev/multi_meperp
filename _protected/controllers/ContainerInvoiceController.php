<?php
namespace app\controllers;

use app\components\Helpers;
use app\models\Container;
use app\models\ContainerInvoice;
use app\models\ContainerInvoiceSearch;
use app\models\Contract;
use app\models\ContractDetail;
use app\models\DeliveryTerm;
use app\models\Document;
use app\models\DocumentDetail;
use app\models\DocumentType;
use app\models\Invoice;
use app\models\InvoiceDetail;
use app\models\InvoicePartProblem;
use app\models\Part;
use app\models\PartOrder;
use app\models\PartOrderDetail;
use app\models\ShipMode;
use app\models\Stock;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use Yii;
use yii\base\DynamicModel;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\widgets\ActiveForm;

/**
 * ContainerInvoiceController implements the CRUD actions for ContainerInvoice model.
 */
class ContainerInvoiceController extends AppController {

  /**
   * Lists all ContainerInvoice models.
   *
   * @return mixed
   */
  public function actionIndex() {
    $order_invoice_problem_cnt = InvoicePartProblem::find()->count();
    $searchModel = new ContainerInvoiceSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    return $this->render('index',
      [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
        'order_invoice_problem_cnt' => $order_invoice_problem_cnt ?? null,
        'errorlist' => $errorlist ?? null,
      ]
    );
  }

  /**
   * Displays a single ContainerInvoice model.
   *
   * @param int    $container_id
   * @param int    $invoice_id
   * @param string $app_arr_at
   * @param string $shipped_at
   *
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionView($id) {
    return $this->render(
      'view',
      [
        'model' => $this->findModel($id),
        'modelContainer' => $modelContainer ?? null,
        'modelItems' => $modelItems ?? null,
        'errorlist' => $errorlist ?? null,
        'searchModel' => $searchModel ?? null,
        'items' => $items ?? null,
        'modelInvoice' => $modelInvoice ?? null,
      ]
    );
  }

  /**
   * Creates a new Invoice model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   *
   * @return mixed
   */
  public function actionCreate($nomer_order = null) {
    $partOrder = "";
    $contract ="";
    if ($nomer_order) {
      $partOrder = PartOrder::findOne(['order_no' => $nomer_order]);
      $contract = Contract::findOne(['id' => $partOrder->contract_id]);
    }

    $modelInvoice = new Invoice();
    $modelContainer = new Container();
    $model = new ContainerInvoice();
    $errorlist = [];
    if($model->load(Yii::$app->request->post())) {
      if(is_array($_POST['items']['container_no']) and count($_POST['items']['container_no']) < 2) {
        $errorlist = ['no_item' => [[Yii::t('app', 'You must select at least one container.')]]];
        return $this->render(
          'create', [
            'errorlist' => $errorlist ?? null,
            'items' => $_POST['items'] ?? null,
            'modelInvoice' => $modelInvoice ?? null,
            'modelContainer' => $modelContainer ?? null,
            'model' => $model ?? null,
            'modelItems' => $modelItems ?? null,
          ]
        );
      }
      $transaction = Yii::$app->db->beginTransaction();
      //invoice bazani tekshirib yo`q bo`lsa, qo`shib kelish
      $invoice = Invoice::find()->where(['invoice_no' => $_POST['ContainerInvoice']['invoice_no']])->one();
      if($invoice === null) {
        $invoice = new Invoice();
        $invoice->invoice_no = $_POST['ContainerInvoice']['invoice_no'];
        $invoice->supplier_id = (int)$_POST['ContainerInvoice']['supplier'];
        $invoice->currency_id = $_POST['ContainerInvoice']['currency'];
        $invoice->created_by = Yii::$app->user->id;
        $invoice->created_at = time();
        if($invoice->save()) {
          $invoice_id = $invoice->id;
        } else {
          $errorlist['err_invoice'] = $invoice->errors;
          $transaction->rollBack();
          return $this->render(
            'create',
            [
              'errorlist' => $errorlist ?? null,
              'items' => $_POST['items'] ?? null,
              'modelInvoice' => $modelInvoice ?? null,
              'modelContainer' => $modelContainer ?? null,
              'model' => $model ?? null,
              'modelItems' => $modelItems ?? null,
            ]
          );
        }
      } else {
        $invoice_id = $invoice->id;
      }
      if(count($_POST['items']['container_no']) > 1) {
        foreach($_POST['items']['container_no'] as $key => $value) {
          if($key == 0) {
            continue;
          }
          //container bazani tekshirib yo`q bo`lsa, qo`shib kelish
          $container = Container::find()->where(['container_no' => $_POST['items']['container_no'][$key]])->one();
          if($container == null) {
            $container = new Container();
            $container->container_no = $_POST['items']['container_no'][$key];

            $container->container_type = (isset($_POST['items']['container_type'][$key - 1])) ? $_POST['items']['container_type'][$key - 1] : null;

            $container->created_by = Yii::$app->user->id;
            $container->created_at = time();
            if(!$container->save()) {
              $errorlist['err_container'] = $container->errors;
            }
          }
          $container_id = $container->id;
          $item = new ContainerInvoice();
          $item->invoice_id = $invoice_id;
          $delivery_term = $model->deliveryTerm;
          $item->delivery_term_id = $delivery_term->id;
          $item->container_id = $container_id;
          $item->app_arr_at = $_POST['items']['app_arr_at'][$key];
          $item->shipped_at = $_POST['items']['ship_dt'][$key];
          $item->ship_mode_id = $_POST['ContainerInvoice']['ship_mode_id'];
          $item->shipped_by = Yii::$app->user->id;
          if(!$item->save()) {
            $errorlist[$_POST['items']['num'][$key]] = $item->errors;
          }
        }
      }
      if(count($errorlist) == 0) {
        // echo $item->id;
        $transaction->commit();
        return $this->redirect(['/container-invoice/view?id=' . $item->id]);
      } else {
        $transaction->rollBack();
        return $this->render(
          'create',
          [
            'errorlist' => $errorlist ?? null,
            'items' => $_POST['items'] ?? null,
            'modelInvoice' => $modelInvoice ?? null,
            'modelContainer' => $modelContainer ?? null,
            'model' => $model ?? null,
            'modelItems' => $modelItems ?? null,
          ]
        );
      }
    } else {
      return $this->render(
        'create',
        [
          'errorlist' => $errorlist ?? null,
          'items' => $_POST['items'] ?? null,
          'modelInvoice' => $modelInvoice ?? null,
          'modelContainer' => $modelContainer ?? null,
          'model' => $model ?? null,
          'partOrder' => $partOrder,
          'contract' => $contract,
          'modelItems' => $modelItems ?? null,
        ]
      );
    }
  }

  /**
   * Updates an existing ContainerInvoice model.
   * If update is successful, the browser will be redirected to the 'view' page.
   *
   * @param int    $container_id
   * @param int    $invoice_id
   * @param string $app_arr_at
   * @param string $shipped_at
   *
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionUpdate($id) {
    $model = $this->findModel($id);
    $modelContainer = Container::find()->where(['id' => $model->container_id])->one();
    // Yii::$app->session->setFlash('error', Yii::t('app', 'You are not allowed to do this action.'));
    $errorlist = [];
    if(!empty($model->document_id)) {
      Yii::$app->session->setFlash('error', Yii::t('app', 'You are not allowed to do this action.'));
      return $this->redirect(['index']);
    }
    $container = Container::find()->where(['id' => $model->container_id])->one();
    $invoice = Invoice::find()->where(['id' => $model->invoice_id])->one();
    $ship_mode = ShipMode::find()->where(['id' => $model->ship_mode_id])->one();
    $model->container_no = $container->container_no;
    $model->ship_mode_id = $ship_mode->id;
    $model->invoice_no = $invoice->invoice_no;
    $model->supplier = $invoice->supplier_id;
    $model->currency = $invoice->currency_id;
    if($model->load(Yii::$app->request->post())) {
      $transaction = Yii::$app->db->beginTransaction();
      if((isset($_POST['ContainerInvoice']['supplier'])) || (isset($_POST['ContainerInvoice']['currency']))) {
        $invoice->supplier_id = ($_POST['ContainerInvoice']['supplier']) ? $_POST['ContainerInvoice']['supplier'] : $invoice->supplier_id;
        $invoice->currency_id = ($_POST['ContainerInvoice']['currency']) ? $_POST['ContainerInvoice']['currency'] : $invoice->currency_id;
        $invoice->updated_by = Yii::$app->user->id;
        $invoice->updated_at = time();
        if(!$invoice->save()) {
          $errorlist['err_invoice'] = $invoice->errors;
        }
      }
      $container->container_type = (isset($_POST['Container']['container_type'])) ? $_POST['Container']['container_type'] : null;
      $container->updated_by = Yii::$app->user->id;
      $container->updated_at = time();
      if(!$container->save()) {
        $errorlist['errContainer_model'] = $container->errors;
      }
      if(isset($_POST['ContainerInvoice']['shipped_at'])) {
        $model->shipped_by = Yii::$app->user->id;
      }
      if(isset($_POST['ContainerInvoice']['arrived_at'])) {
        $model->arrived_by = Yii::$app->user->id;
      }
      if(isset($_POST['ContainerInvoice']['received_at'])) {
        $model->received_by = Yii::$app->user->id;
      }
      if(!$model->save()) {
        $errorlist['err_model'] = $model->errors;
      }
      if(count($errorlist) == 0) {
        $transaction->commit();
        return $this->redirect(['index']);
      } else {
        $transaction->rollBack();
        return $this->render(
          'update',
          [
            'err' => $errorlist ?? null,
            'model' => $model ?? null,
            'modelContainer' => $modelContainer ?? null,
            'modelItems' => $modelItems ?? null,
            'errorlist' => $errorlist ?? null,
            'items' => $items ?? null,
            'invoice' => $invoice ?? null,
            'modelInvoice' => $modelInvoice ?? null,
          ]
        );
      }
    }
    return $this->render(
      'update',
      [
        'model' => $model ?? null,
        'modelContainer' => $modelContainer ?? null,
        'modelItems' => $modelItems ?? null,
        'errorlist' => $errorlist ?? null,
        'items' => $items ?? null,
        'invoice' => $invoice ?? null,
        'modelInvoice' => $modelInvoice ?? null,
      ]
    );
  }

  public function actionUpdateRegime($id) {
    $model = $this->findModel($id);
    $errorlist = [];
    if($model->load(Yii::$app->request->post())) {
      if($model->save()) {
        return $this->redirect(['index']);
      }
    }
    return $this->render('update-regime', [
      'model' => $model,
    ]);
  }

  /**
   * ship_mode= 'AIR' bo`lganlar uchun container_no change qilish
   */
  public function actionUpdateAwb($id) {
    $model = $this->findModel($id);
    if(Yii::$app->getRequest()->isAjax) {
      if(isset($_POST['cont_no'])) {
        $post_cont_no = $_POST['cont_no'];
        $container = Container::find()->where(['container_no' => $post_cont_no])->one();
        if(isset($container)) {
          $model->container_id = $container->id;
        } else {
          $modelContainer = new Container();
          $modelContainer->container_no = $post_cont_no;
          $modelContainer->created_by = Yii::$app->user->id;
          $modelContainer->created_at = time();
          if($modelContainer->save()) {
            $model->container_id = $modelContainer->id;
            if($model->save()) {
              $data['status'] = 1;
            } else {
              $data['status'] = 0;
              $data['errors'] = $model->getErrors();
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $data;
          } else {
            $data['status'] = 0;
            $data['errors'] = $modelContainer->getErrors();
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $data;
          }
        }
      } else {
        return $this->renderAjax('update-awb', ['model' => $model]);
      }
    } else {
      return $this->redirect(['index']);
    }
  }

  public function actionValidate($id = null) {
    $model = $id === null ? new ContainerInvoice() : ContainerInvoice::findOne($id);
    if(Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      return ActiveForm::validate($model);
    }
  }

  /**
   * addDetails an existing ContainerInvoice model.
   * If addDetail is successful, the browser will be redirected to the 'view' page.
   *
   * @param int    $container_id
   * @param int    $invoice_id
   * @param string $app_arr_at
   * @param string $shipped_at
   *
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionAddDetail($id) {
    $model = $this->findModel($id);
    $errorlist = [];
    if(!empty($model->document_id)) {
      Yii::$app->session->setFlash('error', Yii::t('app', 'You are not allowed to do this action.'));
      return $this->redirect(['index']);
    }
    $container = Container::find()->where(['id' => $model->container_id])->one();
    $invoice = Invoice::find()->where(['id' => $model->invoice_id])->one();
    $ship_mode = ShipMode::find()->where(['id' => $model->ship_mode_id])->one();
    $model->container_no = $container->container_no;
    $model->ship_mode_id = $ship_mode->id;
    $model->invoice_no = $invoice->invoice_no;
    $model->supplier = $invoice->supplier_id;
    if($model->load(Yii::$app->request->post())) {
      $transaction = Yii::$app->db->beginTransaction();
      $container->container_no = $_POST['ContainerInvoice']['container_no'];
      $container->created_by = Yii::$app->user->id;
      $container->created_at = time();
      if(!$container->save()) {
        $errorlist['err_container'] = $container->errors;
      }
      if(count($errorlist) == 0) {
        $transaction->commit();
        Yii::$app->session->setFlash('success', Yii::t('app', 'File successfully uploaded.'));
        return $this->redirect(['index']);
      } else {
        $transaction->rollBack();
        return $this->render(
          'add-detail',
          [
            '$err' => $errorlist ?? null,
            'model' => $model ?? null,
            'modelContainer' => $modelContainer ?? null,
            'modelItems' => $modelItems ?? null,
            'errorlist' => $errorlist ?? null,
            'searchModel' => $searchModel ?? null,
            'items' => $items ?? null,
            'modelInvoice' => $modelInvoice ?? null,
          ]);
      }
    }
    return $this->render(
      'add-detail',
      [
        'model' => $model ?? null,
        'modelContainer' => $modelContainer ?? null,
        'modelItems' => $modelItems ?? null,
        'errorlist' => $errorlist ?? null,
        'searchModel' => $searchModel ?? null,
        'items' => $items ?? null,
        'modelInvoice' => $modelInvoice ?? null,
      ]);
  }

  /**
   * Deletes an existing ContainerInvoice model.
   * If deletion is successful, the browser will be redirected to the 'index' page.
   *
   * @param int    $container_id
   * @param int    $invoice_id
   * @param string $app_arr_at
   * @param string $shipped_at
   *
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionDelete($id) {
    $model = $this->findModel($id);
    if(!empty($model->document_id)) {
      Yii::$app->session->setFlash('error', Yii::t('app', 'You are not allowed to do this action.'));
      return $this->redirect(['index']);
    }
    $model->delete();
    $invoiceExists = ContainerInvoice::find()->where(['invoice_id' => $model->invoice_id])->exists();
    if(!$invoiceExists) {
      $invoice = Invoice::find()->where(['id' => $model->invoice_id])->one();
      $invoice->delete();
    }
    $containerExists = ContainerInvoice::find()->where(['container_id' => $model->container_id])->exists();
    if(!$containerExists) {
      $container = Container::find()->where(['id' => $model->container_id])->one();
      $container->delete();
    }
    return $this->redirect(['index']);
  }

  public function actionCreateDocument($id) {
    $errorlist = [];
    $stockReceiptResult['success'] = true;
    $document_type_id = 1; // Invoice
    $model = $this->findModel($id);
    if(empty($model->regime)) {
      Yii::$app->session->setFlash('error', Yii::t('app', 'You are not allowed to do this action. Because, customs regime is not set.'));
      return $this->redirect(['index']);
    }
    if($model->regime == ContainerInvoice::REGIME_40 and Yii::$app->params['kdWhId'] != Yii::$app->params['logxWhId']) {
      $to_warehouse_id = Yii::$app->params['kdWhId'];
      $status = 0;
    } else {
      $to_warehouse_id = Yii::$app->params['logxWhId'];
      $status = 1;
    }
    $transaction = Yii::$app->db->beginTransaction();
    // 1. Create document
    $modelDocument = new Document();
    $modelDocument->document_type_id = $document_type_id;
    $modelDocument->docnum = DocumentType::generateDocnum($document_type_id);
    $modelDocument->docdate = date("Y-m-d");
    $modelDocument->from_warehouse_id = Yii::$app->params['inTransitWhId'];
    $modelDocument->to_warehouse_id = $to_warehouse_id;
    $modelDocument->supplier_id = $model->invoice->supplier_id;
    $modelDocument->status = $status;
    if($modelDocument->save()) {
      if(is_array($model->invoiceDetails) and count($model->invoiceDetails) > 0) {
        $data = []; // for stock fucntion
        foreach($model->invoiceDetails as $detail) {
          $item = new DocumentDetail();
          $item->document_id = $modelDocument->id;
          $item->part_id = $detail->part_id;
          $item->qty = $detail->qty;
          if(!$item->save()) {
            $errorlist[] = $item->getErrors();
          } else {
            unset($tmpArr);
            $tmpArr['part_id'] = $item->part_id;
            $tmpArr['qty'] = $item->qty;
            $data[] = $tmpArr;
          }
        }
        if($status != 0) {
          // 2. Change stock
          $stockReceiptResult = Stock::receipt($modelDocument->to_warehouse_id, $data);
          // *********************************************
        }
      }
    } else {
      $errorlist[] = $modelDocument->getErrors();
    }
    // *********************************************
    // 3. Update document_id, received_at, received_by fields on table
    $model->document_id = $modelDocument->id;
    $model->received_at = date("Y-m-d");
    $model->received_by = Yii::$app->user->identity->id;
    // *********************************************
    if($model->save() and $stockReceiptResult['success'] and (count($errorlist) == 0)) {
      Yii::$app->session->setFlash('success', Yii::t('app', 'Action successfully completed.'));
      $transaction->commit();
    } else {
      Yii::$app->session->setFlash('error', Yii::t('app', 'Error! Action not completed.'));
      $transaction->rollBack();
    }
    return $this->redirect(['index']);
  }

  public function actionRemoveDocument($id) {
    $errorlist = [];
    $model = $this->findModel($id);
    $modelDocument = Document::findOne($model->document_id);
    if($modelDocument->status == 1 and $modelDocument->to_warehouse_id == Yii::$app->params['kdWhId'] and Yii::$app->params['kdWhId'] != Yii::$app->params['logxWhId']) {
      throw new ForbiddenHttpException(Yii::t('app', 'You are not allowed to remove this record.'));
    }
    $transaction = Yii::$app->db->beginTransaction();
    // 1. Remove document
    $data_r = []; // for stock issue fucntion
    foreach($modelDocument->documentDetails as $detail) {
      unset($tmpArr);
      $tmpArr['part_id'] = $detail->part_id;
      $tmpArr['qty'] = $detail->qty;
      $data_r[] = $tmpArr;
    }
    // shu joyda stock table ga ham change qilish kk
    $stockIssueResult = true;
    if($modelDocument->to_warehouse_id == Yii::$app->params['logxWhId']) {
      // - to_wh dan rasxod qilish kk
      $stockIssue = Stock::issue($modelDocument->to_warehouse_id, $data_r);
      if(!$stockIssue['success'])
        $stockIssueResult = false;
      // *********************************************
    }
    // 3. Update document_id, received_at, received_by fields on table
    $model->document_id = null;
    $model->received_at = null;
    $model->received_by = null;
    // *********************************************
    // Write to history
    $statusHistory = $this->writeToDocHistory($modelDocument->id, 'delete');
    if($model->save() and $modelDocument->delete() and $stockIssueResult and $statusHistory) {
      Yii::$app->session->setFlash('success', Yii::t('app', 'Action successfully completed.'));
      $transaction->commit();
    } else {
      $errorsText = ($stockIssue) ? implode(',<br>', $stockIssue['errorlist']) : '';
      Yii::$app->session->setFlash('error', Yii::t('app', 'Error! Action not completed.').'<br>'.$errorsText);
      $transaction->rollBack();
    }
    return $this->redirect(['index']);
  }

  /**
   * Finds the ContainerInvoice model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   *
   * @param int    $container_id
   * @param int    $invoice_id
   * @param string $app_arr_at
   * @param string $shipped_at
   *
   * @return ContainerInvoice the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id) {
    if(($model = ContainerInvoice::findOne(['id' => $id])) !== null) {
      return $model;
    }
    throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
  }

  /**
   * IMPORT from PHPEXCEL
   * */
  public function actionImportDetail() {
    $errorlist = [];
    $post_val = Yii::$app->request->post();
    $model = $this->findModel($post_val['shu_id']);
    if(!empty($model->document_id)) {
      return $this->redirect(['index']);
    }
    $container = Container::find()->where(['id' => $model->container_id])->one();
    $invoice = Invoice::find()->where(['id' => $model->invoice_id])->one();
    $ship_mode = ShipMode::find()->where(['id' => $model->ship_mode_id])->one();
    $model->container_no = $container->container_no;
    $model->container_type = $container->container_type;
    $model->ship_mode_id = $ship_mode->id;
    $model->invoice_no = $invoice->invoice_no;
    $model->supplier = $invoice->supplier_id;
    //			$model->id = $id;
    $modelImport = new DynamicModel(['fileImport' => 'File Import',]);
    $modelImport->addRule(['fileImport'], 'required');
    $modelImport->addRule(['fileImport'], 'file', ['extensions' => 'xls,xlsx'], ['maxSize' => 1024*1024]);
    $err_text = "";
    $info_text = "";
    $insert_ok_text = '';
    if(Yii::$app->request->post()) {
      $modelImport->fileImport = UploadedFile::getInstance($modelImport, 'fileImport');
      if($modelImport->fileImport) {
        if($modelImport->fileImport && $modelImport->validate()) {
          if($model->received_at > 0) {
            $err_text = Yii::t('app', 'This invoice has been received');
            return $this->render('import-detail',
              [
                'errorlist' => $errorlist ?? null,
                'modelImport' => $modelImport ?? null,
                'model' => $model ?? null,
                'err_text' => $err_text ?? null,
                'info_text' => $info_text,
                'insert_ok_text' => $insert_ok_text ?? null,
              ]
            );
          }
          $inputFileType = PHPExcel_IOFactory::identify($modelImport->fileImport->tempName);
          $objReader = PHPExcel_IOFactory::createReader($inputFileType);
          $objPHPExcel = $objReader->load($modelImport->fileImport->tempName);
          $ActiveSheetTitle = $objPHPExcel->getActiveSheet()->getTitle();
          $HighestRowAndColumn = $objPHPExcel->getActiveSheet()->getHighestRowAndColumn();
          $highestRow = $HighestRowAndColumn['row'];
          $highestColumn = $HighestRowAndColumn['column'];
          $highestColumnIndex = ord($highestColumn) - 64;
          $column_qty = ord($highestColumn) - 64; //column number format
          $err = 0;
          if($column_qty > 7) {
            $err = 1;
            $err_text = Yii::t('app', 'The number of columns is wrong. Columns should be A and G.');
          } else {
            $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
            unset($sheetData[1]); //Birinchi qatorni o`chirish
            $null_part = '';
            $null_qty = '';
            $null_price = '';
            $null_contract = '';
            $null_order = '';
            foreach($sheetData as $key => $value) {
              $null_part = ($value['B'] == null) ? $null_part.", ".($key) : $null_part;
              $null_qty = ($value['C'] == null) ? $null_qty.", ".($key) : $null_qty;
              $null_price = ($value['D'] == null) ? $null_price.", ".($key) : $null_price;
              $null_contract = ($value['E'] == null) ? $null_contract.", ".($key) : $null_contract;
              $null_order = ($value['F'] == null) ? $null_order.", ".($key) : $null_order;
            }
            $null_part = ltrim($null_part, ",");
            $null_qty = ltrim($null_qty, ",");
            if(strlen($null_part) > 0) {
              $err = 1;
              $err_text .= '<br>'.Yii::t('app', 'Empty rows').'('.Yii::t('app', 'Part No').'): '.$null_part;
            }
            if(strlen($null_qty) > 0) {
              $err = 1;
              $err_text .= '<br>'.Yii::t('app', 'Empty rows').'('.Yii::t('app', 'Qty').'): '.$null_qty;
            }
            $null_price = ltrim($null_price, ",");
            $null_contract = ltrim($null_contract, ",");
            $null_order = ltrim($null_order, ",");
            if(strlen($null_price) > 0) {
              $info_text .= '<br>'.Yii::t('app', 'Empty rows').'('.Yii::t('app', 'Price').'): '.$null_price;
            }
            if(strlen($null_contract) > 0) {
              $info_text .= '<br>'.Yii::t('app', 'Empty rows').'('.Yii::t('app', 'Contract no').'): '.$null_contract;
            }
            if(strlen($null_order) > 0) {
              $info_text .= '<br>'.Yii::t('app', 'Empty rows').'('.Yii::t('app', 'Order no').'): '.$null_order;
            }
            $err_text = ltrim($err_text, "<br>");
            $info_text = ltrim($info_text, "<br>");
            if($err == 0) {
              $no_DBparts_rows = '';
              $no_DBparts_ptno = '';
              //Partlar ro`yxati
              $xls_part_no_list = array_column($sheetData, 'B', 'A');
              $db_parts = Part::find()
                              ->where('status=1')
                              ->andWhere(['in', 'part_no', $xls_part_no_list])
                              ->asArray()->all();
              $array_db_part = [];
              foreach($db_parts as $key => $val) {
                $ptno = $val['part_no'];
                $array_db_part[] = $ptno;
              }
              //PartMasterdan farqni tekshirish
              $not_DBparts = array_diff($xls_part_no_list, $array_db_part);
              foreach($not_DBparts as $key => $value) {
                $no_DBparts_rows = $no_DBparts_rows.", ".($key);
              }
              $no_DBparts_rows = ltrim($no_DBparts_rows, ",");
              //Dublikatlarni yo`qotish
              $grouped_array_xls = array_unique($not_DBparts);
              foreach($grouped_array_xls as $key => $value) {
                $no_DBparts_ptno = $no_DBparts_ptno.", ".($value);
              }
              $no_DBparts_ptno = ltrim($no_DBparts_ptno, ", ");
              if(strlen($no_DBparts_ptno) > 0) {
                $err = 1;
                $err_text .= '<br>'.Yii::t('app', 'The following parts are not found in the system:');
                $err_text .= '<br>'.Yii::t('app', 'Rows').': '.$no_DBparts_rows;
                $err_text .= '<br>'.Yii::t('app', 'Part No').': '.$no_DBparts_ptno;
              }
            }
          }
          $insert_ok_text = '';
          if($err > 0) {
            $err_text = ltrim($err_text, "<br>");
            return $this->render(
              'import-detail',
              [
                'errorlist' => $errorlist ?? null,
                'modelImport' => $modelImport ?? null,
                'model' => $model ?? null,
                'err_text' => $err_text ?? null,
                'info_text' => $info_text,
                'insert_ok_text' => $insert_ok_text ?? null,
              ]
            );
          }
          $insert_data = [];
          $err_sts = null;
          $term_id = DeliveryTerm::findOne(['id' => $model->delivery_term_id])->id;
          $transaction = Yii::$app->db->beginTransaction();
          foreach($sheetData as $key => $value) {
            $err_sts_txt = '';
            $part_id = Part::findOne(['part_no' => $value['B']])->id;
            $qty = $value['C'];
            $price = $value['D'];
            $contract_no = $value['E'];
            $order_no = $value['F'];
            if(strlen(trim($price)) == 0) {
              $err_sts = 1;
              $err_sts_txt .= ' Price empty,';
            }
            if(strlen(trim($contract_no)) == 0) {
              $err_sts = 1;
              $err_sts_txt .= ' Сontract empty,';
            }
            if(strlen(trim($order_no)) == 0) {
              $err_sts = 1;
              $err_sts_txt .= ' Order empty,';
            }
            $contract_id = Contract::findOne(['contract_no' => $contract_no])->id ?? null;
            $partOrder = PartOrder::findOne(['order_no' => $order_no]);
            $part_order_id = $partOrder->id ?? null;
            $part_order_error = 0;
            $contract_error = 0;
            if($part_order_id == null) {
              $part_order_error = 1;
              $err_sts = 1;
              $err_sts_txt .= ' (Order not found)';
            } else {
              // Part order nomi bo'lsa ham ichidagi part
              // shu order ichida bo'lmasa muammolar qatoriga qo'shamiz
              $partOrderDetail = PartOrderDetail::find()->where(['part_order_id' => $part_order_id, 'part_id' => $part_id])->one();
              if(!$partOrderDetail) {
                $part_order_error = 1;
                $err_sts = 1;
                $err_sts_txt .= ' (Order found but part('.$value['B'].') not found in order)';
              }
            }
            if($contract_id > 0) {
              $contractDetail = ContractDetail::findOne([
                'contract_id' => $contract_id,
                'part_id' => $part_id,
                'delivery_term_id' => $term_id,
//                'is_primary_price'=>1
              ]);
              $contractPrice = $contractDetail->price ?? null;
              if(!$contractPrice) {
                $err_sts = 1;
                $err_sts_txt .= ' (Contract price is empty)';
                $contract_error = 1;
              }
              if($price != $contractPrice) {
                $err_sts_txt .= ' (this price not equal contract price)';
              }
            } else {
              $err_sts = 1;
              $err_sts_txt .= ' (Contract not found)';
              $contract_error = 1;
            }
            $remarks = $value['G'].$err_sts_txt;
            $invoice_detail_model = new InvoiceDetail();
            $invoice_detail_model->part_order_id = $part_order_id;
            $invoice_detail_model->contract_id = $contract_id;
            $invoice_detail_model->cont_inv_id = $model->id;
            $invoice_detail_model->part_id = $part_id;
            $invoice_detail_model->qty = $qty;
            $invoice_detail_model->price = $price;
            $invoice_detail_model->err_sts = $err_sts;
            $invoice_detail_model->remarks = $remarks;
            $invoice_detail_model->created_at = time();
            $invoice_detail_model->created_by = Yii::$app->user->id;
            if(!$invoice_detail_model->save()) {
              $err = 1;
              $errorlist['invoice_detail_model'] = $invoice_detail_model->errors;
            }
            if(($part_order_error == 1 || $contract_error == 1) and $err == 0) {
              $inv_part_order_problem = new InvoicePartProblem();
              $inv_part_order_problem->inv_detail_id = $invoice_detail_model->id;
              if($contract_id == null) {
                $inv_part_order_problem->contract_no = $value['E'];
              }
              if($part_order_id == null) {
                $inv_part_order_problem->part_order_no = $value['F'];
              } elseif(!$partOrderDetail) {
                $inv_part_order_problem->part_order_no = $value['F'].' ('.$value['B'].')';
              }
              $inv_part_order_problem->created_at = time();
              $inv_part_order_problem->created_by = Yii::$app->user->id;
              if(!$inv_part_order_problem->save()) {
                $err = 1;
                $errorlist['err_inv_part_order_problem'] = $inv_part_order_problem->errors;
              }
            }
          } // foreach
          if($err == 0) {
            $transaction->commit();
            $insert_ok_text = Yii::t('app', 'Upload OK');
          } else {
            $transaction->rollback();
          }
        } else {
          $err_text = Yii::t('app', 'Error validate: '.$modelImport->errors);
        }
      }
    }
    $err_text = ltrim($err_text, "<br>");
    return $this->render(
      'import-detail',
      [
        'errorlist' => $errorlist ?? null,
        'modelImport' => $modelImport ?? null,
        'model' => $model ?? null,
        'err_text' => $err_text ?? null,
        'info_text' => $info_text,
        'insert_ok_text' => $insert_ok_text ?? null,
      ]);
  }

  /**
   * EXPORT TO EXCELx
   * */
  public function actionToXlsx($id) {
    $model = ContainerInvoice::find()
                             ->with(['container', 'invoice.supplier', 'shipMode'])
                             ->where(['id' => $id])->one();
    if(!$model) {
      throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
    $invoice_detail = InvoiceDetail::find()->with('part')->where(['cont_inv_id' => $model->id])->all();
    $invoice_no = $model->invoice ? $model->invoice->invoice_no : '';
    $container_no = $model->container ? $model->container->container_no : '';
    $container_type = $model->container ? $model->container->container_type : '';
    $ship_dt = $model->shipped_at;
    $ship_mode = $model->shipMode ? $model->shipMode->name : '';
    $supplier = $model->invoice ? ($model->supplier ? $model->supplier->name : '') : '';
    ini_set('memory_limit', '-1');
    $HR_center = ['alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER]];
    $objPHPExcel = new PHPExcel();
    $sheet = $objPHPExcel->setActiveSheetIndex(0);
    $sheet->getColumnDimension('A')->setAutoSize(true);
    $sheet->getColumnDimension('B')->setAutoSize(true);
    $sheet->getColumnDimension('C')->setAutoSize(true);
    $sheet->getColumnDimension('E')->setAutoSize(true);
    $sheet->getColumnDimension('F')->setAutoSize(true);
    $sheet->getColumnDimension('G')->setAutoSize(true);
    $sheet->getColumnDimension('G')->setAutoSize(true);
    $sheet->getColumnDimension('H')->setAutoSize(true);
    $sheet->getColumnDimension('I')->setAutoSize(true);
    $sheet->getStyle('A1:I1')->applyFromArray($HR_center);
    $sheet->getStyle('A1:I1')->getFont()->setBold(true);
    $sheet->setCellValue('A1', Yii::t('app', 'Invoice no'));
    $sheet->setCellValue('B1', Yii::t('app', 'Container no'));
    $sheet->setCellValue('C1', Yii::t('app', 'Container no'));
    $sheet->setCellValue('D1', Yii::t('app', 'Shipped at'));
    $sheet->setCellValue('E1', Yii::t('app', 'Supplier')."(".Yii::t('app', 'Ship mode').")");
    $sheet->mergeCells('F1:G1')
          ->setCellValue('F1', Yii::t('app', 'Part name'))
          ->setCellValue('H1', Yii::t('app', 'Quantity'))
          ->setCellValue('I1', Yii::t('app', 'CIP Price'))
          ->setCellValue('J1', Yii::t('app', 'FOB Price'));
    $row = 2;
    foreach($invoice_detail as $detail) {
      $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $invoice_no);
      $objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $container_no);
      $objPHPExcel->getActiveSheet()->setCellValue('C'.$row, $container_type);
      $objPHPExcel->getActiveSheet()->setCellValue('D'.$row, $ship_dt);
      $objPHPExcel->getActiveSheet()->setCellValue('E'.$row, $supplier."(".$ship_mode.")");
      $objPHPExcel->getActiveSheet()->setCellValue('F'.$row, $detail->part ? $detail->part->part_color : '');
      $objPHPExcel->getActiveSheet()->setCellValue('G'.$row, $detail->part ? $detail->part->part_no : '');
      $objPHPExcel->getActiveSheet()->setCellValue('H'.$row, $detail->qty);
      $row++;
    }
    $filename = Helpers::downloadFileName('invoice_detail');
    header('Cache-Control: max-age=0');
    //			header('Content-Type: application/vnd.ms-excel');
    //			header('Content-Disposition: attachment;filename='.$filename.'.xls');
    //			$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5'); //xls
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'.$filename.'"');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007'); //xlsx
    //ob_end_clean();
    $objWriter->save('php://output');
    exit;
  }

  /**
   * EXPORT TO EXCELx with Invoice header
   * */
  public function actionCont_invXlsx() {
    $xls_file = [];
    ini_set('memory_limit', '-1');
    $searchModel = new ContainerInvoiceSearch();
    $xls_file = $searchModel->search(Yii::$app->request->queryParams, 'excel');
    $xls_file->send(Helpers::downloadFileName('invoice_container_detail'));
  }

}
