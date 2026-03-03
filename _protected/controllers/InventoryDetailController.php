<?php
	namespace app\controllers;

	use app\models\ApiDetail;
	use app\models\InventoryDetailUploadForm;
	use app\models\Part;
	use Yii;
	use yii\web\NotFoundHttpException;
	use yii\web\UploadedFile;

	/**
		* InventoryDetailController implements the CRUD actions for ApiDetail model.
		*/
	class InventoryDetailController extends AppController{

		public function actionCreate($api_id){
			$model = new ApiDetail();
			$model->api_id = $api_id;
			if($model->load(Yii::$app->request->post()) && $model->save()){
				return $this->redirect(['inventory/update', 'id' => $model->api_id]);
			}
			return $this->render('create', [
				'model' => $model,
			]);
		}

		/**
			* Updates an existing ApiDetail model.
			* If update is successful, the browser will be redirected to the 'view' page.
			* @param integer $id
			* @return mixed
			* @throws NotFoundHttpException if the model cannot be found
			*/
		public function actionUpdate($id){
			$model = $this->findModel($id);
			if($model->load(Yii::$app->request->post()) && $model->save()){
				return $this->redirect(['inventory/update', 'id' => $model->api_id]);
			}
			return $this->render('update', [
				'model' => $model,
			]);
		}

		/**
			* Deletes an existing ApiDetail model.
			* If deletion is successful, the browser will be redirected to the 'index' page.
			* @param integer $id
			* @return mixed
			* @throws NotFoundHttpException if the model cannot be found
			*/
		public function actionDelete($id){
			$model = $this->findModel($id);
			$api_id = $model->api_id;
			$model->delete();
			return $this->redirect(['inventory/update', 'id' => $api_id]);
		}

		/**
			* Finds the ApiDetail model based on its primary key value.
			* If the model is not found, a 404 HTTP exception will be thrown.
			* @param integer $id
			* @return ApiDetail the loaded model
			* @throws NotFoundHttpException if the model cannot be found
			*/
		protected function findModel($id){
			if(($model = ApiDetail::findOne($id)) !== null){
				return $model;
			}
			throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
		}

		public function actionUpload($api_id){
			$model = new InventoryDetailUploadForm();
			$model->api_id = $api_id;
			if($model->load(Yii::$app->request->post())){
				$uploadedFile = UploadedFile::getInstance($model, 'file');
				$data = $model->readExcel($uploadedFile->tempName);
				$emptyCells = [];
				$notExistsParts = [];
				$notExistsTerms = [];
				$modelErrors = [];
				foreach($data['values'] as $key => $row){
					if(empty($row['part_no'])){
						$emptyCells[] = Yii::t('app', 'Row').' '.($key + 2).'. '.Yii::t('app', 'Empty data.');
					}else{
						if(!in_array(trim($row['part_no']), Part::getAllPartNumbers())){
							$notExistsParts[] = Yii::t('app', 'Row').' '.($key + 2).'. '.Yii::t('app', 'Part not found.').'  ('.$row['part_no'].')';
						}else{
							$part_numbers[] = $row['part_no'];
						}
					}
				}
				if(count($notExistsParts) > 0){
					$message = 'These parts are not found in parts list.';
					$errors = implode('<br>', $notExistsParts);
					Yii::$app->session->setFlash('error', '<b>'.Yii::t('app', $message).'</b>'.'<br>'.$errors);
					return $this->render('upload', ['model' => $model]);
				}
				$transaction = Yii::$app->db->beginTransaction();
				foreach($data['values'] as $key => $row){
					$modelInventoryDetail = new ApiDetail();
					$modelInventoryDetail->api_id = $model->api_id;
					$modelInventoryDetail->part_id = Part::findOneByPartNumber($row['part_no'])->id;
					$modelInventoryDetail->inventory_qty = (empty($row['inventory_qty'])) ? 0 : $row['inventory_qty'];
					$modelInventoryDetail->stock_qty = (empty($row['stock_qty'])) ? 0 : $row['stock_qty'];
					if(!$modelInventoryDetail->save()){
						$insertErrors = [];
						foreach($modelInventoryDetail->errors as $value){
							foreach($value as $val){
								$insertErrors[] = $val;
							}
						}
						$modelErrors[] = Yii::t('app', 'Row').' '.($key + 2).'. ('.$row['part_no'].')<br>- '.implode('- <br>', array_unique($insertErrors));
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
					return $this->redirect(['upload', 'api_id' => $api_id]);
				}
			}
			return $this->render('upload', [
				'model' => $model,
			]);
		}

	}
