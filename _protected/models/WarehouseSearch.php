<?php
	namespace app\models;

	use Yii;
	use yii\base\Model;
	use yii\data\ActiveDataProvider;

	/**
	 * WarehouseSearch represents the model behind the search form of `app\models\Warehouse`.
	 */
	class WarehouseSearch extends Warehouse{
		/**
		 * @inheritdoc
		 */
		public function rules(){
			return [
				[['id', 'is_coverable', 'status', 'warehouse_type', 'warehouse_report_group_id', 'supplier_id', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
				[['name', 'description'], 'safe'],
			];
		}

		/**
		 * @inheritdoc
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
			$query = Warehouse::find();
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
															 'status' => $this->status,
															 'warehouse_type' => $this->warehouse_type,
															 'supplier_id' => $this->supplier_id,
															 'warehouse_report_group_id' => $this->warehouse_report_group_id,
															 'created_by' => $this->created_by,
															 'created_at' => $this->created_at,
															 'updated_by' => $this->updated_by,
															 'is_coverable' => $this->is_coverable,
															 'updated_at' => $this->updated_at,
														 ]);
			$query->andFilterWhere(['like', 'name', $this->name])
						->andFilterWhere(['like', 'description', $this->description]);
			if($mode == 'excel'){
        
        
				$file = Yii::createObject([
																		 'class' => 'codemix\excelexport\ExcelFile',
																		 'sheets' => [
																			 'Warehouse' => [
																				 'class' => 'codemix\excelexport\ActiveExcelSheet',
																				 'query' => $query,
																				 'attributes' => [
																					 'id',
																					 'name',
																					 'description',
																					 'typeName',
																					 'statusText',
																					 //'createdBy.fullname',
																					 'createdAtFormatted',
																					 //'updatedBy.fullname',
																					 'updatedAtFormatted',
																					 'supplier.name',
																					 'warehouseReportGroup.title',
																				 ],
																				 'titles' => [
																					 3 => Yii::t('app', 'Warehouse type'),
																					 4 => Yii::t('app', 'Status'),
																					 5 => Yii::t('app', 'Created at'),
																					 6 => Yii::t('app', 'Updated at'),
																					 7 => Yii::t('app', 'Supplier'),
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