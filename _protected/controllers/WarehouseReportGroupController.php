<?php
	namespace app\controllers;

	use app\models\WarehouseReportGroup;
	use app\models\WarehouseReportGroupSearch;
	use Yii;
	use yii\web\NotFoundHttpException;

	/**
		* WarehouseReportGroupController implements the CRUD actions for WarehouseReportGroup model.
		*/
	class WarehouseReportGroupController extends AppController{

		/**
			* Lists all WarehouseReportGroup models.
			* @return mixed
			*/
		public function actionIndex(){
			$searchModel = new WarehouseReportGroupSearch();
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
			$dataProvider->sort->defaultOrder = ['sort_order'=> SORT_ASC];
			return $this->render('index', [
				'searchModel' => $searchModel,
				'dataProvider' => $dataProvider,
			]);
		}

		/**
			* Creates a new WarehouseReportGroup model.
			* If creation is successful, the browser will be redirected to the 'view' page.
			* @return mixed
			*/
		public function actionCreate(){
			$model = new WarehouseReportGroup();
			if($model->load(Yii::$app->request->post())){
				if($model->save()){
					return $this->redirect(['index']);
				}
			}
			return $this->render('create', [
				'model' => $model,
				'errorlist' => $errorlist ?? null,
			]);
		}

		/**
			* Updates an existing WarehouseReportGroup model.
			* If update is successful, the browser will be redirected to the 'view' page.
			* @param integer $id
			* @return mixed
			* @throws NotFoundHttpException if the model cannot be found
			*/
		public function actionUpdate($id){
			$model = $this->findModel($id);
			if($model->load(Yii::$app->request->post())){
				if($model->save()){
					return $this->redirect(['index']);
				}
			}
			return $this->render('update', [
				'model' => $model,
				'errorlist' => $errorlist ?? null,
			]);
		}

		/**
			* Deletes an existing WarehouseReportGroup model.
			* If deletion is successful, the browser will be redirected to the 'index' page.
			* @param integer $id
			* @return mixed
			* @throws NotFoundHttpException if the model cannot be found
			*/
		public function actionDelete($id){
			$this->findModel($id)->delete();
			return $this->redirect(['index']);
		}

		/**
			* Finds the WarehouseReportGroup model based on its primary key value.
			* If the model is not found, a 404 HTTP exception will be thrown.
			* @param integer $id
			* @return WarehouseReportGroup the loaded model
			* @throws NotFoundHttpException if the model cannot be found
			*/
		protected function findModel($id){
			if(($model = WarehouseReportGroup::findOne($id)) !== null){
				return $model;
			}
			throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
		}
	}
