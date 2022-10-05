<?php

namespace app\modules\api\search;

use app\models\Part;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class PartSearch extends Model {
	public $id;
	public $part_no;
	public $part_name;
	public $part_color;
	public $unit;
	public $part_type;

	public $product_group_id;
	public $contract_source_id;

	public $product_line_id;
	public $warehouse_id;
	public $fg_warehouse_id;
	public $remark;
	public $pack_size;
	public $side;
	public $status;
	public $state;

	public function rules() {
		return [
			[['id', 'status', 'state', 'warehouse_id', 'contract_source_id', 'product_line_id'], 'integer'],
			[['part_color', 'part_no', 'part_name', 'unit', 'part_type'], 'safe'],
		];
	}

	public function search($params) {
		$this->load($params, '');

		$query = Part::find()
				->joinWith([
					'partType',
					'unit',
				]);

		$dataProvider = new ActiveDataProvider([
			'query' => $query
		]);

		if ($this->validate()) {
			$query->andFilterWhere([
				'part.id' => $this->id,
				'product_group_id' => $this->product_group_id,
				'contract_source_id' => $this->contract_source_id,
				'warehouse_id' => $this->warehouse_id,
				'fg_warehouse_id' => $this->fg_warehouse_id,
				'pack_size' => $this->pack_size,
				'side' => $this->side,
				'part.status' => $this->status,
				'state' => $this->state,
			]);
			$query->andFilterWhere(['like', 'part_no', $this->part_no])
						->andFilterWhere(['like', 'part_name', $this->part_name])
						->andFilterWhere(['like', 'remark', $this->remark])
						->andFilterWhere(['like', 'part_type.typename', $this->part_type])
						->andFilterWhere(['like', 'unit.unit_value', $this->unit]);
		}

		return $dataProvider;
	}
}