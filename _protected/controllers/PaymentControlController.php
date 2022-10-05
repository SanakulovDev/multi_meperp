<?php

namespace app\controllers;

use app\components\Helpers;
use app\models\Contract;
use app\models\PaymentControl;
use app\models\PaymentControlSearch;
use app\models\PaymentType;
use app\models\Supplier;
use Codeception\Lib\Generator\Helper;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
		* PaymentControlController implements the CRUD actions for PaymentControl model.
		*/
	class PaymentControlController extends AppController {
		/**
			* Lists all PaymentControl models.
			* @return mixed
			*/
		public function actionIndex() {
			$searchModel = new PaymentControlSearch();
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
			$dataProvider->sort->defaultOrder = ['date' => SORT_DESC];

			$contracts = ArrayHelper::map(Contract::find()->all(), 'id', 'contract_no');
			$suppliers = ArrayHelper::map(Supplier::find()->all(), 'id', 'name');
			$payment_types = ArrayHelper::map(PaymentType::find()->all(), 'id', 'title');
			return $this->render('index', [
				'searchModel' => $searchModel,
				'dataProvider' => $dataProvider,
				'contracts' => $contracts,
				'suppliers' => $suppliers,
				'payment_types' => $payment_types,
			]);
		}

		/**
			* Creates a new PaymentControl model.
			* If creation is successful, the browser will be redirected to the 'view' page.
			* @return mixed
			*/
		public function actionCreate() {
			$model = new PaymentControl();
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

		public function actionValidate($id = null) {
			$model = $id === null ? new PaymentControl() : PaymentControl::findOne($id);
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
			$model->amount = Helpers::numberFormatRemoveZero($model->amount, 2,'.','');
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
			* Deletes an existing PaymentControl model.
			* If deletion is successful, the browser will be redirected to the 'index' page.
			* @param integer $id
			* @return mixed
			* @throws NotFoundHttpException if the model cannot be found
			*/
		public function actionDelete($id) {
			Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
			$model = PaymentControl::find()->where(['id' => $id])->one();
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
			* Finds the PaymentControl model based on its primary key value.
			* If the model is not found, a 404 HTTP exception will be thrown.
			* @param integer $id
			* @return PaymentControl the loaded model
			* @throws NotFoundHttpException if the model cannot be found
			*/
		protected function findModel($id) {
			if (($model = PaymentControl::findOne($id)) !== null) {
				return $model;
			}
			throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
		}

		private function loadDictionaries() {
			$suppliers = ArrayHelper::map(Supplier::find()->all(), 'id', 'name');
			return compact('suppliers');
		}

		public function actionXls()
		{
			ini_set('memory_limit', '-1');
			$searchModel = new PaymentControlSearch();
			$xsl_file = $searchModel->search(Yii::$app->request->queryParams, 'excel');
			$xsl_file->send(Helpers::downloadFileName('payments'));
		}
	}
