<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use PHPExcel_Style_NumberFormat;

class DocumentUploadForm extends Model {

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
		
		$fileContent = [];
		$highestRow = min($highestRow, 100);
		for ($row = 2; $row <= $highestRow; $row++) {			
			$part_no = $sheet->getCellByColumnAndRow(0, $row)->getValue();
			$qty = $sheet->getCellByColumnAndRow(1, $row)->getValue();
			$qty = floatval($qty);
			$fileContent[$part_no] = isset($fileContent[$part_no]) ? $fileContent[$part_no] + $qty : $qty;
		}

		$partNos = array_keys($fileContent);


		$dbParts = Part::find()->where(['part_no'=>$partNos])->all();

		$resultList = [];
		foreach($fileContent as $partNo => $qty)
		{
			foreach($dbParts as $record)
			{
				if(strcasecmp($partNo,$record->part_no) == 0){
					$resultList[$record->id]=$qty;
					break;
				}
			}
		}
		return $resultList;
	}
}
