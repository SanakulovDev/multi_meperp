<?php
namespace app\models;

use app\components\Helpers;
use DateInterval;
use DatePeriod;
use DateTime;
use PHPExcel_Cell;
use PHPExcel_Shared_Date;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ProductionOrderSearch represents the model behind the search form of `app\models\ProductionOrder`.
 */
class ProductionOrderSearch extends ProductionOrder {

  /**
   * {@inheritdoc}
   */
  public $ids;

  public function rules() {
    return [
      [['id', 'part_id', 'current_seq', 'is_printed', 'is_label', 'quantity', 'created_by', 'line', 'updated_by', 'created_at', 'updated_at'], 'integer'],
      [['is_bulk', 'current_event', 'serial_number', 'filter_from', 'filter_to', 'ids'], 'safe'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function scenarios() {
    return Model::scenarios();
  }

  /**
   * Creates data provider instance with search query applied
   *
   * @param array $params
   *
   * @return ActiveDataProvider
   */
  public function search($params, $mode = '') {
    $query = ProductionOrder::find();
    $query->joinWith('part');
    $query->joinWith('createdBy');
    // add conditions that should always apply here
    $dataProvider = new ActiveDataProvider(['query' => $query,]);
    $this->load($params);
    if(!$this->validate()) {
      return $dataProvider;
    }
    $thisMonth = date('Y-m', time());
    $to = date('t');
    $filter_from = (!empty($this->filter_from)) ? $this->filter_from.':00' : $thisMonth.'-01 00:00:00';
    $filter_to = (!empty($this->filter_to)) ? $this->filter_to.':00' : $thisMonth.'-'.$to.' 23:59:59';
    $query->andFilterWhere(['between', 'from_unixtime(production_order.created_at)', $filter_from, $filter_to]);
    if(!empty($this->ids)) {
      $query->andFilterWhere(['production_order.id' => $this->ids]);
    }
    // grid filtering conditions
    $query->andFilterWhere([
      'production_order.id' => $this->id,
      'part_id' => $this->part_id,
      'serial_number' => $this->serial_number,
      'is_printed' => $this->is_printed,
      'part.is_bulk' => $this->is_bulk,
      'is_label' => $this->is_label,
      'quantity' => $this->quantity,
      'production_order.created_by' => $this->created_by,
      'production_order.created_at' => $this->created_at,
      'line' => $this->line,
    ]);
    $query->andFilterWhere(['>=', 'current_seq', $this->current_seq]);
    $query->andFilterWhere(['like', 'current_event', $this->current_event]);
    $dataProvider->sort->attributes['part_id'] = [
      'asc' => ['part.part_no' => SORT_ASC],
      'desc' => ['part.part_no' => SORT_DESC],
    ];
    $dataProvider->sort->attributes['created_by'] = [
      'asc' => ['user.fullname' => SORT_ASC],
      'desc' => ['user.fullname' => SORT_DESC],
    ];
    if($mode == 'xlsx') {
      $arrFile = $uProdOrdrs = $uProdOrdr = $arr = $tmpArr = [];
      if(!Yii::$app->user->can('admin')) {
        if(Yii::$app->user->can('counter')) {
          $query->andWhere(['part.warehouse_id' => Yii::$app->user->identity->warehouseIds]);
        }
      }
      $fromDt = substr($filter_from, 0, 10);
      $toDt = substr($filter_to, 0, 10);
      $whId = ltrim(Helpers::arrayToStringRecursive(Yii::$app->user->identity->warehouseIds, ','), ',');
      $poWH = "WHERE part.warehouse_id IN (".$whId.")";
      $poWhere = "from_unixtime(created_at, '%Y-%m-%d') between '".$fromDt."' and '".$toDt."' ";
      $poWhere = ($this->part_id) ? $poWhere." and part_id=".$this->part_id : $poWhere;
      $poWhere = ($this->serial_number) ? $poWhere." and serial_number='".$this->serial_number."'" : $poWhere;
      $poWhere = ($this->is_printed) ? $poWhere." and is_printed=".$this->is_printed : $poWhere;
      $poWhere = ($this->is_label) ? $poWhere." and is_label=".$this->is_label : $poWhere;
      $poWhere = ($this->quantity) ? $poWhere." and quantity=".$this->quantity : $poWhere;
      $shift0000 = Yii::$app->params['shifts']['2']['1']['0'];
      $shift0759 = Yii::$app->params['shifts']['2']['1']['1'];
      $shift0800 = Yii::$app->params['shifts']['1']['0'];
      $shift2000 = Yii::$app->params['shifts']['1']['1'];
      $queryXlsx = "
      SELECT name,part_no,part_name,state,prod_dt,shift,prod_qty FROM 
      (
        SELECT po.part_id, prod_dt, po.shift, SUM(shift1_qty) prod_qty FROM 
        ( 
          SELECT part_id
               , CASE
                  WHEN (FROM_UNIXTIME(created_at, '%Y-%m-%d %H:%i') BETWEEN (CONCAT(FROM_UNIXTIME(created_at, '%Y-%m-%d'), ' ', '".$shift0000."')) 
                      AND(CONCAT(FROM_UNIXTIME(created_at, '%Y-%m-%d'), ' ', '".$shift0759."'))) 
                  THEN FROM_UNIXTIME((created_at - 86400), '%Y-%m-%d')
                  ELSE FROM_UNIXTIME(created_at, '%Y-%m-%d')
             END AS prod_dt
           , CASE
                  WHEN (FROM_UNIXTIME(created_at, '%Y-%m-%d %H:%i') BETWEEN (CONCAT(FROM_UNIXTIME(created_at, '%Y-%m-%d'), ' ', '".$shift0800."')) 
                      AND(CONCAT(FROM_UNIXTIME(created_at, '%Y-%m-%d'), ' ', '".$shift2000."'))) 
                  THEN 1
                  ELSE 2
             END AS shift
               , IFNULL(quantity, 0) shift1_qty     
           FROM production_order
           where ".$poWhere."
           ORDER BY part_id, created_at 
        ) po
        GROUP BY part_id, prod_dt, shift
        ORDER BY part_id, prod_dt, shift
      ) production_order
      LEFT JOIN part ON production_order.part_id = part.id
      LEFT JOIN warehouse ON part.warehouse_id = warehouse.id      
      ".$poWH." 
      ORDER BY name,part_no,prod_dt,shift";
//      echo "<pre>"; print_r($queryXlsx);echo "</pre>"; die;
      $prodOrders = Yii::$app->db->createCommand($queryXlsx)->queryAll();
      $begin = new DateTime($fromDt);
      $end = new DateTime($toDt);
      $end = $end->modify('+1 day');
      $beginSana = $begin->format('Y-m-d');
      $daterange = new DatePeriod($begin, new DateInterval('P1D'), $end);
      $shift = [];
      $shc = 3;
      foreach($daterange as $date) {
        $shift[++$shc] = 1;
        $shift[++$shc] = 2;
      }
      if(!empty($prodOrders)) {
        foreach($prodOrders as $prodOrder) {
          $uProdOrdrs[] = $prodOrder['name']."☺".$prodOrder['part_no']."☺".$prodOrder['state'];
        }
      }
      $uProdOrdr = array_unique($uProdOrdrs);
    //  echo "<pre>"; print_r($prodOrders);echo "</pre>"; die;
      foreach($uProdOrdr as $uProdItem) {
        [$line, $partNo, $partState] = explode("☺", $uProdItem);
        $tmpArr = [];
        foreach($prodOrders as $arrItem) {
          if(
            $line == $arrItem['name'] &&
            $partNo == $arrItem['part_no'] &&
            $partState == $arrItem['state']
          ) {
            
            $tmpArr['line'] = $arrItem['name'];
            $tmpArr['partNo'] = $arrItem['part_no'];
            $tmpArr['partName'] = $arrItem['part_name'];
            $tmpArr['partState'] = $arrItem['state'];
            $tmpArr[$arrItem['prod_dt'].'-'.$arrItem['shift']] = $arrItem['prod_qty'];
            $arrFile = $tmpArr;
          }
        }
        $arr[] = $arrFile;
      }
      $arrFile = [];
      //  echo "<pre>"; print_r($arr);echo "</pre>";    die;
      $part = new Part();
      foreach($arr as $item) {
        $tmpArr = [];
        $tmpArr['line'] = $item['line'];
        $tmpArr['partNo'] = $item['partNo'];
        $tmpArr['partName'] = $item['partName'];
        $tmpArr['partState'] = $part->stateList[$item['partState']];
        foreach($daterange as $date) {
          $prodSana = $date->format('Y-m-d');
          $tmpArr[$prodSana.'-1'] = (isset($item[$prodSana.'-1'])) ? $item[$prodSana.'-1'] : 0;
          $tmpArr[$prodSana.'-2'] = (isset($item[$prodSana.'-2'])) ? $item[$prodSana.'-2'] : 0;
        }
        $arrFile[] = $tmpArr;
      }
    //  echo "<pre>"; print_r($arrFile);echo "</pre>";    die;
      $titles = $shift;
      //  echo "<pre>"; print_r($titles);echo "</pre>";    die;
      $file = Yii::createObject([
        'class' => 'codemix\excelexport\ExcelFile',
        'sheets' => [
          'P.Order' => [
            'startRow' => 3,
            'data' => $arrFile,
            'titles' => $titles,
            'on afterRender' => function($event) {
              $data = $event->sender->getData();
              if(isset($data) && count($data)) {
                $beginSana = array_keys($data[0]);
                $begin = $beginSana[5];
              } else {
                $begin = date('Y-m-', time())."01";
              }
              $fillHeader = [
                'fill' => [
                  'type' => PHPExcel_Style_Fill::FILL_SOLID,
                  'color' => ['rgb' => 'F3F3F3'],
                ],
              ];
              $dateTitle = [
                'font' => [
                  'bold' => false,
                  'size' => 8,
                  'name' => 'Calibri Light'
                ],
                'alignment' => [
                  'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                  'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                ]
              ];
              $shiftTitle = [
                'font' => [
                  'bold' => true,
                  'size' => 10,
                  'name' => 'Calibri Light'
                ],
                'alignment' => [
                  'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                  'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ]
              ];
              $boldFontCenter = [
                'font' => [
                  'bold' => true,
                  'size' => 12,
                  'name' => 'Calibri Light'
                ],
                'alignment' => [
                  'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                  'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                ]
              ];
              $styleFont10 = [
                'font' => [
                  'size' => 10,
                  'name' => 'Calibri Light'
                ],
              ];
              $styleFont12 = [
                'font' => [
                  'size' => 12,
                  'name' => 'Calibri Light'
                ],
              ];
              $styleThinBlackBorderOutline = [
                'borders' => [
                  'allborders' => [
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                  ],
                ],
              ];
              $sheet = $event->sender->getSheet();
              $highestColumn = $sheet->getHighestDataColumn();
              $highestRow = $sheet->getHighestDataRow();
              function getColRange($start_letter, $hCol, $row_number, $count) {
                $alphabets = range('A', 'Z');
                $start_idx = array_search(
                  $start_letter,
                  $alphabets
                );
                return sprintf(
                  "%s%s:%s%s",
                  $start_letter,
                  $row_number,
                  $alphabets[$start_idx + $count],
                  $row_number
                );
              }

              $highestColIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
              $sheet->setCellValue('A1', Yii::t('app', 'Line'))
                    ->setCellValue('B1', Yii::t('app', 'Part No'))
                    ->setCellValue('C1', Yii::t('app', 'Part name'))
                    ->setCellValue('D1', Yii::t('app', 'State'));
              $sheet->mergeCells('A1:A3')
                    ->mergeCells('B1:B3')
                    ->mergeCells('C1:C3')
                    ->mergeCells('D1:D3')
                    // ->mergeCells('E1:E3')
                    ->mergeCells('E1:'.($highestColumn.'1'));
              $sheet->setCellValue('E1', Yii::t('app', 'Production order'));
              $highestColIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
              $dd = 0;
              for($i = 4; $i < $highestColIndex; $i = $i + 2) {
                $fr_colstr = PHPExcel_Cell::stringFromColumnIndex($i);
                $to_colstr = PHPExcel_Cell::stringFromColumnIndex($i + 1);
                $sheet->mergeCells(($fr_colstr.'2:').(($to_colstr).'2'));
                $sheet->setCellValueByColumnAndRow(
                  $i, 2,
                  PHPExcel_Shared_Date::PHPToExcel(date('d.m.Y', strtotime(date('Y-m-d', strtotime($begin))." +".$dd." days")))
                );
                $sheet->getStyleByColumnAndRow($i, 2)->getNumberFormat()->setFormatCode('dd.mm.yyyy');
                $dd++;
              }
              $sheet->getStyle('A1:'.($highestColumn.'3'))
                    ->applyFromArray(array_merge($styleThinBlackBorderOutline, $styleFont10, $boldFontCenter));
              $sheet->getStyle('E2:'.($highestColumn.'2'))->applyFromArray($dateTitle);
              $sheet->getStyle('E3:'.($highestColumn.'3'))->applyFromArray($shiftTitle);
              $sheet->getStyle('A4:'.($highestColumn.$highestRow))->applyFromArray($styleFont12);
              $sheet->getStyle('A1:'.($highestColumn.'3'))->applyFromArray($fillHeader);
              $sheet->freezePane('E4');
              foreach(range('A', $highestColumn) as $columnID) {
                $sheet->getColumnDimension($columnID)->setAutoSize(true);
              }
            },
          ]
        ]
      ]);
      return $file;
    }
    if($mode == 'excel') {
      if(!Yii::$app->user->can('admin')) {
        if(Yii::$app->user->can('counter')) {
          $query->andWhere(['part.warehouse_id' => Yii::$app->user->identity->warehouseIds]);
        }
      }
      // $query->andWhere(['is_label' => 0]);
      $dataProvider->sort->defaultOrder = ['id' => SORT_DESC];
      $file = Yii::createObject(
        [
          'class' => 'codemix\excelexport\ExcelFile',
          'sheets' => [
            'ProductionOrder' => [
              'class' => 'codemix\excelexport\ActiveExcelSheet',
              'query' => $query,
              'attributes' => [
                'id',
                'isLabelText',
                'part.stateText',
                'part.part_no',
                'part.part_color',
                'current_seq',
                'quantity',
                'createdBy.fullname',
                'createdAtFormatted',
                'updatedBy.fullname',
                'updatedAtFormatted'
              ],
              'titles' => [
                1 => Yii::t('app', 'Label type'),
                2 => Yii::t('app', 'State'),
                3 => Yii::t('app', 'Product'),
                4 => Yii::t('app', 'Part color'),
                7 => Yii::t('app', 'Created by'),
                8 => Yii::t('app', 'Created at'),
                9 => Yii::t('app', 'Updated by'),
                10 => Yii::t('app', 'Updated at'),
              ],
            ],
          ]
        ]);
      return $file;
    } else {
      return $dataProvider;
    }
  }

}
