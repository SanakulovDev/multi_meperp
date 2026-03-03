<?php
	namespace app\models;

	use Yii;
	use yii\base\Model;
	use yii\data\ActiveDataProvider;

	/**
	 * GtdSearch represents the model behind the search form of `app\models\Gtd`.
	 */
	class GtdSearch extends Gtd{
		/**
		 * {@inheritdoc}
		 */
		public function rules(){
			return [
				[['id', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
				[['gtd_no', 'gtd_dt', 'post_no'], 'safe'],
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
			$query = Gtd::find();
			// add conditions that should always apply here
			$dataProvider = new ActiveDataProvider(['query' => $query,]);
			$this->load($params);
			if(!$this->validate()){
				return $dataProvider;
			}
			// grid filtering conditions
			$query->andFilterWhere([
															 'id' => $this->id,
															 'gtd_dt' => $this->gtd_dt,
															 'created_by' => $this->created_by,
															 'created_at' => $this->created_at,
															 'updated_by' => $this->updated_by,
															 'updated_at' => $this->updated_at,
														 ]);
			$query->andFilterWhere(['like', 'gtd_no', $this->gtd_no])
						->andFilterWhere(['like', 'post_no', $this->post_no]);
      if($mode == 'excel'){
        $query = GtdInvoice::find()->joinWith(['gtd','invoice','createdBy']);
        // conditions
        $query->andFilterWhere([
          'gtd_id' => $this->id,
          'gtd.gtd_dt' => $this->gtd_dt,
        ]);
        $query->andFilterWhere(['like', 'gtd.gtd_no', $this->gtd_no])
          ->andFilterWhere(['like', 'gtd.post_no', $this->post_no]);

        $file = Yii::createObject([
          'class' => 'codemix\excelexport\ExcelFile',
          'sheets' => [
            'Gtds' => [
              'class' => 'codemix\excelexport\ActiveExcelSheet',
              'query' => $query,
              'attributes' => [
                'gtd.gtd_no',
                'gtd.gtd_dt',
                'gtd.post_no',
                'invoice.invoice_no',
                'amount',
                'createdBy.fullname',
                'createdAtFormatted',
              ],
              'titles' => [
                5 => Yii::t('app', 'Created by'),
                6 => Yii::t('app', 'Created at'),
              ],
            ],
          ]
        ]);
        return $file;
      } else {
        return $dataProvider;
      }
		}
	}
