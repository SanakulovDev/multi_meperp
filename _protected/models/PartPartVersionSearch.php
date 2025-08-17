<?php
	namespace app\models;

	use yii\base\Model;
	use yii\data\ActiveDataProvider;

	/**
	 * PartPartVersionSearch represents the model behind the search form of `app\models\PartPartVersion`.
	 */
	class PartPartVersionSearch extends PartPartVersion{
		/**
		 * {@inheritdoc}
		 */
		public function rules(){
			return [
				[['id', 'version', 'part_id', 'sub_part_id', 'warehouse_id', 'status', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
				[['action', 'remark', 'version', 'part_id', 'sub_part_id', 'warehouse_id'], 'safe'],
				[['usage_qty'], 'number'],
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
			$query = PartPartVersion::find();
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
				                       'version' => $this->version,
				                       'part_id' => $this->part_id,
				                       'sub_part_id' => $this->sub_part_id,
				                       'usage_qty' => $this->usage_qty,
				                       'warehouse_id' => $this->warehouse_id,
				                       'status' => $this->status,
			                       ]);
			$query->andFilterWhere(['like', 'action', $this->action])
			      ->andFilterWhere(['like', 'remark', $this->remark]);
			return $dataProvider;
		}
	}
