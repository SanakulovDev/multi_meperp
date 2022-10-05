<?php

namespace app\modules\api\search;

use app\models\Warehouse;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class WarehouseSearch extends Model {
	public $id;
	public $name;

	public function rules() {
		return [
			[['id'], 'integer'],
			[['name'], 'safe'],
		];
	}

	public function search($params) {
		$this->load($params, '');

		$query = Warehouse::find();

		$dataProvider = new ActiveDataProvider([
			'query' => $query
		]);

		if ($this->validate()) {
			$query->andFilterWhere([
				'id' => $this->id
			]);
			$query->andFilterWhere(['like', 'name', $this->name]);
		}

		return $dataProvider;
	}
}