<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * MoldPartSearch represents the model behind the search form of `app\models\MoldPart`.
 */
class MoldPartSearch extends MoldPart{
  /**
   * {@inheritdoc}
   */
  public function rules(){
    return [
      [['id', 'mold_id', 'part_id', 'quantity', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
      [['mold_no'], 'safe'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function scenarios(){
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
  public function search($params){
    $query = MoldPart::find();

    // add conditions that should always apply here

    $dataProvider = new ActiveDataProvider([
      'query' => $query,
    ]);

    $this->load($params);

    if(!$this->validate()){
      // uncomment the following line if you do not want to return any records when validation fails
      // $query->where('0=1');
      return $dataProvider;
    }
    $query->joinWith(['moldNo']);
    // grid filtering conditions
    $query->andFilterWhere([
      'id' => $this->id,
      'mold_id' => $this->mold_id,
      'part_id' => $this->part_id,
      'quantity' => $this->quantity,
      'created_by' => $this->created_by,
      'created_at' => $this->created_at,
      'updated_by' => $this->updated_by,
      'updated_at' => $this->updated_at,
    ]);

    return $dataProvider;
  }
}
