<?php
	namespace app\controllers;

	use app\models\BomLog;
	use app\models\BomLogSearch;
	use Yii;
	use yii\web\NotFoundHttpException;

	/**
		* BomLogController implements the CRUD actions for BomLog model.
		*/
	class BomLogController extends AppController{
		/**
			* Lists all BomLog models.
			* @return mixed
			*/
		public function actionIndex(){
			$searchModel = new BomLogSearch();
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
			$dataProvider->sort->defaultOrder = ['created_at' => SORT_DESC];
			return $this->render('index', [
				'searchModel' => $searchModel,
				'dataProvider' => $dataProvider,
			]);
		}

	}
