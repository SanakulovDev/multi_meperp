<?php
	namespace app\models;

	use Yii;
	use yii\helpers\ArrayHelper;
	use yii\base\Model;
	use yii\data\ActiveDataProvider;
	use yii\db\Expression;

	/**
	 * FgInvoiceSearch represents the model behind the search form of `app\models\FgInvoice`.
	 */
	class FgInvoiceSearch extends FgInvoice{
		/**
		 * {@inheritdoc}
		 */
		public $contract_factory;
		public $date;
		public $mark_id;
		public function rules(){
			return [
				[['id', 'factory_id', 'customer_id', 'vat', 'excise', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
				[['invoice_no', 'invoice_date', 'contract', 'contract_date', 'rec_person_fullname', 'rec_person_regno', 'driver', 'truck', 'manager', 'account', 'sender', 'comment', 'confirmed_by'], 'safe'],
				[['contract_factory', 'date', 'mark_id'], 'safe']
			];	
		}

		/**
		 * {@inheritdoc}
		 */
		public function scenarios(){
			// bypass scenarios() implementation in the parent class
			return Model::scenarios();
		}

		/**
		 * Creates data provider instance with search query applied
		 * @param array $params
		 * @return ActiveDataProvider
		 */
		public function search($params, $mode = ''){
			$query = FgInvoice::find()->orderBy([
				'created_at' => SORT_DESC
			]);
			$query->with(['factory', 'customer', 'fgInvoiceWaybills', 'createdBy', 'updatedBy', 'confirmedBy']);
			// add conditions that should always apply here
			$dataProvider = new ActiveDataProvider(['query' => $query,]);
			$this->load($params);
			if(!$this->validate()){
				// uncomment the following line if you do not want to return any records when validation fails
				// $query->where('0=1');
				return $dataProvider;
			}
			// grid filtering conditions
			$query->andFilterWhere([
															 'id' => $this->id,
															 'factory_id' => $this->factory_id,
															 'invoice_date' => $this->invoice_date,
															 'customer_id' => $this->customer_id,
															 'created_at' => $this->created_at,
															 'created_by' => $this->created_by,
															 'updated_at' => $this->updated_at,
															 'updated_by' => $this->updated_by,
														 ]);
			$query->andFilterWhere(['like', 'invoice_no', $this->invoice_no])
						->andFilterWhere(['like', 'contract', $this->contract])
//						->andFilterWhere(['like', 'rec_person_fullname', $this->rec_person_fullname])
//						->andFilterWhere(['like', 'rec_person_regno', $this->rec_person_regno])
//						->andFilterWhere(['like', 'driver', $this->driver])
//						->andFilterWhere(['like', 'truck', $this->truck])
						->andFilterWhere(['like', 'manager', $this->manager])
						->andFilterWhere(['like', 'account', $this->account])
//						->andFilterWhere(['like', 'sender', $this->sender])
						->andFilterWhere(['like', 'comment', $this->comment]);
			// marka (part_color) bo'yicha filter
			if (!empty($this->mark_id)) {
				$subQuery = FgInvoiceDetail::find()
					->select('fg_invoice_detail.fg_invoice_id')
					->leftJoin('part', 'part.part_no = fg_invoice_detail.part_no');
				$this->applyMarkFilter($subQuery);
				$query->andWhere(['fg_invoice.id' => $subQuery]);
			}
			if(strlen($this->confirmed_by) > 0){
				switch($this->confirmed_by){
					case 0:
						$query->andFilterWhere(['is', 'confirmed_by', new Expression('null')]);
						break;
					case 1:
						$query->andFilterWhere(['>', 'confirmed_by', 0]);
						break;
				}
			}
//			echo "<pre>"; print_r($query->createCommand()->rawSql);echo "</pre>";
			if($mode == 'excel'){
				$query->joinWith(['factory', 'customer', 'fgInvoiceDetails2']);
				$file = Yii::createObject(
					[
						'class' => 'codemix\excelexport\ExcelFile',
						'sheets' => [
							'Parts' => [
								'class' => 'codemix\excelexport\ActiveExcelSheet',
								'query' => $query,
								'attributes' => [
									'id',
									'factory.name',
									'invoice_no',
									'invoice_date',
									'customer.name',
									'contract',
									'rec_person_fullname',
									'rec_person_regno',
									'driver',
									'truck',
									'manager',
									'account',
									'sender',
									'vat',
									'excise',
									'comment',
                  'fgInvoiceDetails2.part_name',
                  'fgInvoiceDetails2.qty',
									'confirmed_at',
									'confirmed_by',
									'createdBy.fullname',
									'createdAtFormatted',
									'updatedBy.fullname',
									'updatedAtFormatted',
								],
								'titles' => [
									'0' => Yii::t('app', 'ID'),
									'1' => Yii::t('app', 'Factory'),
									'2' => Yii::t('app', 'Invoice no'),
									'3' => Yii::t('app', 'Invoice Date'),
									'4' => Yii::t('app', 'Customer'),
									'5' => Yii::t('app', 'Sales contract'),
									'6' => Yii::t('app', 'Doverennost FIO'),
									'7' => Yii::t('app', 'Doverennost RegNo'),
									'8' => Yii::t('app', 'Driver'),
									'9' => Yii::t('app', 'Truck'),
									'10' => Yii::t('app', 'Manager'),
									'11' => Yii::t('app', 'Account'),
									'12' => Yii::t('app', 'Sender'),
									'13' => Yii::t('app', 'QQS % xisobida'),
									'14' => Yii::t('app', 'Аксиз налог % xisobida'),
									'15' => Yii::t('app', 'Comment'),
									'16' => Yii::t('app', 'Марка'),
									'17' => Yii::t('app', 'Количество'),
									'18' => Yii::t('app', 'Confirmed at'),
									'19' => Yii::t('app', 'Confirmed by'),
									'20' => Yii::t('app', 'Created by'),
									'21' => Yii::t('app', 'Created at'),
									'22' => Yii::t('app', 'Updated by'),
									'23' => Yii::t('app', 'Updated at'),
								],
							],
						]
					]);
				return $file;
			}else{
				return $dataProvider;
			}
		}

		public function getCustomerFilterOptions(): array
		{
			$query = Customer::find()
				->alias('customer')
				->select(['customer.id', 'customer.name'])
				->innerJoin('fg_invoice', 'fg_invoice.customer_id = customer.id')
				->groupBy(['customer.id', 'customer.name'])
				->orderBy(['customer.name' => SORT_ASC]);

			if (!empty($this->mark_id)) {
				$query
					->innerJoin('fg_invoice_detail', 'fg_invoice_detail.fg_invoice_id = fg_invoice.id')
					->innerJoin('part', 'part.part_no = fg_invoice_detail.part_no');
				$this->applyMarkFilter($query);
			}

			return ArrayHelper::map($query->asArray()->all(), 'id', 'name');
		}

		public function getMarkFilterOptions(): array
		{
			$marks = FgInvoiceDetail::find()
				->select([
					'part_name' => 'fg_invoice_detail.part_name',
					'part_color' => 'part.part_color',
				])
				->innerJoin('part', 'part.part_no = fg_invoice_detail.part_no')
				->innerJoin('fg_invoice', 'fg_invoice.id = fg_invoice_detail.fg_invoice_id')
				->where(['not', ['part.part_color' => null]])
				->andWhere("TRIM(part.part_color) <> ''")
				->groupBy(['fg_invoice_detail.part_name', 'part.part_color'])
				->orderBy(['fg_invoice_detail.part_name' => SORT_ASC, 'part.part_color' => SORT_ASC])
				->asArray()
				->all();

			$options = [];
			foreach ($marks as $mark) {
				$value = self::buildMarkLabel($mark['part_name'] ?? '', $mark['part_color'] ?? '');
				if ($value === '') {
					continue;
				}
				$options[$value] = self::buildMarkPreview($value);
			}

			return $options;
		}

		private function applyMarkFilter($query): void
		{
			$mark = trim((string)$this->mark_id);
			$query->andWhere($this->getMarkExpression().' = :mark', [':mark' => $mark]);
		}

		private function getMarkExpression(): string
		{
			return "TRIM(CONCAT_WS(' ', NULLIF(TRIM(fg_invoice_detail.part_name), ''), NULLIF(TRIM(part.part_color), '')))";
		}

		public static function buildMarkLabel(string $partName, string $partColor): string
		{
			return trim(trim($partName) . ' ' . trim($partColor));
		}

		public static function buildMarkPreview(string $label): string
		{
			return mb_substr($label, 0, 30);
		}



		// contract factories search
		public function searchContractFactory($params)
		{
			$year = date('Y');
			if(isset($params['FgInvoiceSearch']['date'])){
				$year = $params['FgInvoiceSearch']['date'];
			}
			$contract_factory = null;
			if(isset($params['FgInvoiceSearch']['contract_factory'])){
				$contract_factory = $params['FgInvoiceSearch']['contract_factory'];
			}
			$query = FgInvoice::find()->select(['contract', 'customer_id'])
				->joinWith(['fgInvoiceWaybills'])
				->leftJoin('waybill', 'waybill.id = fg_invoice_waybill.waybill_id')
				->where(['not', ['waybill.waybill_no' => null]])
				->andWhere(['DATE_FORMAT(waybill.waybill_date, \'%Y\')' => $year])
				->groupBy(['customer_id','contract'])
				->orderBy(['customer_id' => SORT_ASC]);
			if($contract_factory != null){
				$query->andFilterWhere(['waybill.waybill_no' => $contract_factory]);
			}
			// vd($query->createCommand()->rawSql);
			// $query = FgInvoice::find()->select(['distinct(contract)', 'customer.name customer_name', 'customer_id'])
			// ->orderBy(['contract' => SORT_ASC])
			// ->joinWith(['customer', 'fgInvoiceWaybills'])
			// ->leftJoin('waybill', 'waybill.id = fg_invoice_waybill.waybill_id');
			// ->andWhere(['1'=>1]);
			$dataProvider = new ActiveDataProvider([
				'query' => $query,
			]);
			$this->load($params);
			
			if(!$this->validate()){
				// uncomment the following line if you do not want to return any records when validation fails
				// $query->where('0=1');
				return $dataProvider;
			}
			$query->andFilterWhere(['like', 'contract', $this->contract]);
			$query->andFilterWhere(['customer_id'=> $this->customer_id]);

			return $dataProvider;
		}
	}
