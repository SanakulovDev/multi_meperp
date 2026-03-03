<?php
namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ReceptControl;

/**
 * ReceptControlSearch represents the model behind the search form of `app\models\ReceptControl`.
 */
class PostsSearch extends Posts {

  /**
   * {@inheritdoc}
   */
  public function rules() {
    return [
      [['date'], 'safe'],
      [['is_where', 'material', 'weight','comment'], 'string'],
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
    $query = Posts::find()->orderBy(['id'=>SORT_DESC]);
    $dataProvider = new ActiveDataProvider([
                                             'query' => $query,
                                           ]);
    $this->load($params);
    if (!$this->validate()) {
      return $dataProvider;
    }
    // grid filtering conditions
    $query->andFilterWhere([
                             'id' => $this->id,
                             'date' => $this->date,
                             'created_at' => $this->created_at,
                             'created_by' => $this->created_by,
                             'updated_by' => $this->updated_by,
                             'updated_at' => $this->updated_at,
                           ]);
    $query->andFilterWhere(['like', 'material', $this->material]);
    $query->andFilterWhere(['like', 'is_where', $this->is_where]);
    $query->andFilterWhere(['like', 'weight', $this->weight]);
    $query->andFilterWhere(['like', 'comment', $this->comment]);
    if ($mode == 'excel') {

      $file = \Yii::createObject([
                                   'class' => 'codemix\excelexport\ExcelFile',
                                   'sheets' => [
                                     'Receipt data' => [
                                       'class' => 'codemix\excelexport\ActiveExcelSheet',
                                       'query' => $query,
                                       'attributes' => [
                                         'id',
                                         'date',
                                         'material',
                                         'weight',
                                         'is_where',
                                         'comment',
                                         'created_at',
                                       ],
                                       'titles' => [
                                         3 => Yii::t('app', 'Payment term'),
                                         5 => Yii::t('app', 'Contract'),
                                         6 => Yii::t('app', 'Supplier'),
                                         8 => Yii::t('app', 'Created by'),
                                         9 => Yii::t('app', 'Created at'),
                                         10 => Yii::t('app', 'Updated by'),
                                         11 => Yii::t('app', 'Updated at')
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
