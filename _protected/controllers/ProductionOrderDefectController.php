<?php
	namespace app\controllers;

	use app\models\ProductionOrder;
	use app\models\ProductionOrderDefect;
	use app\models\ProductionOrderDefectSearch;
	use Yii;
	use yii\db\Exception;
	use yii\web\NotFoundHttpException;

	/**
		* ProductionOrderDefectController implements the CRUD actions for ProductionOrderDefect model.
		*/
	class ProductionOrderDefectController extends AppController{
		/**
			* Lists all ProductionOrderDefect models.
			* @return mixed
			*/
		public function actionIndex(){
			$searchModel = new ProductionOrderDefectSearch();
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
			return $this->render('index', [
				'searchModel' => $searchModel,
				'dataProvider' => $dataProvider,
			]);
		}

		/**
			* Displays a single ProductionOrderDefect model.
			* @param integer $id
			* @return mixed
			* @throws NotFoundHttpException if the model cannot be found
			*/
		public function actionView($id){
			return $this->render('view', [
				'model' => $this->findModel($id),
			]);
		}

		/**
			* Finds the ProductionOrderDefect model based on its primary key value.
			* If the model is not found, a 404 HTTP exception will be thrown.
			* @param integer $id
			* @return ProductionOrderDefect the loaded model
			* @throws NotFoundHttpException if the model cannot be found
			*/
		protected function findModel($id){
			if(($model = ProductionOrderDefect::findOne($id)) !== null){
				return $model;
			}
			throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
		}

		/**
			* Creates a new ProductionOrderDefect model.
			* If creation is successful, the browser will be redirected to the 'view' page.
			* @return mixed
			*/
		public function actionCreate(){
			$model = new ProductionOrderDefect();
			if(Yii::$app->request->isPost){
				$post = Yii::$app->request->post('ProductionOrderDefect');
				$order = ProductionOrder::find()->where(['serial_number' => $post['serial_number']])->one();
				if($order && isset($post['defect_id']) && count($post['defect_id']) > 0){
					$qtys = $post['qty'];
					// var_dump($qtys);
					ProductionOrderDefect::deleteAll(['production_order_id' => $order->id]);
					foreach($post['defect_id'] as $defect => $on){
						$de = new ProductionOrderDefect();
						$de->production_order_id = $order->id;
						$de->qty = $qtys[$defect];
						$de->defect_id = $defect;
						$de->save();
					}
					$model = new ProductionOrderDefect();
				}
			}
			return $this->render('_create', [
				'model' => $model,
			]);
		}

		/**
			* Updates an existing ProductionOrderDefect model.
			* If update is successful, the browser will be redirected to the 'view' page.
			* @param integer $id
			* @return mixed
			* @throws NotFoundHttpException if the model cannot be found
			*/
		public function actionUpdate($id){
			$model = $this->findModel($id);
			if($model->load(Yii::$app->request->post()) && $model->save()){
				return $this->redirect(['view', 'id' => $model->id]);
			}
			return $this->render('update', [
				'model' => $model,
			]);
		}

		/**
			* Deletes an existing ProductionOrderDefect model.
			* If deletion is successful, the browser will be redirected to the 'index' page.
			* @param integer $id
			* @return mixed
			* @throws NotFoundHttpException if the model cannot be found
			*/
		public function actionDelete($id){
			$model = $this->findModel($id);
			try{
				$model->delete();
				Yii::$app->session->setFlash('success', Yii::t('app', 'Deleted successfully'));
			}catch(Exception $e){
				if($e->errorInfo[1] == 1451){
					Yii::$app->session->setFlash('error', Yii::t('app', 'error_delete_fr_key'));
				}else{
					throw $e;
				}
			}
			return $this->redirect(['index']);
		}

		public function actionXls(){
			ini_set('memory_limit', '-1');
			$searchModel = new ProductionOrderDefectSearch();
			$xsl_file = $searchModel->search(Yii::$app->request->queryParams, 'excel');
			$xsl_file->send('order_defects_'.date("YmdHis").'.xlsx');
		}
	}
