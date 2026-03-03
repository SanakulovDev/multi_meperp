<?php

namespace app\controllers;

use app\components\Helpers;
use Yii;
use app\models\AirShipment;
use app\models\AirShipmentReason;
use app\models\AirShipmentSearch;
use app\models\CountryCode;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\widgets\ActiveForm;
use app\models\Supplier;
use DateTime;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use yii\helpers\ArrayHelper;

/**
 * AirShipmentController implements the CRUD actions for AirShipment model.
 */
class AirShipmentController extends AppController {
	/**
	 * Lists all AirShipment models.
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel = new AirShipmentSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		return $this->render('index', array_merge([
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider
		], self::loadDictionaries()));
	}

	/**
	 * Creates a new AirShipment model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionCreate() {
		$model = new AirShipment();
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
	 * Updates an existing AirShipment model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionUpdate($id) {
		$model = $this->findModel($id);
		if($model->status == AirShipment::STATUS_INACTIVE) {
			throw new AccessDeniedException();
		}
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
	 * Deletes an existing AirShipment model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionDelete($id) {
		Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
		$model = AirShipment::find()->where(['id' => $id])->one();
		if($model->status == AirShipment::STATUS_INACTIVE) {
			throw new AccessDeniedException();
		}
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
	 * Finds the AirShipment model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * @param integer $id
	 * @return AirShipment the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id) {
		if (($model = AirShipment::findOne($id)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
	}

	private function loadDictionaries() {
		$uzb_id = CountryCode::find()->where(['alpha_2' => 'UZ'])->one();
		if ($uzb_id) {
			$suppliers = ArrayHelper::map(Supplier::find()->where(['<>', 'country_code_id', $uzb_id->id])->all(), 'id', 'name');
		} else {
			$suppliers = ArrayHelper::map(Supplier::find()->all(), 'id', 'name');
		}

		$reasons = ArrayHelper::map(AirShipmentReason::find()->all(), 'id', 'title');
		return compact('suppliers', 'reasons');
	}

	public function actionXls() {
		ini_set('memory_limit', '-1');
		$searchModel = new AirShipmentSearch();
		$xsl_file = $searchModel->search(Yii::$app->request->queryParams, 'excel');
		$xsl_file->send(Helpers::downloadFileName('lms'));
	}

	public function actionValidate($id = null) {
		$model = $id === null ? new AirShipment() : AirShipment::findOne($id);
		if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
			Yii::$app->response->format = Response::FORMAT_JSON;
			return ActiveForm::validate($model);
		}
	}

	public function actionLock() {
		if (Yii::$app->request->isGet) {
			$type = Yii::$app->request->get('submit');
			$format = 'Y-m';
			$period = Yii::$app->request->get('period');
			// validation
			$d = DateTime::createFromFormat($format, $period);
			if($d && $d->format($format) === $period && in_array($type, ['lock','unlock'])) {
				$status = $type == 'lock' ? AirShipment::STATUS_INACTIVE : AirShipment::STATUS_ACTIVE;
				AirShipment::updateAll(['status'=> $status], ['period'=>$period]);
			}
	 }

	 return $this->redirect('index');
	}
}
