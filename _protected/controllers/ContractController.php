<?php

namespace app\controllers;

use app\components\Helpers;
use app\models\Contract;
use app\models\ContractDetail;
use app\models\ContractSearch;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\Response;

/**
 * ContractController implements the CRUD actions for Contract model.
 */
class ContractController extends AppController
{

	/**
	 * Lists all Contract models.
	 * @return mixed
	 */
	public function actionIndex()
	{
		$searchModel = new ContractSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionCreate()
	{
		$model = new Contract();
		$modelDetail = new ContractDetail();

		$errorlist = [];
		$isNewRecord = true;
		if ($model->load(Yii::$app->request->post())) {
			$transaction = Yii::$app->db->beginTransaction();
			if ($model->save()) {
				if (count($errorlist) == 0) {
					$transaction->commit();
					Yii::$app->session->setFlash('success', Yii::t('app', 'Contract created successfully.'));
					return $this->redirect(["update?id=" . $model->id . "&status=" . $model->status]);
				} else {
					$transaction->rollBack();
					return $this->render('create', [
						'errorlist' => ['details' => $errorlist],
						'model' => $model,
						'isNewRecord' => $isNewRecord
					]);
				}
			} else {
				return $this->render('create', [
					'model' => $model,
					'model_detail' => $modelDetail,
					'isNewRecord' => $isNewRecord
				]);
			}
		} else {
			return $this->render('create', [
				'model' => $model,
				'model_detail' => $modelDetail,
				'isNewRecord' => $isNewRecord
			]);
		}
	}

	public function actionUpdate($id, $status)
	{
		$model = $this->findModel($id);
		$modelDetail = new ContractDetail();

		$errorlist = [];
		if ($model->load(Yii::$app->request->post())) {
			$transaction = Yii::$app->db->beginTransaction();
			if ($model->save()) {
				if (count($errorlist) == 0) {
					$transaction->commit();
					Yii::$app->session->setFlash('success', Yii::t('app', 'Contract changed successfully.'));
					return $this->redirect(['index']);
				} else {
					$transaction->rollBack();
					return $this->render('update', [
						'errorlist' => ['details' => $errorlist],
						'model' => $model,
						'model_detail' => $modelDetail,
					]);
				}
			} else {
				return $this->render('update', [
					'model' => $model,
					'count' => $status,
					'model_detail' => $modelDetail,
				]);
			}
		} else {
			$arr = [];
			for ($x = 0; $x < $status; $x++) {
				$arr[$x] = $x + 1;
			  }
			return $this->render('update', [
				'model' => $model,
				'count' => $arr,
				'model_detail' => $modelDetail,
			]);
		}
	}

	/**
	 * Deletes an existing Contract model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionDelete($id)
	{
		$model = $this->findModel($id);
		if ($model->delete()) {
			Yii::$app->session->setFlash('success', Yii::t('app', 'Contract removed successfully.'));
		} else {
			Yii::$app->session->setFlash('error', Yii::t('app', 'Error! Contract not removed.'));
		}
		return $this->redirect(['index']);
	}

	public function actionView($id)
	{
		return $this->render('view', [
			'model' => $this->findModel($id),
		]);
	}

	public function actionXls()
	{
		ini_set('memory_limit', '-1');
		$searchModel = new ContractSearch();
		$xsl_file = $searchModel->search(Yii::$app->request->queryParams, 'excel');
		if (is_array($xsl_file->sheets['contract']['data']) and count($xsl_file->sheets['contract']['data']) == 0)
			return $this->redirect(['index']);
		$xsl_file->send(Helpers::downloadFileName('contract'));
	}

	/**
	 * Finds the Contract model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * @param integer $id
	 * @return Contract the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id)
	{
		if (($model = Contract::findOne($id)) !== null) {
			return $model;
		}
		throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
	}

	public function actionListBySupplier($id)
	{
		Yii::$app->response->format = Response::FORMAT_JSON;
		$list = Contract::find()->with('currency')->where(['supplier_id' => $id, 'status'=>Contract::STATUS_ACTIVE])->all();
		$data = [];
		foreach ($list as $item) {
			$data[] = ['id' => $item->id, 'text' => $item->contract_no, 'currency'=>$item->currency ? $item->currency->code : ''];
		}
		return $data;
	}
}
