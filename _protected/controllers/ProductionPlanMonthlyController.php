<?php
namespace app\controllers;

use app\components\Helpers;
use app\models\Part;
use app\models\ProductionMonthlyPlan;
use app\services\TelegramService;
use app\models\ProductSpecification;
use app\models\ProductionPlanComment;
use app\models\ProductionMonthlyPlanSearch;
use app\models\UploadForm;
use app\models\UserWarehouse;
use app\models\Warehouse;
use app\models\Model;
use app\models\ProductionPlanShort;
use DateInterval;
use DatePeriod;
use DateTime;
use Exception;
use PHPExcel_Cell;
use PHPExcel_Shared_Date;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\widgets\ActiveForm;
use yii\web\Controller; 

class ProductionPlanMonthlyController extends Controller {

  /**
   * Lists all ProductionMonthlyPlan models.
   *
   * @return mixed
   */
  public function actionIndex() {
    $searchModel = new ProductionMonthlyPlanSearch();
    if(!Yii::$app->request->queryParams) {
      $searchModel->production_date = date('Y-m');
    }
    $dataProvider = $searchModel->searchMonthly(Yii::$app->request->queryParams);
    $user_warehouse = Yii::$app->user->identity->warehouseIds;
    $cond_wh = (!Yii::$app->user->can('admin')) ? ['and', ['in', 'id', $user_warehouse], ['warehouse_type' => [0, 1]]] : ['warehouse_type' => [0, 1]];
    $query_wh = Warehouse::find()->where($cond_wh)->createCommand()->rawSql;
    $warehouses = ArrayHelper::map(Warehouse::find()->where($cond_wh)->all(), 'id', 'name');
    $cond_part = (!Yii::$app->user->can('admin'))
      ? ['and', ['in', 'warehouse_id', $user_warehouse], ['not in', 'state', 0], ['status' => [1]]]
      : ['and', ['not in', 'state', 0], ['status' => [1]]];
    $query_part = Part::find()->where($cond_part);
    $parts = ArrayHelper::map($query_part->all(), 'id', 'part_no');
    return $this->render('index',
      compact(
        'searchModel',
        'dataProvider',
        'warehouses',
        'parts'
      )
    );
  }

  public function actionValidate($id = null) {
    $model = $id === null ? new ProductionMonthlyPlan() : ProductionMonthlyPlan::findOne($id);
    if(Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      return ActiveForm::validate($model);
    }
  }

  public function actionValidateComment($id = null) {
    $model = $id === null ? new ProductionPlanComment() : ProductionPlanComment::findOne($id);
    if(Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      return ActiveForm::validate($model);
    }
  }

  public function actionIndex_report() {
    $need_month = (isset($_POST['need_month'])) ? $_POST['need_month'] : 0;
    $shift = (isset($_POST['ProductionMonthlyPlan']['shift'])) ? $_POST['ProductionMonthlyPlan']['shift'] : 0;
    $warehouse_id = (isset($_POST['ProductionMonthlyPlan']['warehouse_id'])) ? $_POST['ProductionMonthlyPlan']['warehouse_id'] : 0;
    $model = new ProductionMonthlyPlan();
    $where = "";
    if($need_month) {
      $where .= "production_plan.production_date between '".$need_month."-01' and '".$need_month."-31'";
    }
    if($shift) {
      $where .= " and production_plan.shift=".$shift;
    }
    if($warehouse_id) {
      $where .= " and production_plan.warehouse_id=".$warehouse_id;
    }
    if($where) {
      $DB_part_list = Part::find()
                          ->joinWith([
                            "productionMonthlyPlans" => function($query) use ($where) {
                              $query->onCondition($where);
                            }
                          ])
                          ->where('status=1 and production_plan.part_id is not null');
      $DB_part_list->all();
    } else {
      $upl_file_yiloy = Yii::$app->session->get('upl_file_yiloy');
      $need_month = (strlen($upl_file_yiloy) > 0) ? $upl_file_yiloy : date("Y-m");
      $DB_part_list = Part::find()
                          ->where('status=1 and production_plan.part_id is not null')
                          ->joinWith([
                              "productionMonthlyPlans" => function($query) use ($need_month) {
                                $query->onCondition("production_plan.production_date between '".$need_month."-01' and '".$need_month."-31'");
                              }
                            ]
                          )
                          ->all();
    }
    return $this->render('index', [
      'DB_part_list' => $DB_part_list ?? null,
      'need_month' => $need_month ?? null,
      'shift' => $shift ?? null,
      'model' => $model ?? null,
      'warehouse_id' => $warehouse_id ?? null,
      'warehouses' => $warehouses ?? null,
      'parts' => $parts ?? null,
      'searchModel' => $searchModel ?? null,
      'dataProvider' => $dataProvider ?? null,
    ]);
  }

  /**
   * Creates a new ProductionMonthlyPlan model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   *
   * @return mixed
   */
  public function actionCreate() {
    $modelMain = new ProductionPlanShort();
    $models	= [new ProductionMonthlyPlan];
    if(Yii::$app->getRequest()->isAjax) {
      if($modelMain->load(Yii::$app->request->post())) {
		  $models = Model::createMultiple(ProductionMonthlyPlan::classname());
		  Model::loadMultiple($models, Yii::$app->request->post());
		//   vd($models);
        $valid = $modelMain->validate();
        $valid = Model::validateMultiple($models) ;
		    $valid = true;
        if($valid){
          // $transaction = \Yii::$app->db->beginTransaction();

          // try{
            $flag = true;
              	foreach ($models as $index => $model) {
                  	$model->part_id = $modelMain->part_id;
                    $model->warehouse_id = $modelMain->warehouse_id;
                    $model->production_date = date('Y-m').'-01';
                    $model->type = 1;
                    $model->clearErrors();
                    // vd($model->save(false));
                    if (! ($flag = $model->save(false))) {
                      $data['errors'] = $model->getErrors();
                      $transaction->rollBack();
                      break;
                    }
                  	$data['status'] = 1;
                  	$w_house = Warehouse::find()
                  	  ->where(["id" => $model->warehouse_id])
                  	  ->one();
                  	$spec = ProductSpecification::find()
                  	  ->where(["part_id" => $model->part_id])
                  	  ->one();
                  	$tg["warehouse_id"] 	= $w_house->name;
                  	$tg["production_date"] 	= $model->production_date;
                  	$tg["part_id"] 			= $spec->code;
                  	$tg["shift"] 			= $model->shift;
                  	$tg["target_qty"] 		= $model->target_qty;
                  //TelegramService::productionMonthlyPlan($tg);
              	}
            if ($flag) {
                // $transaction->commit();
                Yii::$app->response->format = Response::FORMAT_JSON;
        		    return $data;
            }
          // } catch (Exception $e) {
          //   $transaction->rollBack();
          //   $data['status'] = 0;
          //   Yii::$app->response->format = Response::FORMAT_JSON;
          //   return $data;
          // }
        }
        
      } else {
        return $this->renderAjax('_form2', [
          'modelMain' 	=> $modelMain,
          'models' 		=> (empty($models))?[new ProductionMonthlyPlan]:$models
        ]);
      }
    } else {
      return $this->redirect(['index']);
    }
  }


  //dynamicform create
  public function actionCreate2() {
    $model = new ProductionMonthlyPlan();
    if(Yii::$app->getRequest()->isAjax) {
      if($model->load(Yii::$app->request->post())) {
        $model->production_date = date('Y-m').'-01';
        if($model->save(false)) {
          $data['status'] = 1;
          $w_house = Warehouse::find()
            ->where(["id" => $model["warehouse_id"]])
            ->one();
            $spec = ProductSpecification::find()
              ->where(["part_id" => $model->part_id])
              ->one();
          $tg["warehouse_id"] = $w_house["name"];
          $tg["production_date"] = $model["production_date"];
          $tg["part_id"] = $spec["code"];
          $tg["shift"] = $model["shift"];
          $tg["target_qty"] = $model["target_qty"];
          TelegramService::productionMonthlyPlan($tg);
        } else {
          $data['status'] = 0;
          $data['errors'] = $model->getErrors();
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $data;
      } else {
        return $this->renderAjax('_form2', ['model' => $model]);
      }
    } else {
      return $this->redirect(['index']);
    }
  }
  /**
   * Updates an existing ProductionMonthlyPlan model.
   * If update is successful, the browser will be redirected to the 'view' page.
   *
   * @param int $id
   *
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionUpdate2($id) {
    $modelMain = new ProductionPlanShort();
    $generalModel = $this->findModel($id);
    $modelMain->part_id 	= $generalModel->part_id;
    $modelMain->warehouse_id = $generalModel->warehouse_id;
    $models = ProductionMonthlyPlan::findAll($id);

      if(Yii::$app->getRequest()->isAjax) {
        if($modelMain->load(Yii::$app->request->post())) {
        $oldIDs = ArrayHelper::map($models, 'id', 'id');
        $models = Model::createMultiple(ProductionMonthlyPlan::classname(), $models);
        Model::loadMultiple($models, Yii::$app->request->post());
        $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($models, 'id', 'id')));
        
        $valid = $modelMain->validate();
        $valid = Model::validateMultiple($models) && $valid;
        $valid = true;

      if ($valid) {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
          if (!empty($deletedIDs)) {
            ProductionMonthlyPlan::deleteAll(['id' => $deletedIDs]);
          }
          foreach ($models as $model) {
            $model->part_id 		= $modelMain->part_id;
            $model->warehouse_id 	= $modelMain->warehouse_id;
            $model->clearErrors();
            if (! ($flag = $model->save(false))) {
              $transaction->rollBack();
              $data['status'] = 0;
              $data['errors'] = $model->getErrors();
              Yii::$app->response->format = Response::FORMAT_JSON;
              return $data;
            }
          }
          if ($flag) {
            $transaction->commit();
            $data['status'] = 1;
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $data;
          }
        } catch (Exception $e) {
          $transaction->rollBack();
        }
      }
        } else {
          return $this->renderAjax('_form2', [
            'modelMain' => $modelMain,
        'models' => (empty($models)) ? [new ProductionMonthlyPlan] : $models
          ]);
        }
      } else {
        return $this->redirect(['index']);
      }
  }

  public function actionUpdate($id) {
    $model = $this->findModel($id);
    if(Yii::$app->getRequest()->isAjax) {
      $model->production_date = date('Y-m', strtotime($model->production_date));
      if($model->load(Yii::$app->request->post())) {
        if($model->save(false)) {
          $data['status'] = 1;
        } else {
          $data['status'] = 0;
          $data['errors'] = $model->getErrors();
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $data;
      } else {
        return $this->renderAjax('_form', [
          'model' => $model,
        ]);
      }
    } else {
      return $this->redirect(['index']);
    }
  }
  /**
   * Comment an existing ProductionMonthlyPlan definition.
   * If comment is successful, the browser will be redirected to the 'view' page.
   *
   * @param int $id
   *
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionComment($id) {
    $model = ProductionPlanComment::find()->where(['production_plan_id' => $id])->one();
    if(!$model)
      $model = new ProductionPlanComment();
    $model->production_plan_id = $id;
    $model->created_at = time();
    $model->created_by = Yii::$app->user->id;
    if(Yii::$app->getRequest()->isAjax) {
      if($model->load(Yii::$app->request->post())) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model->production_plan_id = $id;
        $model->created_at = time();
        $model->created_by = Yii::$app->user->id;
        if($model->save(false)) {
          $data['status'] = 1;
        } else {
          $data['status'] = 0;
          $data['errors'] = $model->getErrors();
        }
        return $data;
      } else {
        return $this->renderAjax('_comment', ['model' => $model]);
      }
    } else {
      return $this->redirect(['production-plan/index']);
    }
  }

  /**
   * Deletes an existing ProductionMonthlyPlan model.
   * If deletion is successful, the browser will be redirected to the 'index' page.
   *
   * @param int $id
   *
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionDelete($id) {
    Yii::$app->response->format = Response::FORMAT_JSON;
    $model = $this->findModel($id);
    if($model) {
      if($model->delete()) {
        return [
          "status" => 1
        ];
      }
    }
    return [
      "status" => 0
    ];
  }
  public function actionDeleteAll() {
    Yii::$app->response->format = Response::FORMAT_JSON;
    //table truncate
    $table = ProductionMonthlyPlan::tableName();
    Yii::$app->db->createCommand("TRUNCATE TABLE $table")->execute();
    return [
      "status" => 1
    ];
    return $this->redirect(['index']);
  }
  public function actionUpload() {
    $user_id = Yii::$app->user->id;
    $user_warehouse = UserWarehouse::find()->where('user_id='.$user_id)->select('warehouse_id')->all();
    $wh_ids = [];
    foreach($user_warehouse as $wh) {
      $wh_ids[] = $wh->warehouse->id;
    }
    $model = new ProductionMonthlyPlan();
    $model_uploadForm = new UploadForm();
    if(!file_exists('uploads')) {
      FileHelper::createDirectory('uploads');
    }
    if(Yii::$app->request->isPost) {
      $model_uploadForm->xls_file = UploadedFile::getInstance($model_uploadForm, 'xls_file');
      if($model_uploadForm->xls_file && $model_uploadForm->validate()) {
        $model_uploadForm->xls_file->saveAs('uploads/'.$model_uploadForm->xls_file->baseName.'.'.$model_uploadForm->xls_file->extension);
        $data = $model_uploadForm->read_excel('uploads/'.$model_uploadForm->xls_file->baseName.'.'.$model_uploadForm->xls_file->extension);
        //        $unix_date = (($data['header_need_dt'][0][1]) - 25569) * 86400;
        //        $xls_dt = ($data['header_need_dt'][0][0] > 0) ? $data['header_need_dt'][0][0] : $data['header_need_dt'][0][1];
        //        $unix_date = ($xls_dt - 25569) * 86400;
        //        $month_begin = date("Y-m-d", $unix_date);
        $month_begin = date("Y-m-d", strtotime($data['header_need_dt']));
        $today = date("Y-m-d", time());
        $kecha = date('Y-m-d', strtotime('-1 day', time()));
        $erta = date("Y-m-d", strtotime('+1 day', time()));
        $month_end = date("Y-m-t", strtotime($month_begin));
        $month_end_cols = date('t', strtotime($month_end));
        $yil_oy = date("Y-m", strtotime($month_begin));
        $xls_product_list = [];
        $insert_data1 = [];
        $insert_data11 = [];
        $insert_data21 = [];
        $insert_data22 = [];
        $no_WH_parts = '';
        $part_ids = 0;
        $err_text = "";
        $err = 0;

        if($data['highestColIndex'] - ($month_end_cols * 2) !== 4) {
          Yii::$app->session->setFlash('error', Yii::t('app', 'Excel faylning ustunlar soni noto`g`ri'));
          return $this->render('upload', [
            'model' => $model,
            'model_uploadForm' => $model_uploadForm
          ]);
        }


        for($rows = 4; $rows <= ($data['highestRow']); $rows++) {
          $part_no = $data['values'][$rows - 4][0][0];
          $part = Part::findOne(['part_no' => $part_no, 'status' => 1]);
          $part_id = $part['id'];
          $part_ids = $part_ids.",".$part['id'];
          $part_warehouse_id = $part['warehouse_id'];
          if(!(Yii::$app->user->can('admin') || Yii::$app->user->can('superadmin'))) {
            if(!in_array($part_warehouse_id, $wh_ids)) {
              $no_WH_parts = $no_WH_parts.$part_no."<br>";
            }
          }
          if($part_warehouse_id === 0 || $part_warehouse_id === null) {
            $err = 1;
            $err_text = Yii::t('app', 'The following parts are not in the user`s warehouse:');
            $err_text .= '<br>'.$part_no;
          }
          $xls_product_list[] = $part_no;
          $val_pos = 3;
//            echo "<pre>"; print_r($today);echo "</pre>";
          for($j = 0; $j < $month_end_cols; $j++) {
            $part_date = date('Y-m-d', strtotime($month_begin.' +'.$j.' day'));
            $target_qty1 = is_numeric($data['values'][$rows - 4][0][$val_pos]) ? $data['values'][$rows - 4][0][$val_pos] : 0;
            $target_qty2 = is_numeric($data['values'][$rows - 4][0][$val_pos + 1]) ? $data['values'][$rows - 4][0][$val_pos + 1] : 0;
            if($part_date <= $today) {
              if(Yii::$app->params['plan_freeze_time'] > 0) {
                /** 1-smena chegaralangan vaqt oralig`i uchun */
                if(ProductionMonthlyPlan::allowEdit(1, $part_date) == 1) {
                  $insert_data11[] = [$part_id, $part_date, $part_warehouse_id, 1, $target_qty1];
//                  $insert_data21[] = [$part_id, $part_date, $part_warehouse_id, 2, $target_qty2];
                }
                /** 2-smena chegaralangan vaqt oralig`i uchun */
                if( // bugungi 2-smenadan boshlab
                  (ProductionMonthlyPlan::allowEdit(2, $part_date) == 1) &&
                  time() >= strtotime(Yii::$app->params['shifts']['1']['0'])
                ) {
                  $insert_data21[] = [$part_id, $part_date, $part_warehouse_id, 2, $target_qty2];
                } elseif( // kechagi 2-smenadan boshlab
                  (ProductionMonthlyPlan::allowEdit(2, $kecha) == 1) &&
                  time() >= strtotime(Yii::$app->params['shifts']['2']['1']['0']) &&
                  time() < strtotime(Yii::$app->params['shifts']['2']['1']['1'])
                ) {
                  $insert_data22[] = [$part_id, $part_date, $part_warehouse_id, 2, $target_qty2];
                }
//                // 1-smena chegaralangan vaqt oralig`i uchun
//                if (
//                  $part_date == $today &&
//                  time() >= strtotime(Yii::$app->params['shifts']['1']['0']) &&
//                  time() <= strtotime(Yii::$app->params['shifts']['1']['0']) + Yii::$app->params['plan_freeze_time']*60*60
//                ) {
//                  $insert_data11[] = [$part_id, $part_date, $part_warehouse_id, 1, $target_qty1];
//                  $insert_data21[] = [$part_id, $part_date, $part_warehouse_id, 2, $target_qty2];
//                }
//                elseif (
//                  // 2-smena chegaralangan vaqt oralig`i uchun 1-qism
//                  $part_date == $today &&
//                  time() > strtotime(Yii::$app->params['shifts']['1']['0']) + Yii::$app->params['plan_freeze_time']*60*60 &&
//                  time() <= strtotime(Yii::$app->params['shifts']['2']['0']['1'])
//                ) {
//                  $insert_data21[] = [$part_id, $part_date, $part_warehouse_id, 2, $target_qty2];
//                } elseif (
//                  // 2-smena chegaralangan vaqt oralig`i uchun 2- qism
//                  $part_date == $kecha &&
//                  time() >= strtotime(Yii::$app->params['shifts']['2']['1']['0']) &&
//                  time() <= strtotime(Yii::$app->params['shifts']['2']['1']['1']) &&
//                  time() < strtotime('-1 day', strtotime(Yii::$app->params['shifts']['2']['0']['0'])) + Yii::$app->params['plan_freeze_time']*60*60
//                ) {
//                  $insert_data22[] = [$part_id, $part_date, $part_warehouse_id, 2, $target_qty2];
//                }
              } elseif($part_date == $today) {
                $insert_data1[] = [$part_id, $part_date, $part_warehouse_id, 1, $target_qty1];
                $insert_data1[] = [$part_id, $part_date, $part_warehouse_id, 2, $target_qty2];
              }
            } else {
              $insert_data3[] = [$part_id, $part_date, $part_warehouse_id, 1, $target_qty1];
              $insert_data3[] = [$part_id, $part_date, $part_warehouse_id, 2, $target_qty2];
            }
            $val_pos = $val_pos + 2;
          }
        }
        if($err > 0) {
          Yii::$app->session->setFlash('error', $err_text);
          return $this->render('upload', [
            'model' => $model,
            'model_uploadForm' => $model_uploadForm
          ]);
        }
        $DB_part_list = ArrayHelper::map(Part::find()->where('status>0')->all(), 'id', 'part_no');
        $not_DBparts = array_diff($xls_product_list, $DB_part_list);
        $no_DBparts_rows = '';
        foreach($not_DBparts as $key => $value) {
          $no_DBparts_rows = $no_DBparts_rows.(($key + 1)."-".$value)."<br>";
        }
        $no_DBparts_rows = ltrim($no_DBparts_rows, "<br>");
        if(strlen($no_DBparts_rows) > 0) {
          $err = 1;
          $err_text = Yii::t('app', 'The following parts are not found in the system:');
          $err_text .= '<br>'.$no_DBparts_rows;
        }
        if(strlen($no_WH_parts) > 0) {
          $err = 1;
          $err_text = Yii::t('app', 'The following parts are not in the user`s warehouse:');
          $err_text .= '<br>'.$no_WH_parts;
        }
        if($err > 0) {
          Yii::$app->session->setFlash('error', $err_text);
          return $this->render('upload', [
            'model' => $model,
            'model_uploadForm' => $model_uploadForm
          ]);
        }
        $transaction = Yii::$app->db->beginTransaction();
        Yii::$app->db->createCommand('SET SESSION wait_timeout = 36000;')->execute();
        unlink(Yii::getAlias('@webroot').'/'.$data['filename']);
        $insert_data1 = array_unique($insert_data1, SORT_REGULAR);
        $insert_data11 = array_unique($insert_data11, SORT_REGULAR);
        $insert_data21 = array_unique($insert_data21, SORT_REGULAR);
        $insert_data22 = array_unique($insert_data22, SORT_REGULAR);
        $insert_data3 = array_unique($insert_data3, SORT_REGULAR);
//        echo "<pre>"; print_r($insert_data11);echo "</pre>";
//        die;
        try {
          if($yil_oy >= date("Y-m")) {
            if($yil_oy == date("Y-m")) {
              if(count($insert_data11) > 0 || count($insert_data1) > 0) {
                ProductionMonthlyPlan::deleteAll(
                  "shift=1 and production_date between '".$today."' and '".$month_end."' and part_id in(".$part_ids.")"
                );
              }
              if(count($insert_data21) > 0 || count($insert_data1) > 0) {
                ProductionMonthlyPlan::deleteAll(
                  "shift=2 and production_date between '".$today."' and '".$month_end."' and part_id in(".$part_ids.")"
                );
              }
              if(count($insert_data22) > 0) {
                ProductionMonthlyPlan::deleteAll(
                  "shift=2 and production_date between '".$kecha."' and '".$month_end."' and part_id in(".$part_ids.")"
                );
              }
              ProductionMonthlyPlan::deleteAll(
                "production_date between '".$erta."' and '".$month_end."' and part_id in(".$part_ids.")"
              );
            }
            if($yil_oy > date("Y-m")) {
              if(count($insert_data3) > 0) {
                ProductionMonthlyPlan::deleteAll(
                  "production_date between '".$yil_oy."-01' and '".$month_end."' and part_id in(".$part_ids.")"
                );
              }
            }
            $insert_data_all = array_merge($insert_data1, $insert_data11, $insert_data21, $insert_data22, $insert_data3);
            $insert_data_all = array_unique($insert_data_all, SORT_REGULAR);
            $instQuery = Yii::$app->db->createCommand()
                                      ->batchInsert(
                                        'production_plan',
                                        ['part_id', 'production_date', 'warehouse_id', 'shift', 'target_qty'],
                                        $insert_data_all
                                      );
//            echo "<pre>"; print_r($instQuery->rawSql);echo "</pre>";
//            die;
            $instQuery->execute();
            $transaction->commit();
            Yii::$app->session->setFlash('success', Yii::t('app', 'File uploaded successfully.')." ( ".$yil_oy." )");
            Yii::$app->session->set('upl_file_yiloy', $yil_oy);
          } else {
            Yii::$app->session->setFlash('warning', Yii::t('app', 'File not uploaded for this period.'));
          }
          return $this->redirect(['index']);
        }
        catch(Exception $e) {
          $transaction->rollback();
          Yii::$app->session->setFlash('error', $e);
        }
      } else {
        Yii::$app->session->setFlash('error', $model_uploadForm->errors);
      }
    }
    Yii::$app->db->createCommand('SET SESSION wait_timeout = 6000;')->execute();
    return $this->render('upload', [
      'model' => $model,
      'model_uploadForm' => $model_uploadForm
    ]);
  }

  public function actionUploadToday() {
    $model = new ProductionMonthlyPlan();
    $model_uploadForm = new UploadForm();
    $user_id = Yii::$app->user->id;
    $user_warehouse = UserWarehouse::find()->select('warehouse_id')
                                   ->where('user_id='.$user_id)->all();
    $wh_ids = [];
    if(count($user_warehouse) < 1) {
      Yii::$app->session->setFlash('error', Yii::t('app', 'You don`t have access to any warehouse.'));
      goto redirectView;
    }
    foreach($user_warehouse as $wh) {
      $wh_ids[] = $wh->warehouse->id;
    }
    if(Yii::$app->request->isPost) {
      $model_uploadForm->xls_file = UploadedFile::getInstance($model_uploadForm, 'xls_file');
      if($model_uploadForm->xls_file && $model_uploadForm->validate()) {
        $model_uploadForm->xls_file->saveAs('uploads/'.$model_uploadForm->xls_file->baseName.'.'.$model_uploadForm->xls_file->extension);
        $data = $model_uploadForm->read_excel('uploads/'.$model_uploadForm->xls_file->baseName.'.'.$model_uploadForm->xls_file->extension);
        $today = date("Y-m-d", time());
        $xls_product_list = [];
        $insert_data1 = [];
        $insert_data2 = [];
        $insert_data = [];
        $no_WH_parts = '';
        $part_ids1 = 0;
        $part_ids2 = 0;
        $err_text = "";
        $err = 0;
        /** 1-smena chegaralangan vaqt oralig`i uchun */
        $allowShift1 = (ProductionMonthlyPlan::allowEdit(1, $today) == 1) ? 1 : 0;
        /** 2-smena chegaralangan vaqt oralig`i uchun */
        $allowShift2 = (ProductionMonthlyPlan::allowEdit(2, $today) == 1) ? 1 : 0;
        for($rows = 4; $rows <= ($data['highestRow']); $rows++) {
          $part_no = $data['values'][$rows - 4][0][0];
          $part = Part::findOne(['part_no' => $part_no, 'status' => 1]);
          $part_id = $part['id'];
          $part_warehouse_id = $part['warehouse_id'];
          if(!Yii::$app->user->can('admin')) {
            if(!in_array($part_warehouse_id, $wh_ids)) {
              $no_WH_parts = $no_WH_parts.$part_no."<br>";
            }
          }
          if($part_warehouse_id == 0 || $part_warehouse_id == null) {
            $err = 1;
            $err_text = Yii::t('app', 'The following parts are not in the user`s warehouse:');
            $err_text .= '<br>'.$part_no;
          }
          $xls_product_list[] = $part_no;
          $target_qty1 = is_numeric($data['values'][$rows - 4][0][3]) ? $data['values'][$rows - 4][0][3] : null;
          $target_qty2 = is_numeric($data['values'][$rows - 4][0][3 + 1]) ? $data['values'][$rows - 4][0][3 + 1] : null;
          //						echo "<hr>";
          //						echo "<pre>0.0: "; print_r(date('Ymd H:i',time() ));echo "</pre>";
          //						echo "<pre>1.1: "; print_r(date('Ymd H:i',strtotime(Yii::$app->params['shifts']['1']['0']) + Yii::$app->params['plan_freeze_time']*60*60));echo "</pre>";
          //						echo "<pre>1.2: "; print_r(date('Ymd H:i',strtotime(Yii::$app->params['shifts']['2']['0']['1']) ));echo "</pre>";
          //						echo "<pre>1.3: "; print_r(date('Ymd H:i',strtotime(Yii::$app->params['shifts']['2']['0']['0']) + Yii::$app->params['plan_freeze_time']*60*60 ));echo "</pre>";
          //						echo "<pre>2.1: "; print_r(date('Ymd H:i',strtotime(Yii::$app->params['shifts']['2']['1']['0']) ));echo "</pre>";
          //						echo "<pre>2.2: "; print_r(date('Ymd H:i',strtotime(Yii::$app->params['shifts']['2']['1']['1']) ));echo "</pre>";
          //						echo "<pre>2.3: "; print_r(date('Ymd H:i',strtotime('-1 day', strtotime(Yii::$app->params['shifts']['2']['0']['0'])) + Yii::$app->params['plan_freeze_time']*60*60 ));echo "</pre>";
          /** smena va target_qty lar bo`yicha insert datani tayyorlash */
          if($target_qty1 !== null) {
            if((Yii::$app->params['plan_freeze_time'] == 0) ||
              (Yii::$app->params['plan_freeze_time'] > 0 && $allowShift1 == 1)
            ) {
              $part_ids1 = $part_ids1.",".$part_id;
              $insert_data1[] = [$part_id, $today, $part_warehouse_id, 1, $target_qty1];
            }
          }
          if($target_qty2 !== null) {
            if((Yii::$app->params['plan_freeze_time'] == 0) ||
              (Yii::$app->params['plan_freeze_time'] > 0 && $allowShift2 == 1)
            ) {
              $part_ids2 = $part_ids2.",".$part_id;
              $insert_data2[] = [$part_id, $today, $part_warehouse_id, 2, $target_qty2];
            }
          }
          /** smenalar bo`yicha insert datani tayyorlash */
//          if(Yii::$app->params['plan_freeze_time'] > 0) {
//            /** 1-smena chegaralangan vaqt oralig`i uchun */
//            if($allowShift1 == 1 && $target_qty1 !== null) {
//              $part_ids1 = $part_ids1.",".$part_id;
//              $insert_data1[] = [$part_id, $today, $part_warehouse_id, 1, $target_qty1];
//            }
//            /** 2-smena chegaralangan vaqt oralig`i uchun */
//            if($allowShift2 == 1 && $target_qty2 !== null) {
//              $part_ids2 = $part_ids2.",".$part_id;
//              $insert_data2[] = [$part_id, $today, $part_warehouse_id, 2, $target_qty2];
//            }
////            if (
////              time() >= strtotime(Yii::$app->params['shifts']['1']['0']) &&
////              time() <= strtotime(Yii::$app->params['shifts']['1']['0']) + Yii::$app->params['plan_freeze_time']*60*60
////            ) {
////              $insert_data1[] = [$part_id, $today, $part_warehouse_id, 1, $target_qty1];
////              $insert_data2[] = [$part_id, $today, $part_warehouse_id, 2, $target_qty2];
////            } elseif (/** 2-smena chegaralangan vaqt oralig`i uchun */
////              (
////                time() > strtotime(Yii::$app->params['shifts']['1']['0']) + Yii::$app->params['plan_freeze_time']*60*60 &&
////                time() < strtotime(Yii::$app->params['shifts']['2']['0']['1']) &&
////                time() < strtotime(Yii::$app->params['shifts']['2']['0']['0']) + Yii::$app->params['plan_freeze_time']*60*60
////              ) ||
////              (
////                time() >= strtotime(Yii::$app->params['shifts']['2']['1']['0']) &&
////                time() <= strtotime(Yii::$app->params['shifts']['2']['1']['1']) &&
////                time() < strtotime('-1 day', strtotime(Yii::$app->params['shifts']['2']['0']['0'])) + Yii::$app->params['plan_freeze_time']*60*60
////              )
////            ) {
////              //							echo "<pre>1:"; print_r("OK");echo "</pre>";
////              $insert_data2[] = [$part_id, $today, $part_warehouse_id, 2, $target_qty2];
////            }
//          } else {
//            if($target_qty1 !== null) {
//              $part_ids1 = $part_ids1.",".$part_id;
//              $insert_data1[] = [$part_id, $today, $part_warehouse_id, 1, $target_qty1];
//            }
//            if($target_qty2 !== null) {
//              $part_ids2 = $part_ids2.",".$part_id;
//              $insert_data2[] = [$part_id, $today, $part_warehouse_id, 2, $target_qty2];
//            }
//          }
        }
        //					echo "<pre>err: "; print_r($err);echo "</pre>";
        //					echo "<pre>insert_data: "; print_r($insert_data);echo "</pre>";
        //					echo "<pre>insert_data2: "; print_r($insert_data2);echo "</pre>";
        //					die;
        if($err > 0) {
          Yii::$app->session->setFlash('error', $err_text);
          goto redirectView;
        }
        /** DB dan topilmagan partlar*/
        $DB_part_list = ArrayHelper::map(Part::find()->where('status>0')->all(), 'id', 'part_no');
        $not_DBparts = array_diff($xls_product_list, $DB_part_list);
        $no_DBparts_rows = '';
        foreach($not_DBparts as $key => $value) {
          $no_DBparts_rows = $no_DBparts_rows.(($key + 1)."-".$value)."<br>";
        }
        $no_DBparts_rows = ltrim($no_DBparts_rows, "<br>");
        if(strlen($no_DBparts_rows) > 0) {
          $err = 1;
          $err_text = Yii::t('app', 'The following parts are not found in the system:');
          $err_text .= '<br>'.$no_DBparts_rows;
        }
        if(strlen($no_WH_parts) > 0) {
          $err = 1;
          $err_text = Yii::t('app', 'The following parts are not in the user`s warehouse:');
          $err_text .= '<br>'.$no_WH_parts;
        }
        if($err > 0) {
          Yii::$app->session->setFlash('error', $err_text);
          goto redirectView;
        }
        unlink(Yii::getAlias('@webroot').'/'.$data['filename']);
        Yii::$app->db->createCommand('SET SESSION wait_timeout = 36000;')->execute();
        $insert_data = array_merge($insert_data1, $insert_data2);
        //					echo "<pre>insert_data: "; print_r($insert_data);echo "</pre>";
        //					echo "<pre>insert_data1: "; print_r($insert_data1);echo "</pre>";
        //					echo "<pre>insert_data2: "; print_r($insert_data2);echo "</pre>";
        //					die;
        if(count($insert_data) > 0) {
          $transaction = Yii::$app->db->beginTransaction();
          try {
            if(count($insert_data1) > 0) {
              ProductionMonthlyPlan::deleteAll(
                "shift=1 and production_date = '".$today."' and part_id in(".$part_ids1.")"
              );
            }
            if(count($insert_data2) > 0) {
              ProductionMonthlyPlan::deleteAll(
                "shift=2 and production_date = '".$today."' and part_id in(".$part_ids2.")"
              );
            }
            $batchInsert = Yii::$app->db->createCommand()
                                        ->batchInsert(
                                          'production_plan',
                                          ['part_id', 'production_date', 'warehouse_id', 'shift', 'target_qty'],
                                          $insert_data);
//            echo "<pre>"; print_r($batchInsert->rawSql);echo "</pre>";die;
            $batchInsert->execute();
            $transaction->commit();
            Yii::$app->session->setFlash('success', Yii::t('app', 'File uploaded successfully.')." ( ".$today." )");
            Yii::$app->session->set('upl_file_yiloy', $today);
            return $this->redirect(['index']);
          }
          catch(Exception $e) {
            $transaction->rollback();
//            $errMsg = "<i><u><strong>Upload excel file:</strong></u></i>".Helpers::arrayToStringRecursive($e->errorInfo);
            $errMsg = "<i><u><strong>Upload excel file:</strong></u></i> ".$e->errorInfo[2];
            Yii::$app->session->setFlash('error', $errMsg);
          }
        } else {
          Yii::$app->session->setFlash('warning', 'No data upload');
        }
      } else {
        Yii::$app->session->setFlash('error', $model_uploadForm->errors);
      }
    }
    Yii::$app->db->createCommand('SET SESSION wait_timeout = 6000;')->execute();
    redirectView:
    return $this->render('upload-today', [
      'model' => $model,
      'model_uploadForm' => $model_uploadForm
    ]);
  }

  public function actionDownloadTemplate() {
    $user_warehouse = Yii::$app->user->identity->warehouseIds;
    $cond_part = (!Yii::$app->user->can('admin'))
      ? ['and', ['in', 'warehouse_id', $user_warehouse], ['not in', 'state', 0], ['status' => [1]]]
      : ['and', ['not in', 'state', 0], ['status' => [1]]];
    $query = Part::find()->where($cond_part);
    $parts = $query->all();
    $from = date('Y-m-01');
    $to = date('Y-m-t');
    $begin = new DateTime($from);
    $end = new DateTime($to);
    $end = $end->modify('+1 day');
    $daterange = new DatePeriod($begin, new DateInterval('P1D'), $end);
    $shift = [];
    $shc = 3;
    foreach($daterange as $date) {
      $shift[++$shc] = 1;
      $shift[++$shc] = 2;
    }
    $arrFile = [];
    $i = 0;
    if(!empty($parts)) {
      foreach($parts as $part) {
        unset($tmpArray);
        $tmpArray['num'] = ++$i;
        $tmpArray['part_no'] = $part->part_no;
        $tmpArray['part_state'] = $part->stateText;
        $tmpArray['part_name'] = $part->part_name;
        $arrFile[] = $tmpArray;
      }
    } else {
      unset($tmpArray);
      $tmpArray['num'] = 1;
      $tmpArray['part_no'] = 'part_no';
      $tmpArray['part_state'] = 'Продукт';
      $tmpArray['part_name'] = 'part_name';
      $arrFile[] = $tmpArray;
    }
    $titles = $shift;
    if(empty($arrFile)) {
      $query->orderBy(['part_no' => SORT_ASC]);
    }
    $file = Yii::createObject([
      'class' => 'codemix\excelexport\ExcelFile',
      'sheets' => [
        'plan' => [
          'startRow' => 3,
          'data' => $arrFile,
          'titles' => $titles,
          'on afterRender' => function($event) {
            $fillRequared = [
              'fill' => [
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => ['rgb' => 'FCE4D6'],
              ],
            ];
            $dateTitle = [
              'font' => [
                'bold' => false,
                'size' => 8,
                'name' => 'Calibri Light'
              ],
              'alignment' => [
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'rotation' => 90
              ]
            ];
            $shiftTitle = [
              'font' => [
                'bold' => false,
                'size' => 10,
                'name' => 'Calibri Light'
              ],
              'alignment' => [
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
              ]
            ];
            $boldFontCenter = [
              'font' => [
                'bold' => true,
                'size' => 12,
                'name' => 'Calibri Light'
              ],
              'alignment' => [
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
              ]
            ];
            $styleFont10 = [
              'font' => [
                'size' => 10,
                'name' => 'Calibri Light'
              ],
            ];
            $styleFont12 = [
              'font' => [
                'size' => 12,
                'name' => 'Calibri Light'
              ],
            ];
            $styleThinBlackBorderOutline = [
              'borders' => [
                'allborders' => [
                  'style' => PHPExcel_Style_Border::BORDER_THIN,
                ],
              ],
            ];
            $sheet = $event->sender->getSheet();
            $highestColumn = $sheet->getHighestDataColumn();
            $highestRow = $sheet->getHighestDataRow();
            function getColRange($start_letter, $hCol, $row_number, $count) {
              $alphabets = range('A', 'Z');
              $start_idx = array_search(
                $start_letter,
                $alphabets
              );
              return sprintf(
                "%s%s:%s%s",
                $start_letter,
                $row_number,
                $alphabets[$start_idx + $count],
                $row_number
              );
            }

            $highestColIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
            $sheet->setCellValue('A1', '№')
                  ->setCellValue('B1', Yii::t('app', 'Part No'))
                  ->setCellValue('C1', Yii::t('app', 'Part type'))
                  ->setCellValue('D1', Yii::t('app', 'Part name'));
            $sheet->mergeCells('A1:A3')
                  ->mergeCells('B1:B3')
                  ->mergeCells('C1:C3')
                  ->mergeCells('D1:D3')
                  ->mergeCells('E1:'.($highestColumn.'1'));
            $sheet->setCellValue('E1', Yii::t('app', 'Quantity of plans'));
            $sheet->getStyle('A1:'.($highestColumn.'3'))->applyFromArray($boldFontCenter)
                  ->applyFromArray($styleThinBlackBorderOutline)
                  ->applyFromArray($styleFont10);
            $highestColIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
            $dd = 0;
            for($i = 4; $i < $highestColIndex; $i = $i + 2) {
              $fr_colstr = PHPExcel_Cell::stringFromColumnIndex($i);
              $to_colstr = PHPExcel_Cell::stringFromColumnIndex($i + 1);
              $sheet->mergeCells(($fr_colstr.'2:').(($to_colstr).'2'));
              $sheet->setCellValueByColumnAndRow(
                $i, 2,
                PHPExcel_Shared_Date::PHPToExcel(date('d.m.Y', strtotime(date('Y-m-'.$dd)."+1 days")))
              );
              $sheet->getStyleByColumnAndRow($i, 2)->getNumberFormat()->setFormatCode('dd.mm.yyyy');
              $dd++;
            }
            $sheet->getStyle('E2:'.($highestColumn.'2'))->applyFromArray($dateTitle);
            $sheet->getRowDimension('2')->setRowHeight(52);
            $sheet->getStyle('E3:'.($highestColumn.'3'))->applyFromArray($shiftTitle);
            $sheet->getStyle('A4:'.($highestColumn.$highestRow))->applyFromArray($styleFont12);
            $sheet->getStyle('B1:'.('B3'))->applyFromArray($fillRequared);
            $sheet->getStyle('D4:'.('D'.$highestRow))->applyFromArray($styleFont10);
            $maxDateCols = $highestColumn;
            $highestColumn++;
            for($col = 'E'; $col !== $highestColumn; $col++) {
              $sheet->getColumnDimension($col)->setWidth(4);
            }
            $highestColumn = $maxDateCols;
            $sheet->getColumnDimension('B')->setWidth(17);
            $sheet->getColumnDimension('C')->setWidth(15);
            $sheet->getColumnDimension('D')->setWidth(45);
            $sheet->freezePane('E4');
          },
        ]
      ]
    ]);
    if(is_array($file->sheets['plan']['data']) and count($file->sheets['plan']['data']) == 0) {
      return $this->redirect(['index']);
    }
    $file->send(Helpers::downloadFileName('plan-template'));
  }

  /**
   * Finds the ProductionMonthlyPlan model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   *
   * @param int $id
   *
   * @return ProductionMonthlyPlan the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id) {
    if(($model = ProductionMonthlyPlan::findOne($id)) !== null) {
      return $model;
    }
    throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
  }

  public function actionWhListByPart($id) {
    Yii::$app->response->format = Response::FORMAT_JSON;
    $partWhIds = Part::find()->select('warehouse_id')->where(['id' => $id]);
    $list = Warehouse::find()->where(['in', 'id', $partWhIds])->all();
//      echo "<pre>"; print_r($list->createCommand()->rawSql);echo "</pre>";
//      die;
    $data = [];
    foreach($list as $item) {
      $data[] = ['id' => $item->id, 'text' => $item->name];
    }
    return $data;
  }

}