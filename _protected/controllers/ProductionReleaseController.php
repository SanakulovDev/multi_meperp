<?php

namespace app\controllers;

use Yii;
use app\models\ProductionRelease;
use app\models\ProductionReleaseItem;
use app\models\ProductionOrder;
use app\models\Part;
use app\models\ProductionReleaseSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Model;
use app\models\ProductionReleaseFactHistory;
/**
 * ProductionReleaseController implements the CRUD actions for ProductionRelease model.
 */
class ProductionReleaseController extends Controller
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
     * Lists all ProductionRelease models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProductionReleaseSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $parts  = Part::find()
            ->select(['id', 'concat(part_no, " ", part_name) as part_no'])
            ->where(['status' => Part::STATUS_ACTIVE])->all();
        $parts  = ArrayHelper::map($parts, 'id', 'part_no');
        $lines  = ProductionOrder::getLines();
        $shifts = ProductionOrder::getShifts();
        $selectTimes = ProductionRelease::selectTimes();
        return $this->render('index', compact('searchModel', 'dataProvider', 'parts', 'shifts', 'lines', 'selectTimes'));
    }

    /**
     * Displays a single ProductionRelease model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $identity = Yii::$app->user->identity;
        $lines  = ProductionOrder::getLines();
        $shifts = ProductionOrder::getShifts();
        $model  = $this->findModel($id);
        if($model && $model->status == 0){
          Yii::$app->session->setFlash('error', Yii::t('app', 'This release is closed'));
          return $this->redirect(['index']);
        }
        $model2 = ProductionRelease::getProductSpecificationItems($model->part_id, 0, 0);
        $model3 = ProductionRelease::getProductSpecificationItems($model->part_id, 0, 1);
        $mainProductSpecification = ProductionRelease::getProductSpecificationItems($model->part_id, 1);

        $special = $model->powerPlan?$model->powerPlan->special:0;
        $model->mixerPlan = $special;
        if($special > 0){
          $model->planQty = round($model->quantity / $special);
        }
        else{
          $model->planQty = 0;
        }
        $dataSiro = ProductionRelease::getData($model2, $model);
        $dataZames = ProductionRelease::getData($model3, $model);
        $releaseIds = ProductionRelease::getReleaseId($model->target_date);
        $dynamicModels = [ new ProductionReleaseItem()];

        $post = Yii::$app->request->post();
        if($post){
          $dynamicModels = Model::createMultiple(ProductionReleaseItem::classname());
          Model::loadMultiple($dynamicModels, $post);
          // vd($post);
          $valid = Model::validateMultiple($dynamicModels);
          if($valid){
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                foreach ($dynamicModels as $dynamicModel) {
                  $modelItem = ProductionReleaseItem::find()->where(['release_id' => $id])->andWhere(['partId' => $dynamicModel->partId])->one();
                  if(empty($modelItem)){
                    $modelItem = new ProductionReleaseItem();
                  }
                  $modelItem->setAttributes($dynamicModel->attributes);
                  if(in_array($modelItem->qty, [0, null])){
                    $modelItem->status = 1;
                  }
                  if(empty($modelItem->status)){
                    $modelItem->status = 0;
                  }
                  // vd($modelItem);
                  if (! ($flag = $modelItem->save())) {
                    $transaction->rollBack();
                    break;
                  }
                }
              if ($flag) {
                $transaction->commit();
                return $this->redirect(['view', 'id' => $model->id]);
              }
            } catch (Exception $e) {
              $transaction->rollBack();
            }
          }
        }
        return $this->render('view', compact('model', 'lines', 'shifts', 'dataSiro', 'dataZames', 'mainProductSpecification', 'id', 'releaseIds', 'dynamicModels'));  
    }

    /**
     * Creates a new ProductionRelease model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ProductionRelease();
        $selectTimes = ProductionRelease::selectTimes();
        $data = [];
        if(Yii::$app->request->isAjax){
            if ($model->load(Yii::$app->request->post())) {
                $model->part_name = $model->part->part_name;
                $model->created_by = Yii::$app->user->identity->id;
                if($model->save()){
                    $data['status'] = 1;
                   
                }else{
                    $data['errors'] = $model->errors;
                }
                Yii::$app->response->format = Response::FORMAT_JSON;
                return $data;
                
            }
            
            return $this->renderAjax('create', [
                'model' => $model,
                'selectTimes' => $selectTimes
            
            ]);
        }
        return $this->$this->redirect(['index']);
    }
    public function actionValidate($id = null) {
        $model = $id === null ? new ProductionRelease() : ProductionRelease::findOne($id);
        if(Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ActiveForm::validate($model);
        }
    }
    /**
     * Updates an existing ProductionRelease model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $selectTimes = ProductionRelease::selectTimes();
        $data = [];
        if ($model->load(Yii::$app->request->post())) {
            $model->part_name = $model->part->part_name;
            if($model->save(false)){
                $data['status'] = 1;
                return $this->redirect(['index']);
               
            }else{
                $data['errors'] = $model->errors;
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $data;
            
        }
        if(Yii::$app->request->isAjax){
            
            
            return $this->renderAjax('update', compact('model', 'selectTimes'));
        }
        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing ProductionRelease model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ProductionRelease model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProductionRelease the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProductionRelease::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
    public function actionGenerateOrderNumber()
    {
        if($post = Yii::$app->request->post()){
            $date = date('Y-m-d', strtotime($post['date']));
            $year = date('Y', strtotime($date));
            $year = substr($year, 2);
            $month = date('m', strtotime($date));
            $day = date('d', strtotime($date));
            $line = $post['line'];
            $lastId = ProductionRelease::find()->select('id')->orderBy('id DESC')->one();
            $lastId = $lastId->id + 1;
            $orderNumber = $year.$month.$day.$line.$lastId;
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $orderNumber;
        }
    }




    // 2023-07-31
    // Sanakulov Anvar
    public function actionAddFact()
    {
      $identity = Yii::$app->user->identity;
      $post = Yii::$app->request->post();
      $productionReleaseFactHistory = new ProductionReleaseFactHistory();
      Yii::$app->response->format = Response::FORMAT_JSON;


      if($post && isset($post['id']) && isset($post['fact'])){
        $productionReleaseFactHistory->releaseId  = $post['id'];
        $productionReleaseFactHistory->quantity   = $post['fact'];
        $productionReleaseFactHistory->userId     = $identity->id;
        if($productionReleaseFactHistory->save(false)){
          $model = ProductionRelease::findOne($post['id']);
          $model->fact += $post['fact'];
          if($model->save()){
            return [
              'status' => 1,
            ];
          }
        }
        
        return [
          'status' => 0,
          'errors' => $model->errors,
        ];
        
      }
    }


    // release fact history
    public function actionHistory($id)
    {
        $model = ProductionRelease::findOne($id);
        if($model){

          $history = ProductionReleaseFactHistory::find()->where(['releaseId' => $id])->all();
          if(Yii::$app->request->isAjax){
            return $this->renderAjax('history', compact('model', 'history'));
          }
          return $this->render('history', compact('model', 'history'));
        }
    }



    // close zames 
    public function actionCloseRelease($id)
    {
      $model = ProductionRelease::findOne($id);
      if($model){
        $model->status = 0;
        if($model->save()){
          return $this->redirect(['index']);
        }
      }
    }
}
