<?php
	namespace app\models;

	use yii\base\Model;
	use yii\data\ActiveDataProvider;

	/**
	 * TruckSearch represents the model behind the search form of `app\models\Truck`.
	 */
	class TruckSearch extends Truck{
		/**
		 * {@inheritdoc}
		 */
		public function rules(){
			return [
				[['id', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
				[['model', 'number'], 'safe'],
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
			$query = Truck::find();
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
															 'created_by' => $this->created_by,
															 'created_at' => $this->created_at,
															 'updated_by' => $this->updated_by,
															 'updated_at' => $this->updated_at,
														 ]);
			$query->andFilterWhere(['like', 'model', $this->model])
						->andFilterWhere(['like', 'number', $this->number]);
			return $dataProvider;
		}
	}
