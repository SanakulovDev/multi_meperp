<?php
	namespace app\controllers;

	use app\models\DocumentType;
	use app\models\DocumentTypeSearch;
	use Yii;
	use yii\db\Exception;
	use yii\web\NotFoundHttpException;

	/**
		* DocumentTypeController implements the CRUD actions for DocumentType model.
		*/
	class DocumentTypeController extends AppController{

		/**
			* Lists all DocumentType models.
			* @return mixed
			*/
		public function actionIndex(){
			$searchModel = new DocumentTypeSearch();
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
			return $this->render('index', [
				'searchModel' => $searchModel,
				'dataProvider' => $dataProvider,
			]);
		}

		/**
			* Creates a new DocumentType model.
			* If creation is successful, the browser will be redirected to the 'view' page.
			* @return mixed
			*/
		public function actionCreate(){
			return $this->redirect(['index']);
			$model = new DocumentType();
			if($model->load(Yii::$app->request->post())){
				if($model->save()){
					return $this->redirect(['index']);
				}
			}
			return $this->render('create', ['model' => $model,]);
		}

		/**
			* Updates an existing DocumentType model.
			* If update is successful, the browser will be redirected to the 'view' page.
			* @param integer $id
			* @return mixed
			* @throws NotFoundHttpException if the model cannot be found
			*/
		public function actionUpdate($id){
			return $this->redirect(['index']);
			$model = $this->findModel($id);
			if($model->load(Yii::$app->request->post())){
				if($model->save()){
					return $this->redirect(['index']);
				}
			}
			return $this->render('update', ['model' => $model,]);
		}

		/**
			* Deletes an existing DocumentType model.
			* If deletion is successful, the browser will be redirected to the 'index' page.
			* @param integer $id
			* @return mixed
			* @throws NotFoundHttpException if the model cannot be found
			*/
		public function actionDelete($id){
			$model = $this->findModel($id);
			try{
				$model->delete();
				Yii::$app->session->setFlash('success', Yii::t('app', 'Deleted successfully'));
			}catch(Exception $e){
				if($e->errorInfo[1] == 1451){
					Yii::$app->session->setFlash('error', Yii::t('app', 'error_delete_fr_key'));
				}else{
					throw $e;
				}
			}
			return $this->redirect(['index']);
		}

		/**
			* Finds the DocumentType model based on its primary key value.
			* If the model is not found, a 404 HTTP exception will be thrown.
			* @param integer $id
			* @return DocumentType the loaded model
			* @throws NotFoundHttpException if the model cannot be found
			*/
		protected function findModel($id){
			if(($model = DocumentType::findOne($id)) !== null){
				return $model;
			}
			throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
		}
	}
