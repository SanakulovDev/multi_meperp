<?php
	namespace app\models;

	use Yii;
	use yii\base\Model;
	use yii\data\ActiveDataProvider;

	/**
	 * PartSearch represents the model behind the search form of `app\models\Part`.
	 */
	class PartSearch extends Part{
		/**
		 * @inheritdoc
		 */
		public function rules(){
			return [
				[['id', 'status', 'state', 'part_type_id', 'warehouse_id', 'created_by', 'created_at', 'updated_by', 'updated_at', 'contract_source_id'], 'integer'],
				[['part_no', 'part_name', 'part_color', 'remark', 'pack_size', 'unit_id'], 'safe'],
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
			$query = Part::find();
			// add conditions that should always apply here
			$dataProvider = new ActiveDataProvider(['query' => $query]);
			$this->load($params);
			if(!$this->validate()){
				return $dataProvider;
			}
			$query->joinWith(['unit', 'partType', 'contractSource', 'warehouse']);
			// grid filtering conditions
			$query->andFilterWhere([
															 'part.id' => $this->id,
															 'part.status' => $this->status,
															 'part.state' => $this->state,
															 'unit_id' => $this->unit_id,
															 'part_type_id' => $this->part_type_id,
															 'part.created_by' => $this->created_by,
															 'part.created_at' => $this->created_at,
															 'part.updated_by' => $this->updated_by,
															 'part.updated_at' => $this->updated_at,
															 'part.warehouse_id' => $this->warehouse_id,
															 'contract_source_id' => $this->contract_source_id,
															 'warehouse_id' => $this->warehouse_id,
														 ]);
			$query->andFilterWhere(['like', 'part_no', $this->part_no])
						->andFilterWhere(['like', 'part.part_color', $this->part_color])
						->andFilterWhere(['like', 'part.remark', $this->remark])
						->andFilterWhere(['like', 'part.pack_size', $this->pack_size])
						->andFilterWhere(['like', 'part_name', $this->part_name]);
			if($mode == 'excel'){
				$file = Yii::createObject([
																		 'class' => 'codemix\excelexport\ExcelFile',
																		 'sheets' => [
																			 'Материали' => [
																				 'class' => 'codemix\excelexport\ActiveExcelSheet',
																				 'query' => $query,
																				 'attributes' => [
																					 'id',
																					 'part_no',
																					 'pack_size',
																					 'part_name',
																					 'part_color',
																					 'unit.unit_value',
																					 'partType.typename',
																					 'statusText',
																					 'stateText',
																					 'createdBy.fullname',
																					 'createdAtFormatted',
																					 'updatedBy.fullname',
																					 'updatedAtFormatted',
																					 'contractSource.name',
																					 'remark',
																					 'warehouse.name',
																				 ],
																				 'titles' => [
																					 5 => Yii::t('app', 'Unit'),
																					 7 => Yii::t('app', 'Status'),
																					 8 => Yii::t('app', 'State'),
																					 9 => Yii::t('app', 'Created by'),
																					 10 => Yii::t('app', 'Created at'),
																					 11 => Yii::t('app', 'Updated by'),
																					 12 => Yii::t('app', 'Updated at'),
																					 13 => Yii::t('app', 'Contract source'),
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
