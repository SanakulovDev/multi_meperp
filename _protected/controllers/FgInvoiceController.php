<?php
namespace app\controllers;

use app\components\Helpers;
use app\models\Customer;
use app\models\Factory;
use app\models\FgInvoice;
use app\models\FgInvoiceDetail;
use app\models\FgInvoiceSearch;
use app\models\Part;
use app\models\PartPart;
use app\models\ProductionOrder;
use app\models\SalesContract;
use app\models\SalesContractDetail;
use app\models\Stock;
use PHPExcel_IOFactory;
use Yii;
use yii\base\DynamicModel;
use yii\db\Exception;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * FgInvoiceController implements the CRUD actions for FgInvoice model.
 */
class FgInvoiceController extends AppController {

  private function loadDictionaries() {
    $customers = ArrayHelper::map(Customer::find()->all(), 'id', 'name');
    $factories = ArrayHelper::map(Factory::find()->all(), 'id', 'name');
    return compact('customers', 'factories');
  }

  public function actionIndex() {
    $searchModel = new FgInvoiceSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    $dataProvider->sort->defaultOrder = [
      'confirmed_by' => SORT_ASC,
      'invoice_no' => SORT_ASC,
      'invoice_date' => SORT_ASC
    ];
    return $this->render('index',
      array_merge(
        [
          'searchModel' => $searchModel ?? null,
          'dataProvider' => $dataProvider
        ], self::loadDictionaries()));
  }

  public function actionXls() {
    ini_set('memory_limit', '-1');
    $searchModel = new FgInvoiceSearch();
    $xsl_file = $searchModel->search(Yii::$app->request->queryParams, 'excel');
    $xsl_file->send(Helpers::downloadFileName('fg-invoice'));
  }

  public function actionConfirm($id) {
    $model = $this->findModel($id);
    $transaction = Yii::$app->db->beginTransaction();
    $model->confirmed_by = Yii::$app->user->id;
    $model->confirmed_at = time();
    if(!$model->save()) {
      $errorlist['Error'] = $model->errors;
      $transaction->rollback();
      Yii::$app->session->setFlash('warning', Yii::t('app', 'Confirmed, Stock-issue error:').$model->errors);
      return $this->redirect(['index']);
    } else {
      $transaction->commit();
      Yii::$app->session->setFlash('success', Yii::t('app', 'Confirmed successfully.'));
    }
    return $this->redirect(['index']);
  }

  protected function findModel($id) {
    if(($model = FgInvoice::findOne($id)) !== null) {
      return $model;
    }
    throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
  }

  public function actionReject($id) {
    $model = $this->findModel($id);
    $transaction = Yii::$app->db->beginTransaction();
    $model->confirmed_by = null;
    $model->confirmed_at = time();
    if(!$model->save()) {
      $errorlist['Error'] = $model->errors;
      $transaction->rollback();
      Yii::$app->session->setFlash('warning', Yii::t('app', 'Rejected error:'));
      return $this->redirect(['index']);
    } else {
      $transaction->commit();
      Yii::$app->session->setFlash('success', Yii::t('app', 'Rejected successfully.'));
    }
    return $this->redirect(['index']);
  }

  public function actionView($id) {
    return $this->render('view', [
      'model' => $this->findModel($id),
    ]);
  }

  public function actionPrint($id) {
    return $this->render('print', [
      'model' => $this->findModel($id),
    ]);
  }

  public function actionCreate() {
    $model = new FgInvoice();
    $errorlist = [];
    if($model->load(Yii::$app->request->post())) {
      if(
        !isset($_POST['FgInvoice']['invoice_no']) ||
        !isset($_POST['FgInvoice']['invoice_date']) ||
        !isset($_POST['FgInvoice']['factory_id']) ||
        !isset($_POST['FgInvoice']['customer_id']) ||
        !isset($_POST['FgInvoice']['contract'])
      ) {
        $errorlist['Header'] = Yii::t('app', 'You must fill required field.');
        return $this->render('create', [
          'errorlist' => $errorlist ?? null,
          'model' => $model,
        ]);
      }

      if(isset($_POST['items'])) {
        $items = $_POST['items'];
        if((count($items['part_no']) < 1) || (count($items['qty']) < 1)) {
          Yii::$app->session->setFlash('danger', Yii::t('app', 'You must select at least one part.'));
          return $this->render('create', [
            'errorlist' => $errorlist ?? null,
            'model' => $model,
          ]);
        }
        $transaction = Yii::$app->db->beginTransaction();
        $model->created_by = Yii::$app->user->id;
        $model->created_at = time();
        $sales_contract = SalesContract::findOne($model->contract);
        $model->contract = $sales_contract->contract_no;
        $model->contract_date = $sales_contract->contract_date;
        // korxona raxbari va korxona buhgalterini factory ga qarab olamiz
        $model->manager = $model->factory->head ?? '';
        $model->account = $model->factory->chief_accountant ?? '';
        if($model->save()) {
          foreach($items['part_no'] as $key => $value) {
            if($items['qty'][$key] < 0.0000000001) {
              Yii::$app->session->setFlash('danger', Yii::t('app', 'Quantity must be greater than zero'));
              $transaction->rollback();
              return $this->render('create', [
                'errorlist' => $errorlist ?? null,
                'model' => $model,
              ]);
            }
            $item = new FgInvoiceDetail();
            $item->fg_invoice_id = $model->id;
            $part = Part::findOne($items['part_no'][$key]);
            $item->part_no = $part->part_no;
            $item->part_name = $part->part_name;
            $item->qty = $items['qty'][$key];
            $item->unit_id = $part->unit->id;
            $item->price = $items['price'][$key];
            $item->created_by = Yii::$app->user->id;
            $item->created_at = time();
            $item->source = FgInvoiceDetail::SOURCE_WH;
            if(!$item->save()) {
              $errorlist['Detail save'] = $item->errors;
              $transaction->rollback();
              return $this->render('create', [
                'errorlist' => $errorlist ?? null,
                'model' => $model,
              ]);
            }
          }
          // ostatkadan ayrish
          $fg_inv_detail = FgInvoiceDetail::find()->where(['fg_invoice_id' => $model->id])->all();
          $fg_warehouse_id = Factory::findOne(['id' => $model->factory_id])->fg_warehouse_id;
          $partList = [];
          foreach($fg_inv_detail as $k => $v) {
            $part = Part::findOne(['part_no' => $v->part_no]);
            $partList[] = [
              'part_id' => $part->id,
              'qty' => (float)($v->qty)
            ];
          }
          $res = Stock::issueFromShop($fg_warehouse_id, $partList);
          if($res['success']) {
            $transaction->commit();
            Yii::$app->session->setFlash('success', Yii::t('app', 'FG Invoice created successfully.'));
          } else {
            $transaction->rollBack();
            Yii::$app->session->setFlash('danger', Yii::t('app', 'Confirmed, Stock-issue error:').$model->errors);
          }
          return $this->redirect(['index', 'model' => $model]);
        } else {
          $transaction->rollback();
          $errorlist['Insert'] = $model->errors;
        }
      } else {
        Yii::$app->session->setFlash('danger', Yii::t('app', 'You must select at least one part.'));
        return $this->render('create', [
          'errorlist' => $errorlist ?? null,
          'model' => $model,
        ]);
      }
    }
    return $this->render('create', [
      'model' => $model, 'errorlist' => $errorlist
    ]);
  }

  public function actionUploadFginvoice() {
    $searchModel = new FgInvoiceSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    $modelImport = new DynamicModel(['fileImport' => 'File Import',]);
    $modelImport->addRule(['fileImport'], 'required');
//    $modelImport->addRule(['fileImport'], 'file', ['extensions' => 'ods,xls,xlsx'], ['maxSize' => 1024*1024]);
    $modelImport->addRule(['fileImport'], 'file', ['maxSize' => 1024*1024]);
    $err_text = "";
    $insert_ok_text = '';
    if(Yii::$app->request->post()) {
      $modelImport->fileImport = UploadedFile::getInstance($modelImport, 'fileImport');
      if($modelImport->fileImport) {
        if($modelImport->fileImport && $modelImport->validate()) {
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
          if($highestRow > 3000) {
            $err_text = Yii::t('app', 'The max rows are wrong. Rows should be lower than 3000.');
            goto uploadForm;
          }
          if($column_qty != 19) {
            $err_text = Yii::t('app', 'The number of columns is wrong. Columns should be A and S.');
            goto uploadForm;
          }
          $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
          unset($sheetData[1]); //Birinchi qatorni o`chirish
          $null_invno = null;
          $null_invdt = null;
          $null_zavod = null;
          $null_client = null;
          $null_contract = null;
          $null_contractdt = null;
          $null_part = null;
          $null_qty = null;
          $null_price = null;
          $no_ptno_contract = '';
          $fg_unique = [];
          $fg_insert_data = [];
          foreach($sheetData as $key => $value) {
            $shinv_no = trim($value['A']);
            $shinv_dt = trim($value['B']);
            $shzavod_id = $value['C'];
            $shclient_id = $value['D'];
            $shcontract_no = trim($value['E']);
            $shcontract_dt = trim($value['F']);
            $shpart_no = trim($value['G']);
            $qty = $value['H'];
            $price = $value['I'];
            $vat = ($value['J']) ?? 0;
            $excise = ($value['K']) ?? 0;
            $rec_person_fullname = trim(($value['L'])) ?? null;
            $rec_person_regno = trim(($value['M'])) ?? null;
            $driver = trim(($value['N'])) ?? null;
            $truck = trim(($value['O'])) ?? null;
            $manager = trim(($value['P'])) ?? null;
            $account = trim(($value['Q'])) ?? null;
            $sender = trim(($value['R'])) ?? null;
            $comment = trim(($value['S'])) ?? null;
            $null_invno = (strlen($shinv_no) < 1) ? $null_invno.", ".($key) : $null_invno;
            $null_invdt = (strlen($shinv_dt) < 1) ? $null_invdt.", ".($key) : $null_invdt;
            $null_contractdt = (strlen($shcontract_dt) < 1) ? $null_contractdt.", ".($key) : $null_contractdt;
            $null_zavod = (strlen($shzavod_id) < 1) ? $null_zavod.", ".($key) : $null_zavod;
            $null_client = (strlen($shclient_id) < 1) ? $null_client.", ".($key) : $null_client;
            $null_contract = (strlen($shcontract_no) < 1) ? $null_contract.", ".($key) : $null_contract;
            $null_part = (strlen($shpart_no) < 1) ? $null_part.", ".($key) : $null_part;
            $null_qty = (strlen($qty) < 1 || $qty < 1) ? $null_qty.", ".($key) : $null_qty;
            $null_price = (strlen($price) < 1) ? $null_price.", ".($key) : $null_price;
            $xls_zavod_list[] = $shzavod_id;
            $xls_client_list[] = $shclient_id;
            $xls_contract_list[] = $shcontract_no;
            $xls_part_list[] = $shpart_no;
            //PARTni contractda mavjudligini tekshirish
            $contract_dt = date("Y-m-d", strtotime($shcontract_dt));
            if(strlen($shpart_no) > 0 && strlen($shcontract_no) > 0 && strlen($shclient_id) > 0) {
              $db_parts = Part::findOne(['status' => 1, 'part_no' => $shpart_no]) ?? null;
              if($db_parts != null) {
                if($db_parts->id > 0) {
                  $db_contract_details = SalesContractDetail::find()
                                                            ->where(['part_id' => $db_parts->id])
                                                            ->andWhere("sales_contract_id=(
                                                            SELECT id FROM sales_contract WHERE contract_no='".$shcontract_no."'
                                                            and contract_date='".$contract_dt."' and customer_id=".$shclient_id.")")
                                                            ->asArray()->all();
                  if(empty($db_contract_details) == true) {
                    $err = 1;
                    $no_ptno_contract .= ", ".($db_parts->part_no);
                  }
                  if($err == 0) {
                    $inv_dt = date("Y-m-d", strtotime($shinv_dt));
                    $fg_key = $shinv_no."¯".$inv_dt."¯".$shzavod_id."¯".$shclient_id;
                    $fg_unique[] = $fg_key;
                    if(!array_key_exists($fg_key, $fg_insert_data)) {
                      $fg_insert_data[$fg_key] = [
                        $shinv_no, $shinv_dt, $shzavod_id, $shclient_id, $shcontract_no, $contract_dt,
                        $vat, $excise, $rec_person_fullname, $rec_person_regno, $driver, $truck,
                        $manager, $account, $sender, $comment
                      ];
                    }
                    $fg_insert_data[$fg_key]['detail'][] = [
                      $db_parts->part_no, $db_parts->part_name, $qty, $price, $db_parts->unit_id, $db_parts->id
                    ];
                  }
                }
              }
            }
          }
          $db_fg_inv = ArrayHelper::getColumn(
            FgInvoice::find()
                     ->groupBy(['factory_id', 'invoice_no', 'invoice_date', 'customer_id'])
                     ->select(['concat(invoice_no,"¯",invoice_date, "¯", factory_id, "¯", customer_id) as fg_inv'])
                     ->asArray()->all(),
            'fg_inv');
          $fg_unique = array_unique($fg_unique);
          if(count($db_fg_inv) != 0 && count($fg_unique) != 0) {
            $fg_has_db = ltrim(implode(", ", array_intersect($fg_unique, $db_fg_inv)), ",");
            if(strlen($fg_has_db) > 0) {
              $err_text .= ' <strong class="hr_top5">'.Yii::t('app', 'The following data already exist in the system:')."</strong>";
              $err_text .= '<br><u>'.
                Yii::t('app', 'Invoice no').'</u>¯<u>'.
                Yii::t('app', 'Invoice Date').'</u>¯<u>'.
                Yii::t('app', 'Factory').'</u>¯<u>'.
                Yii::t('app', 'Customer').'</u>: '.$fg_has_db;
              $err_text = ltrim($err_text, "<br>");
              goto uploadForm;
            }
          }
          //Dublikatlarni yo`qotish
          $xls_zavod_list = array_unique($xls_zavod_list);
          $xls_client_list = array_unique($xls_client_list);
          $xls_contract_list = array_unique($xls_contract_list);
          $xls_part_list = array_unique($xls_part_list);
          $db_all_zavods = ArrayHelper::getColumn(Factory::find()->select('id')->where("status=1")->all(), 'id');
          $db_all_clients = ArrayHelper::getColumn(Customer::find()->select('id')->where("status=1")->all(), 'id');
          $db_all_contracts = ArrayHelper::getColumn(SalesContract::find()->select('contract_no')->where("status=1")->all(), 'contract_no');
          $db_all_parts = ArrayHelper::getColumn(Part::find()->select('part_no')->where("status=1")->all(), 'part_no');
          $not_DBzavods = implode(", ", array_diff($xls_zavod_list, $db_all_zavods));
          $not_DBclients = implode(", ", array_diff($xls_client_list, $db_all_clients));
          $not_DBcontracts = implode(", ", array_diff($xls_contract_list, $db_all_contracts));
          $not_DBparts = implode(", ", array_diff($xls_part_list, $db_all_parts));
          if(strlen($not_DBzavods) > 0 || strlen($not_DBclients) > 0 || strlen($not_DBcontracts) > 0 || strlen($not_DBparts) > 0) {
            $err_text .= ' <strong class="hr_top5">'.Yii::t('app', 'The following data are not found in the system:')."</strong>";
            $err_text .= (strlen($not_DBzavods) > 0) ? '<br><u>'.Yii::t('app', 'Factory').'</u>: '.$not_DBzavods : '';
            $err_text .= (strlen($not_DBclients) > 0) ? '<br><u>'.Yii::t('app', 'Customer').'</u>: '.$not_DBclients : '';
            $err_text .= (strlen($not_DBcontracts) > 0) ? '<br><u>'.Yii::t('app', 'Contract').'</u>: '.$not_DBcontracts : '';
            $err_text .= (strlen($not_DBparts) > 0) ? '<br><u>'.Yii::t('app', 'Part No').'</u>: '.$not_DBparts : '';
            goto uploadForm;
          }
          $null_invno = ltrim($null_invno, ",");
          $null_invdt = ltrim($null_invdt, ",");
          $null_zavod = ltrim($null_zavod, ",");
          $null_client = ltrim($null_client, ",");
          $null_contract = ltrim($null_contract, ",");
          $null_part = ltrim($null_part, ",");
          $null_qty = ltrim($null_qty, ",");
          $null_price = ltrim($null_price, ",");
          if(strlen($null_invno) > 0 || strlen($null_invdt) > 0 || strlen($null_zavod) > 0 || strlen($null_client) > 0 || strlen($null_contract) > 0 || strlen($null_part) > 0 || strlen($null_qty) > 0 || strlen($null_price) > 0) {
            $err_text .= " <strong class='hr_top5'>".Yii::t('app', 'Empty or wrong rows').":</strong>";
            $err_text .= (strlen($null_invno) > 0) ? '<br><u>'.Yii::t('app', 'Invoice No').'</u>:'.$null_invno : '';
            $err_text .= (strlen($null_invdt) > 0) ? '<br><u>'.Yii::t('app', 'Invoice date').'</u>: '.$null_invdt : '';
            $err_text .= (strlen($null_zavod) > 0) ? '<br><u>'.Yii::t('app', 'Factory').'</u>:'.$null_zavod : '';
            $err_text .= (strlen($null_client) > 0) ? '<br><u>'.Yii::t('app', 'Customer').'</u>: '.$null_client : '';
            $err_text .= (strlen($null_contract) > 0) ? '<br><u>'.Yii::t('app', 'Contract').'</u>: '.$null_contract : '';
            $err_text .= (strlen($null_part) > 0) ? '<br><u>'.Yii::t('app', 'Part No').'</u>: '.$null_part : '';
            $err_text .= (strlen($null_qty) > 0) ? '<br><u>'.Yii::t('app', 'Qty').'</u>: '.$null_qty : '';
            $err_text .= (strlen($null_price) > 0) ? '<br><u>'.Yii::t('app', 'Price').'</u>: '.$null_price : '';
            goto uploadForm;
          }
          $no_ptno_contract = ltrim($no_ptno_contract, ", ");
          if(strlen($no_ptno_contract) > 0) {
            $err_text .= " <strong class='hr_top5'>".Yii::t('app', 'The following parts are not found in Contracts:')."</strong>";
            $err_text .= ' <br><u>'.Yii::t('app', 'Part No').'</u>: '.$no_ptno_contract;
            goto uploadForm;
          } //$no_ptno_contract end
          // Insert to db /////////////////////////////////
          if(count($fg_insert_data) > 0) {
            $transaction = Yii::$app->db->beginTransaction();
            $crt_confrmd_time = time();
            foreach($fg_insert_data as $fg_inv) {
              $model = new FgInvoice();
              $model->invoice_no = $fg_inv[0];
              $model->invoice_date = date("Y-m-d", strtotime($fg_inv[1]));
              $model->factory_id = $fg_inv[2];
              $model->customer_id = $fg_inv[3];
              $model->contract = $fg_inv[4];
              $model->contract_date = $fg_inv[5];
              $model->vat = $fg_inv[6];
              $model->excise = $fg_inv[7];
              $model->rec_person_fullname = $fg_inv[8];
              $model->rec_person_regno = $fg_inv[9];
              $model->driver = $fg_inv[10];
              $model->truck = $fg_inv[11];
              $model->manager = $fg_inv[12];
              $model->account = $fg_inv[13];
              $model->sender = $fg_inv[14];
              $model->comment = $fg_inv[15];
              $model->created_by = Yii::$app->user->id;
              $model->created_at = $crt_confrmd_time;
              $model->confirmed_by = Yii::$app->user->id;
              $model->confirmed_at = $crt_confrmd_time;
              if(!$model->save()) {
                foreach($model->errors as $key => $err_val) {
                  $err_text .= ' <strong class="hr_top5">'.Yii::t('app', 'FG Invoices').'</strong>: ';
                  $err_text .= ' <br><u>'.$key.'</u>: '.$err_val[0];
                }
                $transaction->rollback();
                $err_text = ltrim($err_text, "<br>");
                goto uploadForm;
              }
              $insert_detail = [];
              foreach($fg_inv['detail'] as $key => $detail) {
                $part = Part::findOne(['part_no' => $detail['0']]);
                $isSequence = $part->is_sequence;
                if((Yii::$app->params['ga2shop'] == 1) && ($isSequence == 1)) {
                  $source = FgInvoiceDetail::SOURCE_PRODUCTION;
                } else {
                  $source = FgInvoiceDetail::SOURCE_WH;
                }
                $insert_detail[] = [$model->id, $detail['0'], $detail['1'], $detail['2'], $detail['3'], $detail['4'], $source, Yii::$app->user->id, strtotime($fg_inv[1].'23:00:00')];
              }
              try {
                $instQuery = Yii::$app->db->createCommand()
                                          ->batchInsert(
                                            'fg_invoice_detail',
                                            ['fg_invoice_id', 'part_no', 'part_name', 'qty', 'price', 'unit_id', 'source', 'created_by', 'created_at'],
                                            $insert_detail
                                          );
//                echo "<pre>"; print_r($instQuery->rawSql);echo "</pre>";
//                die;
                $instQuery->execute();
              }
              catch(Exception $e) {
                $transaction->rollBack();
                $err_text = Yii::t('app', 'Error Insert: '.$e->getMessage());
                $err_text = ltrim($err_text, "<br>");
                goto uploadForm;
              }
              // ostatkadan ayrish
              $fg_inv_detail = FgInvoiceDetail::find()->where(['fg_invoice_id' => $model->id])->all();
              $fg_warehouse_id = Factory::findOne(['id' => $model->factory_id])->fg_warehouse_id;
              $partListConsumption = [];
              $partListIssueFromShop = [];
              foreach($fg_inv_detail as $k => $v) {
                $part = Part::findOne(['part_no' => $v->part_no]);
                if($v->source == FgInvoiceDetail::SOURCE_PRODUCTION) {
                  $partListConsumption['ProductionOrder'] = [
                    'part_id' => $part->id,
                    'quantity' => (float)($v->qty)
                  ];
                  $modelPo = new ProductionOrder();
                  $modelPo->load($partListConsumption);
                  $modelPo->current_event = ProductionOrder::EVENT_PRODUCED;
                  $modelPo->current_seq = $modelPo->getCurrentSeq($modelPo->part_id) + 1;
                  $modelPo->created_at = strtotime($fg_inv[1].'23:00:00');
                  $modelPo->serial_number = $modelPo->generateSerialNumber();
                  $modelPo->is_label = ProductionOrder::LABEL_ACTUAL;
                  if($modelPo->save()) {
                    $resultCons = Stock::consumption($modelPo, 1);
                    if($resultCons['success'] != 1) {
                      $err = 1;
                      Yii::$app->session->setFlash('warning', Yii::t('app', 'Confirmed, Consumption-issue error:'));
                    }
                  } else {
                    $transaction->rollBack();
                    $message = 'Production order not created.';
                    $errors = '';
                    foreach($modelPo->errors as $err) {
                      foreach($err as $err_text) {
                        $errors .= '<br>'.$err_text;
                      }
                    }
                    Yii::$app->session->setFlash('error', Yii::t('app', $message).'<br>'.$errors);
                    goto uploadForm;
                  }
                } else {
                  $partListIssueFromShop[] = [
                    'part_id' => $part->id,
                    'qty' => (float)($v->qty)
                  ];
                }
              }
              if(count($partListIssueFromShop) > 0) {
                $res = Stock::issueFromShop($fg_warehouse_id, $partListIssueFromShop);
                if($res['success']) {
                  Yii::$app->session->setFlash('success', Yii::t('app', 'FG Invoice uploaded successfully.'));
                } else {
                  $err = 1;
                  Yii::$app->session->setFlash('warning', Yii::t('app', 'Confirmed, Stock-issue error:').$model->errors);
                }
              }
              if($err !== 0) {
                $transaction->rollBack();
                $err_text = ltrim($err_text, "<br>");
                goto uploadForm;
              }
            } //foreach end
            if($err !== 0) {
              $transaction->rollback();
              $err_text = ltrim($err_text, "<br>");
              goto uploadForm;
            } else {
              $transaction->commit();
              Yii::$app->session->setFlash('success', Yii::t('app', 'File uploaded successfully.'));
              return $this->redirect(['index', 'model' => $model]);
            } // if($err !== 0) end
          } // Insert to db /////////////////////////////////
        } // validate
      } // fileImport
    }
    uploadForm:
    return $this->render('upload-fginvoice',
      array_merge(
        [
          'modelImport' => $modelImport ?? null,
          'err_text' => $err_text ?? null,
          'insert_ok_text' => $insert_ok_text ?? null,
          'searchModel' => $searchModel ?? null,
          'dataProvider' => $dataProvider ?? null,
        ], self::loadDictionaries()
      ));
  }

  public function actionUpdate($id) {
    $model = $this->findModel($id);
    $detail_count = (FgInvoiceDetail::find()->where(['fg_invoice_id' => $id])->count()) ? 1 : 0;
    if($model->load(Yii::$app->request->post())) {
      $model->updated_by = Yii::$app->user->id;
      $model->updated_at = time();
      if($model->save()) {
        return $this->redirect(['index']);
      }
    }
    return $this->render('update', ['model' => $model, 'detail_count' => $detail_count,]);
  }

  public function actionDelete($id) {
    $model = $this->findModel($id);
    try {
      // Ostatkaga qo`shib qo`yish
      $fg_inv_detail = FgInvoiceDetail::find()->where(['fg_invoice_id' => $id])->all();
      $fg_warehouse_id = Factory::findOne(['id' => $model->factory_id])->fg_warehouse_id;
      $partList = [];
      $partListReceipt = [];
      $partListDeconsumption = [];
      $poDeleteId = [];
      $partDetailListReceipt = [];
      foreach($fg_inv_detail as $k => $v) {
        $part = Part::findOne(['part_no' => $v->part_no]);
        $partList = [
          'part_id' => $part->id,
          'qty' => (float)($v->qty)
        ];
        if($v->source == FgInvoiceDetail::SOURCE_PRODUCTION) {
          $partListDeconsumption['ProductionOrder'] = $partList;
          $po = ProductionOrder::findOne([
            'current_event' => ProductionOrder::EVENT_PRODUCED,
            'is_label' => ProductionOrder::LABEL_ACTUAL,
            'part_id' => $part->id,
            'quantity' => (float)($v->qty),
            'created_at' => ($v->created_at),
            'created_by' => ($v->created_by)
          ]);
          if(!empty($po)) {
            $poDeleteId[] = $po->id;
          }
          $partListForDelete[] = [
            'part_id' => $part->id,
            'qty' => (float)($v->qty)
          ];
        } else {
          $partListReceipt[] = $partList;
        }
      }
      $transaction = Yii::$app->db->beginTransaction();
      if(!$model->delete()) {
        $transaction->rollBack();
        Yii::$app->session->setFlash('error', Yii::t('app', 'Error! Action not completed.').$model->errors);
        return $this->redirect(['index']);
      }
      if(count($partListDeconsumption) > 0) {
        $modelPo = new ProductionOrder();
        $modelPo->load($partListDeconsumption);
        $resultCons = Stock::deconsumption($modelPo, 1);
        if($resultCons['success'] != 1) {
          $transaction->rollBack();
          Yii::$app->session->setFlash('error', Yii::t('app', 'Confirmed, Consumption-issue error:').$model->errors);
          return $this->redirect(['index']);
        }
        ProductionOrder::deleteAll(['in', 'id', $poDeleteId]);
        /*BOMdagi componentlarini ko`paytirib qo`yish */
        foreach($partListForDelete as $parentPartId) {
          $childPartList = [];
          $childPart = PartPart::find()->where(['part_id' => $parentPartId])->all();
          foreach($childPart as $childParts) {
            if(!empty($childParts)) {
              $childPartList[] = [
                'part_id' => $childParts['sub_part_id'],
                'qty' => (float)($v->qty)*$childParts['usage_qty']
              ];
              $res = Stock::receipt($childParts['warehouse_id'], $childPartList);
              if($res['success'] != 1) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', Yii::t('app', 'Stock-issue error:').$model->errors);
                return $this->redirect(['index']);
              }
            }
          }
        }/*BOMdagi componentlarini ko`paytirib qo`yish */
      }
      if(count($partListReceipt) > 0) {
        $res = Stock::receipt($fg_warehouse_id, $partListReceipt);
        if($res['success'] != 1) {
          $transaction->rollBack();
          Yii::$app->session->setFlash('error', Yii::t('app', 'Stock-issue error:').$model->errors);
          return $this->redirect(['index']);
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
    $transaction->commit();
    Yii::$app->session->setFlash('success', Yii::t('app', 'Deleted successfully.'));
    return $this->redirect(['index']);
  }

  public function actionPartList($q = null, $id = null, $fg_contract = null, $part_ids = null) {
    Yii::$app->response->format = Response::FORMAT_JSON;
    $out = ['results' => ['id' => '', 'text' => '']];
    $query = new Query();
    $query->select(['part.id as id, CONCAT(part_no, " (" , part_name,")") AS text'])->from('part');
    $query->leftJoin('sales_contract_detail', 'part.id = sales_contract_detail.part_id');
    $query->where(['sales_contract_id' => $fg_contract]);
    $query->andWhere('part.id not in('.$part_ids.')');
    if($id > 0) {
      $out['results'] = ['id' => $id, 'text' => Part::findOne(['part_no' => $id])->part_no];
    } elseif(!is_null($q)) {
      $query->andWhere(['like', 'CONCAT(part_no, part_name)', $q]);
    }
    $command = $query->createCommand();
    $data = $command->queryAll();
    $out['results'] = array_values($data);
    return $out;
  }

  public function actionPartData() {
    $post = Yii::$app->request->post();
    $sales_contract_detail = SalesContractDetail::findone(['sales_contract_id' => $post['fg_contractid'], 'part_id' => $post['partid']]);
    Yii::$app->response->format = Response::FORMAT_JSON;
    $unit = Part::findOne($post['partid'])->unit;
    $out['unit_id'] = $unit->id;
    $out['unit'] = $unit->unit_value;
    $out['price'] = $sales_contract_detail->price;
    return $out;
  }

}
