<?php
namespace app\models;

use PHPExcel_Style_Alignment;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ContainerInvoiceSearch represents the model behind the search form of `app\models\ContainerInvoice`.
 */
class ContainerInvoiceSearch extends ContainerInvoice {

  /**
   * @inheritdoc
   */
  public $ids;

  public function rules() {
    return [
      [['shipped_by', 'arrived_by'], 'integer'],
      [['container_id', 'container_type', 'invoice_id', 'ship_mode_id', 'shipped_at', 'app_arr_at', 'document_id', 'need_at', 'current_locate', 'current_at', 'arrived_at', 'received_at', 'ids', 'net_weight', 'gross_weight', 'cbm', 'station_date', 'cargo_type'], 'safe'],
    ];
  }

  /**
   * @inheritdoc
   */
  public function scenarios() {
    // bypass scenarios() implementation in the parent class
    return Model::scenarios();
  }

  /**
   * Creates data provider instance with search query applied   *
   *
   * @param array $params *
   *
   * @return ActiveDataProvider
   */
  public function search($params, $mode = '') {
    $query = ContainerInvoice::find();
    // add conditions that should always apply here
    $dataProvider = new ActiveDataProvider(['query' => $query]);
//			echo "<pre>1:"; print_r($params);echo "</pre>";
    $this->load($params);
    if(!$this->validate()) {
      // uncomment the following line if you do not want to return any records when validation fails
      // $query->where('0=1');
      return $dataProvider;
    }
    $query->joinWith('invoice');
    $query->joinWith('container');
    $query->joinWith('document');
    $query->joinWith('shipMode');
    $query->joinWith('deliveryTerm');
    // grid filtering conditions
    $query->andFilterWhere(
      [
        'app_arr_at' => $this->app_arr_at,
        'shipped_at' => $this->shipped_at,
        'need_at' => $this->need_at,
        'current_locate' => $this->current_locate,
        'current_at' => $this->current_at,
        'ship_mode_id' => $this->ship_mode_id,
        'shipped_by' => $this->shipped_by,
        'arrived_at' => $this->arrived_at,
        'arrived_by' => $this->arrived_by,
        'container_invoice.id' => $this->ids,
      ]
    );
    $query->andFilterWhere(['like', 'invoice.invoice_no', $this->invoice_id])
          ->andFilterWhere(['like', 'container.container_no', $this->container_id])
          ->andFilterWhere(['like', 'container.container_type', $this->container_type])
          ->andFilterWhere(['like', 'document.docnum', $this->document_id]);
    if($mode == 'excel') {
      $query->joinWith('parts');
      $queryContInvoice = $query->select(
        [
          'id' => 'container_invoice.id',
          'invoice_id' => 'invoice.id',
          'container_id' => 'container.id',
          'document_id' => 'document.id',
          'ship_mode_id' => 'ship_mode.id',
          'delivery_term_id' => 'delivery_term.id',
          'inv_no' => 'invoice.invoice_no',
          'cont_no' => 'container.container_no',
          'container_type' => 'container.container_type',
          'shipped_at' => 'container_invoice.shipped_at',
          'app_arr_at' => 'container_invoice.app_arr_at',
          'need_at' => 'container_invoice.need_at',
          'current_locate' => 'container_invoice.current_locate',
          'current_at' => 'container_invoice.current_at',
          'arrived_at' => 'container_invoice.arrived_at',
          'received_at' => 'container_invoice.received_at',
          'truck_type' => 'ship_mode.description',
          'part_no' => 'part.part_no',
          'part_nm' => 'part.part_name',
          'part_color' => 'part.part_color',
          'qty' => 'invoice_detail.qty',
          'remark' => 'invoice_detail.remarks',
          'term_nm' => 'delivery_term.name'
        ]
      );
      $contInvoice = $queryContInvoice->all();
//      echo "<pre>";print_r($queryContInvoice->createCommand()->rawSql);echo "</pre>";die;
      $arrFile = [];
      foreach($contInvoice as $invoice_list) {
        foreach($invoice_list->invoiceDetails as $detail) {
          unset($tmpArray);
          $tmpArray['inv_no'] = $invoice_list->invoice->invoice_no;
          $tmpArray['cont_no'] = $invoice_list->container->container_no;
          $tmpArray['container_type'] = $invoice_list->container->container_type;
          $tmpArray['order_no'] = $detail->partOrder ? $detail->partOrder->order_no : '';
          $tmpArray['shiped'] = $invoice_list->shipped_at;
          $tmpArray['app_arrived'] = $invoice_list->app_arr_at;
          $tmpArray['need_dt'] = $invoice_list->need_at;
          $tmpArray['cur_locale'] = $invoice_list->current_locate;
          $tmpArray['cur_dt'] = $invoice_list->current_at;
          $tmpArray['arrived'] = $invoice_list->arrived_at;
          $tmpArray['received'] = $invoice_list->received_at;
          $tmpArray['truck_type'] = $invoice_list->shipMode ? $invoice_list->shipMode->description : '';
          $tmpArray['part_no'] = $detail->part->part_no;
          $tmpArray['part_nm'] = $detail->part->part_name;
          $tmpArray['part_color'] = $detail->part->part_color;
          $tmpArray['qty'] = $detail->qty;
          $tmpArray['remark'] = $detail->remarks;
          $tmpArray['term_nm'] = $invoice_list->deliveryTerm ? $invoice_list->deliveryTerm->name : '';
          $arrFile[] = $tmpArray;
        }
      }
      $file = Yii::createObject(
        [
          'class' => 'codemix\excelexport\ExcelFile',
          'writerClass' => '\PHPExcel_Writer_Excel2007', // '\PHPExcel_Writer_Excel5'
          'sheets' => [
            'ContInv' => [
              'data' => $arrFile,
              'styles' => [
                'A1:Z1' => [
                  'font' => [
                    'bold' => true,
                  ],
                  'alignment' => [
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                  ],
                ],
              ],
              'titles' => [
                0 => Yii::t('app', 'Invoice no'),
                1 => Yii::t('app', 'Container no'),
                2 => Yii::t('app', 'Container type'),
                3 => Yii::t('app', 'Part orders'),
                4 => Yii::t('app', 'Shipped at'),
                5 => Yii::t('app', 'Approximate arrival date'),
                6 => Yii::t('app', 'Need date'),
                7 => Yii::t('app', 'Current location'),
                8 => Yii::t('app', 'Current date'),
                9 => Yii::t('app', 'Arrived at'),
                10 => Yii::t('app', 'Received at'),
                11 => Yii::t('app', 'Ship mode'),
                13 => Yii::t('app', 'Part No'),
                14 => Yii::t('app', 'Part name'),
                12 => Yii::t('app', 'Part color'),
                15 => Yii::t('app', 'Qty'),
                16 => Yii::t('app', 'Remarks'),
                17 => Yii::t('app', 'Delivery term'),
              ],
            ]
          ]
        ]
      );
      return $file;
    } else {
      $dataProvider->sort->defaultOrder = ['shipped_at' => SORT_DESC, 'id' => SORT_DESC];
      $dataProvider->pagination->pageSize = 20;
      return $dataProvider;
    }
  }

}
