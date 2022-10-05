<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * UploadForm is the model behind the upload form.
 */
class SalesContractDetailUploadForm extends Model {

    /**
     * @var UploadedFile file attribute
     */
    public $sales_contract_id, $file;

    /**
     * @return array the validation rules.
     */
    public function rules() {
        return [
            [['sales_contract_id', 'file'], 'required'],
            [['file'], 'file', 'skipOnEmpty' => false, 'extensions' => 'xls, xlsx'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'sales_contract_id' => Yii::t('app', 'Contract'),
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
                    $rowData['vat'] = trim($sheet->getCell('D' . $row)->getValue()); 
                    $rowData['excise'] = trim($sheet->getCell('E' . $row)->getValue()); 
                    $data['values'][] = $rowData;
            }
        }
        //send the data in an array format
        return $data;
    }

}
