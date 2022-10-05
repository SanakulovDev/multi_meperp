<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * UploadForm is the model behind the upload form.
 */
class ContractDetailUploadForm extends Model {

    /**
     * @var UploadedFile file attribute
     */
    public $contract_id, $file;

    /**
     * @return array the validation rules.
     */
    public function rules() {
        return [
            [['contract_id', 'file'], 'required'],
            [['file'], 'file', 'skipOnEmpty' => false, 'extensions' => 'xls, xlsx'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'contract_id' => Yii::t('app', 'Contract'),
            'file' => Yii::t('app', 'File'),
        ];
    }

    public function readExcel($file_path_name) {
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
        $data['filename'] = $file_path;
        $data['highestColumn'] = $highestColumn;
        $data['highestColIndex'] = $highestColIndex;
        $data['highestRow'] = $highestRow;
        for ($row = 1; $row <= $highestRow; $row++) {
            if ($row >= 2) {
                    unset($rowData);
                    $rowData['part_no'] = trim($sheet->getCell('A' . $row)->getValue()); 
                    $rowData['delivery_term'] = trim($sheet->getCell('B' . $row)->getValue()); 
                    $rowData['price'] = trim($sheet->getCell('C' . $row)->getValue()); 
                    $rowData['cnfea'] = trim($sheet->getCell('D' . $row)->getValue()); 
                    $rowData['weekly_capacity'] = trim($sheet->getCell('E' . $row)->getValue()); 
                    $rowData['sub_source'] = trim($sheet->getCell('F' . $row)->getValue()); 
                    $rowData['lead_time'] = trim($sheet->getCell('G' . $row)->getValue());

                    $data['values'][] = $rowData;
            }
        }
        //send the data in an array format
        return $data;
    }

}
