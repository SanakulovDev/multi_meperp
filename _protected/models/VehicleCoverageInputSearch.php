<?php
namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * VehicleCoverageInputSearch represents the model behind the search form of `app\models\VehicleCoverageInput`.
 */
class VehicleCoverageInputSearch extends VehicleCoverageInput {

  /**
   * {@inheritdoc}
   */
  public function rules() {
    return [
      [['id', 'model_id', 'description', 'created_at', 'created_by'], 'integer'],
      [['quantity'], 'number'],
      [['for_date'], 'safe'],
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
    $query = VehicleCoverageInput::find()->joinWith(
      [
        'model' => function($query) {
          $query->from(['model' => ProductModel::tableName()]);
        },
        'createdBy'
      ]
    );
    // add conditions that should always apply here
    $dataProvider = new ActiveDataProvider(['query' => $query,]);
    $dataProvider->sort->defaultOrder = [
      'model_id' => SORT_ASC,
      'description' => SORT_ASC,
      'for_date' => SORT_ASC
    ];
    $this->load($params);
    if (!$this->validate()) {
      // uncomment the following line if you do not want to return any records when validation fails
      // $query->where('0=1');
      return $dataProvider;
    }
    // grid filtering conditions
    $query->andFilterWhere([
      'id' => $this->id,
      'model_id' => $this->model_id,
      'quantity' => $this->quantity,
      'for_date' => $this->for_date,
      'vehicle_coverage_input.description' => $this->description,
      'created_at' => $this->created_at,
      'created_by' => $this->created_by,
    ]);

    return $dataProvider;
  }

}
