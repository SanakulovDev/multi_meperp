<?php
	namespace app\models;

	use Yii;
	use yii\base\Model;
	use yii\data\ActiveDataProvider;

	/**
	 * FactorySearch represents the model behind the search form of `app\models\Factory`.
	 */
	class FactorySearch extends Factory{
		/**
		 * {@inheritdoc}
		 */
		public function rules(){
			return [
				[['id', 'is_main', 'status'], 'integer'],
				[['name', 'head','chief_accountant', 'alias', 'address', 'tin', 'vat', 'duns', 'remark', 'fg_warehouse_id', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'safe'],
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
			$query = Factory::find()->joinWith(['createdBy', 'fgWarehouse',
			                                    'updatedBy' => function($query){
				                                    $query->from(['u2' => User::tableName()]);
			                                    }
			                                   ]);
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
				                       'factory.id' => $this->id,
				                       'factory.status' => $this->status,
				                       'is_main' => $this->is_main,
			                       ]);
			$query->andFilterWhere(['like', 'name', $this->name])
			      ->andFilterWhere(['like', 'head', $this->head])
			      ->andFilterWhere(['like', 'chief_accountant', $this->chief_accountant])
			      ->andFilterWhere(['like', 'alias', $this->alias])
			      ->andFilterWhere(['like', 'address', $this->address])
			      ->andFilterWhere(['like', 'tin', $this->tin])
			      ->andFilterWhere(['like', 'vat', $this->vat])
			      ->andFilterWhere(['like', 'warehouse.name', $this->fg_warehouse_id])
			      ->andFilterWhere(['like', 'remark', $this->remark])
			      ->andFilterWhere(['like', 'duns', $this->duns]);
			if($mode == 'excel'){
				$file = Yii::createObject([
					                           'class' => 'codemix\excelexport\ExcelFile',
					                           'sheets' => [
						                           'Factories' => [
							                           'class' => 'codemix\excelexport\ActiveExcelSheet',
							                           'query' => $query,
							                           'attributes' => [
								                           'id',
								                           'name',
								                           'head',
								                           'chief_accountant',
								                           'is_main',
								                           'alias',
								                           'address',
								                           'tin',
								                           'vat',
								                           'duns',
								                           'remark',
								                           'statusText',
								                           'createdBy.fullname',
								                           'createdAtFormatted',
								                           'updatedBy.fullname',
								                           'updatedAtFormatted',
							                           ],
							                           'titles' => [
								                           11 => Yii::t('app', 'Status'),
								                           12 => Yii::t('app', 'Created by'),
								                           13 => Yii::t('app', 'Created at'),
								                           14 => Yii::t('app', 'Updated by'),
								                           15 => Yii::t('app', 'Updated at'),
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
