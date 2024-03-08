<?php
namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * StockSearch represents the model behind the search form of `app\models\Stock`.
 */
class StockSearch extends Stock {

  /**
   * {@inheritdoc}
   */
  public function rules() {
    return [
      [['id', 'created_at', 'updated_at'], 'integer'],
      [['qty'], 'number'],
      [['warehouse_id', 'part_id', 'part_name', 'part_color', 'part_status', 'unit', 'model', 'part_state'], 'safe'],
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
    $query = Stock::find()->andWhere(['not in', 'stock.warehouse_id', [12]]);
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
    $query->joinWith('part.unit');
    $query->joinWith('warehouse');
    //    $query->joinWith('userWarehouse');
    // grid filtering conditions
    $query->andFilterWhere([
                             'id' => $this->id,
                             'stock.warehouse_id' => $this->warehouse_id,
                             'part.state' => $this->part_state,
                             'created_at' => $this->created_at,
                             'updated_at' => $this->updated_at,
                             'unit.id' => $this->unit,
                             'part.status' => $this->part_status,
                             'part.id' => $this->part_id
                           ])->orderBy(['id' => SORT_DESC])->all();
    if (isset($this->qty)) {
      if ($this->qty == '0') {
        $query->andFilterWhere(['>', 'qty', '0']);
      }
    }

    $query->andFilterWhere(['like', 'part.part_name', $this->part_name])
      ->andFilterWhere(['like', 'part.part_color', $this->part_color]);
    $dataProvider->sort->attributes['part_state'] = [
      'asc' => ['part.state' => SORT_ASC],
      'desc' => ['part.state' => SORT_DESC],
    ];
    $dataProvider->sort->attributes['part_name'] = [
      'asc' => ['part.part_name' => SORT_ASC],
      'desc' => ['part.part_name' => SORT_DESC],
    ];
    $dataProvider->sort->attributes['part_color'] = [
      'asc' => ['part.part_color' => SORT_ASC],
      'desc' => ['part.part_color' => SORT_DESC],
    ];
    $dataProvider->sort->attributes['unit'] = [
      'asc' => ['unit.unit_value' => SORT_ASC],
      'desc' => ['unit.unit_value' => SORT_DESC],
    ];
    $dataProvider->sort->attributes['part_status'] = [
      'asc' => ['part.status' => SORT_ASC],
      'desc' => ['part.status' => SORT_DESC],
    ];
    if ($mode == 'excel') {
      if (!Yii::$app->user->can('admin')) {
        if (Yii::$app->user->can('mrp')) {
          $query->andWhere(['stock.warehouse_id' => Yii::$app->user->identity->warehouseIds]);
        }
      }
      $file = Yii::createObject([
                                  'class' => 'codemix\excelexport\ExcelFile',
                                  'sheets' => [
                                    'Stock' => [
                                      'class' => 'codemix\excelexport\ActiveExcelSheet',
                                      'query' => $query,
                                      'attributes' => [
                                        'id',
                                        'warehouse.name',
                                        'part.part_no',
                                        'part.part_name',
                                        'part.part_color',
                                        'part.stateText',
                                        'part.unit.unit_value',
                                        'part.partType.typename',
                                        'part.status',
                                        'qty',
                                        'createdAtFormatted',
                                        'updatedAtFormatted'
                                      ],
                                      'titles' => [
                                        1 => Yii::t('app', 'Warehouse'),
                                        5 => Yii::t('app', 'State'),
                                        6 => Yii::t('app', 'Unit'),
                                        7 => Yii::t('app', 'Part type'),
                                        10 => Yii::t('app', 'Created at'),
                                        11 => Yii::t('app', 'Updated at'),
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
