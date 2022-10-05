<?php

namespace app\controllers;

use app\components\Helpers;
use app\models\Invoice;
use Yii;
use app\models\InvoicePayment;
use app\models\InvoicePaymentSearch;
use app\models\PaymentControl;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * InvoicePaymentController implements the CRUD actions for InvoicePayment model.
 */
class InvoicePaymentController extends AppController {
	/**
	 * Lists all InvoicePayment models.
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel = new InvoicePaymentSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionCreate() {
		$model = new InvoicePayment();
		if (Yii::$app->getRequest()->isAjax) {
			if ($model->load(Yii::$app->request->post())) {
				if ($model->save()) {
					$data['status'] = 1;
					$this->fixParentStatuses($model);
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

	public function actionValidate($id = null) {
		$model = $id === null ? new InvoicePayment() : InvoicePayment::findOne($id);
		if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
			Yii::$app->response->format = Response::FORMAT_JSON;
			return ActiveForm::validate($model);
		}
	}

	/**
		* Updates an existing PaymentControl model.
		* If update is successful, the browser will be redirected to the 'view' page.
		* @param integer $id
		* @return mixed
		* @throws NotFoundHttpException if the model cannot be found
		*/
	public function actionUpdate($id) {
		$model = $this->findModel($id);
		if (Yii::$app->getRequest()->isAjax) {
			if ($model->load(Yii::$app->request->post())) {
				if ($model->save()) {
					$data['status'] = 1;
					$this->fixParentStatuses($model);
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

	private function fixParentStatuses($model) {
		// get sum of invoice_payments for given invoice
		$totalPayed = InvoicePayment::find()
			->where(['invoice_id'=>$model->invoice_id])
			->sum('amount');

		$totalSpent = InvoicePayment::find()
			->where(['payment_control_id'=>$model->payment_control_id])
			->sum('amount');
		$payment = PaymentControl::findOne($model->payment_control_id);
		
		if($payment->amount == $totalSpent) {
			$payment->is_spend = 1;
			$payment->save();
		}

		$invoice = Invoice::findOne($model->invoice_id);
		if($invoice->invoice_amount == $totalPayed) {
			$invoice->is_payed = 1;
			$invoice->save();
		}
	}

	/**
		* Deletes an existing PaymentControl model.
		* If deletion is successful, the browser will be redirected to the 'index' page.
		* @param integer $id
		* @return mixed
		* @throws NotFoundHttpException if the model cannot be found
		*/
	public function actionDelete($id) {
		Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
		$model = InvoicePayment::find()->where(['id' => $id])->one();
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
	 * Finds the InvoicePayment model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * @param integer $id
	 * @return InvoicePayment the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id) {
		if (($model = InvoicePayment::findOne($id)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
    
    private function loadDictionaries() {
        $payments = ArrayHelper::map(PaymentControl::find()->all(), 'id', 'no');
        $invoices = ArrayHelper::map(Invoice::find()->all(), 'id', 'invoice_no');
        return compact('invoices','payments');
    }

    public function actionXls()
    {
        ini_set('memory_limit', '-1');
        $searchModel = new InvoicePaymentSearch();
        $xsl_file = $searchModel->search(Yii::$app->request->queryParams, 'excel');
        $xsl_file->send(Helpers::downloadFileName('invoice_payment'));
    }
}
