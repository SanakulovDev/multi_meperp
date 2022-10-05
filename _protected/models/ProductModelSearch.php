<?php
namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ProductModelSearch represents the model behind the search form of `app\models\ProductModel`.
 */
class ProductModelSearch extends ProductModel {

  /**
   * {@inheritdoc}
   */
  public function rules() {
    return [
      [['id'], 'integer'],
      [['modelname', 'description', 'is_vehicle'], 'safe'],
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
    $query = ProductModel::find();
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
      'is_vehicle' => $this->id,
    ]);
    $query->andFilterWhere(['like', 'modelname', $this->modelname]);
    $query->andFilterWhere(['like', 'description', $this->description]);

    return $dataProvider;
  }

}
