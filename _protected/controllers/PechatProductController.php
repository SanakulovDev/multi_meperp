<?php

namespace app\controllers;

use Yii;
use app\models\PechatProduct;
use app\models\PechatProductSearch;
use app\models\Part;
use kartik\mpdf\Pdf;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use app\controllers\AppController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PechatProductController implements the CRUD actions for PechatProduct model.
 */
class PechatProductController extends AppController
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
     * Lists all PechatProduct models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PechatProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $items = PechatProduct::getPartsList();
        $colorList = PechatProduct::getPartColorList();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'items' => $items,
            'colorList' => $colorList
        ]);
    }

    /**
     * Displays a single PechatProduct model.
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
     * Creates a new PechatProduct model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PechatProduct();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }
        $items = PechatProduct::getPartsList();
        $colorList = PechatProduct::getPartColorList();
        return $this->render('create', [
            'model' => $model,
            'items' => $items,
            'colorList' => $colorList
        ]);
    }

    /**
     * Updates an existing PechatProduct model.
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
        $items = PechatProduct::getPartsList();
        $colorList = PechatProduct::getPartColorList();
        return $this->render('update', [
            'model' => $model,
            'items' => $items,
            'colorList' => $colorList
        ]);
    }

    /**
     * Deletes an existing PechatProduct model.
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

    public function actionPrint($id, $count=1)
    {
        $this->layout = 'print';
        $model = $this->findModel($id);
        // $content = $this->renderPartial('_print',[
        //    'model' => $model,
        // ]);
        // $pdf = new Pdf([
        //     'mode' => Pdf::MODE_UTF8,
        //     'format' => Pdf::FORMAT_A4, 
        //     'filename' => md5(time()).".pdf",
        //     'orientation' => Pdf::ORIENT_PORTRAIT, 
        //     'destination' => Pdf::DEST_BROWSER, 
        //     'content' => $content,  
        //     // 'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.css',
        //     'cssFile' => Yii::getAlias('@webroot') . '/css/bootstrap.min.css',
        //     'cssInline' => '.kv-heading-1{font-size:18px}', 
        //     'options' => ['title' => time()],
        // ]);
    
        // return $pdf->render();
        $url = Url::base(true);
        $url = $url . '/pechat-product/print?id=' . $id;
        return $this->render('_print', [
            'model' => $model,
            'url' => $url,
            'count' => $count
        ]);

    }
    public function actionPrintForm($id)
    {
        $model = $this->findModel($id);
        $url = Url::base(true);
        $url = $url . '/pechat-product/print?id=' . $id;
        // vd($url);
        if (Yii::$app->getRequest()->isAjax) {
            return $this->renderAjax('_print-form', [
                'model' => $model,
                'url' => $url,
            ]);
        }
        if(isset($_GET) && isset($_GET['number'])){    
            $count = $_GET['number'];
            return $this->redirect(['print', 'id' => $id, 'count' => $count]);
        }
        

    }
    
    /**
     * Finds the PechatProduct model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PechatProduct the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PechatProduct::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
