<?php

namespace app\controllers;

use app\components\Helpers;
use app\models\BomLog;
use app\models\PartPart;
use app\models\ProductionOrder;
use Yii;
use app\models\ProductSpecification;
use app\models\ProductSpecificationItem;
use app\models\ProductSpecificationSearch;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * ProductSpecificationController implements the CRUD actions for ProductSpecification model.
 */
class ProductSpecificationController extends AppController {
	/**
	 * Lists all ProductSpecification models.
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel = new ProductSpecificationSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single ProductSpecification model.
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionView($id) {
		$model = ProductSpecification::find()
							->with(['part.unit'])
							->where(['id'=>$id])->one();
		if(!$model) throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
		return $this->render('view', [
			'model' => $model,
			'tree' => $model->getTree(),
		]);
	}

		
	/**
	 * Creates a new ProductSpecification model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionCreate() {
		$model = new ProductSpecification();
		$model->status = ProductSpecification::STATUS_INACTIVE;
		$model->amount = 1;
		$errorlist = [];
		if ($model->load(Yii::$app->request->post())) {
			if (is_array($_POST['items']['detail']) and count($_POST['items']['detail']) < 2) {
				$errorlist = [
					'details' => [
						'no_item' => [
							[Yii::t('app', 'You must select at least one part.')]
						]
					]
				];
				return $this->render('create', [
					'model' => $model,
					'items' => $_POST['items'],
					'errorlist' => $errorlist,
				]);
			}
			$transaction = Yii::$app->db->beginTransaction();
			if ($model->save()) {
				if (is_array($_POST['items']['detail']) and count($_POST['items']['detail']) > 1) {
					foreach ($_POST['items']['detail'] as $key => $value) {
						if ($key == 0) {
							continue;
						}
						if($_POST['items']['detail'][$key] == $model->part_id){
							$errorlist[$_POST['items']['num'][$key]] = ["message"=>[Yii::t('app','Part and sub part should not be equal')]];
						}
						$item = new ProductSpecificationItem();
						$item->product_specification_id = $model->id;
						$item->part_id = $_POST['items']['detail'][$key];
						$item->usage_qty = $_POST['items']['quantity'][$key];
						$item->warehouse_id = $_POST['items']['warehouse'][$key];
						$item->related_specification_id = ISSET($_POST['items']['spec'][$key]) ? $_POST['items']['spec'][$key] : null;
						if (!$item->save()) {
							$errorlist[$_POST['items']['num'][$key]] = $item->getErrors();
						}
					}
				}
				if($model->status == ProductSpecification::STATUS_ACTIVE){
					$model->copyToPartPart(true);
					$log = new BomLog();
					$log->attributes = [
						'fullname' => Yii::$app->user->identity->fullname,
						'action' => 'create',
						'subject' => $model->code,
						'comment' => $model->description
					];
					$log->save();
				}

				if (count($errorlist) == 0) {
					$transaction->commit();
					Yii::$app->session->setFlash('success', Yii::t('app', 'Document created successfully.'));
					return $this->redirect(['index']);
				} else {
					$transaction->rollBack();
					return $this->render('create', [
						'errorlist' => ['details' => $errorlist],
						'model' => $model,
						'items' => $_POST['items'],
					]);
				}
			} else {
				return $this->render('create', [
					'model' => $model,
					'items' => $_POST['items'],
					'errorlist' => $errorlist,
				]);
			}
		} else {
			return $this->render('create', [
				'model' => $model,
				'items' => [],
				'errorlist' => $errorlist,
			]);
		}
	}

	/**
	 * Updates an existing ProductSpecification model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionUpdate($id) {
		$model = ProductSpecification::find()->with('productSpecificationItems.part')->where(['id' => $id])->one();
		
		if (!$model) {
			throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
		}

		// if(ProductionOrder::find()->where(['product_specification_id'=>$id])->exists()) {
		// 	throw new ForbiddenHttpException(Yii::t('app', 'You are not allowed to edit this record.'));
		// }

		$errorlist = [];

		if ($model->load(Yii::$app->request->post())) {
			if (is_array($_POST['items']['detail']) and count($_POST['items']['detail']) < 2) {
				$errorlist = [
					'details' => [
						'no_item' => [
							[Yii::t('app', 'You must select at least one part.')]
						]
					]
				];
				return $this->render('update', [
					'errorlist' => $errorlist,
					'model' => $model,
					'items' => $_POST['items'],
				]);
			}
			
			$transaction = Yii::$app->db->beginTransaction();
			if ($model->save()) {
				$changes = '';
				if (is_array($_POST['items']['detail']) and count($_POST['items']['detail']) > 1) {
					ProductSpecificationItem::deleteAll(['product_specification_id'=>$model->id]);
					foreach ($_POST['items']['detail'] as $key => $value) {
						if ($key == 0) {
							continue;
						}
						if($_POST['items']['detail'][$key] == $model->part_id){
							$errorlist[$_POST['items']['num'][$key]] = ["message"=>[Yii::t('app','Part and sub part should not be equal')]];
						}
						$item = new ProductSpecificationItem();
						$item->product_specification_id = $model->id;
						$item->part_id = $_POST['items']['detail'][$key];
						$item->usage_qty = $_POST['items']['quantity'][$key];
						$item->warehouse_id = $_POST['items']['warehouse'][$key];
						$item->related_specification_id = isset($_POST['items']['spec'][$key]) ? $_POST['items']['spec'][$key] : null;
						if (!$item->save()) {
							$errorlist[$_POST['items']['num'][$key]] = $item->getErrors();
						}
						$changes .= $item->part_id.','.$item->usage_qty.','.$item->warehouse_id.';';
					}
				}
				if($model->status == ProductSpecification::STATUS_ACTIVE){
					$model->copyToPartPart(true);
					$log = new BomLog();
					$log->attributes = [
						'fullname' => Yii::$app->user->identity->fullname,
						'action' => 'update',
						'subject' => $changes,
						'comment' => $model->description
					];
					$log->save();
				}
				if (count($errorlist) == 0) {
					$transaction->commit();
					Yii::$app->session->setFlash('success', Yii::t('app', 'Document created successfully.'));
					return $this->redirect(['index']);
				} else {
					$transaction->rollBack();
					return $this->render('create', [
						'errorlist' => ['details' => $errorlist],
						'model' => $model,
						'items' => $_POST['items'],
					]);
				}
			} else {
				return $this->render('create', [
					'model' => $model,
					'items' => $_POST['items'],
					'errorlist' => $errorlist,
				]);
			}
		} else {
			
			$items['num'][] = '';
			$items['detail'][] = '';
			$items['partname'][] = '';
			$items['parttype'][] = '';
			$items['quantity'][] = '';
			$items['warehouse'][] = '';
			$items['spec'][] = '';
			$i = 0;
			foreach ($model->productSpecificationItems as $item) {
				$items['num'][] = ++$i;
				$items['detail'][] = $item->part_id;
				$items['partname'][] = $item->part->part_name;
				$items['parttype'][] = $item->part->partType ? $item->part->partType->typename : null;
				$items['quantity'][] = $item->usage_qty;
				$items['warehouse'][] = $item->warehouse_id;
				$items['spec'][] = $item->related_specification_id;
			}

			return $this->render('update', [
				'model' => $model,
				'items' => $items,
				'errorlist' => $errorlist,
			]);
		}
	}

	public function actionFetchByPart($id) {
		Yii::$app->response->format = Response::FORMAT_JSON;
		$data = ProductSpecification::find()->where(['part_id'=>$id])
			->orderBy([
				'status' => SORT_DESC,
				'code'=>SORT_ASC
			])
			->all();
		$list = [];
		foreach($data as $item)
		{
			$list[] = ['id'=>$item->id, 'text' => $item->code, 'status' => $item->status == ProductSpecification::STATUS_ACTIVE ? 'black': 'red'];
		}
		return $list;
	}

	public function actionDuplicate($id) {
		$model = ProductSpecification::find()->with('productSpecificationItems')->where(['id' => $id])->one();
		$model->status = ProductSpecification::STATUS_INACTIVE;
		if (!$model) {
			throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
		}
    $model->code = $model->getNextCode();
		$errorlist = [];
		$items['num'][] = '';
		$items['detail'][] = '';
		$items['quantity'][] = '';
		$items['warehouse'][] = '';
		$items['spec'][] = '';
		$i = 0;
		foreach ($model->productSpecificationItems as $item) {
			$items['num'][] = ++$i;
			$items['detail'][] = $item->part_id;
			$items['quantity'][] = $item->usage_qty;
			$items['warehouse'][] = $item->warehouse_id;
			$items['spec'][] = $item->related_specification_id;
		}

		return $this->render('create', [
			'model' => $model,
			'items' => $items,
			'errorlist' => $errorlist,
		]);

	}

	public function actionActivate($id) {
		$model = $this->findModel($id);
		$model->status = ProductSpecification::STATUS_ACTIVE;
		if($model->save()) {
			$model->copyToPartPart(true);
		}
		return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
	}

	/**
	 * Deletes an existing ProductSpecification model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionDelete($id) {
		if(ProductionOrder::find()->where(['product_specification_id'=>$id])->exists()) {
			throw new ForbiddenHttpException(Yii::t('app', 'You are not allowed to edit this record.'));
		}
		$model = $this->findModel($id);
		if($model->status == ProductSpecification::STATUS_ACTIVE) {
			PartPart::deleteAll(['part_id' => $model->part_id]);
		}
		$log = new BomLog();
		$log->attributes = [
			'fullname' => Yii::$app->user->identity->fullname,
			'action' => 'delete',
			'subject' => $model->code,
			'comment' => $model->description
		];
		$log->save();
		$model->delete();

		return $this->redirect(['index']);
	}

	/**
	 * Finds the ProductSpecification model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * @param integer $id
	 * @return ProductSpecification the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id) {
		if (($model = ProductSpecification::findOne($id)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
	}

	public function actionXls() {
    ini_set('memory_limit', '-1');
    $searchModel = new ProductSpecificationSearch();
    $xsl_file = $searchModel->search(Yii::$app->request->queryParams, 'excel');
    $xsl_file->send(Helpers::downloadFileName('specs'));
    die;
  }
}
