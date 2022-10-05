<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use Yii;

/**
 * InvoiceSearch represents the model behind the search form of `app\models\Invoice`.
 */
class InvoiceSearch extends Invoice {
	/**
	 * {@inheritdoc}
	 */
	public function rules() {
		return [
			[['id',  'invoice_amount', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
			[['invoice_no', 'supplier_id', 'invoice_date', 'currency_id'], 'safe'],
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
		$query = Invoice::find()->joinWith('supplier','currency');

		// add conditions that should always apply here

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		if (!$this->validate()) {
			return $dataProvider;
		}

		// grid filtering conditions
		$query->andFilterWhere([
			'invoice.id' => $this->id,
			'invoice_date' => $this->invoice_date,
		]);

		$query->andFilterWhere(['like', 'invoice_no', $this->invoice_no])
			  ->andFilterWhere(['like', 'supplier.name', $this->supplier_id]);

		if ($mode == 'excel') {
			$file = \Yii::createObject([
				'class' => 'codemix\excelexport\ExcelFile',
				'sheets' => [
					'Invoice data' => [
						'class' => 'codemix\excelexport\ActiveExcelSheet',
						'query' => $query,
						'attributes' => [
							'id',
							'invoice_no',
							'invoice_date',
							'invoice_amount',
							'currency.code',
							'supplier.name',
						],
						'titles' => [
							4 => Yii::t('app', 'Currency'),
							5 => Yii::t('app', 'Supplier'),
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
