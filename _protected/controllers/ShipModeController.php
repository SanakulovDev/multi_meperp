<?php
	namespace app\controllers;

	use app\models\ShipMode;
	use app\models\ShipModeSearch;
	use Yii;
	use yii\web\NotFoundHttpException;

	/**
		* ShipModeController implements the CRUD actions for ShipMode model.
		*/
	class ShipModeController extends AppController{
		/**
			* Lists all ShipMode models.
			* @return mixed
			*/
		public function actionIndex(){
			$searchModel = new ShipModeSearch();
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
			return $this->render('index', [
				'searchModel' => $searchModel,
				'dataProvider' => $dataProvider,
			]);
		}

		/**
			* Displays a single ShipMode model.
			* @param integer $id
			* @return mixed
			* @throws NotFoundHttpException if the model cannot be found
			*/
		/**
			* Creates a new ShipMode model.
			* If creation is successful, the browser will be redirected to the 'view' page.
			* @return mixed
			*/
		public function actionCreate(){
			$model = new ShipMode();
			if($model->load(Yii::$app->request->post()) && $model->save()){
				return $this->redirect(['index']);
			}
			return $this->render('create', [
				'model' => $model,
			]);
		}

		/**
			* Updates an existing ShipMode model.
			* If update is successful, the browser will be redirected to the 'view' page.
			* @param integer $id
			* @return mixed
			* @throws NotFoundHttpException if the model cannot be found
			*/
		public function actionUpdate($id){
			$model = $this->findModel($id);
			if($model->load(Yii::$app->request->post()) && $model->save()){
				return $this->redirect(['index']);
			}
			return $this->render('update', [
				'model' => $model,
			]);
		}

		/**
			* Deletes an existing ShipMode model.
			* If deletion is successful, the browser will be redirected to the 'index' page.
			* @param integer $id
			* @return mixed
			* @throws NotFoundHttpException if the model cannot be found
			*/
		public function actionDelete($id){
			$this->findModel($id)
			     ->delete();
			return $this->redirect(['index']);
		}

		/**
			* Finds the ShipMode model based on its primary key value.
			* If the model is not found, a 404 HTTP exception will be thrown.
			* @param integer $id
			* @return ShipMode the loaded model
			* @throws NotFoundHttpException if the model cannot be found
			*/
		protected function findModel($id){
			if(($model = ShipMode::findOne($id)) !== null){
				return $model;
			}
			throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
		}
	}
