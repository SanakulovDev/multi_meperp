<?php
namespace app\controllers;

use app\components\Helpers;
use app\models\Part;
use app\models\ProductionDailyPlan;
use app\services\TelegramService;
use app\models\ProductSpecification;
use app\models\ProductionPlanComment;
use app\models\ProductionDailyPlanSearch;
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

class ProductionDailyPlanController extends Controller {

  /**
   * Lists all ProductionDailyPlan models.
   *
   * @return mixed
   */
  public function actionIndex() {
    $searchModel = new ProductionDailyPlanSearch();
    if(!Yii::$app->request->queryParams) {
      // $searchModel->production_date = date('Y-m');
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


    $countQuery = clone $dataProvider->query;
		$total = 0;
		foreach ($countQuery->all() as $row) {
			$total += $row->target_qty;
		}

    $parts = ArrayHelper::map($query_part->all(), 'id', 'part_no');
    return $this->render('index',
      compact(
        'searchModel',
        'dataProvider',
        'warehouses',
        'parts',
        'total'
      )
    );
  }

  public function actionValidate($id = null) {
    $model = $id === null ? new ProductionDailyPlan() : ProductionDailyPlan::findOne($id);
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



  /**
   * Creates a new ProductionDailyPlan model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   *
   * @return mixed
   */
  public function actionCreate() {
    $models	= [new ProductionDailyPlan];
    if(Yii::$app->getRequest()->isAjax) {
        
      if(Yii::$app->request->post()) {
        $models = Model::createMultiple(ProductionDailyPlan::classname());
        Model::loadMultiple($models, Yii::$app->request->post());
        $valid = Model::validateMultiple($models);
        if($valid){
          $transaction = \Yii::$app->db->beginTransaction();
          try{
                $flag = true;
              	foreach ($models as $index => $model) {
                    $model->production_date = date('Y-m').'-01';
                    $model->type = 1;
                    $model->shift = 1;
                    $model->line = 1;
                    $model->clearErrors();
                    // vd($model->save(false));
                    if (! ($flag = $model->save(false))) {
                      $data['errors'] = $model->getErrors();
                      
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
                  //TelegramService::ProductionDailyPlan($tg);
              	}
            if ($flag) {
                $transaction->commit();
                Yii::$app->response->format = Response::FORMAT_JSON;
        		    return $data;
            }
          } catch (Exception $e) {
            $transaction->rollBack();
            $data['status'] = 0;
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $data;
          }
        }
        
      } else {
        return $this->renderAjax('_form2', [
          'models' 		=> (empty($models))?[new ProductionDailyPlan]:$models
        ]);
      }
    } else {
      return $this->redirect(['index']);
    }
  }


 

  public function actionUpdate($id) {
    $model = $this->findModel($id);
    if(Yii::$app->getRequest()->isAjax) {
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
   * Comment an existing ProductionDailyPlan definition.
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
   * Deletes an existing ProductionDailyPlan model.
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
    $table = ProductionDailyPlan::tableName();
    Yii::$app->db->createCommand("TRUNCATE TABLE $table")->execute();
    return [
      "status" => 1
    ];
    return $this->redirect(['index']);
  }
  
  /**
   * Finds the ProductionDailyPlan model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   *
   * @param int $id
   *
   * @return ProductionDailyPlan the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id) {
    if(($model = ProductionDailyPlan::findOne($id)) !== null) {
      return $model;
    }
    throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
  }

  public function actionWhListByPart($id) {
    Yii::$app->response->format = Response::FORMAT_JSON;
    $partWhIds = Part::find()->select('warehouse_id')->where(['id' => $id]);
    $list = Warehouse::find()->where(['in', 'id', $partWhIds])->all();
    $data = [];
    foreach($list as $item) {
      $data[] = ['id' => $item->id, 'text' => $item->name];
    }
    return $data;
  }

  /**
   * Anvar Sanakulov
   * 2024-02-05 19:57:33
   * @sanakulov_dev
   * Norma rasxod
   */
  public function actionSpecification()
  {
    $items1 = [];
    $items2 = [];
    if($post = Yii::$app->request->post()){
      $model = $this->findModel($post['id']);
      if($model){
        $productSpecification = ProductSpecification::find()->where(['part_id' => $model->part_id])->andWhere(['status' => 1])->one();
        if($productSpecification){
          $items1  = \app\models\Dashboard::normaRasxoda($model->part_id, null, $productSpecification->amount);
          $items2  = \app\models\Dashboard::normaRasxoda($model->part_id, null, $model->target_qty);
        }
      }
    }
    return $this->renderAjax('specification', 
        compact('items1', 'items2')
    );
  }

}