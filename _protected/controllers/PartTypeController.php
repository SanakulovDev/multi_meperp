<?php
	namespace app\controllers;

	use app\models\PartType;
	use app\models\PartTypeSearch;
	use Yii;
	use yii\web\NotFoundHttpException;

	/**
		* PartTypeController implements the CRUD actions for PartType model.
		*/
	class PartTypeController extends AppController{
		/**
			* Lists all PartType models.
			* @return mixed
			*/
		public function actionIndex(){
			$searchModel = new PartTypeSearch();
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
			return $this->render('index', [
				'searchModel' => $searchModel,
				'dataProvider' => $dataProvider,
			]);
		}

		/**
			* Creates a new PartType model.
			* If creation is successful, the browser will be redirected to the 'view' page.
			* @return mixed
			*/
		public function actionCreate(){
			$model = new PartType();
			if($model->load(Yii::$app->request->post()) && $model->save()){
                $this->clearCache();
				return $this->redirect(['index']);
			}
			return $this->render('create', [
				'model' => $model,
			]);
		}

		/**
			* Updates an existing PartType model.
			* If update is successful, the browser will be redirected to the 'view' page.
			* @param integer $id
			* @return mixed
			* @throws NotFoundHttpException if the model cannot be found
			*/
		public function actionUpdate($id){
			$model = $this->findModel($id);
			if($model->load(Yii::$app->request->post()) && $model->save()){
                $this->clearCache();
				return $this->redirect(['index']);
			}
			return $this->render('update', [
				'model' => $model,
			]);
		}

		/**
			* Deletes an existing PartType model.
			* If deletion is successful, the browser will be redirected to the 'index' page.
			* @param integer $id
			* @return mixed
			* @throws NotFoundHttpException if the model cannot be found
			*/
		public function actionDelete($id){
			$this->findModel($id)
			     ->delete();
			$this->clearCache();
			return $this->redirect(['index']);
		}

		/**
			* Finds the PartType model based on its primary key value.
			* If the model is not found, a 404 HTTP exception will be thrown.
			* @param integer $id
			* @return PartType the loaded model
			* @throws NotFoundHttpException if the model cannot be found
			*/
		protected function findModel($id){
			if(($model = PartType::findOne($id)) !== null){
				return $model;
			}
			throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
		}

        private function clearCache()
        {
            Yii::$app->cache->delete('partTypesAll');
        }
	}
