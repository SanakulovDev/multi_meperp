<?php
	namespace app\controllers;

	use app\models\Factory;
	use app\models\Line;
	use app\models\LineSearch;
	use Yii;
	use yii\db\Exception;
	use yii\helpers\ArrayHelper;
	use yii\web\NotFoundHttpException;
	use yii\web\Response;
	use yii\widgets\ActiveForm;

	/**
		* LineController implements the CRUD actions for Line model.
		*/
	class LineController extends AppController{

		/**
			* Lists all Line models.
			* @return mixed
			*/
		public function actionIndex(){
			$searchModel = new LineSearch();
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
			return $this->render('index', array_merge([
				                                          'searchModel' => $searchModel,
				                                          'dataProvider' => $dataProvider
			                                          ], self::loadDictionaries()));
		}

		/**
			* Creates a new Line model.
			* If creation is successful, the browser will be redirected to the 'view' page.
			* @return mixed
			*/
		public function actionCreate(){
			$model = new Line();
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
					$lines = ArrayHelper::map(Line::find()->where(['status' => Line::STATUS_ACTIVE])->all(), 'id', 'line_name');
					return $this->renderAjax('_form', array_merge(['model' => $model], ['lines' => $lines], self::loadDictionaries()));
				}
			}else{
				return $this->redirect(['index']);
			}
		}

		/**
			* Updates an existing Line model.
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
					$lines = ArrayHelper::map(Line::find()->where(['status' => Line::STATUS_ACTIVE])->andWhere(['<>', 'id', $id])->all(), 'id', 'line_name');
					return $this->renderAjax('_form', array_merge(['model' => $model], ['lines' => $lines], self::loadDictionaries()));
				}
			}else{
				return $this->redirect(['index']);
			}
		}

		/**
			* Deletes an existing Line model.
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
			* Finds the Line model based on its primary key value.
			* If the model is not found, a 404 HTTP exception will be thrown.
			* @param integer $id
			* @return Line the loaded model
			* @throws NotFoundHttpException if the model cannot be found
			*/
		protected function findModel($id){
			if(($model = Line::findOne($id)) !== null){
				return $model;
			}
			throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
		}

		private function loadDictionaries(){
			$factories = ArrayHelper::map(Factory::find()->where(['status' => Factory::STATUS_ACTIVE])->all(), 'id', 'name');
			return compact('factories');
		}

		public function actionXls(){
			ini_set('memory_limit', '-1');
			$searchModel = new LineSearch();
			$xsl_file = $searchModel->search(Yii::$app->request->queryParams, 'excel');
			$xsl_file->send('lines'.date("YmdHis").'.xlsx');
		}

		public function actionValidate($id = null){
			$model = $id === null ? new Line() : Line::findOne($id);
			if(Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())){
				Yii::$app->response->format = Response::FORMAT_JSON;
				return ActiveForm::validate($model);
			}
		}
	}
