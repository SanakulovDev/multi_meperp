<?php
	namespace app\controllers;

	use app\models\Contact;
	use app\models\ContactSearch;
	use Yii;
	use yii\web\NotFoundHttpException;
	use yii\web\Response;
	use yii\widgets\ActiveForm;

	/**
		* ContactController implements the CRUD actions for Contact model.
		*/
	class ContactController extends AppController{
		/**
			* Lists all Contact models.
			* @return mixed
			*/
		public function actionIndex(){
			$searchModel = new ContactSearch();
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
			return $this->render('index', [
				'searchModel' => $searchModel,
				'dataProvider' => $dataProvider,
			]);
		}

		public function actionValidate($id = null){
			$model = $id === null ? new Contact() : Contact::findOne($id);
			if(Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())){
				Yii::$app->response->format = Response::FORMAT_JSON;
				return ActiveForm::validate($model);
			}
		}

		/**
			* Creates a new Contact model.
			* If creation is successful, the browser will be redirected to the 'view' page.
			* @return mixed
			*/
		public function actionCreate(){
			$model = new Contact();
			if(Yii::$app->getRequest()->isAjax){
				if($model->load(Yii::$app->request->post())){
					if($model->save()){
						$data['status'] = 1;
					}else{
						$data['status'] = 0;
						$data['errors'] = $model->getErrors();
					}
					//return $this->redirect(['index']);
					echo json_encode($data);
				}else{
					return $this->renderAjax('_form', ['model' => $model]);
				}
			}else{
				return $this->redirect(['index']);
			}
		}

		/**
			* Updates an existing Contact model.
			* If update is successful, the browser will be redirected to the 'view' page.
			* @param integer $id
			* @return mixed
			* @throws NotFoundHttpException if the model cannot be found
			*/
		public function actionUpdate($id){
			$model = $this->findModel($id);
			if(Yii::$app->getRequest()->isAjax){
				if($model->load(Yii::$app->request->post())){
					if($model->save()){
						$data['status'] = 1;
					}else{
						$data['status'] = 0;
						$data['errors'] = $model->getErrors();
					}
					echo json_encode($data);
				}else{
					return $this->renderAjax('_form', ['model' => $model]);
				}
			}else{
				return $this->redirect(['index']);
			}
		}

		/**
			* Deletes an existing Contact model.
			* If deletion is successful, the browser will be redirected to the 'index' page.
			* @param integer $id
			* @return mixed
			* @throws NotFoundHttpException if the model cannot be found
			*/
		public function actionDelete($id){
			Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
			$model = Contact::find()->where(['id' => $id])->one();
			if($model && $model->delete()){
				return [
					"status" => 1
				];
			}
			return [
				"status" => 0
			];
		}

		/**
			* Finds the Contact model based on its primary key value.
			* If the model is not found, a 404 HTTP exception will be thrown.
			* @param integer $id
			* @return Contact the loaded model
			* @throws NotFoundHttpException if the model cannot be found
			*/
		protected function findModel($id){
			if(($model = Contact::findOne($id)) !== null){
				return $model;
			}
			throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
		}
	}
