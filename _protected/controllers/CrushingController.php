<?php
	namespace app\controllers;

use app\components\Helpers;
use app\models\Crushing;
	use app\models\CrushingSearch;
	use app\models\Part;
	use app\models\User;
	use Yii;
	use yii\helpers\ArrayHelper;
	use yii\web\NotFoundHttpException;

	/**
		* CrushingController implements the CRUD actions for Crushing model.
		*/
	class CrushingController extends AppController{

		public function actionIndex(){
			$searchModel = new CrushingSearch();
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
			return $this->render('index', array_merge([
				                                          'searchModel' => $searchModel,
				                                          'dataProvider' => $dataProvider
			                                          ], self::loadDictionaries()));
		}

		public function actionCreate(){
			$model = new Crushing();
			if($model->load(Yii::$app->request->post()) && $model->save()){
				return $this->redirect(['index']);
			}
			return $this->render('create', array_merge([
				                                           'model' => $model,
			                                           ], self::loadDictionaries()));
		}

		/**
			* Updates an existing Crushing model.
			* If update is successful, the browser will be redirected to the 'view' page.
			* @param integer $id
			* @return mixed
			* @throws NotFoundHttpException if the model cannot be found
			*/
		public function actionUpdate($id){
			$model = $this->findModel($id);
			if($model->is_processed != 0){
				Yii::$app->session->setFlash('error', Yii::t('app', 'You are not allowed to do this action.'));
				return $this->redirect(['index']);
			}
			if($model->load(Yii::$app->request->post()) && $model->save()){
				return $this->redirect(['index']);
			}
			return $this->render('update', array_merge([
				                                           'model' => $model,
			                                           ], self::loadDictionaries()));
		}

		/**
			* Deletes an existing Crushing model.
			* If deletion is successful, the browser will be redirected to the 'index' page.
			* @param integer $id
			* @return mixed
			* @throws NotFoundHttpException if the model cannot be found
			*/
		public function actionDelete($id){
			$model = $this->findModel($id);
			if($model->is_processed != 0){
				Yii::$app->session->setFlash('error', Yii::t('app', 'You are not allowed to do this action.'));
				return $this->redirect(['index']);
			}
			$model->delete();
			return $this->redirect(['index']);
		}

		public function actionXls(){
			ini_set('memory_limit', '-1');
			$searchModel = new CrushingSearch();
			$xsl_file = $searchModel->search(Yii::$app->request->queryParams, 'excel');
			if(is_array($xsl_file->sheets['Crushing']['data'] ?? null) and count($xsl_file->sheets['Crushing']['data']) == 0){
				return $this->redirect(['index']);
			}
			$xsl_file->send(Helpers::downloadFileName('crushing'));
			die;
		}

		/**
			* Finds the Crushing model based on its primary key value.
			* If the model is not found, a 404 HTTP exception will be thrown.
			* @param integer $id
			* @return Crushing the loaded model
			* @throws NotFoundHttpException if the model cannot be found
			*/
		protected function findModel($id){
			if(($model = Crushing::findOne($id)) !== null){
				return $model;
			}
			throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
		}

		private function loadDictionaries(){
			$parts = ArrayHelper::map(Part::find()->where(['status' => Part::STATUS_ACTIVE])->all(), 'id', 'partinfo');
			$users = ArrayHelper::map(User::find()->where(['status' => User::STATUS_ACTIVE])->all(), 'id', 'fullname');
			return compact('parts', 'users');
		}

	}
