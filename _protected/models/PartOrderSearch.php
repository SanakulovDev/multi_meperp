<?php
namespace app\models;

use PHPExcel_Style_Alignment;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * PartOrderSearch represents the model behind the search form of `app\models\PartOrder`.
 */
class PartOrderSearch extends PartOrder {

  /**
   * {@inheritdoc}
   */
  public function rules() {
    return [
      [['id', 'contract_id', 'created_by', 'created_at', 'updated_by', 'updated_at', 'delivery_term_id'], 'integer'],
      [['order_no', 'order_type', 'iss_dt', 'mr_dt', 'for_month'], 'safe'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function scenarios() {
    // bypass scenarios() implementation in the parent class
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
    $query = PartOrder::find();
    $query->with(['contract','deliveryTerm','createdBy','updatedBy']);
    // add conditions that should always apply here
    $dataProvider = new ActiveDataProvider(['query' => $query,]);
    $this->load($params);
    if(!$this->validate()) {
      return $dataProvider;
    }
    // grid filtering conditions
    $query->andFilterWhere([
      'id' => $this->id,
      'order_type' => $this->order_type,
      'iss_dt' => $this->iss_dt,
      'mr_dt' => $this->mr_dt,
      'for_month' => $this->for_month,
      'contract_id' => $this->contract_id,
      'delivery_term_id' => $this->delivery_term_id,
      'created_by' => $this->created_by,
      'created_at' => $this->created_at,
      'updated_by' => $this->updated_by,
      'updated_at' => $this->updated_at,
    ]);
    $query->andFilterWhere(['like', 'order_no', $this->order_no]);
    $query->orderBy(['id' => SORT_DESC]);
    $dataProvider->pagination->pageSize = 20;
    if($mode == 'excel') {
      $orders = $query->with('partOrderDetails')->all();
      $arrFile = [];
      foreach($orders as $order) {
        foreach($order->partOrderDetails as $detail) {
          unset($tmpArray);
          $tmpArray['order_no'] = $detail->partOrder->order_no;
          $tmpArray['iss_dt'] = $detail->partOrder->iss_dt;
          $tmpArray['mr_dt'] = $detail->partOrder->mr_dt;
          $tmpArray['for_month'] = $detail->partOrder->for_month;
          $tmpArray['contract_no'] = $detail->partOrder->contract->contract_no;
          $tmpArray['order_amount'] = $detail->partOrder->amount;
          $tmpArray['order_type'] = $this->getOrderTypeTextById($detail->partOrder->order_type);
          $tmpArray['part_no'] = $detail->part->part_no;
          $tmpArray['part_nm'] = $detail->part->part_name;
          $tmpArray['part_color'] = $detail->part->part_color;
          $tmpArray['qty'] = $detail->qty;
          $tmpArray['price'] = $detail->price;
          $tmpArray['amount'] = $detail->amount;
          $tmpArray['shipped_amount'] = PartOrder::getInvoiceDetailPartAmount($order->id, $detail->part->id, $detail->partOrder->contract->id);
          $tmpArray['exwrk_plan'] = $detail->exwrk_plan;
          $tmpArray['exwrk_actual'] = $detail->exwrk_actual;
          $arrFile[] = $tmpArray;
        }
      }
      //if(empty($arrFile)) $query->orderBy(['id' => SORT_DESC]);
      $file = Yii::createObject(
        [
          'class' => 'codemix\excelexport\ExcelFile',
          'writerClass' => '\PHPExcel_Writer_Excel2007', // '\PHPExcel_Writer_Excel5'
          'sheets' => [
            'PartOrder' => [
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
                0 => Yii::t('app', 'Order no'),
                1 => Yii::t('app', 'Issued date'),
                2 => Yii::t('app', 'MRD date'),
                3 => Yii::t('app', 'For month'),
                4 => Yii::t('app', 'Contract no'),
                5 => Yii::t('app', 'Order amount'),
                6 => Yii::t('app', 'Order type'),
                7 => Yii::t('app', 'Part color'),
                8 => Yii::t('app', 'Part No'),
                9 => Yii::t('app', 'Part name'),
                10 => Yii::t('app', 'Qty'),
                11 => Yii::t('app', 'Price'),
                12 => Yii::t('app', 'Amount'),
                13 => Yii::t('app', 'Shipped amount'),
                14 => Yii::t('app', 'exwrk_plan'),
                15 => Yii::t('app', 'exwrk_actual'),
              ],
            ]
          ]
        ]
      );
      return $file;
    }
    return $dataProvider;
  }

}
