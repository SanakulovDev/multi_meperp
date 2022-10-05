<?php
	namespace app\models;

	use Yii;
	use yii\base\Model;
	use yii\data\ActiveDataProvider;

	/**
	 * SupplierSearch represents the model behind the search form of `app\models\Supplier`.
	 */
	class SupplierSearch extends Supplier{
		/**
		 * {@inheritdoc}
		 */
		public function rules(){
			return [
				[['id', 'transit_time', 'country_code_id'], 'integer'],
				[['name', 'duns', 'alias', 'address', 'city', 'postal', 'contact_name', 'contact_position', 'contact_email', 'contact_phone', 'contact_cellular'], 'safe'],
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
		public function search($params, $mode = ''){
			$query = Supplier::find();
			$query->with(['countryCode']);
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
				                       'transit_time' => $this->transit_time,
				                       'country_code_id' => $this->country_code_id,
			                       ]);
			$query->andFilterWhere(['like', 'name', $this->name])
			      ->andFilterWhere(['like', 'duns', $this->duns])
			      ->andFilterWhere(['like', 'alias', $this->alias])
			      ->andFilterWhere(['like', 'address', $this->address])
			      ->andFilterWhere(['like', 'city', $this->city])
			      ->andFilterWhere(['like', 'postal', $this->postal])
//      ->andFilterWhere(['like', 'country', $this->country])
//      ->andFilterWhere(['like', 'country_code', $this->country_code])
            ->andFilterWhere(['like', 'contact_name', $this->contact_name])
			      ->andFilterWhere(['like', 'contact_position', $this->contact_position])
			      ->andFilterWhere(['like', 'contact_email', $this->contact_email])
			      ->andFilterWhere(['like', 'contact_phone', $this->contact_phone])
			      ->andFilterWhere(['like', 'contact_cellular', $this->contact_cellular]);
			if($mode == 'excel'){
				$file = Yii::createObject([
					                          'class' => 'codemix\excelexport\ExcelFile',
					                          'sheets' => [
						                          'Suppliers' => [
							                          'class' => 'codemix\excelexport\ActiveExcelSheet',
							                          'query' => $query,
							                          'attributes' => [
								                          'id',
								                          'name',
								                          'duns',
								                          'alias',
								                          'address',
								                          //              'country',
								                          //              'country_code',
								                          'countryCode.name',
								                          'city',
								                          'postal',
								                          'transit_time',
								                          'contact_name',
								                          'contact_position',
								                          'contact_email',
								                          'contact_phone',
								                          'contact_cellular',
							                          ],
						                          ],
					                          ]
				                          ]);
				return $file;
			}else{
				return $dataProvider;
			}
		}
	}
