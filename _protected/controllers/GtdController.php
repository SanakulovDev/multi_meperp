<?php
	namespace app\controllers;

	use app\components\Helpers;
	use app\models\Gtd;
	use app\models\GtdInvoice;
	use app\models\GtdSearch;
	use app\models\Invoice;
	use Yii;
	use yii\db\Exception;
	use yii\db\Query;
	use yii\web\NotFoundHttpException;
	use yii\web\Response;

	/**
	 * GtdController implements the CRUD actions for Gtd model.
	 */
	class GtdController extends AppController{
		public function actionIndex(){
			$searchModel = new GtdSearch();
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
			$dataProvider->sort->defaultOrder = ['gtd_dt' => SORT_DESC];
			return $this->render('index', [
				'searchModel' => $searchModel,
				'dataProvider' => $dataProvider,
			]);
		}

		public function actionXls(){
			ini_set('memory_limit', '-1');
			$searchModel = new GtdSearch();
			$xsl_file = $searchModel->search(Yii::$app->request->queryParams, 'excel');
			$xsl_file->send(Helpers::downloadFileName('gtd'));
		}

		/**
		 * Displays a single Gtd model.
		 * @param int $id
		 * @return mixed
		 * @throws NotFoundHttpException if the model cannot be found
		 */
		public function actionView($id){
			return $this->render('view', [
				'model' => $this->findModel($id),
			]);
		}

		public function actionCreate(){
			$model = new Gtd();
			$errorlist = '';
			if($model->load(Yii::$app->request->post())){
				if(isset($_POST['items'])){
					$items = $_POST['items'];
					if((count($items['inv_no']) < 1)){
						$errorlist = Yii::t('app', 'You must select at least one invoice.');
						return $this->render('create', [
							'errorlist' => $errorlist ?? null,
							'model' => $model,
						]);
					}
					$transaction = Yii::$app->db->beginTransaction();
					$gtd_exist = Gtd::findone(['gtd_no' => $model->gtd_no, 'gtd_dt' => $model->gtd_dt]);
					if(isset($gtd_exist)){
						$gtd_id = $gtd_exist->id;
					}else{
						$model->created_by = Yii::$app->user->id;
						$model->created_at = time();
						if($model->save()){
							$gtd_id = $model->id;
						}else{
							$transaction->rollback();
							$errorlist = Yii::t('app', 'Error Insert: '.$model->errors);
							return $this->render('create', [
								'errorlist' => $errorlist ?? null,
								'model' => $model,
							]);
						}
					}
					foreach($items['inv_no'] as $key => $value){
						if($items['amount'][$key] < 1){
							$transaction->rollback();
							$errorlist = Yii::t('app', 'Amount cannot be blank.');
							return $this->render('create', [
								'errorlist' => $errorlist ?? null,
								'model' => $model,
							]);
						}
						$gtd_invoice = new GtdInvoice();
						$gtd_invoice->gtd_id = $gtd_id;
						$gtd_invoice->invoice_id = $items['inv_no'][$key];
						$gtd_invoice->amount = $items['amount'][$key];
						$gtd_invoice->created_by = Yii::$app->user->id;
						$gtd_invoice->created_at = time();
						if(!$gtd_invoice->save()){
							$errorlist = $gtd_invoice->errors;
							$err = "<pre>Error:".print_r($gtd_invoice->errors)."</pre>";
							Yii::$app->session->setFlash('success', $err);
							$transaction->rollback();
							return $this->render('create', [
								'errorlist' => $errorlist ?? null,
								'model' => $model,
							]);
						}
					}
					$transaction->commit();
					Yii::$app->session->setFlash('success', Yii::t('app', 'FG Invoice created successfully.'));
					return $this->redirect(['index', 'model' => $model]);
				}else{
					$errorlist = Yii::t('app', 'You must select at least one invoice.');
					return $this->render('create', [
						'errorlist' => $errorlist ?? null,
						'model' => $model,
					]);
				}
			}
			return $this->render('create', ['model' => $model, 'errorlist' => $errorlist]);
		}
	
		public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
						$model->updated_by = Yii::$app->user->id;
						$model->updated_at = time();
            if ($model->save()) {
                return $this->redirect('index');
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

		/**
		 * Deletes an existing Gtd model.
		 * If deletion is successful, the browser will be redirected to the 'index' page.
		 * @param int $id
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

		/**
		 * Finds the Gtd model based on its primary key value.
		 * If the model is not found, a 404 HTTP exception will be thrown.
		 * @param int $id
		 * @return Gtd the loaded model
		 * @throws NotFoundHttpException if the model cannot be found
		 */
		protected function findModel($id){
			if(($model = Gtd::findOne($id)) !== null){
				return $model;
			}
			throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
		}

		public function actionInvoiceList($q = null, $id = null, $invoive_ids = null){
			Yii::$app->response->format = Response::FORMAT_JSON;
			$out = ['results' => ['id' => '', 'text' => '']];
			$query = new Query();
			$query->select(['id, CONCAT(invoice_no, " (" , IFNULL(invoice_date,"-"),")") AS text'])->from('invoice');
			$query->andWhere('invoice.id not in('.$invoive_ids.')');
			if($id > 0){
				$invoive_list = Invoice::findOne(['id' => $id]);
				$out['results'] = ['id' => $id, 'text' => $invoive_list->invoice_no."(".$invoive_list->invoice_date.")"];
			}elseif(!is_null($q)){
				$query->where(['like', 'invoice_no', $q]);
			}
			$command = $query->createCommand();
//			echo "<pre>1:"; print_r($command->rawSql);echo "</pre>";
//			die;
			$data = $command->queryAll();
			$out['results'] = array_values($data);
			return $out;
		}

		public function actionInvoiceData(){
			$post = Yii::$app->request->post();
			Yii::$app->response->format = Response::FORMAT_JSON;
			$out['supplier'] = Invoice::findOne($post['invoice_id'])->supplier->name;
			return $out;
		}

	}
