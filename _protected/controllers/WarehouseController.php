<?php
	namespace app\controllers;

use app\components\Helpers;
use app\models\Supplier;
	use app\models\Warehouse;
	use app\models\WarehouseReportGroup;
	use app\models\WarehouseSearch;
	use Yii;
	use yii\db\Exception;
	use yii\helpers\ArrayHelper;
	use yii\web\NotFoundHttpException;

	/**
		* WarehouseController implements the CRUD actions for Warehouse model.
		*/
	class WarehouseController extends AppController{
		/**
			* Lists all Warehouse models.
			* @return mixed
			*/
		public function actionIndex(){
			$searchModel = new WarehouseSearch();
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
			return $this->render('index', [
				'searchModel' => $searchModel,
				'dataProvider' => $dataProvider,
			]);
		}

		/**
			* Creates a new Warehouse model.
			* If creation is successful, the browser will be redirected to the 'view' page.
			* @return mixed
			*/
		public function actionCreate(){
			$model = new Warehouse();
			if($model->load(Yii::$app->request->post())){
				$model->created_by = Yii::$app->user->id;
				$model->created_at = time();
				if($model->save()){
					return $this->redirect(['index']);
				}
			}
			return $this->render('create', ['model' => $model,]);
		}

		/**
			* Updates an existing Warehouse model.
			* If update is successful, the browser will be redirected to the 'view' page.
			* @param integer $id
			* @return mixed
			* @throws NotFoundHttpException if the model cannot be found
			*/
		public function actionUpdate($id){
			$model = $this->findModel($id);
			if($model->load(Yii::$app->request->post())){
				$model->updated_by = Yii::$app->user->id;
				$model->updated_at = time();
				if($model->save()){
					return $this->redirect(['index']);
				}
			}
			return $this->render('update', ['model' => $model,]);
		}

		/**
			* Deletes an existing Warehouse model.
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
			* Finds the Warehouse model based on its primary key value.
			* If the model is not found, a 404 HTTP exception will be thrown.
			* @param integer $id
			* @return Warehouse the loaded model
			* @throws NotFoundHttpException if the model cannot be found
			*/
		protected function findModel($id){
			if(($model = Warehouse::findOne($id)) !== null){
				return $model;
			}
			throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
		}

		public function actionXls(){
			ini_set('memory_limit', '-1');
			$searchModel = new WarehouseSearch();
			$xsl_file = $searchModel->search(Yii::$app->request->queryParams, 'excel');
			$xsl_file->send(Helpers::downloadFileName('warehouses'));
            die;
		}
	}
