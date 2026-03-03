<?php

namespace app\controllers;

use app\components\Helpers;
use app\models\Contract;
use app\models\ContractDetail;
use app\models\DeliveryTerm;
use app\models\Part;
use app\models\PartOrder;
use app\models\PartOrderDetail;
use app\models\PartOrderSearch;
use app\models\Req;
use Codeception\Lib\Generator\Helper;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use Yii;
use yii\base\DynamicModel;
use yii\db\Exception;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

/**
	 * PartOrderController implements the CRUD actions for PartOrder model.
	 */
	class PartOrderController extends AppController {
		/**
		 * Lists all PartOrder models.
		 * @return mixed
		 */
		public function actionIndex() {
			$searchModel = new PartOrderSearch();
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
			$contract = ArrayHelper::map(Contract::find()->where('status>0')->all(), 'id', 'contract_no');
			$deliveryTerm = ArrayHelper::map(DeliveryTerm::find()->all(), 'id', 'name');
			$months = $searchModel->getMonths();
			return $this->render('index', compact('searchModel', 'dataProvider', 'contract', 'deliveryTerm', 'months'));
		}

		/**
		 * Displays a single ContainerInvoice model.
		 * @param int    $container_id
		 * @param int    $invoice_id
		 * @param string $app_arr_at
		 * @param string $shipped_at
		 * @return mixed
		 * @throws NotFoundHttpException if the model cannot be found
		 */
		public function actionView($id = null) {
			$model = $this->findModel($id);
			//////////////////////////////////////////////////////////////////////////////////////////
			$items = $_POST['items'] ?? null;
			$errorlist = [];
			if ($items == null) {
				$errorlist['nopart'] = Yii::t('app', 'You must select at least one part.');
				return $this->render('view', ['model' => $model, 'errorlist' => $errorlist]);
			}
			if ((count($items['part_no']) < 1) || (count($items['qty']) < 1)) {
				$errorlist['Part no'] = Yii::t('app', 'You must select at least one part.');
				return $this->render('view', ['model' => $model, 'errorlist' => $errorlist]);
			}
			foreach ($items['part_no'] as $key => $part_id) {
//				$req = ( Req::find()->select('days_count')->where(['part_id' => $part_id, 'type' => 'D' ])->one() ) ?? null;
//				$dayCount =($req) ? $req->days_count : null;
				$dayCount = (Req::findOne(['part_id' => $part_id, 'type' => 'D'])->days_count) ?? null;
				$insert_data[] = [$model->id, $part_id, $items['qty'][$key], null, null, $dayCount, Yii::$app->user->id, time()];
			}
			$transaction = Yii::$app->db->beginTransaction();
			try {
				$res = Yii::$app->db->createCommand()
									->batchInsert(
										'part_order_detail',
										['part_order_id', 'part_id', 'qty', 'exwrk_plan', 'exwrk_actual', 'comment', 'created_by', 'created_at'],
										$insert_data
									);
				$res->execute();
				$transaction->commit();
				Yii::$app->session->setFlash('success', Yii::t('app', 'Created successfully.'));
			} catch (Exception $e) {
				$transaction->rollback();
				Yii::$app->session->setFlash('warning', Yii::t('app', 'Error Insert: ' . $e->getMessage()));
			}
			return $this->redirect(['view', 'id' => $model->id, 'model' => $model]);
			//////////////////////////////////////////////////////////////////////////////////////////
			return $this->render('view', ['model' => $model, 'errorlist' => $errorlist]);
		}

		/**
		 * Creates a new PartOrder model.
		 * If creation is successful, the browser will be redirected to the 'view' page.
		 * @return mixed
		 */
		public function actionCreate($id = null) {
			if ($id) {
				$contract_model = Contract::find()->where(['id' => $id])->one();
			}

			$model = new PartOrder();
			$errorlist = [];
			if ($model->load(Yii::$app->request->post())) {
				$model->created_by = Yii::$app->user->id;
				$model->created_at = time();
				if ($model->save()) {
					Yii::$app->session->setFlash('success', Yii::t('app', 'Successfully.'));
					return $this->redirect(["view?id=" . $model->id]);
				} else {
          $errMsg = "<i><u><strong>OrderCreate:</strong></u></i>".Helpers::arrayToStringRecursive($model->errors);
				}
			}


			return $this->render(
				'create',
				[
					'model' => $model,
					'contract_model' => $contract_model,
          			'errMsg' => $errMsg ?? null
				]
			);
		}

		/**
		 * Updates an existing PartOrder model.
		 * If update is successful, the browser will be redirected to the 'view' page.
		 * @param int $id
		 * @return mixed
		 * @throws NotFoundHttpException if the model cannot be found
		 */
		public function actionUpdate($id) {
			$model = $this->findModel($id);
			$errorlist = [];
			if ($model->load(Yii::$app->request->post())) {
				$model->updated_by = Yii::$app->user->id;
				$model->updated_at = time();
				if ($model->save()) {
					Yii::$app->session->setFlash('success', Yii::t('app', 'Successfully.'));
					return $this->redirect(['index']);
				} else {
					$errMsg = "<i><u><strong>OrderCreate:</strong></u></i>".Helpers::arrayToStringRecursive($model->errors);
				}
			}

			$contract_model = Contract::find()->where(['id' => $model->contract_id])->one();
			return $this->render(
				'update',
				[
					'model' => $model,
          			'errMsg' => $errMsg ?? null,
					'contract_model' => $contract_model,
				]
			);
		}

		/**
		 * Deletes an existing PartOrder model.
		 * If deletion is successful, the browser will be redirected to the 'index' page.
		 * @param int $id
		 * @return mixed
		 * @throws NotFoundHttpException if the model cannot be found
		 */
		public function actionDelete($id) {
			$model = $this->findModel($id);
			try {
				$model->delete();
				Yii::$app->session->setFlash('success', Yii::t('app', 'Deleted successfully'));
			} catch (Exception $e) {
				if ($e->errorInfo[1] == 1451) {
					Yii::$app->session->setFlash('error', Yii::t('app', 'error_delete_fr_key'));
				} else {
					throw $e;
				}
			}
			return $this->redirect(['index']);
		}

		/**
		 * Finds the PartOrder model based on its primary key value.
		 * If the model is not found, a 404 HTTP exception will be thrown.
		 * @param int $id
		 * @return PartOrder the loaded model
		 * @throws NotFoundHttpException if the model cannot be found
		 */
		protected function findModel($id) {
			if (($model = PartOrder::findOne($id)) !== null) {
				return $model;
			}
			throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
		}

		/**
		 * IMPORT from PHPEXCEL
		 * */
		public function actionImportDetail() {
			$modelImport = new DynamicModel(['fileImport' => 'File Import', ]);
			$modelImport->addRule(['fileImport'], 'required');
			$modelImport->addRule(['fileImport'], 'file', ['extensions' => 'xls,xlsx'], ['maxSize' => 1024 * 1024]);
			$err_text = '';
			$insert_ok_text = '';
			$post_val = Yii::$app->request->post();
			if ($post_val['shu_id'] == '' || $post_val['shu_id'] == null) {
				Yii::$app->session->setFlash('error', Yii::t('app', 'Please, select order again!!!'));
				return $this->render(
					'import-detail',
					[
						'modelImport' => $modelImport ?? null,
						'model' => $model ?? null,
						'err_text' => $err_text ?? null,
						'insert_ok_text' => $insert_ok_text ?? null,
					]
				);
			}
			$model = $this->findModel($post_val['shu_id']);
			if (Yii::$app->request->post()) {
				$modelImport->fileImport = UploadedFile::getInstance($modelImport, 'fileImport');
				if ($modelImport->fileImport) {
					if ($modelImport->fileImport && $modelImport->validate()) {
						$inputFileType = PHPExcel_IOFactory::identify($modelImport->fileImport->tempName);
						$objReader = PHPExcel_IOFactory::createReader($inputFileType);
						$objPHPExcel = $objReader->load($modelImport->fileImport->tempName);
						$ActiveSheetTitle = $objPHPExcel->getActiveSheet()->getTitle();
						$HighestRowAndColumn = $objPHPExcel->getActiveSheet()->getHighestRowAndColumn();
						$highestRow = $HighestRowAndColumn['row'];
						$highestColumn = $HighestRowAndColumn['column'];
						$highestColumnIndex = ord($highestColumn) - 64;
						$column_qty = ord($highestColumn) - 64; //column number format
						$err = 0;
						if ($highestRow > 3000) {
							$err_text = Yii::t('app', 'The max rows are wrong. Rows should be lower than 3000.');
							return $this->render(
								'import-detail',
								['modelImport' => $modelImport, 'model' => $model, 'err_text' => $err_text, 'insert_ok_text' => $insert_ok_text, ]
							);
						}
						if ($column_qty != 5) {
							$err_text = Yii::t('app', 'The number of columns is wrong. Columns should be A and E.');
							return $this->render(
								'import-detail',
								['modelImport' => $modelImport, 'model' => $model, 'err_text' => $err_text, 'insert_ok_text' => $insert_ok_text, ]
							);
						} else {
							$sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
							unset($sheetData[1]); //Birinchi qatorni o`chirish
							$null_part = '';
							$null_qty = '';
							foreach ($sheetData as $key => $value) {
								$null_part = ($value['B'] == null) ? $null_part . ', ' . ($key) : $null_part;
								$null_qty = ($value['C'] == null) ? $null_qty . ', ' . ($key) : $null_qty;
							}
							$null_part = ltrim($null_part, ',');
							$null_qty = ltrim($null_qty, ',');
							if (strlen($null_part) > 0) {
								$err = 1;
								$err_text .= '<br>' . Yii::t('app', 'Empty rows') . '(' . Yii::t('app', 'Part No') . '): ' . $null_part;
							}
							if (strlen($null_qty) > 0) {
								$err = 1;
								$err_text .= '<br>' . Yii::t('app', 'Empty rows') . '(' . Yii::t('app', 'Qty') . '): ' . $null_qty;
							}
							if ($err == 0) {
								$no_DBparts_rows = '';
								$no_DBparts_ptno = '';
								$no_ptno_contract = '';
								//Partlar ro`yxati
								$xls_part_no_list = array_column($sheetData, 'B', 'A');
								$db_parts = Part::find()
												->where('status=1')
												->andWhere(['in', 'part_no', $xls_part_no_list])
												->asArray()
												->all();
								$array_db_part = [];
								foreach ($db_parts as $key => $val) {
									$ptno = $val['part_no'];
									$db_contract_details = ContractDetail::find()
																		 ->where('part_id=' . $val['id'] . ' and contract_id=' . $model->contract_id)
																		 ->asArray()
																		 ->all();
									$array_db_part[] = $ptno;
									if (empty($db_contract_details) == true) {
										$no_ptno_contract = $no_ptno_contract . ', ' . ($ptno);
									}
								}
								//PartMasterdan farqni tekshirish
								$not_DBparts = array_diff($xls_part_no_list, $array_db_part);
								foreach ($not_DBparts as $key => $value) {
									$no_DBparts_rows = $no_DBparts_rows . ', ' . ($key);
								}
								$no_DBparts_rows = ltrim($no_DBparts_rows, ',');
								//Dublikatlarni yo`qotish
								$grouped_array_xls = array_unique($not_DBparts);
								foreach ($grouped_array_xls as $key => $value) {
									$no_DBparts_ptno = $no_DBparts_ptno . ', ' . ($value);
								}
								$no_DBparts_ptno = ltrim($no_DBparts_ptno, ', ');
								if (strlen($no_DBparts_ptno) > 0) {
									$err = 1;
									$err_text .= ' <hr>' . Yii::t('app', 'The following parts are not found in the system:');
									$err_text .= '<br>' . Yii::t('app', 'Rows') . ': ' . $no_DBparts_rows . ' => ' . Yii::t('app', 'Part No') . ': ' . $no_DBparts_ptno;
								}
								$no_ptno_contract = ltrim($no_ptno_contract, ', ');
								if (strlen($no_ptno_contract) > 0) {
									$err = 1;
									$err_text .= ' <hr> ' . Yii::t('app', 'The following parts are not found in Contracts:');
									$err_text .= '<br>' . Yii::t('app', 'Part No') . ': ' . $no_ptno_contract;
								}
							}
						}
						$insert_ok_text = '';
						if ($err > 0) {
							$err_text = ltrim($err_text, '<br>');
							return $this->render('import-detail', ['modelImport' => $modelImport, 'model' => $model, 'err_text' => $err_text, 'insert_ok_text' => $insert_ok_text, ]);
						}
						$insert_data = [];
						foreach ($sheetData as $key => $value) {
							$part_id = Part::findOne(['part_no' => $value['B']])->id;
							$dayCount = (Req::findOne(['part_id' => $part_id, 'type' => 'D'])->days_count) ?? null;
							$qty = $value['C'];
							$exwrk_plan = date('Y-m-d', strtotime($value['D']));
							$exwrk_actual = date('Y-m-d', strtotime($value['E']));
							$part_order_id = $model->id;
							$insert_data[] = [$part_order_id, $part_id, $qty, $exwrk_plan, $exwrk_actual, $dayCount, Yii::$app->user->id, time()];
						}
						$transaction = Yii::$app->db->beginTransaction();
						try {
							Yii::$app->db->createCommand()
										 ->batchInsert(
										 	'part_order_detail',
										 	['part_order_id', 'part_id', 'qty', 'exwrk_plan', 'exwrk_actual', 'comment', 'created_by', 'created_at'],
										 	$insert_data
										 )
										 ->execute();
							$transaction->commit();
							$insert_ok_text = Yii::t('app', 'Upload OK');
						} catch (Exception $e) {
							$transaction->rollback();
							$err_text = Yii::t('app', 'Error Insert: ' . $e->getMessage());
						}
					} else {
						$err_text = Yii::t('app', 'Error validate: ' . $modelImport->errors);
					}
				}
			}
			$err_text = ltrim($err_text, '<br>');
			return $this->render(
				'import-detail',
				[
					'modelImport' => $modelImport ?? null,
					'model' => $model ?? null,
					'err_text' => $err_text ?? null,
					'insert_ok_text' => $insert_ok_text ?? null,
				]
			);
		}

		/**
		 * EXPORT TO EXCELx with Invoice header
		 * */
		public function actionXls() {
			$xls_file = [];
			ini_set('memory_limit', '-1');
			$searchModel = new PartOrderSearch();
			$xls_file = $searchModel->search(Yii::$app->request->queryParams, 'excel');
			if (count($xls_file->sheets['PartOrder']['data']) == 0) {
				return $this->redirect(['index']);
			}
			$xls_file->send(Helpers::downloadFileName('part-order'));
		}

		/**
		 * EXPORT TO EXCELx
		 * */
		public function actionToXlsx($id) {
			$model = $this->findModel($id);
			ini_set('memory_limit', '-1');
			$HR_center = ['alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER]];
			$objPHPExcel = new PHPExcel();
			$sheet = $objPHPExcel->setActiveSheetIndex(0);
			$sheet->getColumnDimension('A')->setAutoSize(true);
			$sheet->getColumnDimension('B')->setAutoSize(true);
			$sheet->getColumnDimension('C')->setAutoSize(true);
			$sheet->getColumnDimension('D')->setAutoSize(true);
			$sheet->getColumnDimension('E')->setAutoSize(true);
			$sheet->getColumnDimension('F')->setAutoSize(true);
			$sheet->getColumnDimension('G')->setAutoSize(true);
			$sheet->getColumnDimension('H')->setAutoSize(true);
			$sheet->getColumnDimension('I')->setAutoSize(true);
			$sheet->getColumnDimension('J')->setAutoSize(true);
			$sheet->getColumnDimension('K')->setAutoSize(true);
			$sheet->getColumnDimension('L')->setAutoSize(true);
			$sheet->getStyle('A1:L1')->getFont()->setBold(true);
			$sheet->getStyle('A1:L1')->applyFromArray($HR_center);
			$sheet->setCellValue('A1', Yii::t('app', 'Order no'))
				  ->setCellValue('B1', Yii::t('app', 'Contract no'))
				  ->setCellValue('C1', Yii::t('app', 'Issued date'))
				  ->setCellValue('D1', Yii::t('app', 'MRD date'))
				  ->setCellValue('E1', Yii::t('app', 'Part No'))
				  ->setCellValue('F1', Yii::t('app', 'Part name'))
				  ->setCellValue('G1', Yii::t('app', 'Quantity'))
				  ->setCellValue('H1', Yii::t('app', 'Price'))
				  ->setCellValue('I1', Yii::t('app', 'Amount'))
				  ->setCellValue('J1', Yii::t('app', 'exwrk_plan'))
				  ->setCellValue('K1', Yii::t('app', 'exwrk_actual'))
				  ->setCellValue('L1', Yii::t('app', 'Order type'));
			$row = 2;
			$partOrderDetails = $model->partOrderDetails;
			foreach ($partOrderDetails as $PartDetails) {
				$objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $model->order_no);
				$objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $model->contract->contract_no);
				$objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $model->iss_dt);
				$objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $model->mr_dt);
				$objPHPExcel->getActiveSheet()->setCellValue('E' . $row, $PartDetails->part->part_no);
				$objPHPExcel->getActiveSheet()->setCellValue('F' . $row, $PartDetails->part->part_name);
				$objPHPExcel->getActiveSheet()->setCellValue('G' . $row, $PartDetails->qty);
				$objPHPExcel->getActiveSheet()->setCellValue('H' . $row, $PartDetails->price);
				$objPHPExcel->getActiveSheet()->setCellValue('I' . $row, $PartDetails->amount);
				$objPHPExcel->getActiveSheet()->setCellValue('J' . $row, $PartDetails->exwrk_plan);
				$objPHPExcel->getActiveSheet()->setCellValue('K' . $row, $PartDetails->exwrk_actual);
				$objPHPExcel->getActiveSheet()->setCellValue('L' . $row, $model->orderTypeText);
				$row++;
			}
			$filename = 'part_order_detail_' . date('Ymd(His)');
			header('Cache-Control: max-age=0');
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007'); //xlsx
			$objWriter->save('php://output');
			exit;
		}

		public function actionPartList($q = null, $id = null, $contract = null, $part_ids = null, $model_id = null) {
			Yii::$app->response->format = Response::FORMAT_JSON;
			$out = ['results' => ['id' => '', 'text' => '']];
			$model_parts = PartOrderDetail::find()
										  ->select('part_id')
										  ->where(['part_order_id' => $model_id])->asArray()->all();
			$has_ids = null;
			foreach ($model_parts as $k => $v) {
				$has_ids .= $v['part_id'] . ',';
			}
			$query = new Query();
			$query->select(['part.id as id, CONCAT(part_no, " (" , part_name,")") AS text, req.days_count as days_count'])
				  ->from('part')
				  ->leftJoin('contract_detail', 'part.id = contract_detail.part_id')
				  ->leftJoin('req', 'part.id = req.part_id')
						->where(['contract_id' => $contract]);
			if ($id > 0) {
				$out['results'] = ['id' => $id, 'text' => Part::findOne(['part_no' => $id])->part_no];
			} elseif (!is_null($q)) {
				$query->andwhere(['like', 'CONCAT(part_no, " ", part_name," ",part_color)', $q]);
			}
			$query->andwhere('part.id not in(' . $has_ids . $part_ids . ')');
			//    echo "<pre> ";print_r($query->createCommand()->rawSql);echo "</pre>"; die;
			$command = $query->createCommand();
			$data = $command->queryAll();

			$data_s2 = [];
			foreach ($data as $k => $v) {
				$days_count = ($v['days_count'] > 120) ? ' <i class="label label-danger">' . $v['days_count'] . '</i>' : '';
				$data_s2[] = [
					'id' => $v['id'],
					'text' => $v['text'],
					'days_count' => $days_count
				];
			}
			$out['results'] = array_values($data_s2);
			return $out;
		}

		public function actionPartData() {
			$post = Yii::$app->request->post();
			$contract_detail = ContractDetail::findone(['contract_id' => $post['contractid'], 'part_id' => $post['partid']]);
			Yii::$app->response->format = Response::FORMAT_JSON;
			$unit = Part::findOne($post['partid'])->unit;
			$out['unit_id'] = $unit->id;
			$out['unit'] = $unit->unit_value;
			$out['price'] = $contract_detail->price ?? 0;
			return $out;
		}

		public function actionListByContractId($id) {
			Yii::$app->response->format = Response::FORMAT_JSON;
			$list = PartOrder::find()->where(['contract_id' => $id])->all();
			$data = [];
			foreach ($list as $item) {
				$data[] = ['id' => $item->id, 'text' => $item->order_no];
			}
			return $data;
		}
	}
