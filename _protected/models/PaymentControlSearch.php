<?php
	namespace app\models;

	use yii\base\Model;
	use yii\data\ActiveDataProvider;
	use Yii;

	/**
	 * PaymentControlSearch represents the model behind the search form of `app\models\PaymentControl`.
	 */
	class PaymentControlSearch extends PaymentControl{
		/**
		 * {@inheritdoc}
		 */
		public function rules(){
			return [
				[['id', 'payment_type', 'contract_id', 'is_spend', 'supplier_id', 'created_at', 'created_by', 'updated_at', 'updated_by','dummy_order'], 'integer'],
				[['no', 'part_order_id', 'date', 'expire_date', 'shipment_date', 'bank_name'], 'safe'],
				[['amount'], 'number'],
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
		public function search($params, $mode=''){
			$query = PaymentControl::find()->joinWith(['supplier', 'contract.currency']);
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
				                       'payment_control.id' => $this->id,
				                       'date' => $this->date,
				                       'payment_type' => $this->payment_type,
				                       'amount' => $this->amount,
				                       'is_spend' => $this->is_spend,
				                       'contract_id' => $this->contract_id,
				                       'supplier_id' => $this->supplier_id,
				                       'created_at' => $this->created_at,
				                       'created_by' => $this->created_by,
				                       'updated_at' => $this->updated_at,
				                       'updated_by' => $this->updated_by,
			                       ]);
			$query->andFilterWhere(['like', 'no', $this->no]);
			if($mode == 'excel'){
				$query->joinWith([
					'supplier', 'contract', 'createdBy',
					'updatedBy' => function($query){
						$query->from(['u2' => User::tableName()]);
					}
				]);

				$file = \Yii::createObject([
					'class' => 'codemix\excelexport\ExcelFile',
					'sheets' => [
						'Payment data' => [
							'class' => 'codemix\excelexport\ActiveExcelSheet',
							'query' => $query,
							'attributes' => [
								'id',
								'no',
								'date',
								'typeName',
								'amount',
								'contract.contract_no',
								'supplier.name',
								'contract.currency.code',
								'createdBy.fullname',
								'createdAtFormatted',
								'updatedBy.fullname',
								'updatedAtFormatted',
								'dummyOrderText',
							],
							'titles' => [
								3 => Yii::t('app', 'Payment type'),
								5 => Yii::t('app', 'Contract'),
								6 => Yii::t('app', 'Supplier'),
								8 => Yii::t('app', 'Created by'),
								9 => Yii::t('app', 'Created at'),
								10 => Yii::t('app', 'Updated by'),
								11 => Yii::t('app', 'Updated at')
							],
						],
					]
				]);
				return  $file;
			} else { return $dataProvider; }		
		}
	}
