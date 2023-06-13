<?php

namespace app\controllers;

use app\components\Helpers;
use app\models\Part;
use app\models\Stock;
use app\models\StockUploadForm;
use app\models\StockSearch;
use app\models\Warehouse;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * StockController implements the CRUD actions for Stock model.
 */
class StockController extends AppController
{
	/**
	 * Lists all Stock models.
	 * @return mixed
	 */
	public function actionIndex()
	{

		
		$searchModel = new StockSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		if (!in_array(Yii::$app->user->identity->rolename, ['admin', 'superadmin', 'observer', 'report']) ) {
				$dataProvider->query->andWhere(['stock.warehouse_id' => Yii::$app->user->identity->warehouseIds]);
		}
		
	
		$countQuery = clone $dataProvider->query;
		$total = 0;
		foreach ($countQuery->all() as $row) {
			$total += $row->qty;
		}
		$parts = ArrayHelper::map(Part::find()->all(), 'id', 'partinfo');
		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
			'parts' => $parts,
			'user_warehouses' => $this->userWarehouses,
			'total' => $total,
		]);
	}

	public function actionXls()
	{
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', '-1');
		$searchModel = new StockSearch();
		$xsl_file = $searchModel->search(Yii::$app->request->queryParams, 'excel');
		$xsl_file->send(Helpers::downloadFileName('stock'));
		die;
	}


	public function actionUpload()
	{
		$model = new StockUploadForm();
		if ($model->load(Yii::$app->request->post())) {
			$uploadedFile = UploadedFile::getInstance($model, 'file');
			$data = $model->readExcel($uploadedFile->tempName);
			$emptyCells = [];
			$notExistsParts = [];
			$notExistsWhs = [];
			$modelErrors = [];
			$allPartNumbers = Part::getAllPartNumbers();
			$whNames = Warehouse::getWhNames();
			foreach ($data['values'] as $key => $row) {
				if (empty($row['part_no']) or empty($row['warehouse'])) {
					$emptyCells[] = Yii::t('app', 'Row') . ' ' . ($key + 2) . '. ' . Yii::t('app', 'Empty data.');
				} else {
					if (!in_array(trim($row['part_no']), $allPartNumbers)) {
						$notExistsParts[] = Yii::t('app', 'Row') . ' ' . ($key + 2) . '. ' . Yii::t('app', 'Part not found.') . '  (' . $row['part_no'] . ')';
					} else {
						$part_numbers[] = $row['part_no'];
					}
					if (!in_array(trim($row['warehouse']), $whNames)) {
						$notExistsWhs[] = Yii::t('app', 'Row') . ' ' . ($key + 2) . '. ' . Yii::t('app', 'Warehouse not found.') . '  (' . $row['warehouse'] . ')';
					}
				}
			}
			if (count($emptyCells) > 0) {
				$message = 'Empty cells found in uploaded file.';
				$errors = implode('<br>', $emptyCells);
				Yii::$app->session->setFlash('error', '<b>' . Yii::t('app', $message) . '</b>' . '<br>' . $errors);
				return $this->render('upload', ['model' => $model]);
			}
			if (count($notExistsParts) > 0) {
				$message = 'These parts are not found in active parts list.';
				$errors = implode('<br>', $notExistsParts);
				Yii::$app->session->setFlash('error', '<b>' . Yii::t('app', $message) . '</b>' . '<br>' . $errors);
				return $this->render('upload', ['model' => $model]);
			}
			if (count($notExistsWhs) > 0) {
				$message = 'These warehouses are not found in active warehouses list.';
				$errors = implode('<br>', $notExistsWhs);
				Yii::$app->session->setFlash('error', '<b>' . Yii::t('app', $message) . '</b>' . '<br>' . $errors);
				return $this->render('upload', ['model' => $model]);
			}

			$transaction = Yii::$app->db->beginTransaction();
			foreach ($data['values'] as $key => $row) {
				//          if($row['qty'] == ''){
				//            echo "<pre>";
				//            var_dump($row);
				//            echo "</pre>";
				//          }



				// Agar table da part_id, warehouse_id avval tushgan bo'lsa 
				//   agar qty SPACE bo'lmasa update qilamiz
				//   aks holda row ni o'chiramiz
				// aks holda 
				//   agar qty SPACE bo'lmasa create qilamiz
				//   aks holda hech narsa qilmaymiz
				$part_id = Part::findOneByPartNumber($row['part_no'])->id;
				$warehouse_id = Warehouse::findOneByWhName($row['warehouse'])->id;
				$stock = Stock::find()->where([
					'warehouse_id' => $warehouse_id,
					'part_id' => $part_id
				])->one();
				if ($stock) {
					// ma'lumot bor
					if ($row['qty'] != '') {
						// qty space emas
						$stock->qty = $row['qty'];
						if (!$stock->save()) {
							$updateErrors = [];
							foreach ($stock->errors as $value) {
								foreach ($value as $val) {
									$updateErrors[] = $val;
								}
							}
							$modelErrors[] = Yii::t('app', 'Row') . ' ' . ($key + 2) . '. (' . $row['part_no'] . ')<br>- ' . implode('- <br>', array_unique($updateErrors));
						}
					} else {
						// qty = space 
						$stock->delete();
					}
				} else {
					// yangi ma'lumot
					if ($row['qty'] != '') {
						// qty space emas
						$modelStock = new Stock();
						$modelStock->part_id = $part_id;
						$modelStock->warehouse_id = $warehouse_id;
						$modelStock->qty = $row['qty'];
						if (!$modelStock->save()) {
							$insertErrors = [];
							foreach ($modelStock->errors as $value) {
								foreach ($value as $val) {
									$insertErrors[] = $val;
								}
							}
							$modelErrors[] = Yii::t('app', 'Row') . ' ' . ($key + 2) . '. (' . $row['part_no'] . ')<br>- ' . implode('- <br>', array_unique($insertErrors));
						}
					}
				}
			}
			if (count($modelErrors) > 0) {
				$transaction->rollBack();
				$message = 'Please check this errors.';
				$errors = implode('<br>', $modelErrors);
				Yii::$app->session->setFlash('error', '<b>' . Yii::t('app', $message) . '</b>' . '<br>' . $errors);
				return $this->render('upload', ['model' => $model]);
			} else {
				$transaction->commit();
				$message = 'File successfully uploaded to server.';
				Yii::$app->session->setFlash('success', '<b>' . Yii::t('app', $message) . '</b>');
				return $this->redirect(['upload']);
			}
		}
		return $this->render('upload', [
			'model' => $model,
		]);
	}


	// ostatoka GP
	public function actionDashboard()
	{
		// $this->layout = 'req';
		$query = "SELECT part.remark as remark, part.part_no as part_no, part.part_name as part_name, part.part_color as part_color, sum(stock.qty) as qty FROM stock left join part on part.id = stock.part_id  where qty >0 group by stock.part_id order by part.remark DESC";
		$stocks = Yii::$app->db->createCommand($query)->queryAll();
		return $this->render('dashboard', [
			'stocks' => $stocks,
		]);
	}
	 // excel import
	 public function actionDownloadDashboard()
	 {
	   ini_set("memory_limit", "-1");
	   	$query = "SELECT part.remark as remark, part.part_no as part_no, part.part_name as part_name, part.part_color as part_color, sum(stock.qty) as qty FROM stock left join part on part.id = stock.part_id  where qty >0 group by stock.part_id order by part.remark DESC";
		$stocks = Yii::$app->db->createCommand($query)->queryAll();
		$arrFile = [];
		// vd($data_daily[0]);
		$arr = [[], []];
		$month = [];
		foreach($stocks as $key => $detailWide) {
			$tmpArray['id'] 		= $key+1;
			$tmpArray['remark'] 	= $detailWide['remark'];
			$tmpArray["part_no"] 	= $detailWide['part_no'];
			$tmpArray["part_name"] 	= $detailWide['part_name'];
			$tmpArray["part_color"] = preg_replace('/\d+/', '', $detailWide['part_color']);
			$tmpArray["qty"] 		= number_format($detailWide['qty'], 2, '.', ' ')*1;
			$arrFile[] 				= $tmpArray;
			
		}
		$header_titles = [
			0 => Yii::t('app', 'No'),
			1 => Yii::t('app', 'Remark'),
			2 => Yii::t('app', 'Part No'),
			3 => Yii::t('app', 'Part Name'),
			4 => Yii::t('app', 'Part Color'),
			5 => Yii::t('app', 'Qty'),
		];
		$detail_titles = [];
		// $titles = array_merge($header_titles);

		$titles = $header_titles;
		$file = Yii::createObject([
			"class" => 'codemix\excelexport\ExcelFile',
			"sheets" => [
				"coverage" => [
					"data" => $arrFile,
					"titles" => $titles,
				],
			],
		]);
		$file->send(Helpers::downloadFileName("Остаток GP"));
	 }
	/**
	 * Finds the Stock model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * @param integer $id
	 * @return Stock the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id)
	{
		if (($model = Stock::findOne($id)) !== null) {
			return $model;
		}
		throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
	}

	protected function getUserWarehouses()
	{
		if (in_array(Yii::$app->user->identity->rolename, ['admin', 'superadmin', 'report', 'observer'])) {
			return yii\helpers\ArrayHelper::map(Warehouse::find()->all(), 'id', 'name');
		} else {
			return Yii::$app->user->identity->warehouseNames;
		}
	}
}
