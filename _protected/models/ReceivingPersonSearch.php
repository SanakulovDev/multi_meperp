<?php
	namespace app\models;

	use Yii;
	use yii\base\Model;
	use yii\data\ActiveDataProvider;

	/**
	 * ReceivingPersonSearch represents the model behind the search form of `app\models\ReceivingPerson`.
	 */
	class ReceivingPersonSearch extends ReceivingPerson{
		/**
		 * {@inheritdoc}
		 */
		public function rules(){
			return [
				[['id', 'status', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
				[['fullname', 'doc_number', 'doc_date'], 'safe'],
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
			$query = ReceivingPerson::find()->joinWith(['createdBy',
																									'updatedBy' => function($query){
																										$query->from(['u2' => User::tableName()]);
																									}]);
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
//            'id' => $this->id,
// 'doc_date' => $this->doc_date,
							'receiving_person.status' => $this->status,
//            'created_by' => $this->created_by,
//            'created_at' => $this->created_at,
//            'updated_by' => $this->updated_by,
//            'updated_at' => $this->updated_at,
														 ]);
			$query->andFilterWhere(['like', 'receiving_person.fullname', $this->fullname])
						->andFilterWhere(['like', 'doc_number', $this->doc_number]);

			if($this->doc_date){
				$query->andFilterWhere(['>=', 'doc_date', date('Y-m-d', strtotime($this->doc_date))]);
			}

			if($mode == 'excel'){
				$file = Yii::createObject([
																		'class' => 'codemix\excelexport\ExcelFile',
																		'sheets' => [
																			'Receiving person' => [
																				'class' => 'codemix\excelexport\ActiveExcelSheet',
																				'query' => $query,
																				'attributes' => [
																					'id',
																					'fullname',
																					'doc_number',
																					'doc_date',
																					'statusText',
																					'createdBy.fullname',
																					'createdAtFormatted',
																					'updatedBy.fullname',
																					'updatedAtFormatted',
																				],
																				'titles' => [
																					4 => Yii::t('app', 'Status'),
																					5 => Yii::t('app', 'Created by'),
																					6 => Yii::t('app', 'Created at'),
																					7 => Yii::t('app', 'Updated by'),
																					8 => Yii::t('app', 'Updated at'),
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
