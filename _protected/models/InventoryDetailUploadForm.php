<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * UploadForm is the model behind the upload form.
 */
class InventoryDetailUploadForm extends Model {

    /**
     * @var UploadedFile file attribute
     */
    public $api_id, $file;

    /**
     * @return array the validation rules.
     */
    public function rules() {
        return [
            [['api_id', 'file'], 'required'],
            [['file'], 'file', 'skipOnEmpty' => false, 'extensions' => 'xls, xlsx'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'api_id' => Yii::t('app', 'Inventory ID'),
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
                    $rowData['inventory_qty'] = trim($sheet->getCell('B' . $row)->getValue()); 
                    $rowData['stock_qty'] = trim($sheet->getCell('C' . $row)->getValue()); 
                    $data['values'][] = $rowData;
            }
        }
        //send the data in an array format
        return $data;
    }
    
    public function getApi()
    {
        return Api::findOne($this->api_id);
    }

}
