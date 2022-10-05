<?php
	namespace app\models;

	use yii\base\Model;
	use yii\data\ActiveDataProvider;

	/**
	 * FgInvoiceDetailSearch represents the model behind the search form of `app\models\FgInvoiceDetail`.
	 */
	class FgInvoiceDetailSearch extends FgInvoiceDetail{
		/**
		 * {@inheritdoc}
		 */
		public function rules(){
			return [
				[['id', 'fg_invoice_id', 'unit_id', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
				[['part_no', 'part_name'], 'safe'],
				[['qty', 'price'], 'number'],
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
			$query = FgInvoiceDetail::find();
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
															 'fg_invoice_id' => $this->fg_invoice_id,
															 'qty' => $this->qty,
															 'price' => $this->price,
															 'unit_id' => $this->unit_id,
															 'created_at' => $this->created_at,
															 'created_by' => $this->created_by,
															 'updated_at' => $this->updated_at,
															 'updated_by' => $this->updated_by,
														 ]);
			$query->andFilterWhere(['like', 'part_no', $this->part_no])
						->andFilterWhere(['like', 'part_name', $this->part_name]);
			return $dataProvider;
		}
	}
