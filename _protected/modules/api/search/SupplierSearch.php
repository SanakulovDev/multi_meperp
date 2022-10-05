<?php

namespace app\modules\api\search;

use app\models\Supplier;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class SupplierSearch extends Model {
	public $id;
	public $name;
	public $country_code;
	public $duns;
	public $alias;
	public $address;
	public $city;
	public $postal;
	public $contact_name;
	public $contact_position;
	public $contact_email;
	public $contact_phone;
	public $contact_cellular;

	public function rules() {
		return [
			[['id'], 'integer'],
			[['name', 'country_code', 'duns', 'alias', 'address', 'city', 'postal', 'contact_name', 'contact_position', 'contact_email', 'contact_phone', 'contact_cellular'], 'safe'],
		];
	}

	public function search($params) {
		$this->load($params, '');

		$query = Supplier::find()->joinWith(['countryCode']);

		$dataProvider = new ActiveDataProvider([
			'query' => $query
		]);

		if ($this->validate()) {
			$query->andFilterWhere([
				'supplier.id' => $this->id,
				'country_code.alpha_2' => $this->country_code
			]);
			$query->andFilterWhere(['like', 'name', $this->name])
				->andFilterWhere(['like', 'country_code', $this->country_code])
				->andFilterWhere(['like', 'duns', $this->duns])
				->andFilterWhere(['like', 'alias', $this->alias])
				->andFilterWhere(['like', 'address', $this->address])
				->andFilterWhere(['like', 'city', $this->city])
				->andFilterWhere(['like', 'postal', $this->postal])
				->andFilterWhere(['like', 'contact_name', $this->contact_name])
				->andFilterWhere(['like', 'contact_position', $this->contact_position])
				->andFilterWhere(['like', 'contact_email', $this->contact_email])
				->andFilterWhere(['like', 'contact_phone', $this->contact_phone])
				->andFilterWhere(['like', 'contact_cellular', $this->contact_cellular]);
		}

		return $dataProvider;
	}
}