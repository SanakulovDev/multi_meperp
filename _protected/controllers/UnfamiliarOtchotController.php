<?php

namespace app\controllers;

use app\models\Part;
use Yii;
use app\models\UnfamiliarOtchot;
use app\models\UnfamiliarOtchotSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * UnfamiliarOtchotController implements the CRUD actions for UnfamiliarOtchot model.
 */
class UnfamiliarOtchotController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all UnfamiliarOtchot models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UnfamiliarOtchotSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'list'  => $this->partList()
        ]);
    }

    /**
     * Displays a single UnfamiliarOtchot model.
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
     * Creates a new UnfamiliarOtchot model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UnfamiliarOtchot();
        $list = $this->partList();
        if(Yii::$app->request->isAjax){
            return $this->renderAjax('_form', [
                'model' => $model,
                'list'  =>  $list
            ]);
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('_form', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing UnfamiliarOtchot model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $list = $this->partList();
        if(Yii::$app->request->isAjax){
            return $this->renderAjax('_form', [
                'model' => $model,
                'list'  =>  $list
            ]);
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('_form', [
            'model' => $model,
        ]); 
    }

    /**
     * Deletes an existing UnfamiliarOtchot model.
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
    
    public function actionDeleteAll()
    {
        UnfamiliarOtchot::deleteAll();

        return $this->redirect(['index']);
    }

    /**
     * Finds the UnfamiliarOtchot model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UnfamiliarOtchot the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UnfamiliarOtchot::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
    private function partList()
    {
        $partList = Part::find()->where(['status' => Part::STATUS_ACTIVE])->andWhere(['<>', 'state', 0])->all();
        return ArrayHelper::map($partList, 'id', function ($model) {
            return $model->part_no . ' - ' . $model->part_name . ' (' . $model->part_color . ')'; // Qo'shimcha ustunlarni birlashtirish
        });
    }

    public function actionExportExcel()
    {
        
    }
 


}
