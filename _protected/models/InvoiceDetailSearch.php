<?php
namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;


/**
 * InvoiceDetailSearch represents the model behind the search form of `app\models\InvoiceDetail`.
 */
class InvoiceDetailSearch extends InvoiceDetail{
	/**
	 * @inheritdoc
	 */
	public function rules(){
		return [
			[['id', 'part_order_id', 'contract_id', 'cont_inv_id', 'part_id', 'err_sts', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
			[['qty', 'price'], 'number'],
			[['remarks'], 'safe'],
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
	public function search($params){
		$query = InvoiceDetail::find();
		// add conditions that should always apply here
		$dataProvider = new ActiveDataProvider(['query' => $query,]);
		$this->load($params);
		if(!$this->validate()){
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			return $dataProvider;
		}
		// grid filtering conditions
		$query->joinWith('part');
		$query->joinWith('contract');
		$query->andFilterWhere([
			                       'id'            => $this->id,
			                       'part_order_id' => $this->part_order_id,
			                       'contract_id'   => $this->contract_id,
			                       'cont_inv_id'   => $this->cont_inv_id,
			                       'part_id'       => $this->part_id,
			                       'qty'           => $this->qty,
			                       'price'         => $this->price,
			                       'created_by'    => $this->created_by,
			                       'created_at'    => $this->created_at,
			                       'updated_by'    => $this->updated_by,
			                       'updated_at'    => $this->updated_at,
		                       ]);
		$query->andFilterWhere(['like', 'remarks', $this->remarks]);
//		echo "<pre>1:"; print_r($this);echo "</pre>";
//		echo "<pre>"; print_r($query->createCommand()->rawSql);echo "</pre>";
//		die;

		return $dataProvider;
	}
}
