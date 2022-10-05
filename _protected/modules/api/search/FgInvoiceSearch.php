<?php

namespace app\modules\api\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\FgInvoice;
use app\models\User;

class FgInvoiceSearch extends Model {
	public $id;
	public $invoice_no;
	public $invoice_date;
	public $contract;
	public $contract_date;
	public $rec_person_fullname;
	public $rec_person_regno;
	public $driver;
	public $truck;
	public $manager;
	public $account;
	public $sender;
	public $vat;
	public $excise;
	public $comment;
	public $factory_id;
	public $customer_id;
	public $created_by;
	public $created_at;

	public function rules() {
		return [
			[['id', 'factory_id', 'customer_id', 'created_by', 'created_at'], 'integer'],
			[
				[
					'invoice_no', 'invoice_date', 'contract', 'contract_date', 'rec_person_fullname', 
					'rec_person_regno', 'driver', 'truck', 'manager', 'account', 'sender', 'vat', 'excise', 'comment'
				], 'safe'
			],
		];
	}

	public function search($params) {
		$this->load($params, '');
		$query = FgInvoice::find()
				->with([
					'fgInvoiceDetails.unit',
					'factory',
					'customer',
					'createdBy',
					'updatedBy' => function ($query) {
						$query->from(['u2' => User::tableName()]);
					}
				]);

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);
		if ($this->validate()) {
			$query->andFilterWhere([
				'fg_invoice.id' => $this->id,
				'factory_id' => $this->factory_id,
				'customer_id' => $this->customer_id,
				'fg_invoice.created_by' => $this->created_by,
				'fg_invoice.created_at' => $this->created_at
			]);
			$query->andFilterWhere(['like', 'invoice_no', $this->invoice_no])
						->andFilterWhere(['like', 'invoice_date', $this->invoice_date])
						->andFilterWhere(['like', 'contract', $this->contract])
						->andFilterWhere(['like', 'rec_person_regno', $this->rec_person_regno])
						->andFilterWhere(['like', 'driver', $this->driver])
						->andFilterWhere(['like', 'truck', $this->truck])
						->andFilterWhere(['like', 'sender', $this->sender]);
		}

		return $dataProvider;
	}
}