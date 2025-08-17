<?php

namespace app\controllers;

use Yii;
use app\models\FormulationBase;
use app\models\FormulationBaseSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Part;
use yii\web\Response;

/**
 * FormulationBaseController implements the CRUD actions for FormulationBase model.
 */
class FormulationBaseController extends Controller
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
     * Lists all FormulationBase models.
     * @return mixed
     */
    public function actionIndex()
    {

        $searchModel = new FormulationBaseSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single FormulationBase model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {

        $model = $this->findModel($id);
        
        $specificList = json_decode($model->specifications);
        $titleList = json_decode($model->items);

        return $this->render('view', [
            'model' => $model,
            'titleList'=>$titleList,
            'specificList'=>$specificList
        ]);
    }

    /**
     * Creates a new FormulationBase model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $url_items = '_protected/assets/js/titleList.json';
        $data_items = file_get_contents($url_items); 
        $titleList = json_decode($data_items);

        $url_specific = '_protected/assets/js/specificList.json';
        $data_specific = file_get_contents($url_specific); 
        $specificList = json_decode($data_specific);        

        $model = new FormulationBase();        
        

        if($model->load(Yii::$app->request->post())){
            // echo "<pre>1: ";
            // print_r($model);
            // echo "</pre>";
            // die;
            if($model->save()){
                return $this->redirect(['index']);
            }else{
                $err = $model->getErrors();
                echo "<pre>1: ";
                print_r($err);
                echo "</pre>";
                die;
           }
        }

        return $this->render('create', [
            'model' => $model,
            'titleList'=>$titleList,
            'specificList'=>$specificList
        ]);
    }

    public function actionInfo($fb_id) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return FormulationBase::find()->where(['id'=>$fb_id])->one();
    }

    /**
     * Updates an existing FormulationBase model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        
        $specificList = json_decode($model->specifications);
        $titleList = json_decode($model->items); 

        // if($model->load(Yii::$app->request->post())){
        //     echo "<pre>1: ";
        //     print_r($model);
        //     echo "</pre>";
        //     die;
        // }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'titleList'=>$titleList,
            'specificList'=>$specificList
        ]);
    }

    /**
     * Deletes an existing FormulationBase model.
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
     * Finds the FormulationBase model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return FormulationBase the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = FormulationBase::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
