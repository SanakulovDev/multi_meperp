<?php

namespace app\modules\api\search;

use app\models\PartPart;
use app\models\Part;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class PartPartSearch extends Model {
	public $id;
	public $part_nm;
	public $sub_part_nm;
	public $part_id;
	public $sub_part_id;
	public $usage_qty;
	public $warehouse_id;
	public $remark;
	public $unit_value;
	public $status;
	public $created_by;
	public $created_at;
	public $updated_by;
	public $updated_at;
	public $part_model;

	public function rules() {
		return [
			[['id', 'part_id', 'sub_part_id', 'status', 'warehouse_id', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
			[['usage_qty'], 'number'],
			[['remark', 'unit_value', 'part_nm', 'sub_part_nm', 'part_model'], 'safe'],
		];
	}

	public function search($params) {
		$this->load($params, '');

		$query = PartPart::find()->joinWith([
			'subPart.unit',
			'warehouse',
			'part' => function ($query) {
				$query->from(['mainPart' => Part::tableName()]);
			}
		]);

		$dataProvider = new ActiveDataProvider([
			'query' => $query
		]);

		if ($this->validate()) {
			$query->andFilterWhere([
				'part_part.id' => $this->id,
				'part_id' => $this->part_id,
				'sub_part_id' => $this->sub_part_id,
				'usage_qty' => $this->usage_qty,
				'part_part.warehouse_id' => $this->warehouse_id,
				'part_part.status' => $this->status,
				'part_part.created_by' => $this->created_by,
				'part_part.created_at' => $this->created_at,
				'part_part.updated_by' => $this->updated_by,
				'part_part.updated_at' => $this->updated_at,
			]);

			$query->andFilterWhere(['like', 'part_part.remark', $this->remark])
				->andFilterWhere(['like', 'unit_value', $this->unit_value])
				->andFilterWhere(['like', 'mainPart.part_name', $this->part_nm])
				->andFilterWhere(['like', 'part.part_name', $this->sub_part_nm]);
		}

		return $dataProvider;
	}
}