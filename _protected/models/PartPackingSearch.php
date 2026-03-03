<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * PartPackingSearch represents the model behind the search form of `app\models\PartPacking`.
 */
class PartPackingSearch extends PartPacking
{
  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['id', 'part_id', 'supplier_id', 'returnable', 'pack_id', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
      [['pack_qty', 'piece_weight'], 'number'],
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
   * @param array $params
   * @return ActiveDataProvider
   */
  public function search($params, $mode = '')
  {
    $query = PartPacking::find()->joinWith(['part', 'supplier', 'pack', 'createdBy',

      'updatedBy' => function ($query) {
        $query->from(['u2' => User::tableName()]);
      }]);
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
      'part_id' => $this->part_id,
      'supplier_id' => $this->supplier_id,
      'returnable' => $this->returnable,
      'pack_qty' => $this->pack_qty,
      'piece_weight' => $this->piece_weight,
      'pack_id' => $this->pack_id,
      'created_by' => $this->created_by,
      'created_at' => $this->created_at,
      'updated_by' => $this->updated_by,
      'updated_at' => $this->updated_at,
    ]);

    if ($mode == 'excel') {
      $query->orderBy([
        'part_id' => SORT_ASC
      ]);
      $file = Yii::createObject([
        'class' => 'codemix\excelexport\ExcelFile',
        'sheets' => [
          'PartPacking' => [
            'class' => 'codemix\excelexport\ActiveExcelSheet',
            'query' => $query,
            'attributes' => [
              'id',
              'part.partinfo',
              'part.part_name',
              'supplier.name',
              'supplier.duns',
              'returnableFormatted',
              'pack.code',
              'pack_qty',
              'piece_weight',
              'netto',
              'brutto',
              'createdBy.fullname',
              'createdAtFormatted',
              'updatedBy.fullname',
              'updatedAtFormatted',
            ],
            'titles' => [
              1 => Yii::t('app', 'Part No'),
              2 => Yii::t('app', 'Part name'),
              3 => Yii::t('app', 'Supplier'),
              4 => Yii::t('app', 'Duns'),
              5 => Yii::t('app', 'Returnable'),
              6 => Yii::t('app', 'Packaging code'),
              7 => Yii::t('app', 'Loop size'),
              8 => Yii::t('app', 'Weight (kg)'),
              9 => Yii::t('app', 'Net weight (kg)'),
              10 => Yii::t('app', 'Gross weight (kg)'),

              11 => Yii::t('app', 'Created by'),
              12 => Yii::t('app', 'Created at'),
              13 => Yii::t('app', 'Updated by'),
              14 => Yii::t('app', 'Updated at')
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
