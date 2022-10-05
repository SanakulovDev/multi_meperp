<?php

namespace app\modules\api\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Document;
use app\models\User;
use app\models\Warehouse;
use Yii;

class DocumentSearch extends Model {
	public $id;
	public $docnum;
	public $document_type_id;
	public $from_warehouse_id;
	public $to_warehouse_id;
	public $supplier_id;
	public $serial_number;
	public $created_by;

	public function rules() {
		return [
			[['id', 'document_type_id', 'from_warehouse_id', 'to_warehouse_id', 'created_by'], 'integer'],
			[['docnum', 'series', 'status', 'comment', 'serial_number', 'supplier_id'], 'safe'],
		];
	}

	public function search($params) {
		$this->load($params, '');
		$query = Document::find()
				->with([
					'documentDetails.part',
					'documentType',
					'supplier',
					'fromWarehouse',
					'createdBy',
					'updatedBy' => function ($query) {
						$query->from(['u2' => User::tableName()]);
					},
					'toWarehouse' => function ($query) {
						$query->from(['w2' => Warehouse::tableName()]);
					}
				]);

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);
		if ($this->validate()) {
			$query->andFilterWhere([
				'document.id' => $this->id,
				'document_type_id' => $this->document_type_id,
				'from_warehouse_id' => $this->from_warehouse_id,
				'to_warehouse_id' => $this->to_warehouse_id,
				'supplier_id' => $this->supplier_id,
				'document.created_by' => $this->created_by,
			]);
			$query->andFilterWhere(['like', 'docnum', $this->docnum])
						->andFilterWhere(['like', 'serial_number', $this->serial_number]);
		}

		return $dataProvider;
	}
}