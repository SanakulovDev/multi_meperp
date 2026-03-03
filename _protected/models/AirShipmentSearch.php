<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\AirShipment;
use Yii;

/**
 * AirShipmentSearch represents the model behind the search form of `app\models\AirShipment`.
 */
class AirShipmentSearch extends AirShipment {
	/**
	 * {@inheritdoc}
	 */
	public function rules() {
		return [
			[['id', 'supplier_id', 'air_shipment_reason_id', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
			[['volume', 'cost'], 'number'],
			[['period', 'remark'], 'safe'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function scenarios() {
		// bypass scenarios() implementation in the parent class
		return Model::scenarios();
	}

	/**
	 * Creates data provider instance with search query applied
	 *
	 * @param array $params
	 *
	 * @return ActiveDataProvider
	 */
	public function search($params, $mode = '') {
		$query = AirShipment::find()->joinWith(['supplier.countryCode', 'airShipmentReason']);

		// add conditions that should always apply here

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			return $dataProvider;
		}

		// grid filtering conditions
		$query->andFilterWhere([
			'id' => $this->id,
			'supplier_id' => $this->supplier_id,
			'volume' => $this->volume,
			'cost' => $this->cost,
			'air_shipment_reason_id' => $this->air_shipment_reason_id,
			'created_by' => $this->created_by,
			'created_at' => $this->created_at,
			'updated_by' => $this->updated_by,
			'updated_at' => $this->updated_at,
		]);

		$query->andFilterWhere(['like', 'period', $this->period])
			->andFilterWhere(['like', 'remark', $this->remark]);

		if ($mode == 'excel') {
			$file = \Yii::createObject([
				'class' => 'codemix\excelexport\ExcelFile',
				'sheets' => [
					'Lms data' => [
						'class' => 'codemix\excelexport\ActiveExcelSheet',
						'query' => $query,
						'attributes' => [
							'id',
							'supplier.name',
							'supplier.countryCode.name',
							'supplier.city',
							'volume',
							'cost',
							'period',
							'airShipmentReason.title',
							'remark',
							'createdAtFormatted',
							'updatedAtFormatted',
						],
						'titles' => [
							9 => Yii::t('app', 'Created at'),
							10 => Yii::t('app', 'Updated at'),
						],
					],
				]
			]);
			return  $file;
		} else {
			return $dataProvider;
		}
	}
}
