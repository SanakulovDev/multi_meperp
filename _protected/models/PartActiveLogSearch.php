<?php
namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * PartActiveLogSearch represents the model behind the search form of `app\models\PartActiveLog`.
 */
class PartActiveLogSearch extends PartActiveLog {

  /**
   * {@inheritdoc}
   */
  public function rules() {
    return [
      [['id', 'status'], 'integer'],
      [['part_no', 'begin_date', 'end_date','status'], 'safe'],
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
  public function search($params) {
    $query = PartActiveLog::find();
    // add conditions that should always apply here
    $dataProvider = new ActiveDataProvider([
      'query' => $query,
    ]);
    $this->load($params);
    if(!$this->validate()) {
      // uncomment the following line if you do not want to return any records when validation fails
      // $query->where('0=1');
      return $dataProvider;
    }
    // grid filtering conditions
    $query->andFilterWhere([
      'id' => $this->id,
      'begin_date' => $this->begin_date,
      'end_date' => $this->end_date,
      'status' => $this->status,
    ]);
    $query->andFilterWhere(['like', 'part_no', $this->part_no]);
    $query->orderBy([
      'part_no' => SORT_ASC,
      'begin_date' => SORT_DESC,
    ]);

    return $dataProvider;
  }

}
