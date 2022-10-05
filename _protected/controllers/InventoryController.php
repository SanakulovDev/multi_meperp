<?php
	namespace app\controllers;

use app\components\Helpers;
use app\models\Api;
	use app\models\ApiDetailSearch;
	use app\models\ApiSearch;
	use app\models\Part;
	use app\models\Unit;
	use Yii;
	use yii\db\Exception;
	use yii\helpers\ArrayHelper;
	use yii\web\NotFoundHttpException;

	/**
		* ApiController implements the CRUD actions for Api model.
		*/
	class InventoryController extends AppController{

		public function actionIndex(){
			$searchModel = new ApiSearch();
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
			return $this->render('index',
			                     [
				                     'searchModel' => $searchModel,
				                     'dataProvider' => $dataProvider
			                     ]);
		}

		/**
			* Creates a new Api model.
			* If creation is successful, the browser will be redirected to the 'view' page.
			* @return mixed
			*/
		public function actionCreate(){
			$model = new Api();
			if($model->load(Yii::$app->request->post()) && $model->save()){
				return $this->redirect(['index']);
			}
			return $this->render('create', [
				'model' => $model,
			]);
		}

		/**
			* Updates an existing Api model.
			* If update is successful, the browser will be redirected to the 'view' page.
			* @param integer $id
			* @return mixed
			* @throws NotFoundHttpException if the model cannot be found
			*/
		public function actionUpdate($id){
			$model = $this->findModel($id);
			$searchModel = new ApiDetailSearch();
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
			$dataProvider->query->andWhere(['api_id' => $model->id]);
			if($model->load(Yii::$app->request->post()) && $model->save()){
				return $this->redirect(['update', 'id' => $model->id]);
			}
			return $this->render('update', array_merge([
				                                           'model' => $model,
				                                           'searchModel' => $searchModel,
				                                           'dataProvider' => $dataProvider,
			                                           ], self::loadDictionaries()));
		}

		/**
			* Deletes an existing Api model.
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

		public function actionXls(){
			ini_set('memory_limit', '-1');
			$searchModel = new ApiSearch();
			$xsl_file = $searchModel->search(Yii::$app->request->queryParams, 'excel');
			if(is_array($xsl_file->sheets['api']['data']) and count($xsl_file->sheets['api']['data']) == 0)
				return $this->redirect(['index']);
			$xsl_file->send(Helpers::downloadFileName('inventory'));
		}

		/**
			* Finds the Api model based on its primary key value.
			* If the model is not found, a 404 HTTP exception will be thrown.
			* @param integer $id
			* @return Api the loaded model
			* @throws NotFoundHttpException if the model cannot be found
			*/
		protected function findModel($id){
			if(($model = Api::findOne($id)) !== null){
				return $model;
			}
			throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
		}

		private function loadDictionaries(){
			$parts = ArrayHelper::map(Part::find()->where(['status' => Part::STATUS_ACTIVE])->all(), 'id', 'partinfo');
			$uoms = ArrayHelper::map(Unit::find()->all(), 'id', 'unit_value');
			return compact('parts', 'uoms');
		}
	}
