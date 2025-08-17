<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ContractSearch represents the model behind the search form of `app\models\Contract`.
 */
class ContractSearch extends Contract
{
	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['id', 'supplier_id', 'buyer_id', 'payment_term_id', 'contract_subject_id', 'currency_id', 'contract_source_id', 'status', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
			[['contract_no', 'contract_date', 'expiry_date'], 'safe'],
			[['contract_amount'], 'number'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function scenarios()
	{
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
	public function search($params, $mode = '')
	{
		$query = Contract::find()
		->with(['supplier', 'buyer','paymentTerm', 'contractSubject', 'currency', 'contractSource', 'createdBy', 'updatedBy'])->orderBy([
			'id' => SORT_DESC
		  ]);
		// add conditions that should always apply here
		$dataProvider = new ActiveDataProvider(['query' => $query,]);
		$dataProvider->pagination->pageSize = 20;
		$this->load($params);
		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			return $dataProvider;
		}
		// grid filtering conditions
		$query->andFilterWhere(
			[
				'id'                  => $this->id,
				'supplier_id'         => $this->supplier_id,
				'contract_date'       => $this->contract_date,
				'expiry_date'         => $this->expiry_date,
				'buyer_id'            => $this->buyer_id,
				'payment_term_id'     => $this->payment_term_id,
				'contract_amount'     => $this->contract_amount,
				'contract_subject_id' => $this->contract_subject_id,
				'currency_id'         => $this->currency_id,
				'contract_source_id'  => $this->contract_source_id,
				'status'              => $this->status,
				'created_by'          => $this->created_by,
				'created_at'          => $this->created_at,
				'updated_by'          => $this->updated_by,
				'updated_at'          => $this->updated_at,
			]
		);
		$query->andFilterWhere(['like', 'contract_no', $this->contract_no]);

		if ($mode == 'excel') {


			$contracts = $query->with('contractDetails')->all();
			$arrFile = [];
			$i = 0;
			foreach ($contracts as $contract) {
				foreach ($contract->contractDetails as $detail) {
					unset($tmpArray);
					$tmpArray['num'] = ++$i;
					$tmpArray['contract_no'] = $detail->contract->contract_no;
					$tmpArray['supplier'] = $detail->contract->supplier->name;
					$tmpArray['contract_date'] = $detail->contract->contractDateFormatted;
					$tmpArray['expiry_date'] = $detail->contract->expiryDateFormatted;
					$tmpArray['buyer'] = $detail->contract->buyer->fullname;
					$tmpArray['payment_term'] = $detail->contract->paymentTerm->name;
					$tmpArray['delivery_term'] = $detail->deliveryTerm->name ?? null;
					$tmpArray['contract_amount'] = $detail->contract->contract_amount;
					$tmpArray['contract_subject'] = $detail->contract->contractSubject->name;
					$tmpArray['currency'] = $detail->contract->currency->code;
					$tmpArray['contract_source'] = $detail->contract->contractSource->name;
					$tmpArray['status'] = $detail->contract->statusText;


					$tmpArray['part_number'] = $detail->part->part_no;
					$tmpArray['part_name'] = $detail->part->part_name;
					$tmpArray['part_color'] = $detail->part->part_color;
					$tmpArray['unit'] = $detail->part->unit->unit_value;
					$tmpArray['price'] = $detail->price;
					$tmpArray['is_primary_price'] = $detail->isPrimaryPriceText;
					$tmpArray['cnfea'] = $detail->cnfea;
					$tmpArray['weekly_capacity'] = $detail->weekly_capacity;

					$tmpArray['created_by'] = $detail->contract->createdBy->fullname;
					$tmpArray['created_at'] = $detail->contract->createdAtFormatted;
					$tmpArray['updated_by'] = $detail->contract->updatedBy->fullname ?? null;
					$tmpArray['updated_at'] = $detail->contract->updatedAtFormatted ?? null;

					$arrFile[] = $tmpArray;
				}
			}
			//            echo "<pre>";
			//            print_r($arrFile);
			//            echo "</pre>";
			//            die;
			if (empty($arrFile))
				$query->orderBy(['id' => SORT_DESC]);
			$file = \Yii::createObject([
				'class' => 'codemix\excelexport\ExcelFile',
				'sheets' => [
					'contract' => [
						'data' => $arrFile,

						'titles' => [
							0 => Yii::t('app', '#'),
							1 => Yii::t('app', 'Contract no'),
							2 => Yii::t('app', 'Supplier'),
							3 => Yii::t('app', 'Contract date'),
							4 => Yii::t('app', 'Expiry date'),
							5 => Yii::t('app', 'Buyer'),
							6 => Yii::t('app', 'Payment term'),
							7 => Yii::t('app', 'Delivery term'),
							8 => Yii::t('app', 'Contract amount'),
							9 => Yii::t('app', 'Contract subject'),
							10 => Yii::t('app', 'Currency'),
							11 => Yii::t('app', 'Contract source'),
							12 => Yii::t('app', 'Status'),

							14 => Yii::t('app', 'Part number'),
							15 => Yii::t('app', 'Part name'),
							13 => Yii::t('app', 'Part color'),
							16 => Yii::t('app', 'Unit'),
							17 => Yii::t('app', 'Price'),
							18 => Yii::t('app', 'Primary price'),
							19 => Yii::t('app', 'CNFEA Code'),
							20 => Yii::t('app', 'Weekly capacity'),

							21 => Yii::t('app', 'Created by'),
							22 => Yii::t('app', 'Created at'),
							23 => Yii::t('app', 'Updated by'),
							24 => Yii::t('app', 'Updated at'),



						],
					]
				]
			]);
			return  $file;
		} else {
			return $dataProvider;
		}
	}
}
