<?php
	namespace app\controllers;

	use app\models\HistoryDocument;
	use app\models\HistoryDocumentSearch;
	use Yii;
	use yii\web\NotFoundHttpException;

	/**
		* HistoryDocumentController implements the CRUD actions for HistoryDocument model.
		*/
	class HistoryDocumentController extends AppController{

		public function actionIndex(){
			$searchModel = new HistoryDocumentSearch();
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
			$dataProvider->sort->defaultOrder = ['id' => SORT_DESC];
			return $this->render('index', [
				'searchModel' => $searchModel,
				'dataProvider' => $dataProvider,
			]);
		}

		/**
			* Displays a single HistoryDocument model.
			* @param integer $id
			* @return mixed
			* @throws NotFoundHttpException if the model cannot be found
			*/
		public function actionView($id){
			return $this->render('view', [
				'model' => $this->findModel($id),
			]);
		}

		/**
			* Finds the HistoryDocument model based on its primary key value.
			* If the model is not found, a 404 HTTP exception will be thrown.
			* @param integer $id
			* @return HistoryDocument the loaded model
			* @throws NotFoundHttpException if the model cannot be found
			*/
		protected function findModel($id){
			if(($model = HistoryDocument::findOne($id)) !== null){
				return $model;
			}
			throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
		}
	}
