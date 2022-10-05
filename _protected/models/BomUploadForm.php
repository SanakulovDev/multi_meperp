<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * UploadForm is the model behind the upload form.
 */
class BomUploadForm extends Model {

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
                    $rowData['sub_part_no'] = trim($sheet->getCell('B' . $row)->getValue()); 
                    $rowData['usage_qty'] = trim($sheet->getCell('C' . $row)->getValue());  
                    $rowData['uloc'] = trim($sheet->getCell('D' . $row)->getValue());
                    $rowData['remark'] = trim($sheet->getCell('E' . $row)->getValue());
                    
                    $data['values'][] = $rowData;
            }
        }
        //send the data in an array format
        return $data;
    }

}
