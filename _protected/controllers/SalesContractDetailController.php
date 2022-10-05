<?php
	namespace app\controllers;

	use app\models\DeliveryTerm;
	use app\models\Part;
	use app\models\SalesContractDetail;
	use app\models\SalesContractDetailSearch;
	use app\models\SalesContractDetailUploadForm;
	use Yii;
	use yii\db\Exception;
	use yii\web\NotFoundHttpException;
	use yii\web\UploadedFile;

	/**
		* SalesContractDetailController implements the CRUD actions for SalesContractDetail model.
		*/
	class SalesContractDetailController extends AppController{
		public function actionIndex(){
			$searchModel = new SalesContractDetailSearch();
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
			return $this->render('index', [
				'searchModel' => $searchModel,
				'dataProvider' => $dataProvider,
			]);
		}

		/**
			* Creates a new SalesContractDetail model.
			* If creation is successful, the browser will be redirected to the 'view' page.
			* @return mixed
			*/
		public function actionCreate(){
			$model = new SalesContractDetail();
			if($model->load(Yii::$app->request->post()) && $model->save()){
				return $this->redirect(['index']);
			}
			return $this->render('create', [
				'model' => $model,
			]);
		}

		public function actionUpload(){
      $model = new SalesContractDetailUploadForm();
			if($model->load(Yii::$app->request->post())){
				$uploadedFile = UploadedFile::getInstance($model, 'file');
				$data = $model->readExcel($uploadedFile->tempName);
				$emptyCells = [];
				$notExistsParts = [];
				$notExistsTerms = [];
				$modelErrors = [];
				foreach($data['values'] as $key => $row){
					if(empty($row['part_no']) or empty($row['price']) or empty($row['delivery_term'])){
						$emptyCells[] = Yii::t('app', 'Row').' '.($key + 2).'. '.Yii::t('app', 'Empty data.');
					}else{
						if(!in_array(trim($row['part_no']), Part::getPartNumbers())){
							$notExistsParts[] = Yii::t('app', 'Row').' '.($key + 2).'. '.Yii::t('app', 'Part not found.').'  ('.$row['part_no'].')';
						}else{
							$part_numbers[] = $row['part_no'];
						}
						if(!in_array(trim($row['delivery_term']), DeliveryTerm::getTermNames())){
							$notExistsTerms[] = Yii::t('app', 'Row').' '.($key + 2).'. '.Yii::t('app', 'Delivery term not found.').'  ('.$row['delivery_term'].')';
						}
					}
				}
				if(count($emptyCells) > 0){
					$message = 'Empty cells found in uploaded file.';
					$errors = implode('<br>', $emptyCells);
					Yii::$app->session->setFlash('error', '<b>'.Yii::t('app', $message).'</b>'.'<br>'.$errors);
					return $this->render('upload', ['model' => $model]);
				}
				if(count($notExistsParts) > 0){
					$message = 'These parts are not found in active parts list.';
					$errors = implode('<br>', $notExistsParts);
					Yii::$app->session->setFlash('error', '<b>'.Yii::t('app', $message).'</b>'.'<br>'.$errors);
					return $this->render('upload', ['model' => $model]);
				}
				if(count($notExistsTerms) > 0){
					$message = 'These delivery terms are not found in delivery terms list.';
					$errors = implode('<br>', $notExistsTerms);
					Yii::$app->session->setFlash('error', '<b>'.Yii::t('app', $message).'</b>'.'<br>'.$errors);
					return $this->render('upload', ['model' => $model]);
				}
				$transaction = Yii::$app->db->beginTransaction();
				foreach($data['values'] as $key => $row){
					// Agar table da contract_id, delivery_term_id avval tushgan bo'lsa update qilamiz
					// aks holda create qilamiz
					$part_id = Part::findOneByPartNumber($row['part_no'])->id;
					$delivery_term_id = DeliveryTerm::findOneByTermName($row['delivery_term'])->id;
					$contractDetail = SalesContractDetail::find()->where([
						                                                     'sales_contract_id' => $model->sales_contract_id,
						                                                     'part_id' => $part_id,
						                                                     'delivery_term_id' => $delivery_term_id
					                                                     ])->one();
					if($contractDetail){
						// ma'lumot bor
						$contractDetail->price = $row['price'];
            $contractDetail->vat = $row['vat'];
            $contractDetail->excise = $row['excise'];
						if(!$contractDetail->save()){
							$updateErrors = [];
							foreach($contractDetail->errors as $value){
								foreach($value as $val){
									$updateErrors[] = $val;
								}
							}
							$modelErrors[] = Yii::t('app', 'Row').' '.($key + 2).'. ('.$row['part_no'].')<br>- '.implode('- <br>', array_unique($updateErrors));
						}
					}else{
						// yangi ma'lumot
						$modelSalesContractDetail = new SalesContractDetail();
						$modelSalesContractDetail->sales_contract_id = $model->sales_contract_id;
						$modelSalesContractDetail->part_id = $part_id;
						$modelSalesContractDetail->delivery_term_id = $delivery_term_id;
						$modelSalesContractDetail->price = $row['price'];
            $modelSalesContractDetail->vat = $row['vat'];
            $modelSalesContractDetail->excise = $row['excise'];
						if(!$modelSalesContractDetail->save()){
							$insertErrors = [];
							foreach($modelSalesContractDetail->errors as $value){
								foreach($value as $val){
									$insertErrors[] = $val;
								}
							}
							$modelErrors[] = Yii::t('app', 'Row').' '.($key + 2).'. ('.$row['part_no'].')<br>- '.implode('- <br>', array_unique($insertErrors));
						}
					}
				}
				if(count($modelErrors) > 0){
					$transaction->rollBack();
					$message = 'Please check this errors.';
					$errors = implode('<br>', $modelErrors);
					Yii::$app->session->setFlash('error', '<b>'.Yii::t('app', $message).'</b>'.'<br>'.$errors);
					return $this->render('upload', ['model' => $model]);
				}else{
					$transaction->commit();
					$message = 'File successfully uploaded to server.';
					Yii::$app->session->setFlash('success', '<b>'.Yii::t('app', $message).'</b>');
					return $this->redirect(['upload']);
				}
			}
			return $this->render('upload', [
				'model' => $model,
			]);
		}

		/**
			* Updates an existing SalesContractDetail model.
			* If update is successful, the browser will be redirected to the 'view' page.
			* @param integer $id
			* @return mixed
			* @throws NotFoundHttpException if the model cannot be found
			*/
		public function actionUpdate($id){
			$model = $this->findModel($id);
			$model->price = number_format($model->price, 2, '.', '');
      $model->vat = number_format($model->vat, 2, '.', '');
      $model->excise = number_format($model->excise, 2, '.', '');
			if($model->load(Yii::$app->request->post()) && $model->save()){
				return $this->redirect(['index']);
			}
			return $this->render('update', [
				'model' => $model,
			]);
		}

		/**
			* Deletes an existing SalesContractDetail model.
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

		/**
			* Finds the SalesContractDetail model based on its primary key value.
			* If the model is not found, a 404 HTTP exception will be thrown.
			* @param integer $id
			* @return SalesContractDetail the loaded model
			* @throws NotFoundHttpException if the model cannot be found
			*/
		protected function findModel($id){
			if(($model = SalesContractDetail::findOne($id)) !== null){
				return $model;
			}
			throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
		}

	}
  