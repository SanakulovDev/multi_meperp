<?php
	namespace app\controllers;

use app\components\Helpers;
use app\models\Supplier;
	use app\models\SupplierSearch;
	use Yii;
  use yii\web\NotFoundHttpException;
  use yii\web\Response;

  /**
		* SupplierController implements the CRUD actions for Supplier model.
		*/
	class SupplierController extends AppController{
		/**
			* Lists all Supplier models.
			* @return mixed
			*/
		public function actionIndex(){
			$searchModel = new SupplierSearch();
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
			return $this->render('index', [
				'searchModel' => $searchModel,
				'dataProvider' => $dataProvider,
			]);
		}

		/**
			* Displays a single Supplier model.
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
			* Creates a new Supplier model.
			* If creation is successful, the browser will be redirected to the 'view' page.
			* @return mixed
			*/
		public function actionCreate(){
			$model = new Supplier();
			if($model->load(Yii::$app->request->post()) && $model->save()){
				return $this->redirect(['index']);
			}
			return $this->render('create', [
				'model' => $model,
			]);
		}

		/**
			* Updates an existing Supplier model.
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
			* Deletes an existing Supplier model.
			* If deletion is successful, the browser will be redirected to the 'index' page.
			* @param integer $id
			* @return mixed
			* @throws NotFoundHttpException if the model cannot be found
			*/
		public function actionDelete($id){
        $this->findModel($id)
          ->delete();
			return $this->redirect(['index']);
		}

		/**
			* Finds the Supplier model based on its primary key value.
			* If the model is not found, a 404 HTTP exception will be thrown.
			* @param integer $id
			* @return Supplier the loaded model
			* @throws NotFoundHttpException if the model cannot be found
			*/
		protected function findModel($id){
			if(($model = Supplier::findOne($id)) !== null){
				return $model;
			}
			throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
		}

		public function actionXls(){
			ini_set('memory_limit', '-1');
			$searchModel = new SupplierSearch();
			$xsl_file = $searchModel->search(Yii::$app->request->queryParams, 'excel');
			$xsl_file->send(Helpers::downloadFileName('supplier'));
		}

    public function actionListByPartContract($id)
    {
      Yii::$app->response->format = Response::FORMAT_JSON;
      $list = Supplier::find()
        ->innerJoin('contract', 'supplier.id=contract.supplier_id')
        ->innerJoin('contract_detail', 'contract.id=contract_detail.contract_id')
        ->where(['contract_detail.part_id'=>$id])
        ->select(['supplier.id','supplier.name'])
        // ->distinct()
        ->all();
      $data = [];
      foreach($list as $item){
        $data[] = ['id' => $item->id, 'text' => $item->name];
      }
      return $data;
    }
	}
