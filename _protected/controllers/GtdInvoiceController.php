<?php
	namespace app\controllers;

	use app\models\GtdInvoice;
	use app\models\GtdInvoiceSearch;
	use Yii;
	use yii\db\Exception;
	use yii\filters\VerbFilter;
	use yii\web\Controller;
	use yii\web\NotFoundHttpException;

	class GtdInvoiceController extends AppController{
		

		public function actionUpdate($id){
			$model = $this->findModel($id);
			if($model->load(Yii::$app->request->post())){
				$model->updated_by = Yii::$app->user->id;
				$model->updated_at = time();
				if($model->save()){
					return $this->redirect(['/gtd/view', 'id' => $model->gtd_id]);
				}
			}
			return $this->render('/gtd-invoice/update', ['model' => $model,]);
		}

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
			return $this->redirect(['/gtd/view', 'id' => $model->gtd_id]);
		}

		protected function findModel($id){
			if(($model = GtdInvoice::findOne($id)) !== null){
				return $model;
			}
			throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
		}

		public function actionXls(){
			ini_set('memory_limit', '-1');
			$searchModel = new GtdInvoiceSearch();
			$xsl_file = $searchModel->search(Yii::$app->request->queryParams, 'excel');
			$xsl_file->send('GTD_'.date("YmdHis").'.xlsx');
		}

	}
