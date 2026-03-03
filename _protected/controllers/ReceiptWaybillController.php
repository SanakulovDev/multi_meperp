<?php

namespace app\controllers;

use app\components\Helpers;
use app\models\Waybill;
use Yii;
use app\models\ReceiptWaybill;
use app\models\ReceiptWaybillSearch;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * ReceiptWaybillController implements the CRUD actions for ReceiptWaybill model.
 */
class ReceiptWaybillController extends AppController
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
     * Lists all ReceiptWaybill models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ReceiptWaybillSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', compact('searchModel', 'dataProvider'));
    }

    public function actionValidate($id = null) {
      $model = $id === null ? new ReceiptWaybill() : ReceiptWaybill::findOne($id);
      if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return ActiveForm::validate($model);
      }
    }

    /**
     * Creates a new ReceiptWaybill model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
      $model = new ReceiptWaybill();
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
          return $this->renderAjax('_form', array_merge(['model' => $model], self::loadDictionaries()));
        }
      } else {
        return $this->redirect(['index']);
      }
    }

    /**
     * Updates an existing ReceiptWaybill model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
      $model = $this->findModel($id);
      $model->amount = Helpers::numberFormatRemoveZero($model->amount, 2, '.', '');
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
          return $this->renderAjax('_form', array_merge(['model' => $model], self::loadDictionaries()));
        }
      } else {
        return $this->redirect(['index']);
      }
    }

    /**
     * Deletes an existing ReceiptWaybill model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
      Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
      $model = ReceiptWaybill::find()->where(['id' => $id])->one();
      if ($model && $model->delete()) {
        return [
          'status' => 1
        ];
      }

      return [
        'status' => 0
      ];
    }

    /**
     * Finds the ReceiptWaybill model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ReceiptWaybill the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ReceiptWaybill::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    private function loadDictionaries() {
      $notPayedWaybills = Waybill::find()
                                   ->joinWith('receiptWaybills')
                                   ->where(['receipt_waybill.id' => null])
                                   ->all();
      $waybills = ArrayHelper::map($notPayedWaybills, 'id', 'waybill_no');
      return compact('waybills');
    }

    public function actionXls() {
      ini_set('memory_limit', '-1');
      $searchModel = new ReceiptWaybillSearch();
      $xsl_file = $searchModel->search(Yii::$app->request->queryParams, 'excel');
      $xsl_file->send(Helpers::downloadFileName('receipt-waybill'));
    }
}
