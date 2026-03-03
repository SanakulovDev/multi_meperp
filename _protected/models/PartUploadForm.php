<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * UploadForm is the model behind the upload form.
 */
class PartUploadForm extends Model {

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
                    $rowData['pack_size'] = trim($sheet->getCell('B' . $row)->getValue());
                    $rowData['part_name'] = trim($sheet->getCell('C' . $row)->getValue());
                    $rowData['part_color'] = trim($sheet->getCell('D' . $row)->getValue());
                    $rowData['unit'] = trim($sheet->getCell('E' . $row)->getValue());
                    $rowData['part_type'] = trim($sheet->getCell('F' . $row)->getValue());
                    $rowData['state'] = trim($sheet->getCell('G' . $row)->getValue());
                    $rowData['contract_source'] = trim($sheet->getCell('H' . $row)->getValue());
                    $rowData['remark'] = trim($sheet->getCell('I' . $row)->getValue());
                    $rowData['floc'] = trim($sheet->getCell('J' . $row)->getValue());
                    $rowData['fg_warehouse'] = trim($sheet->getCell('K' . $row)->getValue());
                    
                    $data['values'][] = $rowData;
            }
        }
        //send the data in an array format
        return $data;
    }

}
