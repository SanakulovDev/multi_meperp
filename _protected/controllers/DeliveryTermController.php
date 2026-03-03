<?php
	namespace app\controllers;

	use app\models\DeliveryTerm;
	use app\models\DeliveryTermSearch;
	use Yii;
	use yii\db\Exception;
	use yii\web\NotFoundHttpException;

	/**
		* DeliveryTermController implements the CRUD actions for DeliveryTerm model.
		*/
	class DeliveryTermController extends AppController{

		/**
			* Lists all DeliveryTerm models.
			* @return mixed
			*/
		public function actionIndex(){
      
      $searchModel = new DeliveryTermSearch();
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
			$dataProvider->sort->defaultOrder = ['priority' => SORT_ASC];
			return $this->render('index', [
				'searchModel' => $searchModel,
				'dataProvider' => $dataProvider,
			]);
		}

		/**
			* Creates a new DeliveryTerm model.
			* If creation is successful, the browser will be redirected to the 'view' page.
			* @return mixed
			*/
		public function actionCreate(){
			$model = new DeliveryTerm();
			if($model->load(Yii::$app->request->post()) && $model->save()){
				return $this->redirect('index');
			}
			return $this->render('create', [
				'model' => $model,
			]);
		}

		/**
			* Updates an existing DeliveryTerm model.
			* If update is successful, the browser will be redirected to the 'view' page.
			* @param integer $id
			* @return mixed
			* @throws NotFoundHttpException if the model cannot be found
			*/
		public function actionUpdate($id){
			$model = $this->findModel($id);
			if($model->load(Yii::$app->request->post()) && $model->save()){
				return $this->redirect('index');
			}
			return $this->render('update', [
				'model' => $model,
			]);
		}

		/**
			* Deletes an existing DeliveryTerm model.
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
			* Finds the DeliveryTerm model based on its primary key value.
			* If the model is not found, a 404 HTTP exception will be thrown.
			* @param integer $id
			* @return DeliveryTerm the loaded model
			* @throws NotFoundHttpException if the model cannot be found
			*/
		protected function findModel($id){
			if(($model = DeliveryTerm::findOne($id)) !== null){
				return $model;
			}
			throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
		}
	}
