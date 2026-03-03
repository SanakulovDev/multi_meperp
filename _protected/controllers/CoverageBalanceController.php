<?php

namespace app\controllers;

use app\models\CountryCode;
use Yii;
use app\models\CoverageBalance;
use app\models\CoverageBalanceSearch;
use app\models\PaymentTerm;
use app\models\Supplier;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/**
 * CoverageBalanceController implements the CRUD actions for CoverageBalance model.
 */
class CoverageBalanceController extends AppController
{
    /**
     * Lists all CoverageBalance models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CoverageBalanceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $countries = ArrayHelper::map(CountryCode::find()->all(), 'id','name');
        $suppliers = ArrayHelper::map(Supplier::find()->all(), 'id','name');
        $paymentTerms = ArrayHelper::map(PaymentTerm::find()->all(), 'id','name');
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'suppliers' => $suppliers,
            'countries' => $countries,
            'paymentTerms' => $paymentTerms,
        ]);
    }

    public function actionValidate($id = null){
        $model = $id === null ? new CoverageBalance() : CoverageBalance::findOne($id);
        if(Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
    }

    /**
     * Updates an existing CoverageBalance model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        
        $model = $this->findModel($id);
        if (Yii::$app->getRequest()->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    $data['status'] = 1;
                } else {
                    $data['status'] = 0;
                    $data['errors'] = $model->getErrors();
                }
                Yii::$app->response->format = Response::FORMAT_JSON;

                return $data;
            } else {
                return $this->renderAjax('_form', [ 'model' => $model ]);
            }
        } else {
            return $this->redirect(['index']);
        }
    }

    /**
     * Finds the CoverageBalance model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CoverageBalance the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CoverageBalance::find()->with('supplier')->where(['id'=>$id])->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
