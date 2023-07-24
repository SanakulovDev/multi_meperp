<?php

namespace app\controllers;

use Yii;
use app\models\ProductionPower;
use app\models\ProductionPowerDynamic;
use app\models\ProductionOrder;
use app\models\Part;
use app\models\ProductionRelease;
use app\models\ProductionPowerSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Model;
use app\models\Unit;
/**
 * ProductionPowerController implements the CRUD actions for ProductionPower model.
 */
class ProductionPowerController extends Controller
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
     * Lists all ProductionPower models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProductionPowerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $parts  = Part::find()
            ->select(['id', 'concat(part_no, " ", part_name) as part_no'])
            ->where(['status' => Part::STATUS_ACTIVE])->all();
        $parts  = ArrayHelper::map($parts, 'id', 'part_no');
        $lines  = ProductionOrder::getLines();
        $shifts = ProductionOrder::getShifts();
        $units  = ArrayHelper::map(Unit::find()->all(), 'id', 'unit_value');
        return $this->render('index', compact('searchModel', 'dataProvider', 'parts', 'shifts', 'lines', 'units'));
    }

    /**
     * Displays a single ProductionPower model.
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
     * Creates a new ProductionPower model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {

        $modelMain = new ProductionPowerDynamic();
        $models = [new ProductionPower()];
        $selectTimes = ProductionRelease::selectTimes();
        $data = [];
        if(Yii::$app->request->isAjax){
            if ($modelMain->load(Yii::$app->request->post())) {
                $models = Model::createMultiple(ProductionPower::classname());
                Model::loadMultiple($models, Yii::$app->request->post());
                $valid = $modelMain->validate();
                $valid = Model::validateMultiple($models);
                $valid = true;

                if($valid){
                    $transaction = \Yii::$app->db->beginTransaction();
          
                    try{
                      $flag = true;
                        foreach ($models as $index => $item) {
                            
                            $item->part_id      = $modelMain->part_id;
                            $item->part_name    = $item->part->part_name;
                            $item->test_pr      = $modelMain->test_pr;
                            $item->target_date  = $modelMain->target_date;
                            $item->created_by   = Yii::$app->user->identity->id;
                            $res = ProductionPower::alreadyExists($item);
                            if($res && ($flag = $item->save(false))){
                                $data['status'] = 1;
                            }else{
                                $data['status'] = 0;
                                $data['errors'] = 'Data already exists';
                                break;
                            }
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
                
            }
            
            return $this->renderAjax('_form', [
                'modelMain' => $modelMain,
                'models' => (empty($models)) ? [new ProductionPower()] : $models,   
                'selectTimes' => $selectTimes,
            ]);
        }
        return $this->$this->redirect(['index']);
    }

    /**
     * Updates an existing ProductionPower model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionValidate($id = null) {
        $model = $id === null ? new ProductionPower() : ProductionPower::findOne($id);
        if(Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $models = Model::createMultiple(ProductionPower::classname());
        Model::loadMultiple($models, Yii::$app->request->post());
        
        return ActiveForm::validate($model);
        }

    }
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $data = [];
    
        if ($model->load(Yii::$app->request->post())) {
            if($model->save()){
                return $this->redirect(['index']);
            }
            
        }
        if(Yii::$app->request->isAjax){
            
            
            
            return $this->renderAjax('_form_update', [
                'model' => $model,
            ]);
        }
        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing ProductionPower model.
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
     * Finds the ProductionPower model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProductionPower the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProductionPower::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
