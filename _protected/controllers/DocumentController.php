<?php

namespace app\controllers;

use app\components\Helpers;
use app\models\ConsumptionForm;
use app\models\Contract;
use app\models\ScanningForm;
use app\models\Document;
use app\models\DocumentDetail;
use app\models\DocumentDetailSub;
use app\models\DocumentSearch;
use app\models\DocumentType;
use app\models\DocumentUploadForm;
use app\models\Part;
use app\models\ProductionOrder;
use app\models\Stock;
use app\models\StockInfo;
use app\models\StockInfoWrapper;
use app\models\Supplier;
use app\models\Warehouse;
use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

class DocumentController extends AppController
{

	public function actionIndex()
	{
		
		$searchModel = new DocumentSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->sort->defaultOrder = ['id' => SORT_DESC];

		
		$dataProvider->query->andWhere([
			'or',
			['to_warehouse_id' => Yii::$app->user->identity->warehouseIds],
			['from_warehouse_id' => Yii::$app->user->identity->warehouseIds]
		]);
	

		return $this->render('index', [
			'DocumentSearch' => $searchModel,
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionIndexScan()
	{
		$searchModel = new DocumentSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->sort->defaultOrder = ['id' => SORT_DESC];
		
		
		$dataProvider->query->andWhere([
			'or',
			['to_warehouse_id' => Yii::$app->user->identity->warehouseIds],
			['from_warehouse_id' => Yii::$app->user->identity->warehouseIds]
		]);
		
		
		return $this->render('index-scan', [
			'DocumentSearch' => $searchModel,
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	public function actionView($id)
	{
		return $this->render('view', [
			'model' => $this->findModel($id),
		]);
	}

	public function actionCreateAct()
	{
		$document_type_id = 3; // act
		$model = new Document();
		$model->scenario = 'act';
		$modelItems = new DocumentDetail();
		$model->docdate = date('d.m.Y');
		$errorlist = [];
		$isNewRecord = true;
		if (Yii::$app->user->identity->roleName == 'mrp') {
			if (Yii::$app->user->identity->act_access != 1) {
				Yii::$app->session->setFlash('error', Yii::t('app', 'You are not allowed to do this action.'));
				return $this->redirect(['index']);
			}
		}
		if ($model->load(Yii::$app->request->post())) {
			if (is_array($_POST['items']['detail']) and count($_POST['items']['detail']) < 2) {
				$errorlist = [
					'details' => [
						'no_item' => [
							[Yii::t('app', 'You must select at least one part.')]
						]
					]
				];
				return $this->render('create-act', [
					'errorlist' => $errorlist ?? null,
					'model' => $model,
					'items' => $_POST['items'],
					'modelItems' => $modelItems,
					'isNewRecord' => $isNewRecord,
					'user_warehouses' => $this->userWarehouses,
				]);
			}
			$transaction = Yii::$app->db->beginTransaction();
			$model->document_type_id = $document_type_id;
			$model->docnum = DocumentType::generateDocnum($document_type_id);
			$model->docdate = date("Y-m-d", strtotime($model->docdate));
			if ($model->adj == 0) {
				// rasxod
				$model->from_warehouse_id = $model->adj_wh_id;
				$model->to_warehouse_id = Yii::$app->params['adjustmentWhId'];
			} else {
				// prixod
				$model->from_warehouse_id = Yii::$app->params['adjustmentWhId'];
				$model->to_warehouse_id = $model->adj_wh_id;
			}
			$model->status = 1;
			if ($model->save()) {
				if (is_array($_POST['items']['detail']) and count($_POST['items']['detail']) > 1) {
					$data = []; // for stock fucntion
					foreach ($_POST['items']['detail'] as $key => $value) {
						if ($key == 0) {
							continue;
						}
						$item = new DocumentDetail();
						$item->document_id = $model->id;
						$item->part_id = $_POST['items']['detail'][$key];
						$item->qty = $_POST['items']['quantity'][$key];
						if (!$item->save()) {
							$errorlist[$_POST['items']['num'][$key]] = $item->getErrors();
						} else {
							unset($tmpArr);
							$tmpArr['part_id'] = $item->part_id;
							$tmpArr['qty'] = $item->qty;
							$data[] = $tmpArr;
						}
					}
					if ($model->adj == 0) {
						$stockResult = Stock::issue($model->from_warehouse_id, $data);
					} else {
						$stockReceiptResult = Stock::receipt($model->to_warehouse_id, $data);
					}
				}
				if (count($errorlist) == 0 and (($stockResult['success'] ?? null) or ($stockReceiptResult['success'] ?? null))) {
					$transaction->commit();
					Yii::$app->session->setFlash('success', Yii::t('app', 'Document created successfully.'));
					return $this->redirect(['index']);
				} else {
					$transaction->rollBack();
					return $this->render('create-act', [
						'errorlist' => ['details' => $errorlist, 'stock' => $stockResult['errorlist']],
						'model' => $model,
						'items' => $_POST['items'],
						'modelItems' => $modelItems,
						'isNewRecord' => $isNewRecord,
						'user_warehouses' => $this->userWarehouses,
					]);
				}
			} else {
				return $this->render('create-act', [
					'model' => $model,
					'items' => $_POST['items'],
					'modelItems' => $modelItems,
					'isNewRecord' => $isNewRecord,
					'user_warehouses' => $this->userWarehouses,
				]);
			}
		} else {
			return $this->render('create-act', [
				'model' => $model,
				'modelItems' => $modelItems,
				'isNewRecord' => $isNewRecord,
				'user_warehouses' => $this->userWarehouses,
			]);
		}
	}

	public function actionCreate()
	{
		$document_type_id = 2; // nakladnoy
		$model = new Document();
		$modelItems = new DocumentDetail();
		$model->docdate = date('d.m.Y');
		$errorlist = [];
		$isNewRecord = true;
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
					'errorlist' => $errorlist ?? null,
					'model' => $model,
					'items' => $_POST['items'],
					'modelItems' => $modelItems,
					'isNewRecord' => $isNewRecord,
					'user_warehouses' => $this->userWarehouses,
				]);
			}
			$transaction = Yii::$app->db->beginTransaction();
			$model->document_type_id = $document_type_id;
			$model->docnum = DocumentType::generateDocnum($document_type_id);
			$model->docdate = date("Y-m-d", strtotime($model->docdate));
			if ($model->save()) {
				if (is_array($_POST['items']['detail']) and count($_POST['items']['detail']) > 1) {
					$data = []; // for stock fucntion
					foreach ($_POST['items']['detail'] as $key => $value) {
						if ($key == 0) {
							continue;
						}
						$item = new DocumentDetail();
						$item->document_id = $model->id;
						$item->part_id = $_POST['items']['detail'][$key];
						$item->qty = $_POST['items']['quantity'][$key];
						$hasSubParts = Part::findOne($item->part_id)->hasSubParts ?? null;
						if ($hasSubParts and $model->fromWarehouse->warehouse_type != Warehouse::TYPE_PHYSICAL) {
							$item->sub = 1;
						}
						if (!$item->save()) {
							$errorlist[$_POST['items']['num'][$key]] = $item->getErrors();
						} else {
							// produce changes
							if (false and $hasSubParts and $model->fromWarehouse->warehouse_type != Warehouse::TYPE_PHYSICAL) {
								// agar bu detal sub bomda ota detal bolsa va rasxod qilayotgan sklad fizicheskiy bolmasa uni bollarini rasxod qilamiz
								$dataSub = [];
								foreach ($item->part->subParts as $subPart) {
									// har bir sub detalni ulock boyicha rasxod qilib chiqamiz
									if ($model->from_warehouse_id == $subPart->warehouse_id) {
										$dataSub = [
											[
												'part_id' => $subPart->sub_part_id,
												'qty' => $item->qty * $subPart->usage_qty
											]
										];
										$stockResultSub = Stock::issueFromShop($subPart->warehouse_id, $dataSub);
										if (!$stockResultSub['success']) {
											$errorlist[$_POST['items']['num'][$key]] = [[Yii::t('app', 'Issue problem')]];
										}
										// sub table ga rasxod qilingan detallarni yozib qoyamiz
										$documentDetailSub = new DocumentDetailSub();
										$documentDetailSub->document_id = $model->id;
										$documentDetailSub->part_id = $subPart->part_id;
										$documentDetailSub->sub_part_id = $subPart->sub_part_id;
										$documentDetailSub->qty = $item->qty * $subPart->usage_qty;
										$documentDetailSub->warehouse_id = $subPart->warehouse_id;
										if (!$documentDetailSub->save()) {
											$errorlist[$_POST['items']['num'][$key]] = [[Yii::t('app', 'Document sub-detail insert problem')]];
										}
									}
								}
							} else {
								// agar bu detal sub bomda ota detal bolmasa yoki rasxod qilayotgan sklad fizicheskiy bolsa uni o'zini rasxod qilamiz
								unset($tmpArr);
								$tmpArr['part_id'] = $item->part_id;
								$tmpArr['qty'] = $item->qty;
								$data[] = $tmpArr;
							}
						}
					}
					// shu joyda stock table ga ham change qilish kk
					// - from_wh dan agar ostatkasi yetsa kamaytirish
					$stockResult = Stock::issue($model->from_warehouse_id, $data);
				}
				if (count($errorlist) == 0 and $stockResult['success']) {
					$transaction->commit();
					// $this->writeToDocHistory($model->id, $this->action->id);
					Yii::$app->session->setFlash('success', Yii::t('app', 'Document created successfully.'));
					return $this->redirect(['index']);
				} else {
					$transaction->rollBack();
					return $this->render('create', [
						'errorlist' => ['details' => $errorlist, 'stock' => $stockResult['errorlist'] ?? null],
						'model' => $model,
						'items' => $_POST['items'],
						'modelItems' => $modelItems,
						'isNewRecord' => $isNewRecord,
						'user_warehouses' => $this->userWarehouses,
					]);
				}
			} else {
				return $this->render('create', [
					'model' => $model,
					'items' => $_POST['items'],
					'modelItems' => $modelItems,
					'isNewRecord' => $isNewRecord,
					'user_warehouses' => $this->userWarehouses,
				]);
			}
		} else {
			return $this->render('create', [
				'model' => $model,
				'modelItems' => $modelItems,
				'isNewRecord' => $isNewRecord,
				'user_warehouses' => $this->userWarehouses,
			]);
		}
	}

  /**
   * Anvar Sanakulov
   * 2024-01-24 21:44
   * @sanakulov_Dev
   * actionCreateInfo
   */
  public function actionCreateInfo()
	{
		$document_type_id = 2; // nakladnoy
		$model = new Document();
		$modelItems = new DocumentDetail();
		$model->docdate = date('d.m.Y');
		$errorlist = [];
		$isNewRecord = true;

    // Stock Info Wrapper
    $stockInfoWrapper = new StockInfoWrapper();

		if ($model->load(Yii::$app->request->post())) {
      
			// vd(Yii::$app->request->post());
			if (is_array($_POST['items']['detail']) and count($_POST['items']['detail']) < 2) {
				$errorlist = [
					'details' => [
						'no_item' => [
							[Yii::t('app', 'You must select at least one part.')]
						]
					]
				];
				return $this->render('create-info', [
					'errorlist' => $errorlist ?? null,
					'model' => $model,
					'items' => $_POST['items'],
					'modelItems' => $modelItems,
					'isNewRecord' => $isNewRecord,
					'user_warehouses' => $this->userWarehouses,
				]);
			}
			// $transaction = Yii::$app->db->beginTransaction();
			$model->document_type_id = $document_type_id;
			$model->docnum = DocumentType::generateDocnum($document_type_id);
			$model->docdate = date("Y-m-d", strtotime($model->docdate));
      $model->to_warehouse_id = 0;
			if ($model->save(false)) {
        /**
         * Stock Info Wrapper table insert informations
         */
        $stockInfoWrapper->warehouse_id = $model->from_warehouse_id;
        $stockInfoWrapper->document_id = $model->id;
        $stockInfoWrapper->comment = $model->comment;
        $stockInfoWrapper->type_id = $model->type_id;
        $stockInfoWrapper->give_user_id = Yii::$app->user->id;
        $stockInfoWrapper->save(false);
        
        // vd($model->errors);
				if (is_array($_POST['items']['detail']) and count($_POST['items']['detail']) > 1) {
					$data = []; // for stock fucntion
					foreach ($_POST['items']['detail'] as $key => $value) {
						if ($key == 0) {
							continue;
						}
						$item = new DocumentDetail();
						$item->document_id = $model->id;
						$item->part_id = $_POST['items']['detail'][$key];
						$item->qty = $_POST['items']['quantity'][$key];
						$hasSubParts = Part::findOne($item->part_id)->hasSubParts ?? null;
						if ($hasSubParts and $model->fromWarehouse->warehouse_type != Warehouse::TYPE_PHYSICAL) {
							$item->sub = 1;
						}
						if (!$item->save()) {
							$errorlist[$_POST['items']['num'][$key]] = $item->getErrors();
						} else {
							// produce changes
							if (false and $hasSubParts and $model->fromWarehouse->warehouse_type != Warehouse::TYPE_PHYSICAL) {
								// agar bu detal sub bomda ota detal bolsa va rasxod qilayotgan sklad fizicheskiy bolmasa uni bollarini rasxod qilamiz
								$dataSub = [];
								foreach ($item->part->subParts as $subPart) {
									// har bir sub detalni ulock boyicha rasxod qilib chiqamiz
									if ($model->from_warehouse_id == $subPart->warehouse_id) {
										$dataSub = [
											[
												'part_id' => $subPart->sub_part_id,
												'qty' => $item->qty * $subPart->usage_qty
											]
										];
										$stockResultSub = Stock::issueFromShop($subPart->warehouse_id, $dataSub);
                    
										if (!$stockResultSub['success']) {
											$errorlist[$_POST['items']['num'][$key]] = [[Yii::t('app', 'Issue problem')]];
										}
										// sub table ga rasxod qilingan detallarni yozib qoyamiz
										$documentDetailSub = new DocumentDetailSub();
										$documentDetailSub->document_id = $model->id;
										$documentDetailSub->part_id = $subPart->part_id;
										$documentDetailSub->sub_part_id = $subPart->sub_part_id;
										$documentDetailSub->qty = $item->qty * $subPart->usage_qty;
										$documentDetailSub->warehouse_id = $subPart->warehouse_id;
										if (!$documentDetailSub->save()) {
											$errorlist[$_POST['items']['num'][$key]] = [[Yii::t('app', 'Document sub-detail insert problem')]];
										}
									}
								}
							} else {
								// agar bu detal sub bomda ota detal bolmasa yoki rasxod qilayotgan sklad fizicheskiy bolsa uni o'zini rasxod qilamiz
								unset($tmpArr);

  


								$tmpArr['part_id'] = $item->part_id;
								$tmpArr['qty'] = $item->qty;
                $tmpArr['stock_info_wrapper_id'] = $stockInfoWrapper->id;
								$data[] = $tmpArr;
							}
						}
					}
					// shu joyda stock table ga ham change qilish kk
					// - from_wh dan agar ostatkasi yetsa kamaytirish
					$stockResult = Stock::issue($model->from_warehouse_id, $data, true);
				}
				if (count($errorlist) == 0 and $stockResult['success']) {
					// $transaction->commit();
					// $this->writeToDocHistory($model->id, $this->action->id);
					Yii::$app->session->setFlash('success', Yii::t('app', 'Document created successfully.'));
					return $this->redirect(['index']);
				} else {
					// $transaction->rollBack();
					return $this->render('create-info', [
						'errorlist' => ['details' => $errorlist, 'stock' => $stockResult['errorlist'] ?? null],
						'model' => $model,
						'items' => $_POST['items'],
						'modelItems' => $modelItems,
						'isNewRecord' => $isNewRecord,
						'user_warehouses' => $this->userWarehouses,
					]);
				}
			} else {
				return $this->render('create-info', [
					'model' => $model,
					'items' => $_POST['items'],
					'modelItems' => $modelItems,
					'isNewRecord' => $isNewRecord,
					'user_warehouses' => $this->userWarehouses,
				]);
			}
		} else {
			return $this->render('create-info', [
				'model' => $model,
				'modelItems' => $modelItems,
				'isNewRecord' => $isNewRecord,
				'user_warehouses' => $this->userWarehouses,
			]);
		}
	}

	// Ushbu funksiya Davalskiy detallarni prixod qilish uchun ishlatiladi
	// Yani prixod qilingan detal subass bolsa uni tashkil etuvchilar postavshikni ostatkasidan ayriladi
	// Agar subass bolmasa detalni ozi postavshikni ostatkasidan ayriladi
	public function actionCreateLocal()
	{
		$document_type_id = 2; // nakladnoy
		$model = new Document();
		$modelItems = new DocumentDetail();
		$model->docdate = date('d.m.Y');
		$errorlist = [];
		$isNewRecord = true;
		if ($model->load(Yii::$app->request->post())) {
			if (is_array($_POST['items']['detail']) and count($_POST['items']['detail']) < 2) {
				$errorlist = [
					'details' => [
						'no_item' => [
							[Yii::t('app', 'You must select at least one part.')]
						]
					]
				];
				return $this->render('create-local', [
					'errorlist' => $errorlist ?? null,
					'model' => $model,
					'items' => $_POST['items'],
					'modelItems' => $modelItems,
					'isNewRecord' => $isNewRecord,
					'user_warehouses' => $this->userWarehouses,
				]);
			}
			$transaction = Yii::$app->db->beginTransaction();
			$model->document_type_id = $document_type_id;
			$model->docnum = DocumentType::generateDocnum($document_type_id);
			$model->docdate = date("Y-m-d", strtotime($model->docdate));
			$model->status = 1;
			if ($model->save()) {
				if (is_array($_POST['items']['detail']) and count($_POST['items']['detail']) > 1) {
					$data = []; // for stock fucntion
					$dataReceipt = [];
					foreach ($_POST['items']['detail'] as $key => $value) {
						if ($key == 0) {
							continue;
						}
						$item = new DocumentDetail();
						$item->document_id = $model->id;
						$item->part_id = $_POST['items']['detail'][$key];
						$item->qty = $_POST['items']['quantity'][$key];
						$hasSubParts = Part::findOne($item->part_id)->hasSubParts ?? null;
						if ($hasSubParts) {
							$item->sub = 1;
						}
						if (!$item->save()) {
							$errorlist[$_POST['items']['num'][$key]] = $item->getErrors();
						} else {
							if ($hasSubParts) {
								// agar bu detal sub bomda ota detal bolsa va rasxod qilayotgan sklad fizicheskiy bolmasa uni bollarini rasxod qilamiz
								$dataSub = [];
								foreach ($item->part->subParts as $subPart) {
									// har bir sub detalni ulock boyicha rasxod qilib chiqamiz
									//
									// UzCCda 1 ta davalskiy detal bir necha postavshikdan kelganligi uchun
									// rasxodni BOM dagi ULOC bo'yicha emas, tanlangan postavshik bo'yicha qilamiz
									$dataSub = [
										[
											'part_id' => $subPart->sub_part_id,
											'qty' => $item->qty * $subPart->usage_qty
										]
									];
									$stockResultSub = Stock::issueFromShop($model->from_warehouse_id, $dataSub);
									if (!$stockResultSub['success']) {
										$errorlist[$_POST['items']['num'][$key]] = [[Yii::t('app', 'Issue problem')]];
									}
									// sub table ga rasxod qilingan detallarni yozib qoyamiz
									$documentDetailSub = new DocumentDetailSub();
									$documentDetailSub->document_id = $model->id;
									$documentDetailSub->part_id = $subPart->part_id;
									$documentDetailSub->sub_part_id = $subPart->sub_part_id;
									$documentDetailSub->qty = $item->qty * $subPart->usage_qty;
									$documentDetailSub->warehouse_id = $model->from_warehouse_id;
									if (!$documentDetailSub->save()) {
										$errorlist[$_POST['items']['num'][$key]] = [[Yii::t('app', 'Document sub-detail insert problem')]];
									}
								}
							} else {
								unset($tmpArr);
								$tmpArr['part_id'] = $item->part_id;
								$tmpArr['qty'] = $item->qty;
								$data[] = $tmpArr;
							}
							unset($tmpArr);
							$tmpArr['part_id'] = $item->part_id;
							$tmpArr['qty'] = $item->qty;
							$dataReceipt[] = $tmpArr;
						}
					}
					// Prixod qilinayotgan skladni ostatkasini ko'paytirish
					$stockResultReceipt = Stock::receipt($model->to_warehouse_id, $dataReceipt);
					// Rasxod qilgan Postavshik skladni ostatkasidan yetmasa ham kamaytirish (outsourcing)
					$stockResultIssue = Stock::issueFromShop($model->from_warehouse_id, $data);
				}


				if (count($errorlist) == 0 and $stockResultReceipt['success'] and $stockResultIssue['success']) {
					$transaction->commit();
					Yii::$app->session->setFlash('success', Yii::t('app', 'Document created successfully.'));
					return $this->redirect(['index']);
				} else {
					$transaction->rollBack();
					return $this->render('create-local', [
						'errorlist' => ['details' => $errorlist, 'stock' => $stockResultReceipt['errorlist'] ?? null],
						'model' => $model,
						'items' => $_POST['items'],
						'modelItems' => $modelItems,
						'isNewRecord' => $isNewRecord,
						'user_warehouses' => $this->userWarehouses,
					]);
				}


			} else {
				return $this->render('create-local', [
					'errorlist' => $errorlist ?? null,
					'model' => $model,
					'items' => $_POST['items'],
					'modelItems' => $modelItems,
					'isNewRecord' => $isNewRecord,
					'user_warehouses' => $this->userWarehouses,
				]);
			}
		} else {
			return $this->render('create-local', [
				'errorlist' => $errorlist ?? null,
				'model' => $model,
				'items' => [],
				'modelItems' => $modelItems,
				'isNewRecord' => $isNewRecord,
				'user_warehouses' => $this->userWarehouses,
			]);
		}
	}

	// Ushbu funksiya dovalsiy detallarini zavoddan rasxod qilish uchun ishlatiladi
	// Yani agar zavoddan sub.ass detal chiqib ketsa,
	// chiqib ketayotgan skladdan uni tashkil etuvchilari bom boyicha rasxod qilinadi
	// Agar rasxod qilinayotgan detal oddiy bolsa o'zi zavod skladidan ayriladi
	public function actionCreateLocalIssue()
	{
		$document_type_id = 2; // nakladnoy
		$model = new Document();
		$modelItems = new DocumentDetail();
		$model->docdate = date('d.m.Y');
		$errorlist = [];
		$isNewRecord = true;
		if ($model->load(Yii::$app->request->post())) {
			if (is_array($_POST['items']['detail']) and count($_POST['items']['detail']) < 2) {
				$errorlist = [
					'details' => [
						'no_item' => [
							[Yii::t('app', 'You must select at least one part.')]
						]
					]
				];
				return $this->render('create-local-issue', [
					'errorlist' => $errorlist ?? null,
					'model' => $model,
					'items' => $_POST['items'],
					'modelItems' => $modelItems,
					'isNewRecord' => $isNewRecord,
					'user_warehouses' => $this->userWarehouses,
				]);
			}
			$transaction = Yii::$app->db->beginTransaction();
			$model->document_type_id = $document_type_id;
			$model->docnum = DocumentType::generateDocnum($document_type_id);
			$model->docdate = date("Y-m-d", strtotime($model->docdate));
			$model->status = 1;
			if ($model->save()) {
				if (is_array($_POST['items']['detail']) and count($_POST['items']['detail']) > 1) {
					$data = []; // for stock fucntion
					$dataReceipt = []; // postavshikga katta detalni prixod qilish uchun
					foreach ($_POST['items']['detail'] as $key => $value) {
						if ($key == 0) {
							continue;
						}
						$item = new DocumentDetail();
						$item->document_id = $model->id;
						$item->part_id = $_POST['items']['detail'][$key];
						$item->qty = $_POST['items']['quantity'][$key];
						$hasSubParts = Part::findOne($item->part_id)->hasSubParts ?? null;
						if ($hasSubParts and $model->fromWarehouse->warehouse_type != Warehouse::TYPE_PHYSICAL) {
							$item->sub = 1;
						}
						if (!$item->save()) {
							$errorlist[$_POST['items']['num'][$key]] = $item->getErrors();
						} else {
							// produce changes
							if (false and $hasSubParts and $model->fromWarehouse->warehouse_type != Warehouse::TYPE_PHYSICAL) {
								// agar bu detal sub bomda ota detal bolsa va rasxod qilayotgan sklad fizicheskiy bolmasa uni bollarini rasxod qilamiz
								$dataSub = [];
								foreach ($item->part->subParts as $subPart) {
									// har bir sub detalni ulock boyicha rasxod qilib chiqamiz
									if ($model->from_warehouse_id == $subPart->warehouse_id) {
										$dataSub = [
											[
												'part_id' => $subPart->sub_part_id,
												'qty' => $item->qty * $subPart->usage_qty
											]
										];
										$stockResultSub = Stock::issueFromShop($subPart->warehouse_id, $dataSub);
										if (!$stockResultSub['success']) {
											$errorlist[$_POST['items']['num'][$key]] = [[Yii::t('app', 'Issue problem')]];
										}
										// sub table ga rasxod qilingan detallarni yozib qoyamiz
										$documentDetailSub = new DocumentDetailSub();
										$documentDetailSub->document_id = $model->id;
										$documentDetailSub->part_id = $subPart->part_id;
										$documentDetailSub->sub_part_id = $subPart->sub_part_id;
										$documentDetailSub->qty = $item->qty * $subPart->usage_qty;
										$documentDetailSub->warehouse_id = $subPart->warehouse_id;
										if (!$documentDetailSub->save()) {
											$errorlist[$_POST['items']['num'][$key]] = [[Yii::t('app', 'Document sub-detail insert problem')]];
										}
									}
								}
								// katta detal ni o'zini postavshikka prixod qilish uchun ma'lumot tayyorlaymiz
								unset($tmpArr);
								$tmpArr['part_id'] = $item->part_id;
								$tmpArr['qty'] = $item->qty;
								$dataReceipt[] = $tmpArr;
							} else {
								// agar bu detal sub bomda ota detal bolmasa yoki rasxod qilayotgan sklad fizicheskiy bolsa uni o'zini rasxod qilamiz
								unset($tmpArr);
								$tmpArr['part_id'] = $item->part_id;
								$tmpArr['qty'] = $item->qty;
								$data[] = $tmpArr;
								$dataReceipt = $data;
							}
						}
					}
					// Postavshikka rasxod qilayotgan skladni ostatkasidan yetsa ayirish
					$stockResultIssue = Stock::issue($model->from_warehouse_id, $data);
					// Tanlangan postavshik skladiga prixod qilish (outsourcing)
					$stockResultReceipt = Stock::receipt($model->to_warehouse_id, $dataReceipt);
				}
				if (count($errorlist) == 0 and $stockResultIssue['success'] and $stockResultReceipt['success']) {
					$transaction->commit();
					// $this->writeToDocHistory($model->id, $this->action->id);
					Yii::$app->session->setFlash('success', Yii::t('app', 'Document created successfully.'));
					return $this->redirect(['index']);
				} else {
					$transaction->rollBack();
					//     echo "<pre>";
					// print_r($stockResultReceipt);
					// echo "</pre>";
					// die;
					return $this->render('create-local-issue', [
						'errorlist' => ['details' => $errorlist, 'stock' => $stockResultIssue['errorlist']],
						'model' => $model,
						'items' => $_POST['items'],
						'modelItems' => $modelItems,
						'isNewRecord' => $isNewRecord,
						'user_warehouses' => $this->userWarehouses,
					]);
				}
			} else {
				echo "<pre>";
				print_r($model->errors);
				echo "</pre>";
				die;
				return $this->render('create-local-issue', [
					'model' => $model,
					'items' => $_POST['items'],
					'modelItems' => $modelItems,
					'isNewRecord' => $isNewRecord,
					'user_warehouses' => $this->userWarehouses,
				]);
			}
		} else {
			return $this->render('create-local-issue', [
				'model' => $model,
				'modelItems' => $modelItems,
				'isNewRecord' => $isNewRecord,
				'user_warehouses' => $this->userWarehouses,
			]);
		}
	}

	public function actionUpdate($id)
	{
		$isNewRecord = false;
		$model = $this->findModel($id);
		$errorlist = [];

		if ($model->document_type_id != 2) {
			Yii::$app->session->setFlash('error', Yii::t('app', 'You are not allowed to do this action.'));
			return $this->redirect(['index']);
		}

		if (!in_array($model->from_warehouse_id, Yii::$app->user->identity->warehouseIds)) {
			throw new ForbiddenHttpException(Yii::t('app', 'You are not allowed to edit this record.'));
		}
		

		if ($model->status == 1) {
			throw new ForbiddenHttpException(Yii::t('app', 'You are not allowed to edit this record.'));
		}

		if (!empty($model->serial_number)) {
			throw new ForbiddenHttpException(Yii::t('app', 'You are not allowed to edit this record.'));
		}

		$modelItems = new DocumentDetail();
		$items['num'][] = '';
		$items['detail'][] = '';
		$items['partname'][] = '';
		$items['unit'][] = '';
		$items['quantity'][] = '';
		$i = 0;
		foreach ($model->documentDetails as $item) {
			$items['num'][] = ++$i;
			$items['detail'][] = $item->part_id;
			$items['partname'][] = $item->part->part_name;
			$items['unit'][] = $item->part->unit->unit_value;
			$items['quantity'][] = $item->qty;
		}
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
					'errorlist' => $errorlist ?? null,
					'model' => $model,
					'items' => $_POST['items'],
					'modelItems' => $modelItems,
					'user_warehouses' => $this->userWarehouses,
					'isNewRecord' => $isNewRecord,
				]);
			}
			$transaction = Yii::$app->db->beginTransaction();
			$model->docdate = date("Y-m-d", strtotime($model->docdate));
			// write to history before changing
			$historyStatus = $this->writeToDocHistory($model->id, $this->action->id);
			if ($model->save()) {
				// detallarni o'chirish va sublari bo'lmaganlarini qaytarish
				$data_r = [];
				foreach ($model->documentDetails as $detail) {
					// produce changes
					//if(true and $detail->sub != 1){
					unset($tmpArr);
					$tmpArr['part_id'] = $detail->part_id;
					$tmpArr['qty'] = $detail->qty;
					$data_r[] = $tmpArr;
					//}
				}
				DocumentDetail::deleteAll(['document_id' => $model->id]);
				$stockReceiptResult = Stock::receipt($model->from_warehouse_id, $data_r);
				// *****************
				// sub detallarni o'chirish va qaytarish
				foreach ($model->documentDetailsSub as $subDetail) {
					$dataSub = [
						[
							'part_id' => $subDetail->sub_part_id,
							'qty' => $subDetail->qty
						]
					];
					$stockReceiptResulSub = Stock::receipt($subDetail->warehouse_id, $dataSub);
					if (!$stockReceiptResulSub['success']) {
						$errorlist[] = [[Yii::t('app', 'Problem')]];
					}
				}
				DocumentDetailSub::deleteAll(['document_id' => $model->id]);
				// *****************
				if (is_array($_POST['items']['detail']) and count($_POST['items']['detail']) > 1) {
					$data = []; // for stock fucntion
					foreach ($_POST['items']['detail'] as $key => $value) {
						if ($key == 0) {
							continue;
						}
						$item = new DocumentDetail();
						$item->document_id = $model->id;
						$item->part_id = $_POST['items']['detail'][$key];
						$item->qty = $_POST['items']['quantity'][$key];
						$hasSubParts = Part::findOne($item->part_id)->hasSubParts ?? null;
						if ($hasSubParts and $model->fromWarehouse->warehouse_type != Warehouse::TYPE_PHYSICAL) {
							$item->sub = 1;
						}
						if (!$item->save()) {
							$errorlist[$_POST['items']['num'][$key]] = $item->getErrors();
						} else {
							// produce changes
							if (false and $hasSubParts and $model->fromWarehouse->warehouse_type != Warehouse::TYPE_PHYSICAL) {
								// agar bu detal sub bomda ota detal bolsa va rasxod qilayotgan sklad fizicheskiy bolmasa uni bollarini rasxod qilamiz
								$dataSub = [];
								foreach ($item->part->subParts as $subPart) {
									// har bir sub detalni ulock boyicha rasxod qilib chiqamiz
									if ($model->from_warehouse_id == $subPart->warehouse_id) {
										$dataSub = [
											[
												'part_id' => $subPart->sub_part_id,
												'qty' => $item->qty * $subPart->usage_qty
											]
										];
										$stockResultSub = Stock::issueFromShop($subPart->warehouse_id, $dataSub);
										if (!$stockResultSub['success']) {
											$errorlist[$_POST['items']['num'][$key]] = [[Yii::t('app', 'Issue problem')]];
										}
										// sub table ga rasxod qilingan detallarni yozib qoyamiz
										$documentDetailSub = new DocumentDetailSub();
										$documentDetailSub->document_id = $model->id;
										$documentDetailSub->part_id = $subPart->part_id;
										$documentDetailSub->sub_part_id = $subPart->sub_part_id;
										$documentDetailSub->qty = $item->qty * $subPart->usage_qty;
										$documentDetailSub->warehouse_id = $subPart->warehouse_id;
										if (!$documentDetailSub->save()) {
											$errorlist[$_POST['items']['num'][$key]] = [[Yii::t('app', 'Document sub-detail insert problem')]];
										}
									}
								}
							} else {
								// agar bu detal sub bomda ota detal bolmasa yoki rasxod qilayotgan sklad fizicheskiy bolsa uni o'zini rasxod qilamiz
								unset($tmpArr);
								$tmpArr['part_id'] = $item->part_id;
								$tmpArr['qty'] = $item->qty;
								$data[] = $tmpArr;
							}
						}
					}
					// shu joyda stock table ga ham change qilish kk
					// - from_wh dan agar ostatkasi yetsa kamaytirish
					$stockResult = Stock::issue($model->from_warehouse_id, $data);
				}
				if (count($errorlist) == 0 and $stockResult['success'] and $stockReceiptResult['success'] and $historyStatus) {
					$transaction->commit();
					Yii::$app->session->setFlash('success', Yii::t('app', 'Document changed successfully.'));
					return $this->redirect(['index']);
				} else {
					$transaction->rollBack();
					return $this->render('update', [
						'errorlist' => ['details' => $errorlist, 'stock' => $stockResult['errorlist'] ?? null, 'stock_receipt' => $stockReceiptResult['errorlist'] ?? null],
						'model' => $model,
						'items' => $_POST['items'],
						'modelItems' => $modelItems,
						'user_warehouses' => $this->userWarehouses,
						'isNewRecord' => $isNewRecord,
					]);
				}
			} else {
				return $this->render('update', [
					'model' => $model,
					'items' => $items,
					'modelItems' => $modelItems,
					'user_warehouses' => $this->userWarehouses,
					'isNewRecord' => $isNewRecord,
				]);
			}
		} else {
			return $this->render('update', [
				'model' => $model,
				'items' => $items,
				'modelItems' => $modelItems,
				'user_warehouses' => $this->userWarehouses,
				'isNewRecord' => $isNewRecord,
			]);
		}
	}

	public function actionUpdateLocal($id)
	{
		$model = $this->findModel($id);
		$errorlist = [];
		if ($model->document_type_id != 2) {
			Yii::$app->session->setFlash('error', Yii::t('app', 'You are not allowed to do this action.'));
			return $this->redirect(['index']);
		}
		if (Yii::$app->user->identity->roleName == 'mrp') {
			if (!in_array($model->to_warehouse_id, Yii::$app->user->identity->warehouseIds)) {
				throw new ForbiddenHttpException(Yii::t('app', 'You are not allowed to edit this record.'));
			}
		}
		$modelItems = new DocumentDetail();
		$items['num'][] = '';
		$items['detail'][] = '';
		$items['partname'][] = '';
		$items['unit'][] = '';
		$items['quantity'][] = '';
		$i = 0;
		foreach ($model->documentDetails as $item) {
			$items['num'][] = ++$i;
			$items['detail'][] = $item->part_id;
			$items['partname'][] = $item->part->part_name;
			$items['unit'][] = $item->part->unit->unit_value;
			$items['quantity'][] = $item->qty;
		}
		if ($model->load(Yii::$app->request->post())) {
			if (is_array($_POST['items']['detail']) and count($_POST['items']['detail']) < 2) {
				$errorlist = [
					'details' => [
						'no_item' => [
							[Yii::t('app', 'You must select at least one part.')]
						]
					]
				];
				return $this->render('update-local', [
					'errorlist' => $errorlist ?? null,
					'model' => $model,
					'items' => $_POST['items'],
					'modelItems' => $modelItems,
					'user_warehouses' => $this->userWarehouses,
				]);
			}
			$transaction = Yii::$app->db->beginTransaction();
			$model->docdate = date("Y-m-d", strtotime($model->docdate));
			// write to history before changing
			$historyStatus = $this->writeToDocHistory($model->id, $this->action->id);
			if ($model->save()) {
				$data_i = []; // for stock function
				$data_r = [];
				foreach ($model->documentDetails as $detail) {
					if ($detail->sub != 1) {
						unset($tmpArr);
						$tmpArr['part_id'] = $detail->part_id;
						$tmpArr['qty'] = $detail->qty;
						$data_r[] = $tmpArr;
					}
					unset($tmpArr);
					$tmpArr['part_id'] = $detail->part_id;
					$tmpArr['qty'] = $detail->qty;
					$data_i[] = $tmpArr;
				}
				DocumentDetail::deleteAll(['document_id' => $model->id]);

				// Agar Sklad tipi Shop bolsa, ostatkasini minusga tiqsin
				// aks holda minusga tiqmasdan xato bersin
				if ($model->toWarehouse->warehouse_type == Warehouse::TYPE_SHOP) {
					$stockIssueResult = Stock::issueFromShop($model->to_warehouse_id, $data_i);
				} else {
					$stockIssueResult = Stock::issue($model->to_warehouse_id, $data_i);
				}

				// rasxod qilgan postavshik wh ga qaytarib qo'yish
				$stockReceiptResult = Stock::receipt($model->from_warehouse_id, $data_r);
				// sub detallarni o'chirish va qaytarish
				foreach ($model->documentDetailsSub as $subDetail) {
					$dataSub = [
						[
							'part_id' => $subDetail->sub_part_id,
							'qty' => $subDetail->qty
						]
					];
					$stockReceiptResulSub = Stock::receipt($subDetail->warehouse_id, $dataSub);
					if (!$stockReceiptResulSub['success']) {
						$errorlist[] = [[Yii::t('app', 'Problem')]];
					}
				}
				DocumentDetailSub::deleteAll(['document_id' => $model->id]);
				// *****************
				if (is_array($_POST['items']['detail']) and count($_POST['items']['detail']) > 1) {
					$data = []; // for stock fucntion
					foreach ($_POST['items']['detail'] as $key => $value) {
						if ($key == 0) {
							continue;
						}
						$item = new DocumentDetail();
						$item->document_id = $model->id;
						$item->part_id = $_POST['items']['detail'][$key];
						$item->qty = $_POST['items']['quantity'][$key];
						$hasSubParts = Part::findOne($item->part_id)->hasSubParts ?? null;
						if ($hasSubParts) {
							$item->sub = 1;
						}
						if (!$item->save()) {
							$errorlist[$_POST['items']['num'][$key]] = $item->getErrors();
						} else {
							if ($hasSubParts) {
								// agar bu detal sub bomda ota detal bolsa va rasxod qilayotgan sklad fizicheskiy bolmasa uni bollarini rasxod qilamiz
								$dataSub = [];
								foreach ($item->part->subParts as $subPart) {
									// har bir sub detalni ulock boyicha rasxod qilib chiqamiz
									if ($model->from_warehouse_id == $subPart->warehouse_id) {
										$dataSub = [
											[
												'part_id' => $subPart->sub_part_id,
												'qty' => $item->qty * $subPart->usage_qty
											]
										];
										$stockResultSub = Stock::issueFromShop($subPart->warehouse_id, $dataSub);
										if (!$stockResultSub['success']) {
											$errorlist[$_POST['items']['num'][$key]] = [[Yii::t('app', 'Issue problem')]];
										}
										// sub table ga rasxod qilingan detallarni yozib qoyamiz
										$documentDetailSub = new DocumentDetailSub();
										$documentDetailSub->document_id = $model->id;
										$documentDetailSub->part_id = $subPart->part_id;
										$documentDetailSub->sub_part_id = $subPart->sub_part_id;
										$documentDetailSub->qty = $item->qty * $subPart->usage_qty;
										$documentDetailSub->warehouse_id = $subPart->warehouse_id;
										if (!$documentDetailSub->save()) {
											$errorlist[$_POST['items']['num'][$key]] = [[Yii::t('app', 'Document sub-detail insert problem')]];
										}
									}
								}
							} else {
								unset($tmpArr);
								$tmpArr['part_id'] = $item->part_id;
								$tmpArr['qty'] = $item->qty;
								$data[] = $tmpArr;
							}
							unset($tmpArr);
							$tmpArr['part_id'] = $item->part_id;
							$tmpArr['qty'] = $item->qty;
							$dataReceipt[] = $tmpArr;
						}
					}
					// Prixod qilinayotgan skladni ostatkasini ko'paytirish
					$stockResultReceipt = Stock::receipt($model->to_warehouse_id, $dataReceipt);
					// Rasxod qilgan Postavshik skladni ostatkasidan yetmasa ham kamaytirish (outsourcing)
					$stockResultIssue = Stock::issueFromShop($model->from_warehouse_id, $data);
				}
				if (count($errorlist) == 0 and $stockResultReceipt['success'] and $stockReceiptResult['success'] and $stockIssueResult['success'] and $historyStatus) {
					$transaction->commit();
					Yii::$app->session->setFlash('success', Yii::t('app', 'Document changed successfully.'));
					return $this->redirect(['index']);
				} else {
					$transaction->rollBack();
					return $this->render('update-local', [
						'errorlist' => ['details' => $errorlist, 'stock' => $stockResult['errorlist'] ?? null, 'stock_receipt' => $stockReceiptResult['errorlist'] ?? null, 'stock_result_rec' => $stockResultReceipt['errorlist'] ?? null, 'stock_issue' => $stockIssueResult['errorlist'] ?? null],
						'model' => $model,
						'items' => $_POST['items'],
						'modelItems' => $modelItems,
						'user_warehouses' => $this->userWarehouses,
					]);
				}
			} else {
				return $this->render('update-local', [
					'model' => $model,
					'items' => $items,
					'modelItems' => $modelItems,
					'user_warehouses' => $this->userWarehouses,
				]);
			}
		} else {
			return $this->render('update-local', [
				'model' => $model,
				'items' => $items,
				'modelItems' => $modelItems,
				'user_warehouses' => $this->userWarehouses,
			]);
		}
	}

	public function actionUpdateLocalIssue($id)
	{
		$model = $this->findModel($id);
		$errorlist = [];
		if ($model->document_type_id != 2) {
			Yii::$app->session->setFlash('error', Yii::t('app', 'You are not allowed to do this action.'));
			return $this->redirect(['index']);
		}
		if (Yii::$app->user->identity->roleName == 'mrp') {
			if (!in_array($model->from_warehouse_id, Yii::$app->user->identity->warehouseIds)) {
				throw new ForbiddenHttpException(Yii::t('app', 'You are not allowed to edit this record.'));
			}
		}
		$modelItems = new DocumentDetail();
		$items['num'][] = '';
		$items['detail'][] = '';
		$items['partname'][] = '';
		$items['unit'][] = '';
		$items['quantity'][] = '';
		$i = 0;
		foreach ($model->documentDetails as $item) {
			$items['num'][] = ++$i;
			$items['detail'][] = $item->part_id;
			$items['partname'][] = $item->part->part_name;
			$items['unit'][] = $item->part->unit->unit_value;
			$items['quantity'][] = $item->qty;
		}
		if ($model->load(Yii::$app->request->post())) {
			if (is_array($_POST['items']['detail']) and count($_POST['items']['detail']) < 2) {
				$errorlist = [
					'details' => [
						'no_item' => [
							[Yii::t('app', 'You must select at least one part.')]
						]
					]
				];
				return $this->render('update-local-issue', [
					'errorlist' => $errorlist ?? null,
					'model' => $model,
					'items' => $_POST['items'],
					'modelItems' => $modelItems,
					'user_warehouses' => $this->userWarehouses,
				]);
			}
			$transaction = Yii::$app->db->beginTransaction();
			$model->docdate = date("Y-m-d", strtotime($model->docdate));
			// write to history before changing
			$historyStatus = $this->writeToDocHistory($model->id, $this->action->id);
			if ($model->save()) {
				// detallarni o'chirish va sublari bo'lmaganlarini qaytarish
				$data_r = [];
				$data_r_issue = [];
				foreach ($model->documentDetails as $detail) {
					// produce changes
					//if(true and $detail->sub != 1){
					unset($tmpArr);
					$tmpArr['part_id'] = $detail->part_id;
					$tmpArr['qty'] = $detail->qty;
					$data_r[] = $tmpArr;
					//}
					unset($tmpArr);
					$tmpArr['part_id'] = $detail->part_id;
					$tmpArr['qty'] = $detail->qty;
					$data_r_issue[] = $tmpArr;
				}
				DocumentDetail::deleteAll(['document_id' => $model->id]);
				// rasxod qilgan wh ga qaytarib qo'yish
				$stockReceiptResult = Stock::receipt($model->from_warehouse_id, $data_r);
				// prixod qilingan postavshik wh sidan ayirib tashlash
				$stockIssueResult = Stock::issueFromShop($model->to_warehouse_id, $data_r_issue);
				// *****************
				// sub detallarni o'chirish va qaytarish
				foreach ($model->documentDetailsSub as $subDetail) {
					$dataSub = [
						[
							'part_id' => $subDetail->sub_part_id,
							'qty' => $subDetail->qty
						]
					];
					$stockReceiptResulSub = Stock::receipt($subDetail->warehouse_id, $dataSub);
					if (!$stockReceiptResulSub['success']) {
						$errorlist[] = [[Yii::t('app', 'Problem')]];
					}
				}
				DocumentDetailSub::deleteAll(['document_id' => $model->id]);
				// *****************
				if (is_array($_POST['items']['detail']) and count($_POST['items']['detail']) > 1) {
					$data = []; // for stock fucntion
					$dataReceipt = []; // postavshikga katta detalni prixod qilish uchun
					foreach ($_POST['items']['detail'] as $key => $value) {
						if ($key == 0) {
							continue;
						}
						$item = new DocumentDetail();
						$item->document_id = $model->id;
						$item->part_id = $_POST['items']['detail'][$key];
						$item->qty = $_POST['items']['quantity'][$key];
						$hasSubParts = Part::findOne($item->part_id)->hasSubParts ?? null;
						if ($hasSubParts and $model->fromWarehouse->warehouse_type != Warehouse::TYPE_PHYSICAL) {
							$item->sub = 1;
						}
						if (!$item->save()) {
							$errorlist[$_POST['items']['num'][$key]] = $item->getErrors();
						} else {
							// produce changes
							if (false and $hasSubParts and $model->fromWarehouse->warehouse_type != Warehouse::TYPE_PHYSICAL) {
								// agar bu detal sub bomda ota detal bolsa va rasxod qilayotgan sklad fizicheskiy bolmasa uni bollarini rasxod qilamiz
								$dataSub = [];
								foreach ($item->part->subParts as $subPart) {
									// har bir sub detalni ulock boyicha rasxod qilib chiqamiz
									if ($model->from_warehouse_id == $subPart->warehouse_id) {
										$dataSub = [
											[
												'part_id' => $subPart->sub_part_id,
												'qty' => $item->qty * $subPart->usage_qty
											]
										];
										$stockResultSub = Stock::issueFromShop($subPart->warehouse_id, $dataSub);
										if (!$stockResultSub['success']) {
											$errorlist[$_POST['items']['num'][$key]] = [[Yii::t('app', 'Issue problem')]];
										}
										// sub table ga rasxod qilingan detallarni yozib qoyamiz
										$documentDetailSub = new DocumentDetailSub();
										$documentDetailSub->document_id = $model->id;
										$documentDetailSub->part_id = $subPart->part_id;
										$documentDetailSub->sub_part_id = $subPart->sub_part_id;
										$documentDetailSub->qty = $item->qty * $subPart->usage_qty;
										$documentDetailSub->warehouse_id = $subPart->warehouse_id;
										if (!$documentDetailSub->save()) {
											$errorlist[$_POST['items']['num'][$key]] = [[Yii::t('app', 'Document sub-detail insert problem')]];
										}
									}
								}
								// katta detal ni o'zini postavshikka prixod qilish uchun ma'lumot tayyorlaymiz
								unset($tmpArr);
								$tmpArr['part_id'] = $item->part_id;
								$tmpArr['qty'] = $item->qty;
								$dataReceipt[] = $tmpArr;
							} else {
								// agar bu detal sub bomda ota detal bolmasa yoki rasxod qilayotgan sklad fizicheskiy bolsa uni o'zini rasxod qilamiz
								unset($tmpArr);
								$tmpArr['part_id'] = $item->part_id;
								$tmpArr['qty'] = $item->qty;
								$data[] = $tmpArr;
								$dataReceipt = $data;
							}
						}
					}
					// Postavshikka rasxod qilayotgan skladni ostatkasidan yetsa ayirish
					$stockResultIssue = Stock::issue($model->from_warehouse_id, $data);
					// Tanlangan postavshik skladiga prixod qilish (outsourcing)
					$stockResultReceipt = Stock::receipt($model->to_warehouse_id, $dataReceipt);
				}
				if (count($errorlist) == 0 and $stockResultIssue['success'] and $stockReceiptResult['success'] and $stockResultReceipt['success'] and $historyStatus) {
					$transaction->commit();
					Yii::$app->session->setFlash('success', Yii::t('app', 'Document changed successfully.'));
					return $this->redirect(['index']);
				} else {
					$transaction->rollBack();
					return $this->render('update-local-issue', [
						'errorlist' => ['details' => $errorlist, 'stock' => $stockResultIssue['errorlist'] ?? null, 'stock_receipt' => $stockReceiptResult['errorlist'] ?? null],
						'model' => $model,
						'items' => $_POST['items'],
						'modelItems' => $modelItems,
						'user_warehouses' => $this->userWarehouses,
					]);
				}
			} else {
				return $this->render('update-local-issue', [
					'errorlist' => $errorlist ?? null,
					'model' => $model,
					'items' => $items,
					'modelItems' => $modelItems,
					'user_warehouses' => $this->userWarehouses,
				]);
			}
		} else {
			return $this->render('update-local-issue', [
				'errorlist' => $errorlist ?? null,
				'model' => $model,
				'items' => $items,
				'modelItems' => $modelItems,
				'user_warehouses' => $this->userWarehouses,
			]);
		}
	}

	public function actionUpdateAct($id)
	{
		$model = $this->findModel($id);
		$model->scenario = 'act';
		$document_type_id = 3; // act
		$errorlist = [];
		if ($model->document_type_id != 3) {
			Yii::$app->session->setFlash('error', Yii::t('app', 'You are not allowed to do this action.'));
			return $this->redirect(['index']);
		}
		if (Yii::$app->user->identity->roleName == 'mrp') {
			if (!(in_array($model->from_warehouse_id, Yii::$app->user->identity->warehouseIds) or
				in_array($model->to_warehouse_id, Yii::$app->user->identity->warehouseIds))) {
				throw new ForbiddenHttpException(Yii::t('app', 'You are not allowed to edit this record.'));
			}
		}
		if (Yii::$app->user->identity->roleName == 'mrp') {
			if (Yii::$app->user->identity->act_access != 1) {
				Yii::$app->session->setFlash('error', Yii::t('app', 'You are not allowed to do this action.'));
				return $this->redirect(['index']);
			}
		}
		$modelItems = new DocumentDetail();
		$items['num'][] = '';
		$items['detail'][] = '';
		$items['partname'][] = '';
		$items['unit'][] = '';
		$items['quantity'][] = '';
		$i = 0;
		foreach ($model->documentDetails as $item) {
			$items['num'][] = ++$i;
			$items['detail'][] = $item->part_id;
			$items['partname'][] = $item->part->part_name;
			$items['unit'][] = $item->part->unit->unit_value;
			$items['quantity'][] = $item->qty;
		}
		//        echo "<pre>";
		//        print_r(Yii::$app->request->post());
		//        echo "</pre>";
		//        die;
		if ($model->load(Yii::$app->request->post())) {
			if (is_array($_POST['items']['detail']) and count($_POST['items']['detail']) < 2) {
				$errorlist = [
					'details' => [
						'no_item' => [
							[Yii::t('app', 'You must select at least one part.')]
						]
					]
				];
				return $this->render('update-act', [
					'errorlist' => $errorlist ?? null,
					'model' => $model,
					'items' => $_POST['items'],
					'modelItems' => $modelItems,
				]);
			}
			$transaction = Yii::$app->db->beginTransaction();
			$model->docdate = date("Y-m-d", strtotime($model->docdate));
			// write to history before changing
			$historyStatus = $this->writeToDocHistory($model->id, $this->action->id);
			if ($model->save()) {
				$data_r = []; // for stock receipt fucntion
				foreach ($model->documentDetails as $detail) {
					unset($tmpArr);
					$tmpArr['part_id'] = $detail->part_id;
					$tmpArr['qty'] = $detail->qty;
					$data_r[] = $tmpArr;
				}
				DocumentDetail::deleteAll(['document_id' => $model->id]);
				if ($model->adj == 0) {
					$stockReceiptResult1 = Stock::receipt($model->from_warehouse_id, $data_r);
				} else {
					$stockIssueResult1 = Stock::issue($model->to_warehouse_id, $data_r);
				}
				if (is_array($_POST['items']['detail']) and count($_POST['items']['detail']) > 1) {
					$data = []; // for stock fucntion
					foreach ($_POST['items']['detail'] as $key => $value) {
						if ($key == 0) {
							continue;
						}
						$item = new DocumentDetail();
						$item->document_id = $model->id;
						$item->part_id = $_POST['items']['detail'][$key];
						$item->qty = $_POST['items']['quantity'][$key];
						if (!$item->save()) {
							$errorlist[$_POST['items']['num'][$key]] = $item->getErrors();
						} else {
							unset($tmpArr);
							$tmpArr['part_id'] = $item->part_id;
							$tmpArr['qty'] = $item->qty;
							$data[] = $tmpArr;
						}
					}
					if ($model->adj == 0) {
						$stockIssueResult2 = Stock::issue($model->from_warehouse_id, $data);
					} else {
						$stockReceiptResult2 = Stock::receipt($model->to_warehouse_id, $data);
					}
				}
				//                echo "<pre>";
				//                var_dump(count($errorlist));
				//                echo "</pre>";
				//                die;
				if (count($errorlist) == 0 and (($stockIssueResult1['success'] ?? null) or ($stockReceiptResult1['success'] ?? null)) and (($stockIssueResult2['success'] ?? null) or ($stockReceiptResult2['success'] ?? null)) and $historyStatus) {
					$transaction->commit();
					Yii::$app->session->setFlash('success', Yii::t('app', 'Document changed successfully.'));
					return $this->redirect(['index']);
				} else {
					$transaction->rollBack();
					return $this->render('update-act', [
						'errorlist' => ['details' => $errorlist, 'stock' => $stockResult['errorlist'], 'stock_receipt' => $stockReceiptResult['errorlist']],
						'model' => $model,
						'items' => $_POST['items'],
						'modelItems' => $modelItems,
					]);
				}
			} else {
				echo "<pre>";
				print_r($model->errors);
				echo "</pre>";
				die;
				return $this->render('update-act', [
					'model' => $model,
					'items' => $items,
					'modelItems' => $modelItems,
				]);
			}
		} else {
			//            echo "<pre>";
			//            print_r('ddddd');
			//            echo "</pre>";
			//            die;
			return $this->render('update-act', [
				'model' => $model,
				'items' => $items,
				'modelItems' => $modelItems,
			]);
		}
	}

	public function actionDelete($id)
	{
		$model = $this->findModel($id);
		$errorlist = [];
		if ($model->document_type_id != 2) {
			Yii::$app->session->setFlash('error', Yii::t('app', 'You are not allowed to do this action.'));
			return $this->redirect(['index']);
		}
		
		if (!in_array($model->from_warehouse_id, Yii::$app->user->identity->warehouseIds)) {
			throw new ForbiddenHttpException(Yii::t('app', 'You are not allowed to remove this record.'));
		}
		
		if ($model->status == 1) {
			throw new ForbiddenHttpException(Yii::t('app', 'You are not allowed to remove this record.'));
		}
		if (!empty($model->serial_number)) {
			throw new ForbiddenHttpException(Yii::t('app', 'You are not allowed to remove this record.'));
		}
		$transaction = Yii::$app->db->beginTransaction();
		// detallarni o'chirish va sublari bo'lmaganlarini qaytarish
		$data_r = [];
		foreach ($model->documentDetails as $detail) {
			// produce changes
			//if($detail->sub != 1){
			unset($tmpArr);
			$tmpArr['part_id'] = $detail->part_id;
			$tmpArr['qty'] = $detail->qty;
			$data_r[] = $tmpArr;
			//}
		}
		DocumentDetail::deleteAll(['document_id' => $model->id]);
		$stockReceiptResult = Stock::receipt($model->from_warehouse_id, $data_r);
		// *****************
		// sub detallarni o'chirish va qaytarish
		foreach ($model->documentDetailsSub as $subDetail) {
			$dataSub = [
				[
					'part_id' => $subDetail->sub_part_id,
					'qty' => $subDetail->qty
				]
			];
			$stockReceiptResulSub = Stock::receipt($subDetail->warehouse_id, $dataSub);
			if (!$stockReceiptResulSub['success']) {
				$errorlist[] = [[Yii::t('app', 'Problem')]];
			}
		}
		DocumentDetailSub::deleteAll(['document_id' => $model->id]);
		// *****************
		$statusHistory = $this->writeToDocHistory($model->id, $this->action->id);
		if ($model->delete() and $stockReceiptResult['success'] and $statusHistory and count($errorlist) == 0) {
			Yii::$app->session->setFlash('success', Yii::t('app', 'Document removed successfully.'));
			$transaction->commit();
		} else {
			Yii::$app->session->setFlash('error', Yii::t('app', 'Error! Document not removed.'));
			$transaction->rollBack();
		}
		return $this->redirect(['index']);
	}

	public function actionDeleteLocal($id)
	{
		$model = $this->findModel($id);
		$errorlist = [];
		if ($model->document_type_id != 2) {
			Yii::$app->session->setFlash('error', Yii::t('app', 'You are not allowed to do this action.'));
			return $this->redirect(['index']);
		}
		if (Yii::$app->user->identity->roleName == 'mrp') {
			if (!in_array($model->to_warehouse_id, Yii::$app->user->identity->warehouseIds)) {
				throw new ForbiddenHttpException(Yii::t('app', 'You are not allowed to remove this record.'));
			}
		}
		$transaction = Yii::$app->db->beginTransaction();
		$data_i = []; // for stock function
		$data_r = [];
		foreach ($model->documentDetails as $detail) {
			if ($detail->sub != 1) {
				unset($tmpArr);
				$tmpArr['part_id'] = $detail->part_id;
				$tmpArr['qty'] = $detail->qty;
				$data_r[] = $tmpArr;
			}
			unset($tmpArr);
			$tmpArr['part_id'] = $detail->part_id;
			$tmpArr['qty'] = $detail->qty;
			$data_i[] = $tmpArr;
		}
		// Agar Sklad tipi Shop bolsa, ostatkasini minusga tiqsin
		// aks holda minusga tiqmasdan xato bersin
		if ($model->toWarehouse->warehouse_type == Warehouse::TYPE_SHOP) {
			$stockIssueResult = Stock::issueFromShop($model->to_warehouse_id, $data_i);
		} else {
			$stockIssueResult = Stock::issue($model->to_warehouse_id, $data_i);
		}

		// rasxod qilgan postavshik wh ga qaytarib qo'yish
		$stockReceiptResult = Stock::receipt($model->from_warehouse_id, $data_r);
		// sub detallarni o'chirish va qaytarish
		foreach ($model->documentDetailsSub as $subDetail) {
			$dataSub = [
				[
					'part_id' => $subDetail->sub_part_id,
					'qty' => $subDetail->qty
				]
			];
			$stockReceiptResulSub = Stock::receipt($subDetail->warehouse_id, $dataSub);
			if (!$stockReceiptResulSub['success']) {
				$errorlist[] = [[Yii::t('app', 'Problem')]];
			}
		}
		// *****************
		$statusHistory = $this->writeToDocHistory($model->id, $this->action->id);
		if ($model->delete() and $stockIssueResult['success'] and $stockReceiptResult['success'] and $statusHistory and count($errorlist) == 0) {
			Yii::$app->session->setFlash('success', Yii::t('app', 'Document removed successfully.'));
			$transaction->commit();
		} else {
			Yii::$app->session->setFlash('error', Yii::t('app', 'Error! Document not removed.'));
			$transaction->rollBack();
		}
		return $this->redirect(['index']);
	}

	public function actionDeleteLocalIssue($id)
	{
		$model = $this->findModel($id);
		$errorlist = [];
		if ($model->document_type_id != 2) {
			Yii::$app->session->setFlash('error', Yii::t('app', 'You are not allowed to do this action.'));
			return $this->redirect(['index']);
		}
		if (Yii::$app->user->identity->roleName == 'mrp') {
			if (!in_array($model->from_warehouse_id, Yii::$app->user->identity->warehouseIds)) {
				throw new ForbiddenHttpException(Yii::t('app', 'You are not allowed to remove this record.'));
			}
		}
		$transaction = Yii::$app->db->beginTransaction();
		// detallarni o'chirish va sublari bo'lmaganlarini qaytarish
		$data_r = [];
		$data_r_issue = [];
		foreach ($model->documentDetails as $detail) {
			// produce changes
			//if($detail->sub != 1){
			unset($tmpArr);
			$tmpArr['part_id'] = $detail->part_id;
			$tmpArr['qty'] = $detail->qty;
			$data_r[] = $tmpArr;
			//}
			unset($tmpArr);
			$tmpArr['part_id'] = $detail->part_id;
			$tmpArr['qty'] = $detail->qty;
			$data_r_issue[] = $tmpArr;
		}
		// produce changes
		//if($detail->sub != 1){
		$data_r_issue = $data_r;
		//}
		DocumentDetail::deleteAll(['document_id' => $model->id]);
		// rasxod qilgan wh ga qaytarib qo'yish
		$stockReceiptResult = Stock::receipt($model->from_warehouse_id, $data_r);
		// prixod qilingan postavshik wh sidan ayirib tashlash
		$stockIssueResult = Stock::issueFromShop($model->to_warehouse_id, $data_r_issue);
		// *****************
		// sub detallarni o'chirish va qaytarish
		foreach ($model->documentDetailsSub as $subDetail) {
			$dataSub = [
				[
					'part_id' => $subDetail->sub_part_id,
					'qty' => $subDetail->qty
				]
			];
			$stockReceiptResulSub = Stock::receipt($subDetail->warehouse_id, $dataSub);
			if (!$stockReceiptResulSub['success']) {
				$errorlist[] = [[Yii::t('app', 'Problem')]];
			}
		}
		DocumentDetailSub::deleteAll(['document_id' => $model->id]);
		// *****************
		$statusHistory = $this->writeToDocHistory($model->id, $this->action->id);
		if ($model->delete() and $stockReceiptResult['success'] and $stockIssueResult['success'] and $statusHistory and count($errorlist) == 0) {
			Yii::$app->session->setFlash('success', Yii::t('app', 'Document removed successfully.'));
			$transaction->commit();
		} else {
			Yii::$app->session->setFlash('error', Yii::t('app', 'Error! Document not removed.'));
			$transaction->rollBack();
		}
		return $this->redirect(['index']);
	}

	public function actionDeleteAct($id)
	{
		$model = $this->findModel($id);
		$errorlist = [];
		if ($model->document_type_id != 3) {
			Yii::$app->session->setFlash('error', Yii::t('app', 'You are not allowed to do this action.'));
			return $this->redirect(['index']);
		}
		if (Yii::$app->user->identity->roleName == 'mrp') {
			if (!(in_array($model->from_warehouse_id, Yii::$app->user->identity->warehouseIds) or
				in_array($model->to_warehouse_id, Yii::$app->user->identity->warehouseIds))) {
				throw new ForbiddenHttpException(Yii::t('app', 'You are not allowed to do this record.'));
			}
		}
		if (Yii::$app->user->identity->roleName == 'mrp') {
			if (Yii::$app->user->identity->act_access != 1) {
				Yii::$app->session->setFlash('error', Yii::t('app', 'You are not allowed to do this action.'));
				return $this->redirect(['index']);
			}
		}
		$data_r = []; // for stock receipt fucntion
		foreach ($model->documentDetails as $detail) {
			unset($tmpArr);
			$tmpArr['part_id'] = $detail->part_id;
			$tmpArr['qty'] = $detail->qty;
			$data_r[] = $tmpArr;
		}
		$transaction = Yii::$app->db->beginTransaction();
		if ($model->adjStatus == 0) {
			$stockIssueResult = Stock::receipt($model->from_warehouse_id, $data_r);
		} else {
			$stockReceiptResult = Stock::issue($model->to_warehouse_id, $data_r);
		}
		$statusHistory = $this->writeToDocHistory($model->id, $this->action->id);
		if ($model->delete() and (($stockReceiptResult['success'] ?? null) or ($stockIssueResult['success'] ?? null)) and $statusHistory) {
			Yii::$app->session->setFlash('success', Yii::t('app', 'Document removed successfully.'));
			$transaction->commit();
		} else {
			Yii::$app->session->setFlash('error', Yii::t('app', 'Error! Document not removed.'));
			$transaction->rollBack();
		}
		return $this->redirect(['index']);
	}

	public function actionConfirm($id)
	{
		$model = $this->findModel($id);
		$errorlist = [];
		if (!in_array($model->document_type_id, [1, 2])) {
			Yii::$app->session->setFlash('error', Yii::t('app', 'You are not allowed to do this action.'));
			return $this->redirect(['index']);
		}
		if (Yii::$app->user->identity->roleName == 'mrp') {
			if (!in_array($model->to_warehouse_id, Yii::$app->user->identity->warehouseIds)) {
				throw new ForbiddenHttpException(Yii::t('app', 'You are not allowed to do this action.'));
			}
		}
		if (!empty($model->serial_number)) {
			throw new ForbiddenHttpException(Yii::t('app', 'You are not allowed to do this action.'));
		}
		$model->status = ($model->status == 1) ? 0 : 1;
		$data = []; // for stock fucntion
		foreach ($model->documentDetails as $detail) {
			unset($tmpArr);
			$tmpArr['part_id'] = $detail->part_id;
			$tmpArr['qty'] = $detail->qty;
			$data[] = $tmpArr;
		}
		$transaction = Yii::$app->db->beginTransaction();
		// shu joyda stock table ga ham change qilish kk
		// status = 1 bolsa to_wh ni ostatkasiga qo'shish kk
		// status = 0 bolsa to_wh ni ostatkasidan ayirish
		if ($model->status == 1) {
			$stockReceiptResult = Stock::receipt($model->to_warehouse_id, $data);
		} else {
			$stockResult = Stock::issue($model->to_warehouse_id, $data);
		}

		$historyStatus = $this->writeToDocHistory($model->id, $this->action->id);
		if ($model->save() and (($stockReceiptResult['success'] ?? null) or ($stockResult['success'] ?? null)) and $historyStatus) {
			Yii::$app->session->setFlash('success', Yii::t('app', 'Action successfully completed.'));
			$transaction->commit();
		} else {
			Yii::$app->session->setFlash('error', Yii::t('app', 'Error! Action not completed.'));
			$transaction->rollBack();
		}
		return $this->redirect(['index']);
	}

	public function actionPrint($id)
	{
		$model = $this->findModel($id);
		return $this->render('print', [
			'model' => $model
		]);
	}

	public function actionXls()
	{
		ini_set('memory_limit', '-1');
		$searchModel = new DocumentSearch();
		$xsl_file = $searchModel->search(Yii::$app->request->queryParams, 'excel');
		// if (is_array($xsl_file->sheets['Document']['data']) and count($xsl_file->sheets['Document']['data']) == 0) {
		// 	return $this->redirect(['index']);
		// }
		$xsl_file->send(Helpers::downloadFileName('document'));
		die;
	}



	protected function getUserWarehouses()
	{
		return Yii::$app->user->identity->warehouseNames;
	}

	protected function findModel($id)
	{
		if (($model = Document::findOne($id)) !== null) {
			return $model;
		}
		throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
	}

	public function actionCreateLocalKd()
	{
		$document_type_id = 2; // nakladnoy
		$model = new Document();
		$model->scenario = 'scenario_req_supp';
		$modelItems = new DocumentDetail();
		$model->docdate = date('d.m.Y');
		$errorlist = [];
		$isNewRecord = true;
		if ($model->load(Yii::$app->request->post())) {
			if ($model->toWarehouse->warehouse_type == 1) {
				Yii::$app->session->setFlash('error', Yii::t('app', 'You are not allowed to do this action.'));
				return $this->redirect(['index']);
			}
			if (is_array($_POST['items']['detail']) and count($_POST['items']['detail']) < 2) {
				$errorlist = [
					'details' => [
						'no_item' => [
							[Yii::t('app', 'You must select at least one part.')]
						]
					]
				];
				return $this->render('create-local-kd', [
					'errorlist' => $errorlist ?? null,
					'model' => $model,
					'items' => $_POST['items'],
					'modelItems' => $modelItems,
					'isNewRecord' => $isNewRecord,
					'user_warehouses' => $this->userWarehouses,
				]);
			}
			$transaction = Yii::$app->db->beginTransaction();
			$model->document_type_id = $document_type_id;
			$model->docnum = DocumentType::generateDocnum($document_type_id);
			$model->docdate = date("Y-m-d", strtotime($model->docdate));
			$model->status = 1;
			$model->from_warehouse_id = Yii::$app->params['outsoursingWhId'];
			if ($model->save()) {
				if (is_array($_POST['items']['detail']) and count($_POST['items']['detail']) > 1) {
					$data = []; // for stock fucntion
					foreach ($_POST['items']['detail'] as $key => $value) {
						if ($key == 0) {
							continue;
						}
						$item = new DocumentDetail();
						$item->document_id = $model->id;
						$item->part_id = $_POST['items']['detail'][$key];
						$item->qty = $_POST['items']['quantity'][$key];
						$hasSubParts = Part::findOne($item->part_id)->hasSubParts ?? null;
						if ($hasSubParts) {
							$item->sub = 1;
						}
						if (!$item->save()) {
							$errorlist[$_POST['items']['num'][$key]] = $item->getErrors();
						} else {
							if ($hasSubParts) {
								// agar bu detal sub bomda ota detal bolsa va rasxod qilayotgan sklad fizicheskiy bolmasa uni bollarini rasxod qilamiz
								$dataSub = [];
								foreach ($item->part->subParts as $subPart) {
									// har bir sub detalni ulock boyicha rasxod qilib chiqamiz
									if ($model->from_warehouse_id == $subPart->warehouse_id) {
										$dataSub = [
											[
												'part_id' => $subPart->sub_part_id,
												'qty' => $item->qty * $subPart->usage_qty
											]
										];
										$stockResultSub = Stock::issueFromShop($subPart->warehouse_id, $dataSub);
										if (!$stockResultSub['success']) {
											$errorlist[$_POST['items']['num'][$key]] = [[Yii::t('app', 'Issue problem')]];
										}
										// sub table ga rasxod qilingan detallarni yozib qoyamiz
										$documentDetailSub = new DocumentDetailSub();
										$documentDetailSub->document_id = $model->id;
										$documentDetailSub->part_id = $subPart->part_id;
										$documentDetailSub->sub_part_id = $subPart->sub_part_id;
										$documentDetailSub->qty = $item->qty * $subPart->usage_qty;
										$documentDetailSub->warehouse_id = $subPart->warehouse_id;
										if (!$documentDetailSub->save()) {
											$errorlist[$_POST['items']['num'][$key]] = [[Yii::t('app', 'Document sub-detail insert problem')]];
										}
									}
								}
							}
							unset($tmpArr);
							$tmpArr['part_id'] = $item->part_id;
							$tmpArr['qty'] = $item->qty;
							$data[] = $tmpArr;
						}
					}
					// shu joyda stock table ga ham change qilish kk
					// - from_wh dan agar ostatkasi yetsa kamaytirish
					$stockResult = Stock::receipt($model->to_warehouse_id, $data);
				}
				if (count($errorlist) == 0 and $stockResult['success']) {
					$transaction->commit();
					// $this->writeToDocHistory($model->id, $this->action->id);
					Yii::$app->session->setFlash('success', Yii::t('app', 'Document created successfully.'));
					return $this->redirect(['index']);
				} else {
					$transaction->rollBack();
					return $this->render('create-local-kd', [
						'errorlist' => ['details' => $errorlist, 'stock' => $stockResult['errorlist']],
						'model' => $model,
						'items' => $_POST['items'],
						'modelItems' => $modelItems,
						'isNewRecord' => $isNewRecord,
						'user_warehouses' => $this->userWarehouses,
					]);
				}
			} else {
				return $this->render('create-local-kd', [
					'model' => $model,
					'items' => $_POST['items'],
					'modelItems' => $modelItems,
					'isNewRecord' => $isNewRecord,
					'user_warehouses' => $this->userWarehouses,
				]);
			}
		} else {
			return $this->render('create-local-kd', [
				'model' => $model,
				'modelItems' => $modelItems,
				'isNewRecord' => $isNewRecord,
				'user_warehouses' => $this->userWarehouses,
			]);
		}
	}

	public function actionUpdateLocalKd($id)
	{
		$model = $this->findModel($id);
		$errorlist = [];
		$model->scenario = 'scenario_req_supp';
		if ($model->document_type_id != 2) {
			Yii::$app->session->setFlash('error', Yii::t('app', 'You are not allowed to do this action.'));
			return $this->redirect(['index']);
		}
		if (Yii::$app->user->identity->roleName == 'mrp') {
			if (!in_array($model->to_warehouse_id, Yii::$app->user->identity->warehouseIds)) {
				throw new ForbiddenHttpException(Yii::t('app', 'You are not allowed to edit this record.'));
			}
		}
		$modelItems = new DocumentDetail();
		$items['num'][] = '';
		$items['detail'][] = '';
		$items['partname'][] = '';
		$items['unit'][] = '';
		$items['quantity'][] = '';
		$i = 0;
		foreach ($model->documentDetails as $item) {
			$items['num'][] = ++$i;
			$items['detail'][] = $item->part_id;
			$items['partname'][] = $item->part->part_name;
			$items['unit'][] = $item->part->unit->unit_value;
			$items['quantity'][] = $item->qty;
		}
		if ($model->load(Yii::$app->request->post())) {
			if (is_array($_POST['items']['detail']) and count($_POST['items']['detail']) < 2) {
				$errorlist = [
					'details' => [
						'no_item' => [
							[Yii::t('app', 'You must select at least one part.')]
						]
					]
				];
				return $this->render('update-local-kd', [
					'errorlist' => $errorlist ?? null,
					'model' => $model,
					'items' => $_POST['items'],
					'modelItems' => $modelItems,
					'user_warehouses' => $this->userWarehouses,
				]);
			}
			$transaction = Yii::$app->db->beginTransaction();
			$model->docdate = date("Y-m-d", strtotime($model->docdate));
			// write to history before changing
			$historyStatus = $this->writeToDocHistory($model->id, $this->action->id);
			if ($model->save()) {
				$data_r = []; // for stock receipt fucntion
				foreach ($model->documentDetails as $detail) {
					unset($tmpArr);
					$tmpArr['part_id'] = $detail->part_id;
					$tmpArr['qty'] = $detail->qty;
					$data_r[] = $tmpArr;
				}
				DocumentDetail::deleteAll(['document_id' => $model->id]);
				// shu joyda stock table ga ham change qilish kk
				// - from_wh ga qaytarib qoyish kk
				$stockReceiptResult = Stock::issue($model->to_warehouse_id, $data_r);
				// sub detallarni o'chirish va qaytarish
				foreach ($model->documentDetailsSub as $subDetail) {
					$dataSub = [
						[
							'part_id' => $subDetail->sub_part_id,
							'qty' => $subDetail->qty
						]
					];
					$stockReceiptResulSub = Stock::receipt($subDetail->warehouse_id, $dataSub);
					if (!$stockReceiptResulSub['success']) {
						$errorlist[] = [[Yii::t('app', 'Problem')]];
					}
				}
				DocumentDetailSub::deleteAll(['document_id' => $model->id]);
				// *****************
				if (is_array($_POST['items']['detail']) and count($_POST['items']['detail']) > 1) {
					$data = []; // for stock fucntion
					foreach ($_POST['items']['detail'] as $key => $value) {
						if ($key == 0) {
							continue;
						}
						$item = new DocumentDetail();
						$item->document_id = $model->id;
						$item->part_id = $_POST['items']['detail'][$key];
						$item->qty = $_POST['items']['quantity'][$key];
						$hasSubParts = Part::findOne($item->part_id)->hasSubParts ?? null;
						if ($hasSubParts) {
							$item->sub = 1;
						}
						if (!$item->save()) {
							$errorlist[$_POST['items']['num'][$key]] = $item->getErrors();
						} else {
							if ($hasSubParts) {
								// agar bu detal sub bomda ota detal bolsa va rasxod qilayotgan sklad fizicheskiy bolmasa uni bollarini rasxod qilamiz
								$dataSub = [];
								foreach ($item->part->subParts as $subPart) {
									// har bir sub detalni ulock boyicha rasxod qilib chiqamiz
									if ($model->from_warehouse_id == $subPart->warehouse_id) {
										$dataSub = [
											[
												'part_id' => $subPart->sub_part_id,
												'qty' => $item->qty * $subPart->usage_qty
											]
										];
										$stockResultSub = Stock::issueFromShop($subPart->warehouse_id, $dataSub);
										if (!$stockResultSub['success']) {
											$errorlist[$_POST['items']['num'][$key]] = [[Yii::t('app', 'Issue problem')]];
										}
										// sub table ga rasxod qilingan detallarni yozib qoyamiz
										$documentDetailSub = new DocumentDetailSub();
										$documentDetailSub->document_id = $model->id;
										$documentDetailSub->part_id = $subPart->part_id;
										$documentDetailSub->sub_part_id = $subPart->sub_part_id;
										$documentDetailSub->qty = $item->qty * $subPart->usage_qty;
										$documentDetailSub->warehouse_id = $subPart->warehouse_id;
										if (!$documentDetailSub->save()) {
											$errorlist[$_POST['items']['num'][$key]] = [[Yii::t('app', 'Document sub-detail insert problem')]];
										}
									}
								}
							}
							unset($tmpArr);
							$tmpArr['part_id'] = $item->part_id;
							$tmpArr['qty'] = $item->qty;
							$data[] = $tmpArr;
						}
					}
					// shu joyda stock table ga ham change qilish kk
					// - from_wh dan agar ostatkasi yetsa kamaytirish
					$stockResult = Stock::receipt($model->to_warehouse_id, $data);
				}
				if (count($errorlist) == 0 and $stockResult['success'] and $stockReceiptResult['success'] and $historyStatus) {
					$transaction->commit();
					Yii::$app->session->setFlash('success', Yii::t('app', 'Document changed successfully.'));
					return $this->redirect(['index']);
				} else {
					$transaction->rollBack();
					return $this->render('update-local-kd', [
						'errorlist' => ['details' => $errorlist, 'stock' => $stockResult['errorlist'] ?? null, 'stock_receipt' => $stockReceiptResult['errorlist'] ?? null],
						'model' => $model,
						'items' => $_POST['items'],
						'modelItems' => $modelItems,
						'user_warehouses' => $this->userWarehouses,
					]);
				}
			} else {
				return $this->render('update-local-kd', [
					'errorlist' => $errorlist ?? null,
					'model' => $model,
					'items' => $items,
					'modelItems' => $modelItems,
					'user_warehouses' => $this->userWarehouses,
				]);
			}
		} else {
			return $this->render('update-local-kd', [
				'errorlist' => $errorlist ?? null,
				'model' => $model,
				'items' => $items,
				'modelItems' => $modelItems,
				'user_warehouses' => $this->userWarehouses,
			]);
		}
	}

	public function actionDeleteLocalKd($id)
	{
		$model = $this->findModel($id);
		$errorlist = [];
		if ($model->document_type_id != 2) {
			Yii::$app->session->setFlash('error', Yii::t('app', 'You are not allowed to do this action.'));
			return $this->redirect(['index']);
		}
		if (Yii::$app->user->identity->roleName == 'mrp') {
			if (!in_array($model->to_warehouse_id, Yii::$app->user->identity->warehouseIds)) {
				throw new ForbiddenHttpException(Yii::t('app', 'You are not allowed to remove this record.'));
			}
		}
		$transaction = Yii::$app->db->beginTransaction();
		// detallarni o'chirish va sublari bo'lmaganlarini qaytarish
		$data_r = []; // for stock receipt fucntion
		foreach ($model->documentDetails as $detail) {
			unset($tmpArr);
			$tmpArr['part_id'] = $detail->part_id;
			$tmpArr['qty'] = $detail->qty;
			$data_r[] = $tmpArr;
		}
		// shu joyda stock table ga ham change qilish kk
		// - from_wh ga qaytarib qoyish kk
		$stockIssueResult = Stock::issue($model->to_warehouse_id, $data_r);
		// *****************
		// sub detallarni o'chirish va qaytarish
		foreach ($model->documentDetailsSub as $subDetail) {
			$dataSub = [
				[
					'part_id' => $subDetail->sub_part_id,
					'qty' => $subDetail->qty
				]
			];
			$stockReceiptResulSub = Stock::receipt($subDetail->warehouse_id, $dataSub);
			if (!$stockReceiptResulSub['success']) {
				$errorlist[] = [[Yii::t('app', 'Problem')]];
			}
		}
		DocumentDetailSub::deleteAll(['document_id' => $model->id]);
		// *****************
		$statusHistory = $this->writeToDocHistory($model->id, $this->action->id);
		if ($model->delete() and $stockIssueResult['success'] and $statusHistory and count($errorlist) == 0) {
			Yii::$app->session->setFlash('success', Yii::t('app', 'Document removed successfully.'));
			$transaction->commit();
		} else {
			Yii::$app->session->setFlash('error', Yii::t('app', 'Error! Document not removed.'));
			$transaction->rollBack();
		}
		return $this->redirect(['index']);
	}

	public function actionCreateShopConsumption()
	{
		$errorlist = [];
		$model = new ConsumptionForm();
		$document_type_id = 2; // nakladnoy
		$last_consumption_docs = Document::find()
			->where([
				'and',
				['from_warehouse_id' => Yii::$app->user->identity->warehouseIds],
				['not', ['serial_number' => null]]
			])
			->orderBy(['id' => SORT_DESC])
			->limit(10)
			->all();
		//                                           echo "<pre>";
		//                                           print_r(Yii::$app->user->identity->warehouseIds);
		//                                           echo "</pre>";
		//                                           die;
		if ($model->load(Yii::$app->request->post())) {
			$production_order = ProductionOrder::getOrderBySerial($model->serial_number);
			if (empty($production_order)) {
				Yii::$app->session->setFlash('error', Yii::t('app', 'Production order not found.'));
				return $this->redirect('create-shop-consumption');
			}
			if (Yii::$app->user->identity->roleName == 'counter') {
				if (!in_array($production_order->part->warehouse_id, Yii::$app->user->identity->warehouseIds)) {
					Yii::$app->session->setFlash('error', Yii::t('app', 'You are not allowed to do this action.'));
					return $this->redirect('create-shop-consumption');
				}
			}
			if ($production_order->current_event != ProductionOrder::EVENT_PRODUCED) {
				if ($production_order->current_event == ProductionOrder::EVENT_INITIAL) {
					Yii::$app->session->setFlash('error', Yii::t('app', 'This package is not produced yet.'));
					return $this->redirect('create-shop-consumption');
				}
				if ($production_order->current_event == ProductionOrder::EVENT_SHIPPED) {
					Yii::$app->session->setFlash('error', Yii::t('app', 'This package is already shipped.'));
					return $this->redirect('create-shop-consumption');
				}
				if ($production_order->current_event == ProductionOrder::EVENT_ARRIVED) {
					Yii::$app->session->setFlash('error', Yii::t('app', 'This package is already arrived.'));
					return $this->redirect('create-shop-consumption');
				}
			}
			if (empty($production_order->part->uloc)) {
				Yii::$app->session->setFlash('error', Yii::t('app', 'ULOC not found for this part.'));
				return $this->redirect('create-shop-consumption');
			}
			// create document
			$transaction = Yii::$app->db->beginTransaction();
			$modelDocument = new Document();
			$modelDocument->document_type_id = $document_type_id;
			$modelDocument->docnum = DocumentType::generateDocnum($document_type_id);
			$modelDocument->docdate = date("Y-m-d");
			$modelDocument->from_warehouse_id = $production_order->part->warehouse_id;
			$modelDocument->to_warehouse_id = $production_order->part->uloc;
			$modelDocument->serial_number = $model->serial_number;
			// Agar rasxod qilinayotgan detal Outsourcingga berilaadigan bo'lsa avtomat ravishda status = 1 bo'lsin
			if (Warehouse::findOne($production_order->part->uloc)->warehouse_type == Warehouse::TYPE_OUTSOURCING) {
				$modelDocument->status = 1;
			}
			if ($modelDocument->save()) {
				$data = []; // for stock fucntion
				$item = new DocumentDetail();
				$item->document_id = $modelDocument->id;
				$item->part_id = $production_order->part_id;
				$item->qty = $production_order->quantity;
				if ($item->save()) {
					$data = [
						[
							'part_id' => $item->part_id,
							'qty' => $item->qty
						]
					];
				} else {
					$errorlist[] = 'Document detail save problem';
				}
				$stockResult = Stock::issue($modelDocument->from_warehouse_id, $data);
				if (!$stockResult) {
					$errorlist[] = 'Stock problem';
				}
				// Agar rasxod qilinayotgan detal Outsourcingga beriladigan bo'lsa avtomat ravishda Outsourcingga prixod bo'ladi
				if (Warehouse::findOne($production_order->part->uloc)->warehouse_type == Warehouse::TYPE_OUTSOURCING) {
					$stockResultOut = Stock::receipt($modelDocument->to_warehouse_id, $data);
					if (!$stockResultOut) {
						$errorlist[] = 'Stock problem (Outsourcing)';
					}
				}
				$production_order->current_event = ProductionOrder::EVENT_SHIPPED;
				if (!$production_order->save()) {
					$errorlist[] = 'Event changing problem';
				}
			} else {
				$errorlist[] = 'Document save problem';
			}
			if (count($errorlist) == 0) {
				$transaction->commit();
				Yii::$app->session->setFlash('success', Yii::t('app', 'Document created successfully.'));
				return $this->redirect(['create-shop-consumption']);
			} else {
				echo "<pre>";
				print_r($production_order->errors);
				echo "</pre>";
				die;
				$transaction->rollBack();
				Yii::$app->session->setFlash('error', Yii::t('app', 'Document not created.'));
				return $this->redirect(['create-shop-consumption']);
			}
		}
		return $this->render('create-shop-consumption', [
			'model' => $model,
			'last_consumption_docs' => $last_consumption_docs
		]);
	}

	public function actionCreateShopConsumptionVer2()
	{
		$errorlist = [];
		$model = new ConsumptionForm();
		$document_type_id = 2; // nakladnoy
		$last_consumption_docs = Document::find()
			->where([
				'and',
				['from_warehouse_id' => Yii::$app->user->identity->warehouseIds],
				['not', ['serial_number' => null]]
			])
			->orderBy(['id' => SORT_DESC])
			->limit(10)
			->all();
		if ($model->load(Yii::$app->request->post())) {
			$post = Yii::$app->request->post();
			// converting to array
			$serial_numbers = explode(PHP_EOL, $post['ConsumptionForm']['serial_number']);
			// removing empty elements
			foreach ($serial_numbers as $key => $value) {
				$serial_numbers[$key] = trim($value);
				if (empty($value)) unset($serial_numbers[$key]);
			}
			// removing dublicate serail_numbers
			$serial_numbers = array_unique($serial_numbers);
			foreach ($serial_numbers as $key => $sn) {
				$production_order = ProductionOrder::getOrderBySerial($sn);
				// checking: is serial_number is OK
				if (empty($production_order)) {
					$errorlist[$sn][] = Yii::t('app', 'Production order not found.');
					continue;
				}
				if (empty($production_order->part->uloc)) {
					$errorlist[$sn][] = Yii::t('app', 'ULOC not found for this part.');
					continue;
				}
				if (Yii::$app->user->identity->roleName == 'counter') {
					if (!in_array($production_order->part->warehouse_id, Yii::$app->user->identity->warehouseIds)) {
						$errorlist[$sn][] = Yii::t('app', 'You are not allowed to issue this package.');
						continue;
					}
				}
				if ($production_order->current_event != ProductionOrder::EVENT_PRODUCED) {
					if ($production_order->current_event == ProductionOrder::EVENT_INITIAL) {
						$errorlist[$sn][] = Yii::t('app', 'This package is not produced yet.');
						continue;
					}
					if ($production_order->current_event == ProductionOrder::EVENT_SHIPPED) {
						$errorlist[$sn][] = Yii::t('app', 'This package is already shipped.');
						continue;
					}
					if ($production_order->current_event == ProductionOrder::EVENT_ARRIVED) {
						$errorlist[$sn][] = Yii::t('app', 'This package is already arrived.');
						continue;
					}
				}
				// ************
				// create document
				$transaction = Yii::$app->db->beginTransaction();
				$modelDocument = new Document();
				$modelDocument->document_type_id = $document_type_id;
				$modelDocument->docnum = DocumentType::generateDocnum($document_type_id);
				$modelDocument->docdate = date("Y-m-d");
				$modelDocument->from_warehouse_id = $production_order->part->warehouse_id;
				$modelDocument->to_warehouse_id = $production_order->part->uloc;
				$modelDocument->serial_number = $production_order->serial_number;
				// Agar rasxod qilinayotgan detal Outsourcingga berilaadigan bo'lsa avtomat ravishda status = 1 bo'lsin
				if (Warehouse::findOne($production_order->part->uloc)->warehouse_type == Warehouse::TYPE_OUTSOURCING) {
					$modelDocument->status = 1;
				}
				if ($modelDocument->save()) {
					$data = [];
					// create document detail
					$item = new DocumentDetail();
					$item->document_id = $modelDocument->id;
					$item->part_id = $production_order->part_id;
					$item->qty = $production_order->quantity;
					if ($item->save()) {
						$data = [
							[
								'part_id' => $item->part_id,
								'qty' => $item->qty
							]
						];
					} else {
						$errorlist[$sn][] = Yii::t('app', 'Document detail save problem. Part id: ')
							. $item->part_id . '<br>'
							. implode('<br>', $item->errors);
					}
					// ****************
					// change stock
					$stockResult = Stock::issueFromShop($modelDocument->from_warehouse_id, $data);
					if (!$stockResult) {
						$errorlist[$sn][] = Yii::t('app', 'Stock problem. Part id: ') . $item->part_id;
					}
					// ********
					// Agar rasxod qilinayotgan detal Outsourcingga beriladigan bo'lsa avtomat ravishda Outsourcingga prixod bo'ladi
					if (Warehouse::findOne($production_order->part->uloc)->warehouse_type == Warehouse::TYPE_OUTSOURCING) {
						$stockResultOut = Stock::receipt($modelDocument->to_warehouse_id, $data);
						if (!$stockResultOut) {
							$errorlist[$sn][] = Yii::t('app', 'Stock problem (Outsourcing). Part id: ') . $item->part_id;
						}
					}
					// ************
					// change PO event
					$production_order->current_event = ProductionOrder::EVENT_SHIPPED;
					if (!$production_order->save()) {
						$errorlist[$sn][] = Yii::t('app', 'Event changing problem.');
					}
					// ********
				} else {
					$errorlist[$sn][] = Yii::t('app', 'Document save problem.') . '<br>' . implode('<br>', $modelDocument->errors);
				}
				// commit or rollback
				if (isset($errorlist[$sn])) {
					$transaction->rollBack();
				} else {
					$transaction->commit();
				}
			}
			if (count($errorlist) == 0) {
				Yii::$app->session->setFlash('success', Yii::t('app', 'All packages are issued.'));
				return $this->redirect(['create-shop-consumption-ver2']);
			} else {
				if (count($errorlist) == count($serial_numbers)) {
					$error_title = Yii::t('app', 'No packages are issued.');
				} else {
					$error_title = Yii::t('app', 'Some packages are not issued.');
				}
				$error_message = '';
				foreach ($errorlist as $sn => $errors) {
					$error_message .= '- <u>' . $sn . '</u>';
					$error_message .= '<br>';
					foreach ($errors as $err) {
						$error_message .= '--- <i>' . $err . '</i>';
						$error_message .= '<br>';
					}
				}
				Yii::$app->session->setFlash('error', '<b>' . $error_title . '</b>' . '<br><br>' . $error_message);
				return $this->redirect(['create-shop-consumption-ver2']);
			}
		}
		return $this->render('create-shop-consumption-ver2', [
			'model' => $model,
			'last_consumption_docs' => $last_consumption_docs
		]);
	}

	public function actionDeleteShopConsumption($id, $view = 'create-shop-consumption-ver2')
	{
		$model = $this->findModel($id);
		$production_order = ProductionOrder::getOrderBySerial($model->serial_number);
		$errorlist = [];
		if ($model->document_type_id != 2) {
			Yii::$app->session->setFlash('error', Yii::t('app', 'You are not allowed to do this action.'));
			return $this->redirect([$view]);
		}
		if (Yii::$app->user->identity->roleName == 'counter') {
			if (!in_array($model->from_warehouse_id, Yii::$app->user->identity->warehouseIds)) {
				throw new ForbiddenHttpException(Yii::t('app', 'You are not allowed to remove this record.'));
			}
		}
		if (Warehouse::findOne($production_order->part->uloc)->warehouse_type != Warehouse::TYPE_OUTSOURCING) {
			if ($model->status == 1) {
				throw new ForbiddenHttpException(Yii::t('app', 'This package is already confirmed.'));
			}
		}
		$transaction = Yii::$app->db->beginTransaction();
		// detallarni o'chirish va sublari bo'lmaganlarini qaytarish
		$data_r = [];
		foreach ($model->documentDetails as $detail) {
			unset($tmpArr);
			$tmpArr['part_id'] = $detail->part_id;
			$tmpArr['qty'] = $detail->qty;
			$data_r[] = $tmpArr;
		}
		DocumentDetail::deleteAll(['document_id' => $model->id]);
		$stockReceiptResult = Stock::receipt($model->from_warehouse_id, $data_r);
		$stockIssue = true;
		if (Warehouse::findOne($production_order->part->uloc)->warehouse_type == Warehouse::TYPE_OUTSOURCING) {
			$stockIssueResult = Stock::issue($model->to_warehouse_id, $data_r);
			if ($stockIssueResult['success'] != 1) $stockIssue = false;
		}
		// *****************
		//Production order event changing
		$production_order->current_event = ProductionOrder::EVENT_PRODUCED;
		if (!$production_order->save()) {
			$errorlist[] = 'Event changing problem';
		}
		//
		$statusHistory = $this->writeToDocHistory($model->id, $this->action->id);
		if ($model->delete() and $stockReceiptResult['success'] and $stockIssue and $statusHistory and count($errorlist) == 0) {
			Yii::$app->session->setFlash('success', Yii::t('app', 'Document removed successfully.'));
			$transaction->commit();
		} else {
			//                    echo "<pre>";
			//                    print_r($stockReceiptResult);
			//                    echo "</pre>";
			//                    die;
			Yii::$app->session->setFlash('error', Yii::t('app', 'Error! Document not removed.'));
			$transaction->rollBack();
		}
		return $this->redirect([$view]);
	}

	public function actionShopConfirm()
	{
		$errorlist = [];
		$model = new ConsumptionForm();
		$last_confirmed_docs = Document::find()
			->where([
				'and',
				['to_warehouse_id' => Yii::$app->user->identity->warehouseIds],
				['status' => 1],
				['not', ['serial_number' => null]]
			])
			->orderBy(['id' => SORT_DESC])
			->limit(10)
			->all();
		if ($model->load(Yii::$app->request->post())) {
			$production_order = ProductionOrder::getOrderBySerial($model->serial_number);
			$document = Document::getDocumentBySerial($model->serial_number);
			if (empty($production_order)) {
				Yii::$app->session->setFlash('error', Yii::t('app', 'Production order not found.'));
				return $this->redirect('shop-confirm');
			}
			if (empty($document)) {
				Yii::$app->session->setFlash('error', Yii::t('app', 'Document not found.'));
				return $this->redirect('shop-confirm');
			}
			if (Yii::$app->user->identity->roleName == 'counter') {
				if (!in_array($production_order->part->uloc, Yii::$app->user->identity->warehouseIds)) {
					Yii::$app->session->setFlash('error', Yii::t('app', 'You are not allowed to do this action.'));
					return $this->redirect('shop-confirm');
				}
			}
			if ($production_order->current_event != ProductionOrder::EVENT_SHIPPED) {
				if ($production_order->current_event == ProductionOrder::EVENT_INITIAL) {
					Yii::$app->session->setFlash('error', Yii::t('app', 'This package is not produced yet.'));
					return $this->redirect('shop-confirm');
				}
				if ($production_order->current_event == ProductionOrder::EVENT_PRODUCED) {
					Yii::$app->session->setFlash('error', Yii::t('app', 'This package is not shipped yet.'));
					return $this->redirect('shop-confirm');
				}
				if ($production_order->current_event == ProductionOrder::EVENT_ARRIVED) {
					Yii::$app->session->setFlash('error', Yii::t('app', 'This package is already arrived.'));
					return $this->redirect('shop-confirm');
				}
			}
			$transaction = Yii::$app->db->beginTransaction();
			// change documents tatus
			$document->status = 1;
			if ($document->save()) {
				$data = [
					[
						'part_id' => $document->documentDetails[0]->part_id,
						'qty' => $document->documentDetails[0]->qty
					]
				];
				$stockResult = Stock::receipt($document->to_warehouse_id, $data);
				if (!$stockResult) {
					$errorlist[] = 'Stock problem';
				}
				$production_order->current_event = ProductionOrder::EVENT_ARRIVED;
				if (!$production_order->save()) {
					$errorlist[] = 'Event changing problem';
				}
			} else {
				$errorlist[] = 'Document save problem';
			}
			if (count($errorlist) == 0) {
				$transaction->commit();
				Yii::$app->session->setFlash('success', Yii::t('app', 'Document confirmed successfully.'));
				return $this->redirect(['shop-confirm']);
			} else {
				$transaction->rollBack();
				Yii::$app->session->setFlash('error', Yii::t('app', 'Document not confirmed.'));
				return $this->redirect(['shop-confirm']);
			}
		}
		return $this->render('shop-confirm', [
			'model' => $model,
			'last_confirmed_docs' => $last_confirmed_docs
		]);
	}

	public function actionShopConfirmVer2()
	{
		$errorlist = [];
		$model = new ConsumptionForm();
		$last_confirmed_docs = Document::find()
			->where([
				'and',
				['to_warehouse_id' => Yii::$app->user->identity->warehouseIds],
				['status' => 1],
				['not', ['serial_number' => null]]
			])
			->orderBy(['id' => SORT_DESC])
			->limit(10)
			->all();
		if ($model->load(Yii::$app->request->post())) {
			$post = Yii::$app->request->post();
			// converting to array
			$serial_numbers = explode(PHP_EOL, $post['ConsumptionForm']['serial_number']);
			// removing empty elements
			foreach ($serial_numbers as $key => $value) {
				$serial_numbers[$key] = trim($value);
				if (empty($value))
					unset($serial_numbers[$key]);
			}
			// removing dublicate serail_numbers
			$serial_numbers = array_unique($serial_numbers);
			foreach ($serial_numbers as $key => $sn) {
				$production_order = ProductionOrder::getOrderBySerial($sn);
				$document = Document::getDocumentBySerial($sn);
				// checking: is serial_number is OK
				if (empty($production_order)) {
					$errorlist[$sn][] = Yii::t('app', 'Production order not found.');
					continue;
				}
				if (empty($production_order->part->uloc)) {
					$errorlist[$sn][] = Yii::t('app', 'ULOC not found for this part.');
					continue;
				}
				if (Yii::$app->user->identity->roleName == 'counter') {
					if (!in_array($production_order->part->uloc, Yii::$app->user->identity->warehouseIds)) {
						$errorlist[$sn][] = Yii::t('app', 'You are not allowed to confirm this package.');
						continue;
					}
				}
				if ($production_order->current_event != ProductionOrder::EVENT_SHIPPED) {
					if ($production_order->current_event == ProductionOrder::EVENT_INITIAL) {
						$errorlist[$sn][] = Yii::t('app', 'This package is not produced yet.');
						continue;
					}
					if ($production_order->current_event == ProductionOrder::EVENT_PRODUCED) {
						$errorlist[$sn][] = Yii::t('app', 'This package is not shipped yet.');
						continue;
					}
					if ($production_order->current_event == ProductionOrder::EVENT_ARRIVED) {
						$errorlist[$sn][] = Yii::t('app', 'This package is already arrived.');
						continue;
					}
				}
				if (empty($document)) {
					$errorlist[$sn][] = Yii::t('app', 'Document not found.');
					continue;
				}
				// ************
				$transaction = Yii::$app->db->beginTransaction();
				// change documents tatus
				$document->status = 1;
				if ($document->save()) {
					$data = [
						[
							'part_id' => $document->documentDetails[0]->part_id,
							'qty' => $document->documentDetails[0]->qty
						]
					];
					// change stock
					$stockResult = Stock::receipt($document->to_warehouse_id, $data);
					if (!$stockResult) {
						$errorlist[$sn][] = Yii::t('app', 'Stock problem. Part id: ') . $document->documentDetails[0]->part_id;
					}
					// ********
					// change PO event
					$production_order->current_event = ProductionOrder::EVENT_ARRIVED;
					if (!$production_order->save()) {
						$errorlist[$sn][] = Yii::t('app', 'Event changing problem.');
					}
					// ********
				} else {
					$errorlist[$sn][] = Yii::t('app', 'Document save problem.') . '<br>' . implode('<br>', $document->errors);
				}
				// commit or rollback
				if (isset($errorlist[$sn])) {
					$transaction->rollBack();
				} else {
					$transaction->commit();
				}
				// *************
			}
			if (count($errorlist) == 0) {
				Yii::$app->session->setFlash('success', Yii::t('app', 'All packages are confirmed.'));
				return $this->redirect(['shop-confirm-ver2']);
			} else {
				if (count($errorlist) == count($serial_numbers)) {
					$error_title = Yii::t('app', 'No packages are confirmed.');
				} else {
					$error_title = Yii::t('app', 'Some packages are not confirmed.');
				}
				$error_message = '';
				foreach ($errorlist as $sn => $errors) {
					$error_message .= '- <u>' . $sn . '</u>';
					$error_message .= '<br>';
					foreach ($errors as $err) {
						$error_message .= '--- <i>' . $err . '</i>';
						$error_message .= '<br>';
					}
				}
				Yii::$app->session->setFlash('error', '<b>' . $error_title . '</b>' . '<br><br>' . $error_message);
				return $this->redirect(['shop-confirm-ver2']);
			}
		}
		return $this->render('shop-confirm-ver2', [
			'model' => $model,
			'last_confirmed_docs' => $last_confirmed_docs
		]);
	}

	public function actionShopDisconfirm($id, $view = 'shop-confirm-ver2')
	{
		$model = $this->findModel($id);
		$errorlist = [];
		if ($model->document_type_id != 2) {
			Yii::$app->session->setFlash('error', Yii::t('app', 'You are not allowed to do this action.'));
			return $this->redirect([$view]);
		}
		if (Yii::$app->user->identity->roleName == 'counter') {
			if (!in_array($model->to_warehouse_id, Yii::$app->user->identity->warehouseIds)) {
				throw new ForbiddenHttpException(Yii::t('app', 'You are not allowed to remove this record.'));
			}
		}
		$transaction = Yii::$app->db->beginTransaction();
		// change document status
		$model->status = 0;
		// detallarni o'chirish va sublari bo'lmaganlarini qaytarish
		$data_r = [];
		foreach ($model->documentDetails as $detail) {
			unset($tmpArr);
			$tmpArr['part_id'] = $detail->part_id;
			$tmpArr['qty'] = $detail->qty;
			$data_r[] = $tmpArr;
		}
		$stockResult = Stock::issue($model->to_warehouse_id, $data_r);
		// *****************
		//Production order event changing
		$production_order = ProductionOrder::getOrderBySerial($model->serial_number);
		$production_order->current_event = ProductionOrder::EVENT_SHIPPED;
		if (!$production_order->save()) {
			$errorlist[] = 'Event changing problem';
		}
		//
		$statusHistory = $this->writeToDocHistory($model->id, $this->action->id);
		if ($model->save() and $stockResult['success'] and $statusHistory and count($errorlist) == 0) {
			Yii::$app->session->setFlash('success', Yii::t('app', 'Document disconfirmed successfully.'));
			$transaction->commit();
		} else {
			Yii::$app->session->setFlash('error', Yii::t('app', 'Error! Document not disconfirmed.'));
			$transaction->rollBack();
		}
		return $this->redirect([$view]);
	}


	// scaner

	public function actionIssue($document_id = null)
	{
		$model = new ScanningForm();

		$errorlist = [];
		$isNewRecord = true;
		$barcodeData = null;
		$isValid = true;
		$model->docdate = date('d.m.Y');

		$json_wh_list = json_encode(yii\helpers\ArrayHelper::map(Warehouse::find()->where(['status' => Warehouse::STATUS_ACTIVE])->all(), 'id', 'name'));
		$json_part_list = json_encode(yii\helpers\ArrayHelper::map(Part::find()->where(['status' => Part::STATUS_ACTIVE])->all(), 'part_no', 'partNameSpecial'));


		if ($model->load(Yii::$app->request->post())) {

			if (!empty($model->barCodeData)) {
				$barcodeData = json_decode($model->barCodeData);
				$checkEmptyErrors = [];
				// Agar bironta detal tanlanmasdan submit qilingan bolsa qaytaramiz
				// Aslida frontendda tekshiriladi, lekin bilag`on qo`chqarlar ham chiqib qoladida :))
				if (is_array($barcodeData->partList) and count($barcodeData->partList) < 1) {
					$checkEmptyErrors[] = [Yii::t('app', 'You must select at least one part.')];
					$isValid = false;
				}

				// Agar fromwh tanlamasdan submit qilingan bolsa qaytaramiz
				if (empty($barcodeData->whFromId)) {
					$checkEmptyErrors[] = [Yii::t('app', 'You must select warehouse which issuing.')];
					$isValid = false;
				}

				// Agar towh tanlamasdan submit qilingan bolsa qaytaramiz
				if (empty($barcodeData->whToId)) {
					$checkEmptyErrors[] = [Yii::t('app', 'You must select warehouse which receiving.')];
					$isValid = false;
				}
			} else {
				$checkEmptyErrors[] = [Yii::t('app', 'Empty data.')];
				$isValid = false;
			}

			if ($isValid) {
				// Hammasi OK, davom etamiz
				$document_type_id = 2; // nakladnoy
				//   $from_wh_id = Warehouse::findOneByWhName($barcodeData->whFrom)->id;
				//   $to_wh_id = Warehouse::findOneByWhName($barcodeData->whTo)->id;
				$from_wh_id = $barcodeData->whFromId;
				$to_wh_id = $barcodeData->whToId;

				$transaction = Yii::$app->db->beginTransaction();
				$document = new Document();
				$document->document_type_id = $document_type_id;
				$document->from_warehouse_id = $from_wh_id;
				$document->to_warehouse_id = $to_wh_id;
				$document->docnum = DocumentType::generateDocnum($document_type_id);
				$document->docdate = date("Y-m-d", strtotime($model->docdate));
				if ($document->save()) {
					$data = []; // for stock fucntion
					foreach ($barcodeData->partList as $part) {
						$part_id = Part::findOneByPartNumber($part->partNumber)->id;
						$item = new DocumentDetail();
						$item->document_id = $document->id;
						$item->part_id = $part_id;
						$item->qty = $part->qty;
						if (!$item->save()) {
							$errorlist[] = $item->getErrors();
						} else {
							unset($tmpArr);
							$tmpArr['part_id'] = $item->part_id;
							$tmpArr['qty'] = $item->qty;
							$data[] = $tmpArr;
						}
					}
					// shu joyda stock table ga ham change qilish kk
					// - from_wh dan agar ostatkasi yetsa kamaytirish
					$stockResult = Stock::issue($document->from_warehouse_id, $data);

					if (count($errorlist) == 0 and $stockResult['success']) {
						$transaction->commit();
						Yii::$app->session->setFlash('success', Yii::t('app', 'Document created successfully.') . ' ' . Yii::t('app', 'Document number') . ': <b>' . Html::a($document->docnum, Url::toRoute(['document/view','id' => $document->id]))   . '</b>');
						return $this->redirect(['issue', 'document_id' => $document->id]);
					} else {
						$transaction->rollBack();
						return $this->render('issue', [
							'errorlist' => ['details' => $errorlist, 'stock' => $stockResult['errorlist'] ?? null],
							'model' => $model,
							'barcodeData' => $barcodeData,
							'json_wh_list' => $json_wh_list,
							'json_part_list' => $json_part_list
						]);
					}
				} else {
					$errorlist[] = $document->errors;
					return $this->render('issue', [
						'barcodeData' => $barcodeData,
						'errorlist' => ['details' => $errorlist],
						'model' => $model,
						'json_wh_list' => $json_wh_list,
						'json_part_list' => $json_part_list

					]);
				}
			} else {
				// Ma'lumotlarda kamchiliklar bor xato chiqaramiz
				$errorlist = ['details' => ['no_item' => $checkEmptyErrors]];
				return $this->render('issue', [
					'barcodeData' => $barcodeData,
					'errorlist' => $errorlist,
					'model' => $model,
					'json_wh_list' => $json_wh_list,
					'json_part_list' => $json_part_list
				]);
			}
		}

		return $this->render('issue', [
			'barcodeData' => $barcodeData,
			'errorlist' => $errorlist,
			'model' => $model,
			'modelNewDocument' => (!empty($document_id) ? Document::findOne($document_id) : null),
			'json_wh_list' => $json_wh_list,
			'json_part_list' => $json_part_list
		]);
	}
	
	public function actionReceiptLocalKd($document_id = null)
	{
		$model = new ScanningForm();

		$errorlist = [];
		$isNewRecord = true;
		$barcodeData = null;
		$isValid = true;
		$model->docdate = date('d.m.Y');

		$json_wh_list = json_encode(yii\helpers\ArrayHelper::map(Warehouse::find()->where(['status' => Warehouse::STATUS_ACTIVE])->all(), 'id', 'name'));
		$json_part_list = json_encode(yii\helpers\ArrayHelper::map(Part::find()->where(['status' => Part::STATUS_ACTIVE])->all(), 'part_no', 'partNameSpecial'));

		$localContracts = Contract::find()->where(['contract_source_id' => Yii::$app->params['local_contract_source_ids']])->all();  
		$localSuppliers = [];
		
		foreach($localContracts as $lCon){
			$localSuppliers[$lCon->supplier_id] = $lCon->supplier->name;
		}

		$json_sp_list = json_encode($localSuppliers);
		

		if ($model->load(Yii::$app->request->post())) {

			if (!empty($model->barCodeData)) {
				$barcodeData = json_decode($model->barCodeData);
				$checkEmptyErrors = [];
				// Agar bironta detal tanlanmasdan submit qilingan bolsa qaytaramiz
				// Aslida frontendda tekshiriladi, lekin bilag`on qo`chqarlar ham chiqib qoladida :))
				if (is_array($barcodeData->partList) and count($barcodeData->partList) < 1) {
					$checkEmptyErrors[] = [Yii::t('app', 'You must select at least one part.')];
					$isValid = false;
				}

				// Agar fromwh tanlamasdan submit qilingan bolsa qaytaramiz
				if (empty($barcodeData->whFromId)) {
					$checkEmptyErrors[] = [Yii::t('app', 'You must select warehouse which issuing.')];
					$isValid = false;
				}

				// Agar towh tanlamasdan submit qilingan bolsa qaytaramiz
				if (empty($barcodeData->whToId)) {
					$checkEmptyErrors[] = [Yii::t('app', 'You must select warehouse which receiving.')];
					$isValid = false;
				}

				// Agar tanlangan detallardan birontasi tanlangan supplierga tegishli bo'lmasa qaytaramiz
				$supplierParts = PartController::getPartsBySupplier($barcodeData->whFromId);

				$belongsToSupplier = false;
				foreach ($barcodeData->partList as $part) {
					foreach ($supplierParts as $row) {
						if($part->partNumber == $row['part_no']){
							$belongsToSupplier = true;
							break 2;
						}
					}
				}
				if(!$belongsToSupplier){
					$checkEmptyErrors[] = [Yii::t('app', 'Some parts are not belongs to this supplier.')];
					$isValid = false;	
				}

				if (empty($barcodeData->whToId)) {
					$checkEmptyErrors[] = [Yii::t('app', 'You must select warehouse which receiving.')];
					$isValid = false;
				}
			} else {
				$checkEmptyErrors[] = [Yii::t('app', 'Empty data.')];
				$isValid = false;
			}

			if ($isValid) {
				// Hammasi OK, davom etamiz
				$document_type_id = 2; // nakladnoy
				$from_wh_id = Yii::$app->params['outsoursingWhId'];
				$supplierId = $barcodeData->whFromId;
				$to_wh_id = $barcodeData->whToId;

				$transaction = Yii::$app->db->beginTransaction();
				$document = new Document();
				$document->status = 1;
				$document->document_type_id = $document_type_id;
				$document->supplier_id = $supplierId;
				$document->from_warehouse_id = $from_wh_id;
				$document->to_warehouse_id = $to_wh_id;
				$document->docnum = DocumentType::generateDocnum($document_type_id);
				$document->docdate = date("Y-m-d", strtotime($model->docdate));
				if ($document->save()) {
					$data = []; // for stock fucntion
					foreach ($barcodeData->partList as $part) {
						$part_id = Part::findOneByPartNumber($part->partNumber)->id;
						$item = new DocumentDetail();
						$item->document_id = $document->id;
						$item->part_id = $part_id;
						$item->qty = $part->qty;
						if (!$item->save()) {
							$errorlist[] = $item->getErrors();
						} else {
							unset($tmpArr);
							$tmpArr['part_id'] = $item->part_id;
							$tmpArr['qty'] = $item->qty;
							$data[] = $tmpArr;
						}
					}
					// shu joyda stock table ga ham change qilish kk
					// - to_wh ni ostatkasi ko'paytirish
					$stockResult = Stock::receipt($document->to_warehouse_id, $data);

					if (count($errorlist) == 0 and $stockResult['success']) {
						$transaction->commit();
						Yii::$app->session->setFlash('success', Yii::t('app', 'Document created successfully.') . ' ' . Yii::t('app', 'Document number') . ': <b>' . Html::a($document->docnum, Url::toRoute(['document/view','id' => $document->id]))   . '</b>');
						return $this->redirect(['receipt-local-kd', 'document_id' => $document->id]);
					} else {
						$transaction->rollBack();
						return $this->render('receipt-local-kd', [
							'errorlist' => ['details' => $errorlist, 'stock' => $stockResult['errorlist'] ?? null],
							'model' => $model,
							'barcodeData' => $barcodeData,
							'json_wh_list' => $json_wh_list,
							'json_sp_list' => $json_sp_list,
							'json_part_list' => $json_part_list
						]);
					}
				} else {
					$errorlist[] = $document->errors;
					return $this->render('receipt-local-kd', [
						'barcodeData' => $barcodeData,
						'errorlist' => ['details' => $errorlist],
						'model' => $model,
						'json_wh_list' => $json_wh_list,
						'json_sp_list' => $json_sp_list,
						'json_part_list' => $json_part_list

					]);
				}
			} else {
				// Ma'lumotlarda kamchiliklar bor xato chiqaramiz
				$errorlist = ['details' => ['no_item' => $checkEmptyErrors]];
				return $this->render('receipt-local-kd', [
					'barcodeData' => $barcodeData,
					'errorlist' => $errorlist,
					'model' => $model,
					'json_wh_list' => $json_wh_list,
					'json_sp_list' => $json_sp_list,
					'json_part_list' => $json_part_list
				]);
			}
		}

		return $this->render('receipt-local-kd', [
			'barcodeData' => $barcodeData,
			'errorlist' => $errorlist,
			'model' => $model,
			'modelNewDocument' => (!empty($document_id) ? Document::findOne($document_id) : null),
			'json_wh_list' => $json_wh_list,
			'json_sp_list' => $json_sp_list,
			'json_part_list' => $json_part_list
		]);
	}

	public function actionReceiptLocalCon($document_id = null)
	{
		$model = new ScanningForm();

		$errorlist = [];
		$isNewRecord = true;
		$barcodeData = null;
		$isValid = true;
		$model->docdate = date('d.m.Y');

		$json_wh_list = json_encode(yii\helpers\ArrayHelper::map(Warehouse::find()->where(['status' => Warehouse::STATUS_ACTIVE])->all(), 'id', 'name'));
		$json_part_list = json_encode(yii\helpers\ArrayHelper::map(Part::find()->where(['status' => Part::STATUS_ACTIVE])->all(), 'part_no', 'partNameSpecial'));
		$json_suppwh_ids = json_encode(yii\helpers\ArrayHelper::map(Warehouse::find()->where(['warehouse_type' => Warehouse::TYPE_OUTSOURCING])->all(), 'supplier_id', 'id'));

		$supplierWhs = Warehouse::find()->where(['warehouse_type' => Warehouse::TYPE_OUTSOURCING])->all();  
		$conSuppliers = [];
		
		foreach($supplierWhs as $swh){
			if($swh)
				$conSuppliers[$swh->supplier_id] = $swh->supplier->name ?? '';
		}
		
		$json_sp_list = json_encode($conSuppliers);
		

		if ($model->load(Yii::$app->request->post())) {

			if (!empty($model->barCodeData)) {
				$barcodeData = json_decode($model->barCodeData);
				$checkEmptyErrors = [];
				// Agar bironta detal tanlanmasdan submit qilingan bolsa qaytaramiz
				// Aslida frontendda tekshiriladi, lekin bilag`on qo`chqarlar ham chiqib qoladida :))
				if (is_array($barcodeData->partList) and count($barcodeData->partList) < 1) {
					$checkEmptyErrors[] = [Yii::t('app', 'You must select at least one part.')];
					$isValid = false;
				}

				// Agar fromwh tanlamasdan submit qilingan bolsa qaytaramiz
				if (empty($barcodeData->whFromId)) {
					$checkEmptyErrors[] = [Yii::t('app', 'You must select warehouse which issuing.')];
					$isValid = false;
				}

				// Agar towh tanlamasdan submit qilingan bolsa qaytaramiz
				if (empty($barcodeData->whToId)) {
					$checkEmptyErrors[] = [Yii::t('app', 'You must select warehouse which receiving.')];
					$isValid = false;
				}

				// Agar tanlangan detallardan birontasi tanlangan supplierga tegishli bo'lmasa qaytaramiz
				$supplierParts = PartController::getPartsByFloc($barcodeData->whFromId);

				// echo '<pre>';
				// print_r($supplierParts);
				// echo '</pre>';
				// die;

				$belongsToSupplier = false;
				foreach ($barcodeData->partList as $part) {
					foreach ($supplierParts as $row) {
						if($part->partNumber == $row['part_no']){
							$belongsToSupplier = true;
							break 2;
						}
					}
				}
				if(!$belongsToSupplier){
					$checkEmptyErrors[] = [Yii::t('app', 'Some parts are not belongs to this supplier.')];
					$isValid = false;	
				}

				if (empty($barcodeData->whToId)) {
					$checkEmptyErrors[] = [Yii::t('app', 'You must select warehouse which receiving.')];
					$isValid = false;
				}
			} else {
				$checkEmptyErrors[] = [Yii::t('app', 'Empty data.')];
				$isValid = false;
			}

			if ($isValid) {
				// Hammasi OK, davom etamiz
				$document_type_id = 2; // nakladnoy
				$from_wh_id = $barcodeData->whFromId;
				$to_wh_id = $barcodeData->whToId;

				$transaction = Yii::$app->db->beginTransaction();
				$document = new Document();
				$document->status = 1;
				$document->document_type_id = $document_type_id;
				$document->from_warehouse_id = $from_wh_id;
				$document->to_warehouse_id = $to_wh_id;
				$document->docnum = DocumentType::generateDocnum($document_type_id);
				$document->docdate = date("Y-m-d", strtotime($model->docdate));
				if ($document->save()) {
					$data = []; // for stock fucntion
					foreach ($barcodeData->partList as $part) {
						$part_id = Part::findOneByPartNumber($part->partNumber)->id;
						$item = new DocumentDetail();
						$item->document_id = $document->id;
						$item->part_id = $part_id;
						$item->qty = $part->qty;
						$hasSubParts = Part::findOne($item->part_id)->hasSubParts ?? null;
						if ($hasSubParts) $item->sub = 1;
						if (!$item->save()) {
							$errorlist[] = $item->getErrors();
						} else {

							if ($hasSubParts) {
								// agar bu detal sub bomda ota detal bolsa va rasxod qilayotgan sklad fizicheskiy bolmasa uni bollarini rasxod qilamiz
								$dataSub = [];
								foreach ($item->part->subParts as $subPart) {
									// har bir sub detalni ulock boyicha rasxod qilib chiqamiz
									//
									// UzCCda 1 ta davalskiy detal bir necha postavshikdan kelganligi uchun
									// rasxodni BOM dagi ULOC bo'yicha emas, tanlangan postavshik bo'yicha qilamiz
									$dataSub = [
										[
											'part_id' => $subPart->sub_part_id,
											'qty' => $item->qty * $subPart->usage_qty
										]
									];
									$stockResultSub = Stock::issueFromShop($document->from_warehouse_id, $dataSub);
									if (!$stockResultSub['success']) {
										$errorlist[] = [[Yii::t('app', 'Issue problem')]];
									}
									// sub table ga rasxod qilingan detallarni yozib qoyamiz
									$documentDetailSub = new DocumentDetailSub();
									$documentDetailSub->document_id = $document->id;
									$documentDetailSub->part_id = $subPart->part_id;
									$documentDetailSub->sub_part_id = $subPart->sub_part_id;
									$documentDetailSub->qty = $item->qty * $subPart->usage_qty;
									$documentDetailSub->warehouse_id = $document->from_warehouse_id;
									if (!$documentDetailSub->save()) {
										$errorlist[] = [[Yii::t('app', 'Document sub-detail insert problem')]];
									}
								}
							} else {
								unset($tmpArr);
								$tmpArr['part_id'] = $item->part_id;
								$tmpArr['qty'] = $item->qty;
								$data[] = $tmpArr;
							}

							unset($tmpArr);
							$tmpArr['part_id'] = $item->part_id;
							$tmpArr['qty'] = $item->qty;
							$dataReceipt[] = $tmpArr;
						}
					}
					// Prixod qilinayotgan skladni ostatkasini ko'paytirish
					$stockResultReceipt = Stock::receipt($document->to_warehouse_id, $dataReceipt);
					// Rasxod qilgan Postavshik skladni ostatkasidan yetmasa ham kamaytirish (outsourcing)
					$stockResultIssue = Stock::issueFromShop($document->from_warehouse_id, $data);

					if (count($errorlist) == 0 and $stockResultReceipt['success'] and $stockResultIssue['success']) {
						$transaction->commit();
						Yii::$app->session->setFlash('success', Yii::t('app', 'Document created successfully.') . ' ' . Yii::t('app', 'Document number') . ': <b>' . Html::a($document->docnum, Url::toRoute(['document/view','id' => $document->id]))   . '</b>');
						return $this->redirect(['receipt-local-con', 'document_id' => $document->id]);
					} else {
						$transaction->rollBack();
						return $this->render('receipt-local-con', [
							'errorlist' => ['details' => $errorlist, 'stockResult' => $stockResultReceipt['errorlist'] ?? null, 'stockIssue' => $stockResultIssue['errorlist'] ?? null],
							'model' => $model,
							'barcodeData' => $barcodeData,
							'json_wh_list' => $json_wh_list,
							'json_sp_list' => $json_sp_list,
							'json_part_list' => $json_part_list,
							'json_suppwh_ids' => $json_suppwh_ids
						]);
					}
				} else {
					$errorlist[] = $document->errors;
					return $this->render('receipt-local-con', [
						'barcodeData' => $barcodeData,
						'errorlist' => ['details' => $errorlist],
						'model' => $model,
						'json_wh_list' => $json_wh_list,
						'json_sp_list' => $json_sp_list,
						'json_part_list' => $json_part_list,
						'json_suppwh_ids' => $json_suppwh_ids

					]);
				}
			} else {
				// Ma'lumotlarda kamchiliklar bor xato chiqaramiz
				$errorlist = ['details' => ['no_item' => $checkEmptyErrors]];
				return $this->render('receipt-local-con', [
					'barcodeData' => $barcodeData,
					'errorlist' => $errorlist,
					'model' => $model,
					'json_wh_list' => $json_wh_list,
					'json_sp_list' => $json_sp_list,
					'json_part_list' => $json_part_list,
					'json_suppwh_ids' => $json_suppwh_ids
				]);
			}
		}

		return $this->render('receipt-local-con', [
			'barcodeData' => $barcodeData,
			'errorlist' => $errorlist,
			'model' => $model,
			'modelNewDocument' => (!empty($document_id) ? Document::findOne($document_id) : null),
			'json_wh_list' => $json_wh_list,
			'json_sp_list' => $json_sp_list,
			'json_part_list' => $json_part_list,
			'json_suppwh_ids' => $json_suppwh_ids
		]);
	}

	public function actionUpload() 
	{
		Yii::$app->response->format = Response::FORMAT_JSON;
		$form = new DocumentUploadForm();
		
		$file = $_FILES['file'];

		if($file) {
			return $form->handle($file['tmp_name']);
		} else return ['error' => 'no upload'];
	}
}
