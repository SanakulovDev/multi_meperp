<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use PHPExcel_Style_NumberFormat;

/**
 * UploadForm is the model behind the upload form.
 */
class OemPlanUploadForm extends Model {
	/**
	 * @var UploadedFile file attribute
	 */
	public $file;

	/**
	 * @return array the validation rules.
	 */
	public function rules() {
		return [
			[['file'], 'required'],
			[['file'], 'file', 'skipOnEmpty' => false, 'extensions' => 'xls, xlsx'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels() {
		return [
			'file' => Yii::t('app', 'File'),
		];
	}

	public $errorMessages = [];

	private function getDate($val) {
		return date('Y-m-d', strtotime(PHPExcel_Style_NumberFormat::toFormattedString($val, 'YYYY-MM-DD')));
	}

	public function handle($file_path_name) {
		$file_path = $file_path_name;
		$inputFileType = \PHPExcel_IOFactory::identify($file_path);
		//read file from path
		$objReader = \PHPExcel_IOFactory::createReader($inputFileType);
		//load the excel library
		$objPHPExcel = $objReader->load($file_path);
		//  Get worksheet dimensions
		$sheet = $objPHPExcel->getSheet(0);
		$highestRow = $sheet->getHighestRow();
		$highestColumn = $sheet->getHighestDataColumn();
		$highestColIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);

		// need models names
		$columnHeaders = [];
		for ($col = 1; $col <= $highestColIndex; $col++) {
			$title = trim($sheet->getCellByColumnAndRow($col, 1)->getValue());
			if (empty($title)) {
				break;
			}
			$columnHeaders[$col] = $title;
		}

		$dbModels = ArrayHelper::map(ProductModel::find()->all(), 'id', 'description');
		$modelIds = [];
		// Validate models
		foreach ($columnHeaders as $k => $model) {
			if (($id = array_search($model, $dbModels)) != false) {
				$modelIds[$k] = $id;
			} else {
				$this->errorMessages[] = $model . ' ' . Yii::t('app', 'Data not found');
			}
		}

		if ($this->errorMessages) {
			return $data['errors'] = $this->errorMessages;
		}
		$lenModels = count($modelIds);

		if ($highestRow >= 2) {
			// get dates
			$dates = [];
			for ($row = 2; $row <= $highestRow; $row++) {
				$date = $this->getDate($sheet->getCellByColumnAndRow(0, $row)->getValue());
				if (!in_array($date, $dates)) {
					$dates[] = $date;
				}
			}

			$transaction = Yii::$app->db->beginTransaction();
			try {
				// Delete plan for models and $dates
				for ($col = 1; $col <= $lenModels; $col++) {
					OemPlan::deleteAll(['model_id' => $modelIds[$col], 'target_date' => $dates]);
				}
				// insert new plan
				for ($row = 2; $row <= $highestRow; $row++) {
					for ($col = 1; $col <= $lenModels; $col++) {
						$plan = new OemPlan();
						$plan->model_id = $modelIds[$col];
						$plan->target_date = $this->getDate($sheet->getCellByColumnAndRow(0, $row)->getValue());
						$plan->quantity = $sheet->getCellByColumnAndRow($col, $row)->getValue();
						$plan->save();
					}
				}
				$transaction->commit();
			} catch (\Exception $e) {
				$transaction->rollBack();
				$this->errorMessages[] = $e->getMessage();
			} catch (\Throwable $e) {
				$transaction->rollBack();
				$this->errorMessages[] = $e->getMessage();
			}
		}
	}
}
