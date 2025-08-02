<?php
namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * MachineSearch represents the model behind the search form of `app\models\Machine`.
 */
class MachineSearch extends Machine {

  /**
   * {@inheritdoc}
   */
  public function rules() {
    return [
      [['id', 'product_line_id', 'last_count', 'mold_id', 'sequence', 'status', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
      [['no', 'title', 'linename'], 'safe'],
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
    $query = Machine::find();
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
    $query->joinWith(['mold', 'productLine']);
    // grid filtering conditions
    $query->andFilterWhere([
      'id' => $this->id,
      'product_line_id' => $this->product_line_id,
      'last_count' => $this->last_count,
      'mold_id' => $this->mold_id,
      'sequence' => $this->sequence,
      'status' => $this->status,
      'created_by' => $this->created_by,
      'created_at' => $this->created_at,
      'updated_by' => $this->updated_by,
      'updated_at' => $this->updated_at,
    ]);
    $query->andFilterWhere(['like', 'no', $this->no])
          ->andFilterWhere(['like', 'title', $this->title]);
    return $dataProvider;
  }

}
