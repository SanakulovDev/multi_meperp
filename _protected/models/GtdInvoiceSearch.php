<?php
	namespace app\models;

	use Yii;
	use yii\base\Model;
	use yii\data\ActiveDataProvider;

	/**
	 * GtdInvoiceSearch represents the model behind the search form of `app\models\GtdInvoice`.
	 */
	class GtdInvoiceSearch extends GtdInvoice{
		/**
		 * {@inheritdoc}
		 */
		public function rules(){
			return [
				[['id', 'gtd_id', 'invoice_id', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
				[['amount'], 'number'],
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
			$query = GtdInvoice::find();
			// add conditions that should always apply here
			$dataProvider = new ActiveDataProvider(['query' => $query,]);
			$this->load($params);
			if(!$this->validate()){
				return $dataProvider;
			}
			// grid filtering conditions
			$query->joinWith(['invoice']);
			$query->andFilterWhere(
				[
					'id' => $this->id,
					'gtd_id' => $this->gtd_id,
					'amount' => $this->amount,
					'created_by' => $this->created_by,
					'created_at' => $this->created_at,
					'updated_by' => $this->updated_by,
					'updated_at' => $this->updated_at,
				]);
			$query->andFilterWhere(['like', 'invoice.invoice_no', $this->invoice_id]);

			if($mode == 'excel'){
//				$gtd_invoices = $query->with(['gtd', 'invoice'])->all();
				$gtd_invoices = $query->all();
				$arrFile = [];
				foreach($gtd_invoices as $gtd_invoice){
					unset($tmpArray);
					$tmpArray['gtd_no'] = $gtd_invoice->gtd->gtd_no;
					$tmpArray['gtd_dt'] = $gtd_invoice->gtd->gtd_dt;
					$tmpArray['post_no'] = $gtd_invoice->gtd->post_no;
					$tmpArray['invoice_no'] = $gtd_invoice->invoice->invoice_no;
					$tmpArray['amount'] = $gtd_invoice->amount;
					$tmpArray['created_by'] = $gtd_invoice->createdBy->fullname;
					$tmpArray['created_at'] = $gtd_invoice->createdAtFormatted;
					$tmpArray['updated_by'] = $gtd_invoice->updatedBy->fullname ?? null;
					$tmpArray['updated_at'] = $gtd_invoice->updatedAtFormatted;
					$arrFile[] = $tmpArray;
				}
				if(empty($arrFile))
					$query->orderBy(['id' => SORT_DESC]);
				$file = Yii::createObject(
					[
						'class' => 'codemix\excelexport\ExcelFile',
						'sheets' => [
							'GTD' => [
								'data' => $arrFile,
								'titles' => [
									0 => Yii::t('app', 'GTD no'),
									1 => Yii::t('app', 'GTD date'),
									2 => Yii::t('app', 'Post no'),
									3 => Yii::t('app', 'Invoice'),
									4 => Yii::t('app', 'Amount'),
									5 => Yii::t('app', 'Created by'),
									6 => Yii::t('app', 'Created at'),
									7 => Yii::t('app', 'Updated by'),
									8 => Yii::t('app', 'Updated at'),
								],
							],
						]
					]);
				return $file;
			}else{
				return $dataProvider;
			}
		}
	}
