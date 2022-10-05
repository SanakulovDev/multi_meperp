<?php
namespace app\models;

use PHPExcel_Cell;
use PHPExcel_IOFactory;
use PHPExcel_Style_NumberFormat;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * UploadForm is the model behind the upload form.
 */
class UploadForm extends Model {

  /**
   * @var UploadedFile file attribute
   */
  public $xls_file;

  /**
   * @return array the validation rules.
   */
  public function rules() {
    return [
      [
        ['xls_file'],
        'file',
        'skipOnEmpty' => false,
        'checkExtensionByMimeType' => false,
        'extensions' => 'xls, xlsx'
      ],
      //			[['xls_file'], 'required'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels() {
    return [
      'xls_file' => Yii::t('app', 'Excel file'),
    ];
  }

  public function read_excel($file_path_name) {
    //read file from path
    $objReader = PHPExcel_IOFactory::createReader(PHPExcel_IOFactory::identify($file_path_name));
    //load the excel library
    $objPHPExcel = $objReader->load($file_path_name);
    //  Get worksheet dimensions
    $sheet = $objPHPExcel->getActiveSheet();
    $highestRow = $sheet->getHighestRow();
    $highestColumn = $sheet->getHighestDataColumn();
    $highestColIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
    $data['filename'] = $file_path_name;
    $data['highestColumn'] = $highestColumn;
    $data['highestColIndex'] = $highestColIndex;
    $data['highestRow'] = $highestRow;
    for ($row = 1; $row <= $highestRow; $row++) {
      if ($row == 2) {
        $data['header_need_dt'] = $sheet->getCell('E'.$row)->getValue();
        $data['header_need_dt'] = PHPExcel_Style_NumberFormat::toFormattedString($sheet->getCell('E'.$row)->getValue(), 'YYYY-MM-DD');
      }
      if ($row >= 4) {
        $data['values'][] = $objPHPExcel
          ->getActiveSheet()
          ->rangeToArray('B'.$row.':'.$highestColumn.$row,
                         null,
                         TRUE,
                         FALSE
          );
      }
    }

    //send the data in an array format
    return $data;
  }

}
