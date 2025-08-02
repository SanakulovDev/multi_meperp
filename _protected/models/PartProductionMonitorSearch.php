<?php
namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * PartProductionMonitorSearch represents the model behind the search form of `app\models\PartProductionMonitor`.
 */
class PartProductionMonitorSearch extends PartProductionMonitor {

  /**
   * {@inheritdoc}
   */
  public function rules() {
    return [
      [['id', 'production_monitor_id', 'part_id', 'actual_production_time', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
      [['start_time', 'end_time'], 'safe'],
      [['produced_qty', 'repaired_qty', 'broken_qty'], 'number'],
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
    $query = PartProductionMonitor::find();
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
      'production_monitor_id' => $this->production_monitor_id,
      'part_id' => $this->part_id,
      'start_time' => $this->start_time,
      'end_time' => $this->end_time,
      'produced_qty' => $this->produced_qty,
      'repaired_qty' => $this->repaired_qty,
      'broken_qty' => $this->broken_qty,
      'actual_production_time' => $this->actual_production_time,
      'created_by' => $this->created_by,
      'created_at' => $this->created_at,
      'updated_by' => $this->updated_by,
      'updated_at' => $this->updated_at,
    ]);

    return $dataProvider;
  }

}
