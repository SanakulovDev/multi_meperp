<?php
	namespace app\controllers;

	use app\models\Part;
	use app\models\PartOrderDetail;
	use app\models\PartOrderDetailSearch;
	use Yii;
	use yii\helpers\ArrayHelper;
	use yii\web\NotFoundHttpException;

	/**
	 * PartOrderDetailController implements the CRUD actions for PartOrderDetail model.
	 */
	class PartOrderDetailController extends AppController{

		/**
		 * Lists all PartOrderDetail models.
		 * @return mixed
		 */
		public function actionIndex(){
			$searchModel = new PartOrderDetailSearch();
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
			$parts = ArrayHelper::map(Part::find()->where(['status' => Part::STATUS_ACTIVE])->all(), 'id', 'partinfo');
			return $this->render('index', [
				'searchModel' => $searchModel,
				'dataProvider' => $dataProvider,
			]);
		}

		/**
		 * Updates an existing PartOrderDetail model.
		 * If update is successful, the browser will be redirected to the 'view' page.
		 * @param int $id
		 * @return mixed
		 * @throws NotFoundHttpException if the model cannot be found
		 */
		public function actionUpdate($id){
			$model = $this->findModel($id);
			if($model->load(Yii::$app->request->post())){
				$model->updated_by = Yii::$app->user->id;
				$model->updated_at = time();
				if($model->save()){
					return $this->redirect(['part-order/view', 'id' => $model->part_order_id]);
				}
			}
			return $this->render('update', ['model' => $model]);
		}

		/**
		 * Deletes an existing PartOrderDetail model.
		 * If deletion is successful, the browser will be redirected to the 'index' page.
		 * @param int $id
		 * @return mixed
		 * @throws NotFoundHttpException if the model cannot be found
		 */
		public function actionDelete($id){
			$model = $this->findModel($id);
			$model->delete();
			return $this->redirect(['part-order/view', 'id' => $model->part_order_id]);
		}

		/**
		 * Finds the PartOrderDetail model based on its primary key value.
		 * If the model is not found, a 404 HTTP exception will be thrown.
		 * @param int $id
		 * @return PartOrderDetail the loaded model
		 * @throws NotFoundHttpException if the model cannot be found
		 */
		protected function findModel($id){
			if(($model = PartOrderDetail::findOne($id)) !== null){
				return $model;
			}
			throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
		}
	}
