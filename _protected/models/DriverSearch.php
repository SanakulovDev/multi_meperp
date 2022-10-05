<?php
	namespace app\models;

	use yii\base\Model;
	use yii\data\ActiveDataProvider;

	/**
	 * DriverSearch represents the model behind the search form of `app\models\Driver`.
	 */
	class DriverSearch extends Driver{
		/**
		 * {@inheritdoc}
		 */
		public function rules(){
			return [
				[['id', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
				[['first_name', 'last_name', 'middle_name', 'emp_no'], 'safe'],
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
			$query = Driver::find();
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
			$query->andFilterWhere(['like', 'first_name', $this->first_name])
						->andFilterWhere(['like', 'last_name', $this->last_name])
						->andFilterWhere(['like', 'middle_name', $this->middle_name])
						->andFilterWhere(['like', 'emp_no', $this->emp_no]);
			return $dataProvider;
		}
	}
