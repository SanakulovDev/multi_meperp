<?php
namespace app\controllers;

use app\components\Helpers;
use app\models\BomLog;
use app\models\BomUploadForm;
use app\models\Part;
use app\models\PartPart;
use app\models\PartPartSearch;
use app\models\PartPartVersion;
use app\models\ProductSpecification;
use app\models\Sequence;
use app\models\Warehouse;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\widgets\ActiveForm;

/**
 * PartPartController implements the CRUD actions for PartPart model.
 */
class PartPartController extends AppController {

  /**
   * Lists all PartPart models.
   *
   * @return mixed
   */
  public function actionIndex() {
    $searchModel = new PartPartSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    $dataProvider->sort->defaultOrder = ['part_id' => SORT_ASC, 'sub_part_id' => SORT_ASC];

    return $this->render('index', array_merge(
      [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider
      ], self::loadDictionaries()));
  }

  public function actionValidate($id = null) {
    $model = $id === null ? new PartPart() : PartPart::findOne($id);
    if(Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
      Yii::$app->response->format = Response::FORMAT_JSON;

      return ActiveForm::validate($model);
    }
  }

  /**
   * Creates a new PartPart model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   *
   * @return mixed
   */
//  public function actionCreate() {
//    $model = new PartPart();
//    $data = [];
//    if(Yii::$app->getRequest()->isAjax) {
//      if($model->load(Yii::$app->request->post())) {
//        $model->status = PartPart::STATUS_ACTIVE;
//        $model->created_by = Yii::$app->user->id;
//        $model->created_at = time();
//        $transaction = Yii::$app->db->beginTransaction();
//        if($model->save()) {
//          #region Logging
//          $parts = ArrayHelper::map(Part::find()->where(['in', 'id', [$model->part_id, $model->sub_part_id]])->all(), 'id', 'part_no');
//          $action = $parts[$model->part_id].'-'.$parts[$model->sub_part_id].'-'.$model->usage_qty;
//          self::logToHistory(Yii::$app->user->identity->fullname, $action, 'create', $model->remark);
//          #endregion
//          $lastSequence = Sequence::getLastSequence(Sequence::TYPE_BOMVERSION);
//          $version = $lastSequence['sequence'] + 1;
//          if($lastSequence['sts'] != 'OK') {
//            return ['sts' => 'BAD', 'msg' => $lastSequence['msg']];
//          }
//          $setLastSeqSts = Sequence::setLastSequence(Sequence::TYPE_BOMVERSION, $version);
//          if($setLastSeqSts['sts'] != 'OK') {
//            return ['sts' => 'BAD', 'msg' => "Error(setLastSeq):<br>".$setLastSeqSts['msg']];
//          }
//          $chngBomVerSts = PartPartVersion::changeBomVersion(PartPartVersion::ADDED, $model, $version);
//          if($chngBomVerSts['sts'] == 'OK') {
//            $data['status'] = 1;
//          } else {
//            $data['status'] = 0;
//            $data['errors'] = $chngBomVerSts['msg'];
//          }
//        } else {
//          $data['status'] = 0;
//          $data['errors'] = $model->getErrors();
//        }
//        if($data['status'] == 1) {
//          $transaction->commit();
//        } else {
//          $transaction->rollback();
//        }
//        Yii::$app->response->format = Response::FORMAT_JSON;
//
//        // echo json_encode($data);
//        return $data;
//      } else {
//        return $this->renderAjax('_form', array_merge([
//          'model' => $model,
//        ], self::loadDictionaries()));
//      }
//    } else {
//      return $this->redirect(['index']);
//    }
//  }

  private function logToHistory($fullname, $subject, $action, $comment = null) {
    $log = new BomLog();
    $log->fullname = $fullname;
    $log->subject = $subject;
    $log->action = $action;
    $log->comment = $comment;
    $log->save();
  }

  /**
   * Updates an existing PartPart model.
   * If update is successful, the browser will be redirected to the 'view' page.
   *
   * @param int $id
   *
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
//  public function actionUpdate($id) {
//    $model = $this->findModel($id);
//    $modelDelete = PartPart::findOne(['id' => $id]);
//    $model->scenario = 'update';
//    $oldPart = $model->part_id;
//    $oldSubPart = $model->sub_part_id;
//    $oldQty = $model->usage_qty;
//    if(Yii::$app->getRequest()->isAjax) {
//      if($model->load(Yii::$app->request->post())) {
//        $getLastSequence = Sequence::getLastSequence(Sequence::TYPE_BOMVERSION);
//        $version = $getLastSequence['sequence'] + 1;
//        if($getLastSequence['sts'] != 'OK') {
//          return ['sts' => 'BAD', 'msg' => $getLastSequence['msg']];
//        }
//        $setLastSeqSts = Sequence::setLastSequence(Sequence::TYPE_BOMVERSION, $version);
//        if($setLastSeqSts['sts'] != 'OK') {
//          return ['sts' => 'BAD', 'msg' => "Error(setLastSeq):<br>".$setLastSeqSts['msg']];
//        }
//        $transaction = Yii::$app->db->beginTransaction();
//        $chngBomVerSts = PartPartVersion::changeBomVersion(PartPartVersion::REMOVED, $modelDelete, $version);
//        if($chngBomVerSts['sts'] == 'OK') {
//          if($model->save()) {
//            $data['status'] = 1;
//            #region Logging
//            $action = '';
//            $parts = ArrayHelper::map(Part::find()->where(['in', 'id', [$oldPart, $model->part_id, $oldSubPart, $model->sub_part_id]])->all(), 'id', 'part_no');
//            if($oldPart != $model->part_id) {
//              $action .= $parts[$oldPart].'>'.$parts[$model->part_id];
//            } else {
//              $action .= $parts[$model->part_id];
//            }
//            $action .= '-';
//            if($oldSubPart != $model->sub_part_id) {
//              $action .= $parts[$oldSubPart].'>'.$parts[$model->sub_part_id];
//            } else {
//              $action .= $parts[$model->sub_part_id];
//            }
//            $action .= '-';
//            if($oldQty != $model->usage_qty) {
//              $action .= $oldQty.'>'.$model->usage_qty;
//            } else {
//              $action .= $model->usage_qty;
//            }
//            self::logToHistory(Yii::$app->user->identity->fullname, $action, 'update', $model->remark);
//            #endregion
//            $chngBomVerSts = PartPartVersion::changeBomVersion(PartPartVersion::ADDED, $model, $version);
//            if($chngBomVerSts['sts'] == 'OK') {
//              $data['status'] = 1;
//            } else {
//              $data['status'] = 0;
//              $data['errors'] = $chngBomVerSts['msg'];
//            }
//          } else {
//            $data['status'] = 0;
//            $data['errors'] = $model->getErrors();
//          }
//        } else {
//          $data['status'] = 0;
//          $data['errors'] = $chngBomVerSts['msg'];
//        }
//        if($data['status'] == 1) {
//          $transaction->commit();
//        } else {
//          $transaction->rollback();
//        }
//        Yii::$app->response->format = Response::FORMAT_JSON;
//
//        return $data;
//      } else {
//        return $this->renderAjax('_form', array_merge([
//          'model' => $model,
//        ], self::loadDictionaries()));
//      }
//    } else {
//      return $this->redirect(['index']);
//    }
//  }

  /**
   * Deletes an existing PartPart model.
   * If deletion is successful, the browser will be redirected to the 'index' page.
   *
   * @param int $id
   *
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
//  public function actionDelete($id) {
//    Yii::$app->response->format = Response::FORMAT_JSON;
//    $modelDelete = PartPart::findOne($id);
//    $model = PartPart::find()
//                     ->where(['id' => $id])
//                     ->with([
//                       'part', 'subPart' => function($query) {
//                         $query->from(['p2' => Part::tableName()]);
//                       }
//                     ])->one();
//    $transaction = Yii::$app->db->beginTransaction();
//    if($model) {
//      #region Logging
//      $part = $model->part->part_no;
//      $subPart = $model->subPart->part_no;
//      $remark = $model->remark;
//      $action = $part.'-'.$subPart.'-'.$model->usage_qty;
//      /** Nizomiddin BOM sequence */
//      $lastSequence = Sequence::getLastSequence(Sequence::TYPE_BOMVERSION);
//      $version = $lastSequence['sequence'] + 1;
//      if($lastSequence['sts'] != 'OK') {
//        return ['sts' => 'BAD', 'msg' => $lastSequence['msg']];
//      }
//      $setLastSeqSts = Sequence::setLastSequence(Sequence::TYPE_BOMVERSION, $version);
//      if($setLastSeqSts['sts'] != 'OK') {
//        return ['sts' => 'BAD', 'msg' => "Error(setLastSeq):<br>".$setLastSeqSts['msg']];
//      }
//      $chngBomVerSts = PartPartVersion::changeBomVersion(PartPartVersion::REMOVED, $modelDelete, $version);
//      if($chngBomVerSts['sts'] == 'OK') {
//        if($model->delete()) {
//          self::logToHistory(Yii::$app->user->identity->fullname, $action, 'delete', $remark);
//          $data['status'] = 1;
//        }
//      } else {
//        $data['status'] = 0;
//        $data['errors'] = $chngBomVerSts['msg'];
//      }
//      if($data['status'] == 1) {
//        $transaction->commit();
//      } else {
//        $transaction->rollback();
//      }
//      #endregion
//    }
//
//    return ["status" => 0];
//  }

  /**
   * Finds the PartPart model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   *
   * @param int $id
   *
   * @return PartPart the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id) {
    if(($model = PartPart::findOne($id)) !== null) {
      return $model;
    }
    throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
  }

  public function actionXls() {
    ini_set('memory_limit', '-1');
    $searchModel = new PartPartSearch();
    $xsl_file = $searchModel->search(Yii::$app->request->queryParams, 'excel');
    $xsl_file->send(Helpers::downloadFileName('bom'));
    die;
  }

  public function actionPartRawExcel() {
    ini_set('memory_limit', '-1');
    $searchModel = new PartPartSearch();
    $xsl_file = $searchModel->search(Yii::$app->request->queryParams, 'part-raw-excel');
    $xsl_file->send(Helpers::downloadFileName('part-raw'));
  }

  public function actionDetailedBom($id) {
    Yii::$app->response->format = Response::FORMAT_JSON;
    $data = [];
    $part = Part::findOne($id);
    if(!$part)
      return false;
    $data['selected_part'] = [
      'part_id' => $part->id,
      'part_info' => $part->partinfo,
      'part_name' => $part->part_name,
      'part_color' => $part->part_color,
      'unit' => $part->unit->unit_value,
      'state' => ($part->state == 2) ? 'P' : (($part->state == 1) ? 'S' : 'R'),
      'state_text' => $part->stateText,
    ];
    $data['childs'] = $this->partRecursive($part, []);

    return $data;
  }

  public function actionDownloadDetBom() {
    $partsJSON = Yii::$app->request->post('parts');
    ini_set('memory_limit', '-1');
    $data = json_decode($partsJSON);
    $arrFile = [];
    foreach($data as $row) {
      unset($tmpArray);
      $tmpArray['id'] = $row->id;
      $tmpArray['model'] = $row->model;
      $tmpArray['parent_part_color'] = $row->parent_part_color;
      $tmpArray['parent_part_number'] = $row->parent_part_number;
      $tmpArray['parent_part_name'] = $row->parent_part_name;
      $tmpArray['parent_part_state_text'] = $row->parent_part_state_text;
      $tmpArray['parent_part_status'] = $row->parent_part_status;
      $tmpArray['sub_part_color'] = $row->sub_part_color;
      $tmpArray['sub_part_number'] = $row->sub_part_number;
      $tmpArray['sub_part_name'] = $row->sub_part_name;
      $tmpArray['sub_part_state_text'] = $row->sub_part_state_text;
      $tmpArray['sub_part_status'] = $row->sub_part_status;
      $tmpArray['uloc'] = $row->uloc;
      $tmpArray['usage_qty'] = $row->usage_qty;
      $tmpArray['unit'] = $row->unit;
      $tmpArray['created_by'] = $row->created_by;
      $tmpArray['created_at'] = $row->created_at;
      $tmpArray['updated_by'] = $row->updated_by;
      $tmpArray['updated_at'] = $row->updated_at;
      $tmpArray['status'] = $row->status;
      $tmpArray['remark'] = $row->remark;
      $arrFile[] = $tmpArray;
    }
    $titles = [
      0 => Yii::t('app', 'ID'),
      1 => Yii::t('app', 'Product model'),
      2 => Yii::t('app', 'Part color'),
      3 => Yii::t('app', 'Part No'),
      4 => Yii::t('app', 'Part name'),
      5 => Yii::t('app', 'Part state'),
      6 => Yii::t('app', 'Part status'),
      7 => Yii::t('app', 'Sub Part color'),
      8 => Yii::t('app', 'Sub Part No'),
      9 => Yii::t('app', 'Sub part name'),
      10 => Yii::t('app', 'Sub part state'),
      11 => Yii::t('app', 'Sub part status'),
      12 => Yii::t('app', 'ULOC'),
      13 => Yii::t('app', 'Usage qty'),
      14 => Yii::t('app', 'Unit'),
      15 => Yii::t('app', 'Created by'),
      16 => Yii::t('app', 'Created at'),
      17 => Yii::t('app', 'Updated by'),
      18 => Yii::t('app', 'Updated at'),
      19 => Yii::t('app', 'Status'),
      20 => Yii::t('app', 'Remark'),
    ];
    $file = Yii::createObject([
      'class' => 'codemix\excelexport\ExcelFile',
      'sheets' => [
        'coverage' => [
          'data' => $arrFile,
          'titles' => $titles,
        ]
      ]
    ]);
    $file->send('detailed_bom_'.date("YmdHis").'.xlsx');
    die;
  }

  public function actionUpload() {
    $model = new BomUploadForm();
    if($model->load(Yii::$app->request->post())) {
      $uploadedFile = UploadedFile::getInstance($model, 'file');
      $data = $model->readExcel($uploadedFile->tempName);
      $emptyCells = [];
      $notExistsParts = [];
      $notExistsSubParts = [];
      $notExistsWhs = [];
      $modelErrors = [];
      $allPartNumbers = Part::getAllPartNumbers();
      $whNames = Warehouse::getWhNames();
      if(empty($data['values'])) {
        $message = 'Empty file.';
        Yii::$app->session->setFlash('error', '<b>'.Yii::t('app', $message).'</b>');

        return $this->render('upload', ['model' => $model]);
      }
      foreach($data['values'] as $key => $row) {
        if(
          empty($row['part_no'])
          or
          empty($row['sub_part_no'])
          or
          (!empty($row['usage_qty']) and empty($row['uloc']))
          or
          (empty($row['usage_qty']) and !empty($row['uloc']))
        ) {
          $emptyCells[] = Yii::t('app', 'Row').' '.($key + 2).'. '.Yii::t('app', 'Empty data.');
        } else {
          if(!in_array(trim($row['part_no']), $allPartNumbers)) {
            $notExistsParts[] = Yii::t('app', 'Row').' '.($key + 2).'. '.Yii::t('app', 'Part not found.').'  ('.$row['part_no'].')';
          }
          if(!in_array(trim($row['sub_part_no']), $allPartNumbers)) {
            $notExistsSubParts[] = Yii::t('app', 'Row').' '.($key + 2).'. '.Yii::t('app', 'Sub part not found.').'  ('.$row['sub_part_no'].')';
          }
          if(!empty($row['uloc'])) {
            if(!in_array(trim($row['uloc']), $whNames)) {
              $notExistsWhs[] = Yii::t('app', 'Row').' '.($key + 2).'. '.Yii::t('app', 'Uloc not found.').'  ('.$row['uloc'].')';
            }
          }
        }
      }
      if(count($emptyCells) > 0) {
        $message = 'Empty cells found in uploaded file.';
        $errors = implode('<br>', $emptyCells);
        Yii::$app->session->setFlash('error', '<b>'.Yii::t('app', $message).'</b>'.'<br>'.$errors);

        return $this->render('upload', ['model' => $model]);
      }
      if(count($notExistsParts) > 0) {
        $message = 'These parts are not found in parts list.';
        $errors = implode('<br>', $notExistsParts);
        Yii::$app->session->setFlash('error', '<b>'.Yii::t('app', $message).'</b>'.'<br>'.$errors);

        return $this->render('upload', ['model' => $model]);
      }
      if(count($notExistsSubParts) > 0) {
        $message = 'These sub parts are not found in parts list.';
        $errors = implode('<br>', $notExistsSubParts);
        Yii::$app->session->setFlash('error', '<b>'.Yii::t('app', $message).'</b>'.'<br>'.$errors);

        return $this->render('upload', ['model' => $model]);
      }
      if(count($notExistsWhs) > 0) {
        $message = 'These ulocs are not found in active warehouses list.';
        $errors = implode('<br>', $notExistsWhs);
        Yii::$app->session->setFlash('error', '<b>'.Yii::t('app', $message).'</b>'.'<br>'.$errors);

        return $this->render('upload', ['model' => $model]);
      }
      $transaction = Yii::$app->db->beginTransaction();
      $lastSequence = Sequence::getLastSequence(Sequence::TYPE_BOMVERSION);
      $version = $lastSequence['sequence'] + 1;
      if($lastSequence['sts'] != 'OK') {
        $transaction->rollBack();
        Yii::$app->session->setFlash('error', $lastSequence['msg']);
        return $this->render('upload', ['model' => $model]);
      }
      $setLastSeqSts = Sequence::setLastSequence(Sequence::TYPE_BOMVERSION, $version);
      if($setLastSeqSts['sts'] != 'OK') {
        $transaction->rollBack();
        Yii::$app->session->setFlash('error', "Error(setLastSeq):<br>".$setLastSeqSts['msg']);
        return $this->render('upload', ['model' => $model]);
      }
      $newPsParts = [];
      foreach($data['values'] as $key => $row) {
        $part_id = Part::findOneByPartNumber($row['part_no'])->id;
        $sub_part_id = Part::findOneByPartNumber($row['sub_part_no'])->id;
        if($row['usage_qty'] != '') {
          $warehouse_id = Warehouse::findOneByWhName($row['uloc'])->id;
        }

        $newPsParts[] = $part_id;

        $bom = PartPart::find()->where([
          'part_id' => $part_id,
          'sub_part_id' => $sub_part_id
        ])->one();
        if($bom) {
          if($row['usage_qty'] != '') {
            $bom->usage_qty = $row['usage_qty'];
            $bom->warehouse_id = $warehouse_id;
            $bom->remark = $row['remark'];
            if(!$bom->save()) {
              $updateErrors = [];
              foreach($bom->errors as $value) {
                foreach($value as $val) {
                  $updateErrors[] = $val;
                }
              }
              $modelErrors[] = Yii::t('app', 'Row').' '.($key + 2).'. ('.$row['part_no'].' - '.$row['sub_part_no'].')<br>- '.implode('<br>- ', array_unique($updateErrors));
            }
          } else {
            $bom->delete();
          }
        } else {
          if($row['usage_qty'] != '') {
            $modelBom = new PartPart();
            $modelBom->part_id = $part_id;
            $modelBom->sub_part_id = $sub_part_id;
            $modelBom->warehouse_id = $warehouse_id;
            $modelBom->usage_qty = $row['usage_qty'];
            $modelBom->remark = $row['remark'];
            $modelBom->status = PartPart::STATUS_ACTIVE;
            $modelBom->created_by = Yii::$app->user->id;
            $modelBom->created_at = time();
            if(!$modelBom->save()) {
              $insertErrors = [];
              foreach($modelBom->errors as $value) {
                foreach($value as $val) {
                  $insertErrors[] = $val;
                }
              }
              $modelErrors[] = Yii::t('app', 'Row').' '.($key + 2).'. ('.$row['part_no'].' - '.$row['sub_part_no'].')<br>- '.implode('<br>- ', array_unique($insertErrors));
            }
          }
        }
      }
      // create PS for newly inserted bom
      $newPsParts = array_unique($newPsParts);
      if(count($newPsParts) > 0) {
        // copy from BOM to PS
        $copyQuery = ProductSpecification::queryCreatefromBom($newPsParts);
        Yii::$app->db->createCommand($copyQuery)->execute();
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

  protected $childs = [];

  protected function partRecursive($part, $childs) {
    foreach($part->subParts as $bom) {
      $child = [
        'id' => $bom->id,
        'parent_part_number' => $bom->part->part_no,
        'parent_part_name' => $bom->part->part_name,
        'parent_part_color' => $bom->part_color,
        'parent_part_state_text' => $bom->part->stateText,
        'parent_part_status' => $bom->part->statusText,
        'sub_part_number' => $bom->subPart->part_no,
        'sub_part_name' => $bom->subPart->part_name,
        'sub_part_part_color' => $bom->subPart->part_color,
        'sub_part_state_text' => $bom->subPart->stateText,
        'sub_part_status' => $bom->subPart->statusText,
        'uloc' => $bom->warehouse->name,
        'usage_qty' => Helpers::numberFormatRemoveZero($bom->usage_qty, 10),
        'unit' => $bom->subPart->unit->unit_value,
        'created_by' => $bom->createdBy->username ?? null,
        'created_at' => $bom->createdAtFormatted,
        'updated_by' => $bom->updatedBy->username ?? null,
        'updated_at' => $bom->updatedAtFormatted,
        'status' => $bom->statusText,
        'remark' => $bom->remark,
        'sub_part_state' => ($bom->subPart->state == '0') ? 'C' : (($bom->subPart->state == '1') ? 'S' : 'P'),
        'sub_part_info' => $bom->subPart->partinfo,
        'childs' => [],
      ];
      if($bom->subPart->hasSubParts) {
        $child['childs'] = $this->partRecursive($bom->subPart, $child['childs']);
      }
      $childs[] = $child;
    }

    return $childs;
  }

  private function loadDictionaries() {
    $parts = ArrayHelper::map(Part::find()->all(), 'id', 'partinfo');
    $parentParts = ArrayHelper::map(Part::find()->where('state <> '.Part::STATE_RAW)->all(), 'id', 'partinfo');
    $notRawParts = ArrayHelper::map(Part::find()->where(['state' => [Part::STATE_SEMI, Part::STATE_FINISHED]])->all(), 'id', 'partinfo');
    $notFgParts = ArrayHelper::map(Part::find()->where('state <>'.Part::STATE_FINISHED)->all(), 'id', 'partinfo');
    $warehouses = ArrayHelper::map(Warehouse::find()->where(['status' => Warehouse::STATUS_ACTIVE])->all(), 'id', 'name');

    return compact('parts', 'parentParts', 'notRawParts', 'notFgParts', 'warehouses');
  }

}