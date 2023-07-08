<?php

namespace app\controllers;

use Yii;
use app\models\ProductionRelease;
use app\models\ProductionOrder;
use app\models\Part;
use app\models\ProductionReleaseSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
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
        $lines  = ProductionOrder::getLines();
        $shifts = ProductionOrder::getShifts();
        $model = $this->findModel($id);
        return $this->render('view', compact('model', 'lines', 'shifts'));
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
}
