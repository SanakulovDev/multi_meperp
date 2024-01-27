<?php
namespace app\controllers;

use app\components\Helpers;
use app\console\controllers\CoverageController;
use app\models\Contract;
use app\models\ContractDetail;
use app\models\ContractSource;
use app\models\Part;
use app\models\PartActiveLog;
use app\models\PartPart;
use app\models\PartSearch;
use app\models\PartType;
use app\models\PartUploadForm;
use app\models\PopForm;
use app\models\ProductGroup;
use app\models\ProductionPlan;
use app\models\ProductLine;
use app\models\ProductModel;
use app\models\Req;
use app\models\ReqDetailPlan;
use app\models\ReqDetailWide;
use app\models\Stock;
use app\models\Unit;
use app\models\Warehouse;
use Exception;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * PartController implements the CRUD actions for Part model.
 */
class PartController extends AppController {

  public $cacheTtl = 3600; // hour
  /**
   * Lists all Part models.
   *
   * @return mixed
   */
  public function actionIndex() {
    $searchModel = new PartSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    $unitsAll = Yii::$app->cache->getOrSet('unitsAll', function () {
      return Unit::find()->all();
    }, $this->cacheTtl);

    $partTypesAll = Yii::$app->cache->getOrSet('partTypesAll', function () {
      return PartType::find()->all();
    }, $this->cacheTtl);

    $contractSourceAll = Yii::$app->cache->getOrSet('contractSourceArray', function () {
      return ContractSource::find()->all();
    }, $this->cacheTtl);

    $warehouses = Yii::$app->cache->getOrSet('warehouseShopOutsourceArray', function () {
      return ArrayHelper::map(Warehouse::find()->where(['warehouse_type' => [Warehouse::TYPE_SHOP, Warehouse::TYPE_OUTSOURCING]])->all(), 'id', 'name');
    }, $this->cacheTtl);

    $units = ArrayHelper::map($unitsAll, 'id', 'unit_value');
    $partTypes = ArrayHelper::map($partTypesAll, 'id', 'typename');
    $contractSource = ArrayHelper::map($contractSourceAll, 'id', 'name');
//    $warehouses = ArrayHelper::map(Warehouse::find()->where(['warehouse_type' => [Warehouse::TYPE_SHOP, Warehouse::TYPE_OUTSOURCING]])->all(), 'id', 'name');
    return $this->render('index', compact('searchModel', 'dataProvider', 'units', 'partTypes', 'contractSource', 'warehouses'));
  }

  /**
   * Creates a new Part model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   *
   * @return mixed
   */
  public function actionCreate() {
    $model = new Part();
    if($model->load(Yii::$app->request->post())) {
      $model->created_by = Yii::$app->user->id;
      $model->created_at = time();
      $err = 0;
      $err_msg='';
      $transaction = Yii::$app->db->beginTransaction();
      if($model->save()) {
        $newPartActiveLog = new PartActiveLog();
        $newPartActiveLog->part_no = $model->part_no;
        $newPartActiveLog->begin_date = date('Y-m-d', time());
        $newPartActiveLog->end_date = '9999-12-31';
        $newPartActiveLog->status = $model->status;
        $newPartActiveLog->updated_by = Yii::$app->user->id;
        $newPartActiveLog->updated_at = time();
        if(!$newPartActiveLog->save()) {
          $err = 1;
          $err_msg .= "<i><u><strong>PartActiveLog:</strong></u></i>".Helpers::arrayToStringRecursive($newPartActiveLog->errors);
        }
      }else{
        $err = 1;
        $err_msg .= "<i><u><strong>NewPart:</strong></u></i>".Helpers::arrayToStringRecursive($model->errors);
      }
      if($err == 0) {
        $transaction->commit();
        return $this->redirect(['index']);
      } else {
        $transaction->rollback();
        Yii::$app->session->setFlash('error', $err_msg);
      }
    }

    return $this->render('create', [
      'model' => $model,
    ]);
  }

  /**
   * Updates an existing Part model.
   * If update is successful, the browser will be redirected to the 'view' page.
   *
   * @param integer $id
   *
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionUpdate($id) {
    $model = $this->findModel($id);
    $oldStatus = $model->status;
    if($model->load(Yii::$app->request->post())) {
      $model->updated_by = Yii::$app->user->id;
      $model->updated_at = time();
      $chilCount = PartPart::getChildCount($id);
      if($model->state == Part::STATE_RAW && $chilCount > 0) {
        Yii::$app->session->setFlash('error',
          Yii::t('app', 'This part cannot be updated. It has {childCount} child part', [
            'childCount' => $chilCount,
          ])
        );
        goto updateForma;
      }
      $err = 0;
      $transaction = Yii::$app->db->beginTransaction();
      if($model->save()) {
        /** PartActiveLog ga ma`lumot yozish */
        if($oldStatus != $model->status) {
          $partActiveLog = PartActiveLog::find()
                                        ->where(['part_no' => $model->part_no])
                                        ->orderBy(['end_date' => SORT_DESC, 'begin_date' => SORT_DESC])
                                        ->limit(1)
                                        ->one();
          if(!empty($partActiveLog)) {
            $partActiveLog->end_date = date('Y-m-d', time());
            $partActiveLog->updated_by = Yii::$app->user->id;
            $partActiveLog->updated_at = time();
            if(!$partActiveLog->save()) {
              echo "<pre>";
              print_r($partActiveLog->errors);
              echo "</pre>";
              $err = 1;
              goto endTransaction;
            }
          }
          $newPartActiveLog = new PartActiveLog();
          $newPartActiveLog->part_no = $model->part_no;
          $newPartActiveLog->begin_date = date('Y-m-d', time());
          $newPartActiveLog->end_date = '9999-12-31';
          $newPartActiveLog->status = $model->status;
          $newPartActiveLog->updated_by = Yii::$app->user->id;
          $newPartActiveLog->updated_at = time();
          if(!$newPartActiveLog->save()) {
            $err = 1;
            goto endTransaction;
          }
          if($model->status == Part::STATUS_INACTIVE) {
            $err = ProductionPlan::deletePlanInactivePart($id, time());
            if($err == 1) goto endTransaction;
          }
        }
      }
      endTransaction:
      if($err == 0) {
        $transaction->commit();
        return $this->redirect(['index']);
      } else {
        $transaction->rollback();
      }
    }
    updateForma:
    return $this->render('update', ['model' => $model,]);
  }

  /**
   * Deletes an existing Part model.
   * If deletion is successful, the browser will be redirected to the 'index' page.
   *
   * @param integer $id
   *
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionDelete($id) {
    $transaction = Yii::$app->db->beginTransaction();
    $err = 0;
    try {
      $model = $this->findModel($id);
      $model->delete();
      $err = ProductionPlan::deletePlanInactivePart($id, time());
      $partActiveLog = PartActiveLog::find()
                                    ->where(['part_no' => $model->part_no])
                                    ->orderBy(['end_date' => SORT_DESC, 'begin_date' => SORT_DESC])
                                    ->limit(1)
                                    ->one();
      if(!empty($partActiveLog)) {
        $partActiveLog->end_date = date('Y-m-d', time());
        $partActiveLog->updated_by = Yii::$app->user->id;
        $partActiveLog->updated_at = time();
        if(!$partActiveLog->save()) {
          $err = 1;
        }
      }
    }
    catch(Exception $ex) {
      if($ex->getCode() === 23000) {
        Yii::$app->session->setFlash('error', Yii::t('app', 'Integrity constraint violation'));
      }
    }
    if($err == 0) {
      $transaction->commit();
    } else {
      $transaction->rollback();
    }
    return $this->redirect(['index']);
  }

  /**
   * Finds the Part model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   *
   * @param integer $id
   *
   * @return Part the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id) {
    if(($model = Part::findOne($id)) !== null) {
      return $model;
    }
    throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
  }

  public function actionXls() {
    ini_set('memory_limit', '-1');
    $searchModel = new PartSearch();
    $xsl_file = $searchModel->search(Yii::$app->request->queryParams, 'excel');
    $xsl_file->send(Helpers::downloadFileName('part'));
  }

  public function actionGetPartname($id, $whid = null, $isJSON = true) {
    if($isJSON)
      Yii::$app->response->format = Response::FORMAT_JSON;
    $part = Part::findOne($id);
    return [
      'partname' => $part->part_name,
      'unit' => $part->unit->unit_value,
      'stock' => (!empty($whid)) ? Helpers::formatRemoveDecimal(Stock::find()->where(['part_id' => $id, 'warehouse_id' => $whid])->one()->qty ?? null) : 'N/A',
    ];
  }

  public function actionGetPartsByFloc($whid, $isJSON = true) {
    if($isJSON) Yii::$app->response->format = Response::FORMAT_JSON;
    return self::getPartsByFloc($whid);
  }

  public function actionGetPartsByMarkAndColor($mark, $color) {
    Yii::$app->response->format = Response::FORMAT_JSON;
    $data = [];
    foreach (Part::find()->where(['id' => $mark])->all() as $part) {
      $data[] = [
        'id' => $part->id,
        'text' => $part->partinfo,
      ];
    }

    return $data;
  }

  public static function getPartsByFloc($whid) {
    $data = [];
    $wh = Warehouse::findOne($whid);
    $contract_ids = ArrayHelper::map(Contract::find()->select('id')->where(['supplier_id' => $wh->supplier_id])->all(), 'id', 'id');
    if(!$contract_ids) return $data;
    $contractDetails = ContractDetail::find()->select('part_id')->where(['contract_id' => $contract_ids])->groupBy(['part_id'])
      //->createCommand()->rawSql;
                                     ->all();
    if(!$contractDetails) return $data;
    foreach($contractDetails as $detail) {
      // Agar BOM da katta detal bo'lmasa olmaymiz
      if(!$detail->part->hasSubParts) continue;
      // Agar FLOC wh_type Outsourcing bo'lmasa olmaymiz
      if(($detail->part->warehouse->warehouse_type ?? null) != Warehouse::TYPE_OUTSOURCING) continue;
      $data[] = [
        'id' => $detail->part_id,
        'info' => $detail->part->partinfo,
        'part_no' => $detail->part->part_no
      ];
    }
    return $data;
  }

  public function actionGetPartsBySupplier($spid, $isJSON = true) {
    if($isJSON) Yii::$app->response->format = Response::FORMAT_JSON;
    return self::getPartsBySupplier($spid);
  }

  public static function getPartsBySupplier($spid) {
    $data = [];
    // 1. Shu Supplier bn tuzilgan shartnomalar topiladi. (aktiv, amal qilish muddati mos keladigan)
    // 2. Topilgan kontraktlar ni detallari ro'yxati qaytariladi
    $contractDetails = ContractDetail::find()
                                     ->joinWith('contract')
                                     ->where([
                                       'and',
                                       ['contract.supplier_id' => $spid],
                                       ['contract.status' => Contract::STATUS_ACTIVE],
                                       ['<=', 'contract.contract_date', date('Y-m-d')],
                                       ['>=', 'contract.expiry_date', date('Y-m-d')]
                                     ])
                                     ->all();
    if(!$contractDetails) return $data;
    foreach($contractDetails as $detail) {
      // Agar BOM da katta detal bo'lsa olmaymiz
      if($detail->part->hasSubParts) continue;
      $data[] = [
        'id' => $detail->part_id,
        'info' => $detail->part->partinfo,
        'part_no' => $detail->part->part_no
      ];
    }
    return $data;
  }

  public function actionGetPartsByModelAndSide($floc = null, $model_id = null, $side = null) {
    Yii::$app->response->format = Response::FORMAT_JSON;
    $parts = Part::find()
                 ->where([
                   'status' => Part::STATUS_ACTIVE,
                   'state' => [Part::STATE_SEMI, Part::STATE_FINISHED]
                 ])
                 ->andFilterWhere([
                   'warehouse_id' => $floc,
                  //  'product_model_id' => $model_id,
                   'side' => $side
                 ])->all();
    $data = [];
    foreach($parts as $part) {
      $data[] = [
        'id' => $part->id,
        'info' => $part->part_no.' '.$part->part_name.' ('.($part->part_color).') '. $part->remark
      ];
    }
    return $data;
  }


  /**
   * Part nomini select2 da ko`rsatish uchun
   * */
  public function actionSearch($q = null, $id = null) {
    Yii::$app->response->format = Response::FORMAT_JSON;
    $out = ['results' => ['id' => '', 'text' => '']];
    if(!is_null($q)) {
      $data = Part::find()
                  ->asArray()
                  ->select(['id', "concat(part_no,' - ',part_name) as text"])
                  ->where(['like', "concat(part_no,' ',part_name)", $q])
                  ->all();
      $out['results'] = array_values($data);
    } elseif($id > 0) {
      $out['results'] = ['id' => $id, 'text' => Part::findOne(['id' => $id])->part_no];
    }
    return $out;
  }

  public function actionPop() {
    $model = new PopForm();
    $post = Yii::$app->request->post();
    if(isset($post['PopForm'])) {
      $model->load($post);
      $data = $this->setPOPData($model->part_id);
    }
    if(isset($post['submitComment'])) {
      $data = $this->setPOPData($post['hidden_part_id']);
      $model->part_id = $post['hidden_part_id'];
      $modelPart = $data['part'];
      $modelPart->comment = $post['comment'];
      $modelPart->commented_by = Yii::$app->user->identity->id;
      $modelPart->commented_at = date('Y-m-d H:i:s');
      if(!$modelPart->save()) {
        echo "<pre>";
        print_r($modelPart->errors);
        echo "</pre>";
        die;
      }
    }
    return $this->render('pop', [
      'model' => $model,
      'data' => $data ?? null
    ]);
  }

  public function actionUpload() {
    $model = new PartUploadForm();
    if($model->load(Yii::$app->request->post())) {
      $uploadedFile = UploadedFile::getInstance($model, 'file');
      $data = $model->readExcel($uploadedFile->tempName);
      $emptyCells = [];
      $modelErrors = [];
      $notExistsUnits = [];
      $notExistsPartTypes = [];
      $notExistsContractSources = [];
      $notExistsFlocs = [];
      $notExistsFGs = [];
      $incorrectData = [];
      $unitNames = Unit::getUnitNames();
      $partTypes = PartType::getPartTypes();
      $contractSources = ContractSource::getContractSources();
      $flocs = Warehouse::getWhNames();
      $fgs = Warehouse::getWhNames();
      $states = ['Базовое сырье', 'Полуфабрикат', 'Готовый продукт'];
      $yesno = [Yii::t('app', 'Yes'), Yii::t('app', 'No')];
      if(empty($data['values'])) {
        $message = 'Empty file.';
        Yii::$app->session->setFlash('error', '<b>'.Yii::t('app', $message).'</b>');
        return $this->render('upload', ['model' => $model]);
      }
      foreach($data['values'] as $key => $row) {
        if(empty($row['part_no']) or empty($row['part_name']) or empty($row['unit']) or empty($row['state'])) {
          $emptyCells[] = Yii::t('app', 'Row').' '.($key + 2).'. '.Yii::t('app', 'Empty data.');
        } else {
          if(!in_array(trim($row['unit']), $unitNames)) {
            $notExistsUnits[] = Yii::t('app', 'Row').' '.($key + 2).'. '.Yii::t('app', 'UOM not found.').'  ('.$row['unit'].')';
          }
          if(!empty($row['part_type'])) {
            if(!in_array(trim($row['part_type']), $partTypes)) {
              $notExistsPartTypes[] = Yii::t('app', 'Row').' '.($key + 2).'. '.Yii::t('app', 'Part type not found.').'  ('.$row['part_type'].')';
            }
          }
          if(!empty($row['floc'])) {
            if(!in_array(trim($row['floc']), $flocs)) {
              $notExistsFlocs[] = Yii::t('app', 'Row').' '.($key + 2).'. '.Yii::t('app', 'Floc not found.').'  ('.$row['floc'].')';
            }
          }
          if(!empty($row['contract_source'])) {
            if(!in_array(trim($row['contract_source']), $contractSources)) {
              $notExistsContractSources[] = Yii::t('app', 'Row').' '.($key + 2).'. '.Yii::t('app', 'Contract source not found.').'  ('.$row['contract_source'].')';
            }
          }
          if(!empty($row['fg_warehouse'])) {
            if(!in_array(trim($row['fg_warehouse']), $fgs)) {
              $notExistsFGs[] = Yii::t('app', 'Row').' '.($key + 2).'. '.Yii::t('app', 'FG Warehouse not found.').'  ('.$row['fg_warehouse'].')';
            }
          }
          if(!in_array(trim($row['state']), $states)) {
            $incorrectData[] = Yii::t('app', 'Row').' '.($key + 2).'. '.Yii::t('app', 'Incorrect data on field STATE.').'  ('.$row['state'].')';
          }
        }
      }
      if(count($emptyCells) > 0) {
        $message = 'Empty cells found in uploaded file.';
        $errors = implode('<br>', $emptyCells);
        Yii::$app->session->setFlash('error', '<b>'.Yii::t('app', $message).'</b>'.'<br>'.$errors);
        return $this->render('upload', ['model' => $model]);
      }
      if(count($notExistsUnits) > 0) {
        $message = 'These UOMs are not found in UOMs list.';
        $errors = implode('<br>', $notExistsUnits);
        Yii::$app->session->setFlash('error', '<b>'.Yii::t('app', $message).'</b>'.'<br>'.$errors);
        return $this->render('upload', ['model' => $model]);
      }
      if(count($notExistsPartTypes) > 0) {
        $message = 'These part types are not found in part types list.';
        $errors = implode('<br>', $notExistsPartTypes);
        Yii::$app->session->setFlash('error', '<b>'.Yii::t('app', $message).'</b>'.'<br>'.$errors);
        return $this->render('upload', ['model' => $model]);
      }
      if(count($notExistsContractSources) > 0) {
        $message = 'These contract sources not found in contract sources list.';
        $errors = implode('<br>', $notExistsContractSources);
        Yii::$app->session->setFlash('error', '<b>'.Yii::t('app', $message).'</b>'.'<br>'.$errors);
        return $this->render('upload', ['model' => $model]);
      }
      if(count($notExistsFlocs) > 0) {
        $message = 'These parts are not found in flocs list.';
        $errors = implode('<br>', $notExistsFlocs);
        Yii::$app->session->setFlash('error', '<b>'.Yii::t('app', $message).'</b>'.'<br>'.$errors);
        return $this->render('upload', ['model' => $model]);
      }
      if(count($notExistsFGs) > 0) {
        $message = 'These FG warehouses are not found in warehouses list.';
        $errors = implode('<br>', $notExistsFGs);
        Yii::$app->session->setFlash('error', '<b>'.Yii::t('app', $message).'</b>'.'<br>'.$errors);
        return $this->render('upload', ['model' => $model]);
      }
      if(count($incorrectData) > 0) {
        $message = 'These data is incorrect.';
        $errors = implode('<br>', $incorrectData);
        Yii::$app->session->setFlash('error', '<b>'.Yii::t('app', $message).'</b>'.'<br>'.$errors);
        return $this->render('upload', ['model' => $model]);
      }
      //die;
      $transaction = Yii::$app->db->beginTransaction();
      foreach($data['values'] as $key => $row) {
        $part_type_id = null;
        $contract_source_id = null;
        $warehouse_id = null;
        $fg_warehouse_id = null;
        $unit_id = Unit::findOneByName($row['unit'])->id;
        if($row['part_type'] != '') {
          $part_type_id = PartType::findOneByName($row['part_type'])->id;
        }
        if($row['contract_source'] != '') {
          $contract_source_id = ContractSource::findOneByName($row['contract_source'])->id;
        }
        if($row['floc'] != '') {
          $warehouse_id = Warehouse::findOneByWhName($row['floc'])->id;
        }
        if($row['fg_warehouse'] != '') {
          $fg_warehouse_id = Warehouse::findOneByWhName($row['fg_warehouse'])->id;
        }
        if($row['state'] == 'Базовое сырье') {
          $state = 0;
        } elseif($row['state'] == 'Полуфабрикат') {
          $state = 1;
        } else {
          $state = 2;
        }
        $part = Part::find()->where(['part_no' => $row['part_no']])->one();
        if($part) {
          $part->state = $state;
          $part->part_color = $row['part_color'];
          $part->part_no = $row['part_no'];
          $part->part_name = $row['part_name'];
          $part->unit_id = $unit_id;
          $part->part_type_id = $part_type_id;
          $part->contract_source_id = $contract_source_id;
          $part->pack_size = $row['pack_size'];
          $part->warehouse_id = $warehouse_id;
          $part->remark = $row['remark'];
          $part->updated_by = Yii::$app->user->id;
          $part->updated_at = time();
          if(!$part->save()) {
            $updateErrors = [];
            foreach($part->errors as $value) {
              foreach($value as $val) {
                $updateErrors[] = $val;
              }
            }
            $modelErrors[] = Yii::t('app', 'Row').' '.($key + 2).'. ('.$row['part_no'].')<br>- '.implode('<br>- ', array_unique($updateErrors));
          }
        } else {
          // new part
          $modelPart = new Part();
          $modelPart->state = $state;
          $modelPart->part_no = $row['part_no'];
          $modelPart->part_color = $row['part_color'];
          $modelPart->part_name = $row['part_name'];
          $modelPart->unit_id = $unit_id;
          $modelPart->part_type_id = $part_type_id;
          $modelPart->contract_source_id = $contract_source_id;
          $modelPart->pack_size = $row['pack_size'];
          $modelPart->warehouse_id = $warehouse_id;
          $modelPart->remark = $row['remark'];
          $modelPart->status = Part::STATUS_ACTIVE;
          $modelPart->created_by = Yii::$app->user->id;
          $modelPart->created_at = time();
          if(!$modelPart->save()) {
            $insertErrors = [];
            foreach($modelPart->errors as $value) {
              foreach($value as $val) {
                $insertErrors[] = $val;
              }
            }
            $modelErrors[] = Yii::t('app', 'Row').' '.($key + 2).'. ('.$row['part_no'].')<br>- '.implode('<br>- ', array_unique($insertErrors));
          }
        }
      }
      if(count($modelErrors) > 0) {
        $transaction->rollBack();
        $message = 'Please check this errors.';
        $errors = implode('<br>', $modelErrors);
        Yii::$app->session->setFlash('error', '<b>'.Yii::t('app', $message).'</b>'.'<br>'.$errors);
        return $this->render('upload', ['model' => $model]);
      } else {
        $transaction->commit();
        $message = 'File successfully uploaded to server.';
        Yii::$app->session->setFlash('success', '<b>'.Yii::t('app', $message).'</b>');
        return $this->redirect(['upload']);
      }
    }
    return $this->render('upload', [
      'model' => $model,
    ]);
  }

  protected function setPOPData($part_id) {
    $data['part'] = Part::findOne($part_id);
    //
    // api
    $query = "
                            select * from
                           (
                                   select * from api order by inventory_date desc limit 1
                           ) a,
                           (
                                   select * from api_detail where part_id = :part_id
                           ) b 
                           where a.id = b.api_id
                         ";
    $api = Yii::$app->db->createCommand($query, [':part_id' => $part_id])->queryOne();
    $data['api'] = $api;
    // intransit
    $query = "
                     select qty, con.container_no, inv.invoice_no, ci.current_locate curr_loc, ci.current_at curr_date, ci.app_arr_at estdate from 
                        (
                        select * from container_invoice 
                        where 
                                app_arr_at is not null and
                                app_arr_at >= CURDATE() and
                                shipped_at is not null and
                                arrived_at is null
                        ) ci,
                        (select * from invoice_detail where part_id = :part_id) ind,
                        (select * from container) con,
                        (select * from invoice) inv
                    where 
                        ci.id = ind.cont_inv_id and
                        ci.container_id = con.id and
                        ci.invoice_id = inv.id
                    order by ci.app_arr_at
                ";
    $intransit = Yii::$app->db->createCommand($query, [':part_id' => $part_id])->queryAll();
    $data['intransit'] = $intransit;
    // coverage (15 days)
    $coverage = [];
    // requirement
    $coverage_req = ReqDetailPlan::find()->joinWith('req')
                                 ->where([
                                   'req.type' => CoverageController::TYPE_DAILY,
                                   'req.part_id' => $part_id
                                 ])->one();
    // coverage stock
    $coverage_stock = ReqDetailWide::find()->joinWith('req')
                                   ->where([
                                     'req.type' => CoverageController::TYPE_DAILY,
                                     'req.part_id' => $part_id
                                   ])->one();
    // stock
    $stock = Req::find()->where([
      'type' => CoverageController::TYPE_STOCK,
      'part_id' => $part_id
    ])->one();
    // intransit
    $query = "
                     select ci.app_arr_at estdate, sum(qty) qty    from 
                        (
                        select * from container_invoice 
                        where 
                                app_arr_at is not null and
                                app_arr_at >= CURDATE() and
                                shipped_at is not null and
                                arrived_at is null
                        ) ci,
                        (select * from invoice_detail where part_id = :part_id) ind
	                    where 
	                        ci.id = ind.cont_inv_id
	                    group by ci.app_arr_at
	                    order by ci.app_arr_at
                ";
    $intrans1 = Yii::$app->db->createCommand($query, [':part_id' => $part_id])->queryAll();
    $intrans2 = [];
    foreach($intrans1 as $row) {
      $intrans2[$row['estdate']] = $row['qty'];
    }
    $pcstogo = 0;
    $period = Helpers::getPeriodDay();
    foreach($period as $col => $per) {
      if($col < 30) {
        unset($tmp);
        $tmp['date'] = $per['plandate'];
        $tmp['req'] = $coverage_req['col'.($col + 1)];
        $tmp['intrans'] = $intrans2[$per['plandate']] ?? 0;
        $tmp['stock'] = $coverage_stock['col'.($col + 1)];
        $coverage[] = $tmp;
      }
      $pcstogo += $coverage_req['col'.($col + 1)];
    }
    $data['coverage'] = $coverage;
    $data['pcstogo'] = $pcstogo;
    $data['doh'] = $coverage_stock->req->days_count ?? null;
    $data['req'] = $stock ?? null;
    return $data;
  }

}
