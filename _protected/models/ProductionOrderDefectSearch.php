<?php
namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ProductionOrderDefectSearch represents the model behind the search form of `app\models\ProductionOrderDefect`.
 */
class ProductionOrderDefectSearch extends ProductionOrderDefect{
	/**
	 * {@inheritdoc}
	 */
	public function rules(){
		return [
			[['id'], 'integer'],
			[['production_order_id', 'defect_id', 'created_by', 'created_at','filter_from', 'filter_to','qty'], 'safe']
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
		$query = ProductionOrderDefect::find()->joinWith(['defect','productionOrder']);
		// add conditions that should always apply here
		$dataProvider = new ActiveDataProvider([
			                                       'query' => $query,
		                                       ]);
		$this->load($params);
		if(!$this->validate()){
			return $dataProvider;
		}
		$query->joinWith(['productionOrder', 'createdBy', 'defect']);
		// grid filtering conditions
		$query->andFilterWhere([
			                       'id'                  => $this->id,
			                       'qty'                 => $this->qty,
			                       // 'production_order_id' => $this->production_order_id,
			                       // 'defect_id'           => $this->defect_id,
			                       // 'created_by'          => $this->created_by,
			                       // 'created_at'          => $this->created_at,
		                       ]);

		$filter_from = (!empty($this->filter_from)) ? $this->filter_from.' 00:00:00' : '1970-01-01 00:00:00';
		$filter_to = (!empty($this->filter_to)) ? $this->filter_to.' 23:59:59' : '9999-12-31 23:59:59';

		$query->andFilterWhere(['between', 'production_order_defect.created_at', strtotime($filter_from), strtotime($filter_to)]);

		$query->andFilterWhere(['like', 'productionOrder.serial_number', $this->production_order_id])
					->andFilterWhere(['like', 'defect.code', $this->defect_id])
					->andFilterWhere(['like', 'user.username', $this->created_by])
					->andFilterWhere(['>=', 'production_order_defect.created_at', date('Y-m-d',strtotime($this->created_at))]);

		if($mode == 'excel'){
			$query->orderBy([
												'production_order_id' => SORT_ASC,
												'defect.code'=>SORT_ASC
											]);
			$file = \Yii::createObject([
																	 'class' => 'codemix\excelexport\ExcelFile',
																	 'sheets' => [
																		 'Quality' => [
																			 'class' => 'codemix\excelexport\ActiveExcelSheet',
																			 'query' => $query,
																			 'attributes' => [
																				 'id',
																				 'productionOrder.serial_number',
																				 'productionOrder.quantity',
																				 'defect.code',
																				 'qty',
																				 'createdBy.fullname',
																				 'createdAtFormatted'
																			 ],
																			 'titles' => [
																				 4 => Yii::t('app', 'Quantity'),
																				 5 => Yii::t('app', 'Created by'),
																				 6 => Yii::t('app', 'Created at'),
																			 ],
																		 ],

																	 ]
																 ]);
			return  $file;
		}else{
			return $dataProvider;
		}
	}
}
