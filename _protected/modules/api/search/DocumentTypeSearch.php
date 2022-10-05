<?php

namespace app\modules\api\search;

use app\models\DocumentType;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class DocumentTypeSearch extends Model {
	public $id;
	public $code;
	public $name;
	public $description;

	public function rules() {
		return [
			[['id'], 'integer'],
			[['code', 'name', 'description'], 'safe'],
		];
	}

	public function search($params) {
		$this->load($params, '');

		$query = DocumentType::find();

		$dataProvider = new ActiveDataProvider([
			'query' => $query
		]);

		if ($this->validate()) {
			$query->andFilterWhere([
				'id' => $this->id,
				'code' => $this->code
			]);
			$query->andFilterWhere(['like', 'name', $this->name])
						->andFilterWhere(['like', 'description', $this->description]);
		}

		return $dataProvider;
	}
}