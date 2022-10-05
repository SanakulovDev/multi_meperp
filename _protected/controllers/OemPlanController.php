<?php

namespace app\controllers;

use app\components\Helpers;
use Yii;
use app\models\OemPlan;
use app\models\OemPlanSearch;
use app\models\OemPlanUploadForm;
use app\models\ProductModel;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

/**
 * OemPlanController implements the CRUD actions for OemPlan model.
 */
class OemPlanController extends AppController {
	/**
	 * Lists all OemPlan models.
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel = new OemPlanSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', array_merge([
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		], self::loadDictionaries()));
	}

	/**
	 * Creates a new OemPlan model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionCreate() {
		$model = new OemPlan();
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
	 * Updates an existing OemPlan model.
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
	 * Deletes an existing OemPlan model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionDelete($id) {
		Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
		$model = OemPlan::find()->where(['id' => $id])->one();
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
	 * Finds the OemPlan model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * @param integer $id
	 * @return OemPlan the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id) {
		if (($model = OemPlan::findOne($id)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
	}

	private function loadDictionaries() {
		$models = ArrayHelper::map(ProductModel::find()->where(['is_vehicle' => ProductModel::IS_VEHICLE])->all(), 'id', 'description');
		return compact('models');
	}

	public function actionXls() {
		ini_set('memory_limit', '-1');
		$searchModel = new OemPlanSearch();
		$xsl_file = $searchModel->search(Yii::$app->request->queryParams, 'excel');
		$xsl_file->send(Helpers::downloadFileName('oem-plan'));
	}

	public function actionValidate($id = null) {
		$model = $id === null ? new OemPlan() : OemPlan::findOne($id);
		if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
			Yii::$app->response->format = Response::FORMAT_JSON;
			return ActiveForm::validate($model);
		}
	}

	public function actionUpload() {
		$form = new OemPlanUploadForm();
		
		if ($form->load(Yii::$app->request->post())) {
			$uploadedFile = UploadedFile::getInstance($form, 'file');
			$form->handle($uploadedFile->tempName);
			if($form->errorMessages) {
				Yii::$app->session->setFlash('error', implode(',', $form->errorMessages));
				return $this->render('upload', ['model' => $form]);
			} else {
				return $this->redirect('index');
			}
			
		}
		return $this->render('upload', [
			'model' => $form,
		]);
	}
}
