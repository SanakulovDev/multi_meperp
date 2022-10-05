<?php
	namespace app\controllers;

	use app\models\Part;
	use app\models\PartPartVersion;
	use app\models\PartPartVersionSearch;
	use app\models\Warehouse;
	use Yii;
	use yii\helpers\ArrayHelper;
	use yii\web\NotFoundHttpException;

	/**
	 * PartPartVersionController implements the CRUD actions for PartPartVersion model.
	 */
	class PartPartVersionController extends AppController{

		/**
		 * Lists all PartPartVersion models.
		 * @return mixed
		 */
		public function actionIndex(){
			$searchModel = new PartPartVersionSearch();
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
			$dataProvider->sort->defaultOrder = ['version' => SORT_DESC];
			return $this->render('index',
			                     array_merge(
				                     [
					                     'searchModel' => $searchModel,
					                     'dataProvider' => $dataProvider
				                     ], self::loadDictionaries())
			);
		}

		/**
		 * Finds the PartPartVersion model based on its primary key value.
		 * If the model is not found, a 404 HTTP exception will be thrown.
		 * @param int $id
		 * @return PartPartVersion the loaded model
		 * @throws NotFoundHttpException if the model cannot be found
		 */
		protected function findModel($id){
			if(($model = PartPartVersion::findOne($id)) !== null){
				return $model;
			}
			throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
		}

		private function loadDictionaries(){
			$parentParts = ArrayHelper::map(Part::find()->where('state <> '.Part::STATE_RAW)->all(), 'id', 'partinfo');
			$parts = ArrayHelper::map(Part::find()->all(), 'id', 'partinfo');
      $warehouses = ArrayHelper::map(Warehouse::find()->where(['status' => Warehouse::STATUS_ACTIVE])->all(), 'id', 'name');
			return compact('parentParts', 'parts', 'warehouses');
		}
	}
