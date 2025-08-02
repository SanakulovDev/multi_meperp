<?php
	namespace app\controllers;

use app\components\Helpers;
use app\models\Defect;
	use app\models\DefectSearch;
	use Yii;
	use yii\db\Exception;
	use yii\web\NotFoundHttpException;

	/**
		* DefectController implements the CRUD actions for Defect model.
		*/
	class DefectController extends AppController{
		/**
			* Lists all Defect models.
			* @return mixed
			*/
		public function actionIndex(){
			$searchModel = new DefectSearch();
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
			return $this->render('index', [
				'searchModel' => $searchModel,
				'dataProvider' => $dataProvider,
			]);
		}

		/**
			* Creates a new Defect model.
			* If creation is successful, the browser will be redirected to the 'view' page.
			* @return mixed
			*/
		public function actionCreate(){
			$model = new Defect();
			if($model->load(Yii::$app->request->post()) && $model->save()){
				return $this->redirect(['index']);
			}
			return $this->render('create', [
				'model' => $model,
			]);
		}

		/**
			* Updates an existing Defect model.
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
			* Deletes an existing Defect model.
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
			* Finds the Defect model based on its primary key value.
			* If the model is not found, a 404 HTTP exception will be thrown.
			* @param integer $id
			* @return Defect the loaded model
			* @throws NotFoundHttpException if the model cannot be found
			*/
		protected function findModel($id){
			if(($model = Defect::findOne($id)) !== null){
				return $model;
			}
			throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
		}

		public function actionXls(){
			ini_set('memory_limit', '-1');
			$searchModel = new DefectSearch();
			$xsl_file = $searchModel->search(Yii::$app->request->queryParams, 'excel');
			$xsl_file->send(Helpers::downloadFileName('defects'));
		}
	}
