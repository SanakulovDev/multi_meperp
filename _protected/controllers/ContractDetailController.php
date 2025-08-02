<?php

namespace app\controllers;

use app\models\Contract;
use app\models\ContractDetail;
use app\models\ContractDetailSearch;
use app\models\ContractDetailUploadForm;
use app\models\DeliveryTerm;
use app\models\Part;
use Yii;
use yii\db\Exception;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * ContractDetailController implements the CRUD actions for ContractDetail model.
 */
class ContractDetailController extends AppController
{
	public function actionIndex()
	{
		$searchModel = new ContractDetailSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Creates a new ContractDetail model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionCreate($isAjax = null)
	{
		$model = new ContractDetail();
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			$result = self::definePrimaryPrice($model);
			if(!$result['success'] == 1){
				Yii::$app->session->setFlash('error', Yii::t('app',  implode('<br>',$result['errors'])));
			} else {
				if ($isAjax) {
					return $model->id;
				} else {
					return $this->redirect(['index']);
				}
			}
		}
		return $this->render('create', [
			'model' => $model,
		]);
	}

	public function actionUpload()
	{
		$model = new ContractDetailUploadForm();
		$cd = new ContractDetail();
		if ($model->load(Yii::$app->request->post())) {
			$uploadedFile = UploadedFile::getInstance($model, 'file');
			$data = $model->readExcel($uploadedFile->tempName);
			$emptyCells = [];
			$notExistsParts = [];
			$notExistsTerms = [];
			$notExistsSubSources = [];
			$modelErrors = [];

			foreach ($data['values'] as $key => $row) {
				if (empty($row['part_no']) or empty($row['price']) or empty($row['delivery_term'])) {
					$emptyCells[] = Yii::t('app', 'Row') . ' ' . ($key + 2) . '. ' . Yii::t('app', 'Empty data.');
				} else {
					if (!in_array(trim($row['part_no']), Part::getPartNumbers())) {
						$notExistsParts[] = Yii::t('app', 'Row') . ' ' . ($key + 2) . '. ' . Yii::t('app', 'Part not found.') . '  (' . $row['part_no'] . ')';
					} else {
						$part_numbers[] = $row['part_no'];
					}
					if (!in_array(trim($row['delivery_term']), DeliveryTerm::getTermNames())) {
						$notExistsTerms[] = Yii::t('app', 'Row') . ' ' . ($key + 2) . '. ' . Yii::t('app', 'Delivery term not found.') . '  (' . $row['delivery_term'] . ')';
					}
				}
				if (!empty(trim($row['sub_source']))) {
					if (!in_array(trim($row['sub_source']), $cd->subSourceList)) {
						$notExistsSubSources[] = Yii::t('app', 'Row') . ' ' . ($key + 2) . '. ' . Yii::t('app', 'Sub source not found.') . '  (' . $row['sub_source'] . ')';
					}
				}
			}
			if (count($emptyCells) > 0) {
				$message = 'Empty cells found in uploaded file.';
				$errors = implode('<br>', $emptyCells);
				Yii::$app->session->setFlash('error', '<b>' . Yii::t('app', $message) . '</b>' . '<br>' . $errors);
				return $this->render('upload', ['model' => $model]);
			}
			if (count($notExistsParts) > 0) {
				$message = 'These parts are not found in active parts list.';
				$errors = implode('<br>', $notExistsParts);
				Yii::$app->session->setFlash('error', '<b>' . Yii::t('app', $message) . '</b>' . '<br>' . $errors);
				return $this->render('upload', ['model' => $model]);
			}
			if (count($notExistsTerms) > 0) {
				$message = 'These delivery terms are not found in delivery terms list.';
				$errors = implode('<br>', $notExistsTerms);
				Yii::$app->session->setFlash('error', '<b>' . Yii::t('app', $message) . '</b>' . '<br>' . $errors);
				return $this->render('upload', ['model' => $model]);
			}
			if (count($notExistsSubSources) > 0) {
				$message = 'These sub sources are not found in sub sources list.';
				$errors = implode('<br>', $notExistsSubSources);
				Yii::$app->session->setFlash('error', '<b>' . Yii::t('app', $message) . '</b>' . '<br>' . $errors);
				return $this->render('upload', ['model' => $model]);
			}
			$transaction = Yii::$app->db->beginTransaction();
			foreach ($data['values'] as $key => $row) {
				// Agar table da contract_id, delivery_term_id avval tushgan bo'lsa update qilamiz
				// aks holda create qilamiz
				$part_id = Part::findOneByPartNumber($row['part_no'])->id;
				$delivery_term_id = DeliveryTerm::findOneByTermName($row['delivery_term'])->id;
				$sub_source = ContractDetail::getSubSourceIdByText($row['sub_source']);
				$contractDetail = ContractDetail::find()->where([
					'contract_id' => $model->contract_id,
					'part_id' => $part_id,
					'delivery_term_id' => $delivery_term_id
				])->one();
				if ($contractDetail) {
					// ma'lumot bor
					$contractDetail->price = $row['price'];
					$contractDetail->cnfea = $row['cnfea'];
					$contractDetail->weekly_capacity = $row['weekly_capacity'];
					$contractDetail->sub_source = $sub_source;
					$contractDetail->lead_time = $row['lead_time'];
					if (!$contractDetail->save()) {
						$updateErrors = [];
						foreach ($contractDetail->errors as $value) {
							foreach ($value as $val) {
								$updateErrors[] = $val;
							}
						}
						$modelErrors[] = Yii::t('app', 'Row') . ' ' . ($key + 2) . '. (' . $row['part_no'] . ')<br>- ' . implode('- <br>', array_unique($updateErrors));
					}else{
						// narx masalasi
						$result = self::definePrimaryPrice($contractDetail);
					}
				} else {
					// yangi ma'lumot
					$modelContractDetail = new ContractDetail();
					$modelContractDetail->contract_id = $model->contract_id;
					$modelContractDetail->part_id = $part_id;
					$modelContractDetail->delivery_term_id = $delivery_term_id;
					$modelContractDetail->price = $row['price'];
					$modelContractDetail->cnfea = $row['cnfea'];
					$modelContractDetail->weekly_capacity = $row['weekly_capacity'];
					$modelContractDetail->sub_source = $sub_source;
					$modelContractDetail->lead_time = $row['lead_time'];
					if (!$modelContractDetail->save()) {
						$insertErrors = [];
						foreach ($modelContractDetail->errors as $value) {
							foreach ($value as $val) {
								$insertErrors[] = $val;
							}
						}
						$modelErrors[] = Yii::t('app', 'Row') . ' ' . ($key + 2) . '. (' . $row['part_no'] . ')<br>- ' . implode('- <br>', array_unique($insertErrors));
					}else{
						// narx masalasi
						$result = self::definePrimaryPrice($modelContractDetail);
					}
				}
			}
			if (count($modelErrors) > 0) {
				$transaction->rollBack();
				$message = 'Please check this errors.';
				$errors = implode('<br>', $modelErrors);
				Yii::$app->session->setFlash('error', '<b>' . Yii::t('app', $message) . '</b>' . '<br>' . $errors);
				return $this->render('upload', ['model' => $model]);
			} else {
				$transaction->commit();
				$message = 'File successfully uploaded to server.';
				Yii::$app->session->setFlash('success', '<b>' . Yii::t('app', $message) . '</b>');
				return $this->redirect(['upload']);
			}
		}
		return $this->render('upload', [
			'model' => $model,
		]);
	}

	/**
	 * Updates an existing ContractDetail model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionUpdate($id)
	{
		$model = $this->findModel($id);
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			$result = self::definePrimaryPrice($model);
			if(!$result['success'] == 1){
				Yii::$app->session->setFlash('error', Yii::t('app',  implode('<br>',$result['errors'])));
			}
			return $this->redirect(['index']);
		}
		return $this->render('update', [
			'model' => $model,
		]);
	}

	public function actionSetPrimaryPrice($id)
	{
		$model = $this->findModel($id);

		if ($model->is_primary_price == 1) {
			$model->is_primary_price = 0;
		} else {
			$model->is_primary_price = 1;
		}

		$result = self::definePrimaryPrice($model);
		if(!$result['success'] == 1){
			Yii::$app->session->setFlash('error', Yii::t('app',  implode('<br>',$result['errors'])));
		}


		//
		return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
	}

	public static function definePrimaryPrice($contractDetail,$action = 'update')
	{
		$contract = $contractDetail->contract;
		
		// if(!$contract->isActive)
		// 	return ['success' => 0,'errors' => [Yii::t('app', 'You cannot set this price as primary.')]];
		
		
		$contractDetail->scenario = 'primary-price';

		$part_id = $contractDetail->part_id;
		$is_primary = $contractDetail->is_primary_price;

		
		// bu detalni boshqa narxlarini chaqiriqsh
		$queryOtherContractDetails = ContractDetail::find()
			->joinWith('contract')
			->where([
				'and',
				['<>', 'contract_detail.id', $contractDetail->id],
				['part_id' => $part_id]
			]);

		$queryOtherContractDetailsClone = clone $queryOtherContractDetails;

		$queryOtherContractDetails->andWhere([
				'and',
				['contract.status' => Contract::STATUS_ACTIVE],
				['<=', 'contract.contract_date', date('Y-m-d')],
				['>=', 'contract.expiry_date', date('Y-m-d')]
			]);
			$otherContractDetails = $queryOtherContractDetails->all();

			

		if ($is_primary == 0) {
			// agar is_primary ga 0 qiymat berilayotgan bo'lsa
			if (count($otherContractDetails) == 0) {
				// agar bu detal uchun boshqa narx bo'lmasa, majburan 1 qilamiz
				$contractDetail->is_primary_price = 1;
				if ($contractDetail->save()) {
					return ['success' => 1];
				}else{
					return ['success' => 0,'errors' => $contractDetail->getErrorSummary(1)];
				}
			} else {
				// agar bu detal uchun boshqa narxlar bo'lsa
				$hasPrimary = false;
				foreach ($otherContractDetails as $row) {
					if($row->is_primary_price == 1) {
						$hasPrimary = true;
						break;	
					}
				}
				if(!$hasPrimary){
					// agar hech qaysi narx 1 qilinmagan bo'lsa, delterm priority bo'yicha bittasiga 1 qo'yamiz

					$deliveryTerms = DeliveryTerm::find()->orderBy(['priority' => SORT_ASC])->all();
		
					foreach ($deliveryTerms as $dTerm){
						$otherQueryClone = clone $queryOtherContractDetails;
						$contractDetailDt = $otherQueryClone->andWhere(['delivery_term_id' => $dTerm->id])->one();	
						if ($contractDetailDt) {
							$contractDetailDt->is_primary_price = 1;
							$contractDetailDt->scenario = 'primary-price';
							if ($contractDetailDt->save()) {
								if ($contractDetail->save()) {
									return ['success' => 1];
								}else{
									return ['success' => 0,'errors' => $contractDetail->getErrorSummary(1)];
								}
							}else{
								return ['success' => 0,'errors' => $contractDetailDt->getErrorSummary(1)];
							}
						}
					}

					$contractDetail->is_primary_price = 1;
					if ($contractDetail->save()) {
						return ['success' => 1];
					}else{
						return ['success' => 0,'errors' => $contractDetail->getErrorSummary(1)];
					}

				}else{
					// narxlarni ichida 1 qilingani bo'lsa hech narsa qilmaymiz	
					return ['success' => 1];
				}
				
				
			}
		} else {
			// agar is_primary ga 1 qiymat berilayotgan bo'lsa
			if ($action == 'update') {
					// agar action update bo'lsa
					// ushbu detalning qolgan barcha narxlarini 0 qilamiz, hattoki boshqa perioddagi va statusi 0 bo'lganlarini ham
					foreach ($queryOtherContractDetailsClone->all() as $row) {
							$row->is_primary_price = 0;
							$row->scenario = 'primary-price';
							if (!$row->save()) {
									return ['success' => 0,'errors' => $row->getErrorSummary(1)];
							}
					}
					
					// va o'zini 1 qilamiz
					$contractDetail->is_primary_price = 1;
					if ($contractDetail->save()) {
							return ['success' => 1];
					} else {
							return ['success' => 0,'errors' => $contractDetail->getErrorSummary(1)];
					}
			}
			if ($action == 'delete') {
				// agar action delete bo'lsa
				// sheriklarini del term priority bo'yicha bittasini 1 qilamiz
			
				$deliveryTerms = DeliveryTerm::find()->orderBy(['priority' => SORT_ASC])->all();
		
				foreach ($deliveryTerms as $dTerm){
					$otherQueryClone = clone $queryOtherContractDetails;
					$contractDetailDt = $otherQueryClone->andWhere(['delivery_term_id' => $dTerm->id])->one();	
					if ($contractDetailDt) {
						$contractDetailDt->is_primary_price = 1;
						$contractDetailDt->scenario = 'primary-price';
						if ($contractDetailDt->save()) {
							if ($contractDetail->save()) {
								return ['success' => 1];
							}else{
								return ['success' => 0,'errors' => $contractDetail->getErrorSummary(1)];
							}
						}else{
							return ['success' => 0,'errors' => $contractDetailDt->getErrorSummary(1)];
						}
					}
				}
				return ['success' => 1];


			}


		}
	}

	/**
	 * Deletes an existing ContractDetail model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionDelete($id)
	{
		return $id;
		$model = $this->findModel($id);

		$result = self::definePrimaryPrice($model,'delete');
		if(!$result['success'] == 1){
			Yii::$app->session->setFlash('error', Yii::t('app',  implode('<br>',$result['errors'])));
		}
		
		try {
			$model->delete();
			Yii::$app->session->setFlash('success', Yii::t('app', 'Deleted successfully'));
		} catch (Exception $e) {
			if ($e->errorInfo[1] == 1451) {
				Yii::$app->session->setFlash('error', Yii::t('app', 'error_delete_fr_key'));
			} else {
				throw $e;
			}
		}
		return $this->redirect(['index']);
	}

	public function actionDownloadTemplate()
	{
		return $this->redirect('/public/contract_details_template.xlsx');
	}

	/**
	 * Finds the ContractDetail model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * @param integer $id
	 * @return ContractDetail the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id)
	{
		if (($model = ContractDetail::findOne($id)) !== null) {
			return $model;
		}
		throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
	}
}
