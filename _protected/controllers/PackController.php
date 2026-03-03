<?php
	namespace app\controllers;

use app\components\Helpers;
use app\models\Pack;
	use app\models\PackSearch;
	use Yii;
	use yii\web\NotFoundHttpException;
	use yii\web\Response;
	use yii\widgets\ActiveForm;

	/**
		* PackController implements the CRUD actions for Pack model.
		*/
	class PackController extends AppController{
		/**
			* Lists all Pack models.
			* @return mixed
			*/
		public function actionIndex(){
			$searchModel = new PackSearch();
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
			return $this->render('index', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]);
		}

		/**
			* Displays a single Pack model.
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
			* Creates a new Pack model.
			* If creation is successful, the browser will be redirected to the 'view' page.
			* @return mixed
			*/
		public function actionCreate(){
			$model = new Pack();
			if(Yii::$app->getRequest()->isAjax){
				if($model->load(Yii::$app->request->post())){
					if($model->save()){
						$data['status'] = 1;
					}else{
						$data['status'] = 0;
						$data['errors'] = $model->getErrors();
					}
					Yii::$app->response->format = Response::FORMAT_JSON;
					return $data;
				}else{
					return $this->renderAjax('_form', ['model' => $model]);
				}
			}else{
				return $this->redirect(['index']);
			}
		}

		/**
			* Updates an existing Pack model.
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
					Yii::$app->response->format = Response::FORMAT_JSON;
					return $data;
				}else{
					return $this->renderAjax('_form', ['model' => $model]);
				}
			}else{
				return $this->redirect(['index']);
			}
		}

		/**
			* Deletes an existing Pack model.
			* If deletion is successful, the browser will be redirected to the 'index' page.
			* @param integer $id
			* @return mixed
			* @throws NotFoundHttpException if the model cannot be found
			*/
		public function actionDelete($id){
			Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
			$model = Pack::find()->where(['id' => $id])->one();
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
			* Finds the Pack model based on its primary key value.
			* If the model is not found, a 404 HTTP exception will be thrown.
			* @param integer $id
			* @return Pack the loaded model
			* @throws NotFoundHttpException if the model cannot be found
			*/
		protected function findModel($id){
			if(($model = Pack::findOne($id)) !== null){
				return $model;
			}
			throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
		}

		public function actionXls(){
			ini_set('memory_limit', '-1');
			$searchModel = new PackSearch();
			$xsl_file = $searchModel->search(Yii::$app->request->queryParams, 'excel');
			$xsl_file->send(Helpers::downloadFileName('pack'));
      die;
		}

		public function actionValidate($id = null){
			$model = $id === null ? new Pack() : Pack::findOne($id);
			if(Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())){
				Yii::$app->response->format = Response::FORMAT_JSON;
				return ActiveForm::validate($model);
			}
		}

	}
