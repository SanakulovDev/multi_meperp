<?php
	namespace app\models;

	use yii\base\Model;
	use yii\data\ActiveDataProvider;

	/**
	 * ContractDetailSearch represents the model behind the search form of `app\models\ContractDetail`.
	 */
	class ContractDetailSearch extends ContractDetail{
		/**
		 * {@inheritdoc}
		 */
		public function rules(){
			return [
				[['id', 'delivery_term_id', 'lead_time'], 'integer'],
				[['price'], 'number'],
				[['part_name', 'part_no', 'part_color', 'contract_id', 'part_id', 'weekly_capacity', 'cnfea', 'sub_source','is_primary_price'], 'safe'],
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
			$query = ContractDetail::find()
			->with(['deliveryTerm']);
			$query->joinWith('part');
			$query->joinWith('contract');
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
				                       'delivery_term_id' => $this->delivery_term_id,
				                       'price' => $this->price,
				                       'cnfea' => $this->cnfea,
				                       'weekly_capacity' => $this->weekly_capacity,
				                       'sub_source' => $this->sub_source,
				                       'lead_time' => $this->lead_time,
				                       'is_primary_price' => $this->is_primary_price,
			                       ]);
			$query->andFilterWhere(['like', 'contract_no', $this->contract_id])
			      ->andFilterWhere(['like', 'part.part_color', $this->part_color])
			      ->andFilterWhere(['like', 'part.part_name', $this->part_name])
			      ->andFilterWhere(['like', 'part.part_no', $this->part_no]);
			$dataProvider->sort->attributes['contract_id'] = [
				'asc' => ['contract_no' => SORT_ASC],
				'desc' => ['contract_no' => SORT_DESC],
			];
			$dataProvider->sort->attributes['part_name'] = [
				'asc' => ['part.part_name' => SORT_ASC],
				'desc' => ['part.part_name' => SORT_DESC],
			];
			$dataProvider->sort->attributes['part_no'] = [
				'asc' => ['part.part_no' => SORT_ASC],
				'desc' => ['part.part_no' => SORT_DESC],
			];
			$dataProvider->sort->attributes['part_color'] = [
				'asc' => ['part.part_color' => SORT_ASC],
				'desc' => ['part.part_color' => SORT_DESC],
			];
			return $dataProvider;
		}
	}
