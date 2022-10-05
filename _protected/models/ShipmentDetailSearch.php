<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ShipmentDetail;
use Yii;

/**
 * ShipmentDetailSearch represents the model behind the search form of `app\models\ShipmentDetail`.
 */
class ShipmentDetailSearch extends ShipmentDetail
{
  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['id', 'shipment_id', 'part_id', 'supplier_id'], 'integer'],
      [['pack_size', 'coverage_qty', 'need_qty', 'ready_qty', 'approved_qty'], 'number'],
      [['comment', 'disruption_date', 'part_name', 'unit', 'diff_ready_need', 'diff_appr_ready'], 'safe'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function scenarios()
  {
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
  public function search($params, $mode = '')
  {

    $query = ShipmentDetail::find();

    $query->joinWith(['shipment', 'supplier', 'part.unit']);

    // add conditions that should always apply here

    $dataProvider = new ActiveDataProvider([
      'query' => $query,
    ]);

    $this->load($params);

    if (!$this->validate()) {
      // uncomment the following line if you do not want to return any records when validation fails
      // $query->where('0=1');
      return $dataProvider;
    }

    // grid filtering conditions
    $query->andFilterWhere([
      'id' => $this->id,
      'shipment_id' => $this->shipment_id,
      'part_id' => $this->part_id,
      'supplier_id' => $this->supplier_id,
      'shipment_detail.pack_size' => $this->pack_size,
      'disruption_date' => $this->disruption_date,
      'coverage_qty' => $this->coverage_qty,
      'need_qty' => $this->need_qty,
      'ready_qty' => $this->ready_qty,
      'approved_qty' => $this->approved_qty,
      'part.unit_id' => $this->unit,
      '(ready_qty - need_qty)' => $this->diff_ready_need,
      '(approved_qty - ready_qty)' => $this->diff_appr_ready,
    ]);

    $query->andFilterWhere(['like', 'shipment_detail.comment', $this->comment])
      ->andFilterWhere(['like', 'part.part_name', $this->part_name]);

    // echo '<pre>';
    // print_r($query->createCommand()->rawSql);
    // echo '</pre>';
    // die;

    if ($mode == 'excel') {

      $file = Yii::createObject([
        'class' => 'codemix\excelexport\ExcelFile',
        'sheets' => [
          'Stock' => [
            'class' => 'codemix\excelexport\ActiveExcelSheet',
            'query' => $query,
            'attributes' => [
              'id',
              'shipment.title',
              'shipment.days',
              'shipment.report_date',
              'shipment.created_at',
              'part.partinfo',
              'part.part_name',
              'supplier.name',
              'pack_size',
              'part.unit.unit_value',
              'disruption_date',
              'need_qty',
              'ready_qty',
              'diffReadyNeed',
              'approved_qty',
              'diffApprReady',
              'comment',
            ],
            'titles' => [
              1 => Yii::t('app', 'Calculation'),
              2 => Yii::t('app', 'Days'),
              3 => Yii::t('app', 'Report date'),
              4 => Yii::t('app', 'Created at'),

              5 => Yii::t('app', 'Part'),
              6 => Yii::t('app', 'Part name'),
              7 => Yii::t('app', 'Supplier'),
              9 => Yii::t('app', 'Unit'),
              13 => Yii::t('app', 'Difference'),
              15 => Yii::t('app', 'Difference'),
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
