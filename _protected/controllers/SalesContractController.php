<?php
	namespace app\controllers;

	use app\components\Helpers;
	use app\models\SalesContractDetail;
	use app\models\SalesContract;
	use app\models\SalesContractSearch;
	use Yii;
	use yii\web\NotFoundHttpException;
	use yii\web\Response;

	/**
		* SalesContractController implements the CRUD actions for SalesContract model.
		*/
	class SalesContractController extends AppController{

		/**
			* Lists all SalesContract models.
			* @return mixed
			*/
		public function actionIndex(){
			$searchModel = new SalesContractSearch();
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
			return $this->render('index', [
				'searchModel' => $searchModel,
				'dataProvider' => $dataProvider,
			]);
		}

		public function actionCreate(){
			$model = new SalesContract();
			$errorlist = [];
			$isNewRecord = true;
			if($model->load(Yii::$app->request->post())){
				$transaction = Yii::$app->db->beginTransaction();
				if($model->save()){
					if(count($errorlist) == 0){
						$transaction->commit();
						Yii::$app->session->setFlash('success', Yii::t('app', 'Sales contract created successfully.'));
						return $this->redirect(['/sales-contract/update?id=' . $model->id]);
					}else{
						$transaction->rollBack();
						return $this->render('create', [
							'errorlist' => ['details' => $errorlist],
							'model' => $model,
							'isNewRecord' => $isNewRecord
						]);
					}
				}else{
					return $this->render('create', [
						'model' => $model,
						'isNewRecord' => $isNewRecord
					]);
				}
			}else{
				return $this->render('create', [
					'model' => $model,
					'isNewRecord' => $isNewRecord
				]);
			}
		}

		public function actionUpdate($id){
			$detail = new SalesContractDetail();
			$model = $this->findModel($id);
			$errorlist = [];

			$arr = [];
			if ($model->status) {
				for ($x = 0; $x < $model->status; $x++) {
					$arr[$x] = $x + 1;
				  }
			}
			if($model->load(Yii::$app->request->post())){
				$transaction = Yii::$app->db->beginTransaction();
				if($model->save()){
					if(count($errorlist) == 0){
						$transaction->commit();
						Yii::$app->session->setFlash('success', Yii::t('app', 'Sales contract changed successfully.'));
						return $this->redirect(['/sales-contract/update?id=' . $id]);
					}else{
						$transaction->rollBack();
						return $this->render('update', [
							'errorlist' => ['details' => $errorlist],
							'model' => $model,
							'detail' => $detail,
							'status' => $arr
						]);
					}
				}else{
					return $this->render('update', [
						'model' => $model,
						'detail' => $detail,
						'status' => $arr
					]);
				}
			}else{
				return $this->render('update', [
					'model' => $model,
					'detail' => $detail,
					'status' => $arr
				]);
			}
		}

		/**
			* Deletes an existing SalesContract model.
			* If deletion is successful, the browser will be redirected to the 'index' page.
			* @param integer $id
			* @return mixed
			* @throws NotFoundHttpException if the model cannot be found
			*/
		public function actionDelete($id){
			$model = $this->findModel($id);
			if($model->delete()){
				Yii::$app->session->setFlash('success', Yii::t('app', 'Sales contract removed successfully.'));
			}else{
				Yii::$app->session->setFlash('error', Yii::t('app', 'Error! Sales contract not removed.'));
			}
			return $this->redirect(['index']);
		}

		public function actionView($id){
			return $this->render('view', [
				'model' => $this->findModel($id),
			]);
		}

		public function actionXls(){
			ini_set('memory_limit', '-1');
			$searchModel = new SalesContractSearch();
			$xsl_file = $searchModel->search(Yii::$app->request->queryParams, 'excel');
			if(is_array($xsl_file->sheets['SalesContract']['data']) and count($xsl_file->sheets['SalesContract']['data']) == 0)
				return $this->redirect(['index']);
			$xsl_file->send(Helpers::downloadFileName('sales-contract'));
		}

		/**
			* Finds the SalesContract model based on its primary key value.
			* If the model is not found, a 404 HTTP exception will be thrown.
			* @param integer $id
			* @return SalesContract the loaded model
			* @throws NotFoundHttpException if the model cannot be found
			*/
		protected function findModel($id){
			if(($model = SalesContract::findOne($id)) !== null){
				return $model;
			}
			throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
		}

		public function actionListBySalesSupplier($id){
			Yii::$app->response->format = Response::FORMAT_JSON;
			$list = SalesContract::find()->where(['customer_id' => $id, 'status'=>1])->all();
			$data = [];
			foreach($list as $item){
				$data[] = ['id' => $item->id, 'text' => $item->contract_no];
			}
			return $data; // ArrayHelper::map(Contract::find()->where(['supplier_id'=>$id])->all(),'id','contract_no');
		}

	}
