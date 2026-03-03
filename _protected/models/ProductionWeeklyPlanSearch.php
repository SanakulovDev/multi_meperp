<?php
namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * DeliveryPlanSearch represents the model behind the search form of `app\models\DeliveryPlan`.
 */
class ProductionWeeklyPlanSearch extends ProductionWeeklyPlan{
	/**
	 * {@inheritdoc}
	 */
	public function rules(){
		return [
			[['id', 'part_id', 'warehouse_id', 'shift', 'target_qty', 'line', 'type'], 'integer'],
			[['production_date','comment', 'remark'], 'safe'],
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
	 * @param array $params
	 * @return ActiveDataProvider
	 */
	public function search($params){
		$query = ProductionWeeklyPlan::find();
		// add conditions that should always apply here
		$dataProvider = new ActiveDataProvider(
			[
				'query' => $query,
        'pagination' => false,
				'sort' => ['defaultOrder'=>'production_date desc']
			]
		);
		$this->load($params);
		if(!$this->validate()){
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			return $dataProvider;
		}
		$query->joinWith(['warehouse', 'part','planComment']);
		// grid filtering conditions
    $this->type == 0;
		$query->andFilterWhere(
			[
				'id'                           => $this->id,
				'production_plan.part_id'      => $this->part_id,
				'production_date'              => $this->production_date,
				'production_plan.warehouse_id' => $this->warehouse_id,
				'shift'                        => $this->shift,
				'target_qty'                   => $this->target_qty,
				'line'                         => $this->line,
			])->orderBy(['production_date' => SORT_DESC])->all();

    $query->andFilterWhere(['like', 'production_plan_comment.comment', $this->comment]);
		return $dataProvider;
	}
  public function searchMonthly($params){
    // vd($params);
		$query = ProductionWeeklyPlan::find();
		// add conditions that should always apply here
		$dataProvider = new ActiveDataProvider(
			[
				'query' => $query,
        'pagination' => false,
        'sort' => ['defaultOrder'=>'production_date desc']
			]
		);
		$this->load($params);
		if(!$this->validate()){
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			return $dataProvider;
		}
		// grid filtering conditions
		$query->andFilterWhere(
			[
				'id'                           => $this->id,
				'part_id'      => $this->part_id,
				'production_date'              => !empty($this->production_date)?($this->production_date.'-01'):'',
				'warehouse_id' => $this->warehouse_id,
				'shift'                        => $this->shift,
				'target_qty'                   => $this->target_qty,
				'line'                         => $this->line,
			])->orderBy(['production_date' => SORT_DESC])->all();
      $query->andFilterWhere(['like', 'production_monthly_plan.remark', $this->remark]);
    $query->andFilterWhere(['like', 'production_plan_comment.comment', $this->comment]);
		return $dataProvider;
	}
}
