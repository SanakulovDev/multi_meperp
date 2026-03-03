<?php

namespace app\controllers;

use Yii;
use app\models\LocationType;
use app\models\LocationTypeSearch;
use yii\bootstrap\ActiveForm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * LocationTypeController implements the CRUD actions for LocationType model.
 */
class LocationTypeController extends Controller
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
     * Lists all LocationType models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LocationTypeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', array_merge([
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]));
    }

    /**
     * Displays a single LocationType model.
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
     * Creates a new LocationType model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new LocationType();
        
        if(Yii::$app->getRequest()->isAjax){
            if($model->load(Yii::$app->request->post())){
                if($model->save()){
                    $data['status'] = 1;
                }else{
                    $data['status'] = 0;
                    $data['errors'] = $model->getErrors();
                }
                Yii::$app->response->format = Response::FORMAT_JSON;
                return $data;
            }else{
                return $this->renderAjax('_form', ['model' => $model]);
            }
        }else{
            return $this->redirect(['index']);
        }
    }

    /**
     * Updates an existing LocationType model.
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

        return $this->renderAjax('_form', array_merge([
                                                          'model' => $model,
                                                      ]));
    }

    /**
     * Deletes an existing LocationType model.
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
     * Finds the LocationType model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return LocationType the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = LocationType::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionValidate($id = null){
        $model = $id === null ? new LocationType() : LocationType::findOne($id);
        if(Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
    }
}
