<?php
namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * StockSearch represents the model behind the search form of `app\models\Stock`.
 */
class StockInfoWrapperSearch extends StockInfoWrapper {

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
      return [
          [['warehouse_id', 'type_id', 'give_user_id', 'document_id', 'part_id', 'qty'], 'integer'],
          [['code', 'comment', 'date'], 'string', 'max' => 255],
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
    $query = StockInfoWrapper::find();
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
    // $query->joinWith(['warehouse', 'part', 'user']);
    //    $query->joinWith('userWarehouse');
    // grid filtering conditions
    $query->andFilterWhere([
                             'code' => $this->code,
                             'warehouse_id' => $this->warehouse_id,
                             'date' => $this->date,
                             'part_id' => $this->part_id,
                             'qty' => $this->qty,
                           ])->orderBy(['id' => SORT_DESC])->all();
    if (isset($this->qty)) {
      if ($this->qty == '0') {
        $query->andFilterWhere(['>', 'qty', '0']);
      }
    }

    
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
