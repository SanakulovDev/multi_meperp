<?php
	namespace app\controllers;

	use app\models\InvoicePartProblem;
	use app\models\InvoicePartProblemSearch;
	use Yii;
	use yii\web\NotFoundHttpException;

	/**
		* InvoicePartProblemController implements the CRUD actions for InvoicePartProblem model.
		*/
	class InvoicePartProblemController extends AppController{
		/**
			* Lists all InvoicePartProblem models.
			* @return mixed
			*/
		public function actionIndex(){
			$searchModel = new InvoicePartProblemSearch();
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
			return $this->render('index', [
				'searchModel' => $searchModel,
				'dataProvider' => $dataProvider,
			]);
		}

//		/**
//			* Displays a single InvoicePartProblem model.
//			* @param integer $id
//			* @return mixed
//			* @throws NotFoundHttpException if the model cannot be found
//			*/
//		public function actionView($id){
//			return $this->render('view', [
//				'model' => $this->findModel($id),
//			]);
//		}
//
//		/**
//			* Creates a new InvoicePartProblem model.
//			* If creation is successful, the browser will be redirected to the 'view' page.
//			* @return mixed
//			*/
//		public function actionCreate(){
//			$model = new InvoicePartProblem();
//			if($model->load(Yii::$app->request->post()) && $model->save()){
//				return $this->redirect(['view', 'id' => $model->id]);
//			}
//			return $this->render('create', [
//				'model' => $model,
//			]);
//		}
//
//		/**
//			* Updates an existing InvoicePartProblem model.
//			* If update is successful, the browser will be redirected to the 'view' page.
//			* @param integer $id
//			* @return mixed
//			* @throws NotFoundHttpException if the model cannot be found
//			*/
//		public function actionUpdate($id){
//			$model = $this->findModel($id);
//			if($model->load(Yii::$app->request->post()) && $model->save()){
//				return $this->redirect(['view', 'id' => $model->id]);
//			}
//			return $this->render('update', [
//				'model' => $model,
//			]);
//		}

		/**
			* Deletes an existing InvoicePartProblem model.
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
			* Finds the InvoicePartProblem model based on its primary key value.
			* If the model is not found, a 404 HTTP exception will be thrown.
			* @param integer $id
			* @return InvoicePartProblem the loaded model
			* @throws NotFoundHttpException if the model cannot be found
			*/
		protected function findModel($id){
			if(($model = InvoicePartProblem::findOne($id)) !== null){
				return $model;
			}
			throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
		}
	}
