<?php
	namespace app\controllers;

use app\components\Helpers;
use app\models\Container;
	use app\models\ContainerInvoice;
use app\models\Currency;
use app\models\Invoice;
	use app\models\InvoiceSearch;
	use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
		* InvoiceController implements the CRUD actions for Invoice model.
		*/
	class InvoiceController extends AppController{
		/**
			* Lists all Invoice models.
			* @return mixed
			*/
		public function actionIndex(){
			$searchModel = new InvoiceSearch();
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
			return $this->render('index', [
				'searchModel' => $searchModel,
				'dataProvider' => $dataProvider,
			]);
		}

		/**
			* Displays a single Invoice model.
			* @param integer $id
			* @return mixed
			* @throws NotFoundHttpException if the model cannot be found
			*/
//		public function actionView($id){
//			return $this->render('view', [
//				'model' => $this->findModel($id),
//			]);
//		}

		/**
			* Creates a new Invoice model.
			* If creation is successful, the browser will be redirected to the 'view' page.
			* @return mixed
			*/
		public function actionCreateInvCont(){
			$invoice = new Invoice();
			$modelContainer = new Container();
			$modelContainerInvoice = new ContainerInvoice();
			if($invoice->load(Yii::$app->request->post())){
				if(count($_POST[items]['container']) < 2){
					$errorlist = [
						'no_item' => [
							[Yii::t('app', 'You must select at least one container.')]
						]
					];
					return $this->render('create-inv-cont',
					                     [
						                     'errorlist' => $errorlist ?? null,
						                     'items' => $_POST[items],
						                     'model' => $invoice,
						                     'modelContainer' => $modelContainer,
						                     'modelContainerInvoice' => $modelContainerInvoice,
						                     'modelItems' => $modelItems ?? null,
					                     ]);
				}
				$transaction = Yii::$app->db->beginTransaction();
				//invoice bazani tekshirib yo`q bo`lsa, qo`shib kelish
				$invoice = Invoice::find()->where(['invoice_no' => $_POST['Invoice']['invoice_no']])->one();
				if($invoice == null){
					$invoice = new Invoice();
					$invoice->invoice_no = $_POST['Invoice']['invoice_no'];
					$invoice->created_by = Yii::$app->user->id;
					$invoice->created_at = time();
					if($invoice->save()){
						$invoice_id = $invoice->id;
					}else{
						$errorlist['err_invoice'] = $invoice->errors;
					}
				}
				$invoice_id = $invoice->id;
				if(count($_POST[items]['container']) > 1){
					foreach($_POST[items]['container'] as $key => $value){
						if($key == 0)
							continue;
						//container bazani tekshirib yo`q bo`lsa, qo`shib kelish
						$container = Container::find()->where(['container_no' => $_POST[items]['container'][$key]])->one();
						if($container == null){
							$container = new Container();
							$container->container_no = $_POST[items]['container'][$key];
							$container->created_by = Yii::$app->user->id;
							$container->created_at = time();
							if($container->save()){
								$container_id = $container->id;
							}else{
								$errorlist['err_container'] = $container->errors;
							}
						}
						$container_id = $container->id;
						$item = new ContainerInvoice();
						$item->invoice_id = $invoice_id;
						$item->container_id = $container_id;
						$item->shipped_at = $_POST[items]['ship_dt'][$key];
						$item->shipped_by = Yii::$app->user->id;
						if(!$item->save()){
							$errorlist[$_POST[items]['num'][$key]] = $item->errors;
						}
					}
				}
				if(count($errorlist) == 0){
					$transaction->commit();
					return $this->redirect(['index']);
				}else{
					$transaction->rollBack();
					return $this->render('create-inv-cont', [
						'errorlist' => $errorlist ?? null,
						'items' => $_POST[items],
						'model' => $invoice,
						'modelContainer' => $modelContainer,
						'modelContainerInvoice' => $modelContainerInvoice,
						'modelItems' => $modelItems ?? null,
					]);
				}
			}else{
				return $this->render('create-inv-cont', [
					'errorlist' => $errorlist ?? null,
					'items' => $_POST[items],
					'model' => $invoice,
					'modelContainer' => $modelContainer,
					'modelContainerInvoice' => $modelContainerInvoice,
					'modelItems' => $modelItems ?? null,
				]);
			}
		}

		/**
			* Updates an existing Invoice model.
			* If update is successful, the browser will be redirected to the 'view' page.
			* @param integer $id
			* @return mixed
			* @throws NotFoundHttpException if the model cannot be found
			*/
		public function actionUpdate($id){
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
					return $this->renderAjax('_update', array_merge(['model' => $model], self::loadDictionaries()));
				}
			} else {
				return $this->redirect(['index']);
			}
		}

		/**
			* Deletes an existing Invoice model.
			* If deletion is successful, the browser will be redirected to the 'index' page.
			* @param integer $id
			* @return mixed
			* @throws NotFoundHttpException if the model cannot be found
			*/
		public function actionDelete($id){
			$invoice = $this->findModel($id);
			$container = $invoice->containers;
			$invoice_no = $invoice->invoice_no;
			$container_no = $container[0]['container_no'];
			echo "<pre>";
			var_dump($container_no);
			echo "</pre>";
			die;
			return $this->redirect(['index']);
		}

		/**
			* Finds the Invoice model based on its primary key value.
			* If the model is not found, a 404 HTTP exception will be thrown.
			* @param integer $id
			* @return Invoice the loaded model
			* @throws NotFoundHttpException if the model cannot be found
			*/
		protected function findModel($id){
			if(($model = Invoice::findOne($id)) !== null){
				return $model;
			}
			throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
		}

		public function actionValidate($id = null) {
			$model = $id === null ? new Invoice() : Invoice::findOne($id);
			if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
				Yii::$app->response->format = Response::FORMAT_JSON;
				return ActiveForm::validate($model);
			}
		}

		public function actionXls()
		{
			ini_set('memory_limit', '-1');
			$searchModel = new InvoiceSearch();
			$xsl_file = $searchModel->search(Yii::$app->request->queryParams, 'excel');
			$xsl_file->send(Helpers::downloadFileName('invoiced'));
		}

		private function loadDictionaries() {
			$currencies = ArrayHelper::map(Currency::find()->all(), 'id', 'code');
			return compact('currencies');
		}
	}
