<?php

namespace app\controllers;

use Yii;
use app\models\StockInfoWrapper;
use app\models\StockInfoWrapperSearch;
use yii\web\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

use app\models\Part;
use app\models\Document;
use app\models\Stock;
use app\models\StockInfo;
use app\models\Warehouse;
use app\models\ProductModel;
use app\models\User;
use app\models\Dashboard;

/**
 * StockInfoWrapperController implements the CRUD actions for StockInfoWrapper model.
 */
class StockInfoWrapperController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all StockInfoWrapper models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new StockInfoWrapperSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $notRawParts = ArrayHelper::map(Part::find()->where(['state' => [Part::STATE_SEMI, Part::STATE_FINISHED]])->all(), 'id', 'partinfo');
        $warehouses = ArrayHelper::map(Warehouse::find()->all(), 'id', 'name');
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'notRawParts'  => $notRawParts,
            'warehouses'  => $warehouses,
        ]);
    }

    /**
     * Displays a single StockInfoWrapper model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new StockInfoWrapper model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new StockInfoWrapper();
        $errorList = [];
        if ($model->load(Yii::$app->request->post())) {
          $transaction = Yii::$app->db->beginTransaction();
          $part_models = Dashboard::normaRasxoda($model->part_id,null, $model->qty);
          
          $data = [];
          $tmpArr = [];
          try{
            $model->give_user_id = Yii::$app->user->id;
            $model->date = date('Y-m-d');
            // vd($part_models);
            if($model->save(false)){
              if(!empty($part_models)){
                foreach($part_models as $key => $item){                  
                    unset($tmpArr);
                    $tmpArr['part_id'] = $item['part_id'];
                    $tmpArr['qty'] = $item['quantity'];
                    $tmpArr['stock_info_wrapper_id'] = $model->id;
                    $tmpArr['wh_id'] = $item['warehouse_id'];
                    $data[] = $tmpArr;
                  
                }
                $stockResult = Stock::issue(1, $data, true);
                // vd($stockResult);
                if ($stockResult['success']) {
                  $transaction->commit();
                  // $this->writeToDocHistory($model->id, $this->action->id);
                  Yii::$app->session->setFlash('success', Yii::t('app', 'Stock Info created successfully.'));
                  return $this->redirect(['index']);
                } else {
                  // vd($errorList);
                  return $this->render("create",
                    array_merge(
                      [
                        "model" => $model,
                        'errorlist' => ['details' => $errorList, 'stock' => $stockResult['errorlist'] ?? null],
                      ],
                      self::loadDictionaries()
                    )
                  );
                }
              }
              $transaction->commit();
              Yii::$app->session->setFlash('success', Yii::t('app', 'Stock Info created successfully.'));
              return $this->redirect(['index']);
            }

          }catch(\Exception $e){
            $transaction->rollBack();
          }
          
        }

        // return $this->render('create', [
        //     'model' => $model,
            
        // ]);

        return $this->render("create",
          array_merge(
            [
              "model" => $model,
              'errorList' => $errorList
            ],
            self::loadDictionaries()
          )
        );
    }

    /**
     * Updates an existing StockInfoWrapper model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing StockInfoWrapper model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if($model->qty == 0 || $model->status == 0){
          Yii::$app->session->setFlash('danger', 'Невозможно удалить');
          return $this->redirect(['index']);
        }
        $model->qty = 0;
        $model->status = 0;
        $data = [];
        $tmpArr = [];
        
        $errorList = [];
        $transaction = Yii::$app->db->beginTransaction();
        if(!empty($model->save(false))){
          $errorList[]='Stock Info Wrapper saving errors. Something wrong';
        }
        if($model->stockInfos){
          $part_id = $model->part_id;
          foreach($model->stockInfos as $item){
            $part_id2 = $item->part_id;
            $query = "SELECT psi.warehouse_id from product_specification ps  
              inner join product_specification_item psi on psi.product_specification_id = ps.id
              where ps.part_id=$part_id and psi.part_id=$part_id2 and ps.status=1
            ";
            $warehouseId = Yii::$app->db->createCommand($query)->queryScalar();
            // vd($warehouse);
            unset($tmpArr);
            $tmpArr['part_id'] = $item->part_id;
            $tmpArr['qty']     = $item->qty;
            $tmpArr['info_id'] = $item->id;
            $tmpArr['wh_id']   = $warehouseId;
            $data[] = $tmpArr;
            $item->qty = 0;
            if(!empty($item->save(false))){
              $errorList[]='Stock Info Save Errors'.$item->errors;
            }
            
          }
          $stockResult = Stock::receipt(1, $data, true);
          if($stockResult['success']){
            $transaction->commit();
            Yii::$app->session->setFlash('success', 'Saved Successfully');
            return $this->redirect(['index']);
          }
          if(count($errorList) > 0){
            $transaction->rollBack();
            Yii::$app->session->setFlash('danger', $errorList);
            return $this->redirect(['index']);
          }
          
        }
        $transaction->commit();
        Yii::$app->session->setFlash('success', 'Deleted Successfully');
        return $this->redirect(['index']);
        
    }

    /**
     * Finds the StockInfoWrapper model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return StockInfoWrapper the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = StockInfoWrapper::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }




    private function loadDictionaries()
    {
      $queryPart = Part::find()->where(['status' => 1, 'state' => Part::STATE_FINISHED]);
      if (Yii::$app->user->identity->rolename == "counter" || Yii::$app->user->identity->rolename == "mrpc") {
        $queryPart->andWhere(["warehouse_id" => Yii::$app->user->identity->warehouseIds]);
      }
      $parts = [];
      $parts_withptnm = [];
      $options = [];
      foreach ($queryPart->all() as $part) {
        $parts[$part->id] = $part->part_no . " (" . $part->part_color . ")";
        if ($part->warehouse->warehouse_type ?? null != Warehouse::TYPE_OUTSOURCING) {
          $parts_withptnm[$part->id] = $part->part_no . " (" . $part->part_color . ") " . $part->part_name. " " .$part->remark;
        }
        $options[$part->id] = ["data-pack-size" => $part->pack_size ?? "0"];
      }
      $users = ArrayHelper::map(
        User::find()
          ->where(["status" => User::STATUS_ACTIVE])
          ->all(),
        "id",
        "fullname"
      );
      $models = ArrayHelper::map(ProductModel::find()->all(), "id", "modelinfo");
      $flocs = ArrayHelper::map(
        Warehouse::find()
          ->where(["warehouse_type" => [Warehouse::TYPE_SHOP, Warehouse::TYPE_OUTSOURCING]])
          ->all(),
        "id",
        "name"
      );
      return compact("parts", "parts_withptnm", "users", "options", "models", "flocs");
    }


    /** 
     * Anvar Sanakulov
     * 2024-01-28
     * 
     * ajax code Part List
     */
    public function actionGetStockPartList($id)
    {
      Yii::$app->response->format = Response::FORMAT_JSON;
      $data = [];
      $model = $this->findModel($id);
      if($model && $model->part){
        $part = $model->part;
        $data[] = [
          'id'    => $part->id,
          'info'  => $part->part_no.' '.$part->part_name.' ('.($part->part_color).') '. $part->remark
        ];
      }

      return $data;
    }
}
