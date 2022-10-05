<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * MoldSearch represents the model behind the search form of `app\models\Mold`.
 */
class MoldSearch extends Mold{
  /**
   * {@inheritdoc}
   */
  public function rules(){
    return [
      [['id', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
      [['mold_no', 'production_date', 'project_name', 'company_name', 'part_number', 'part_name'], 'safe'],
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
			$query = Mold::find();

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

    // grid filtering conditions
    $query->andFilterWhere([
      'id' => $this->id,
      'production_date' => $this->production_date,
      'created_by' => $this->created_by,
      'created_at' => $this->created_at,
      'updated_by' => $this->updated_by,
      'updated_at' => $this->updated_at,
    ]);

    $query->andFilterWhere(['like', 'mold_no', $this->mold_no])
      ->andFilterWhere(['like', 'project_name', $this->project_name])
      ->andFilterWhere(['like', 'company_name', $this->company_name])
      ->andFilterWhere(['like', 'part_number', $this->part_number])
      ->andFilterWhere(['like', 'part_name', $this->part_name]);

    return $dataProvider;
  }
}
