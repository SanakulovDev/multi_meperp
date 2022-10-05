<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\OemPlan;
use Yii;

/**
 * OemPlanSearch represents the model behind the search form of `app\models\OemPlan`.
 */
class OemPlanSearch extends OemPlan {
	/**
	 * {@inheritdoc}
	 */
	public function rules() {
		return [
			[['id', 'model_id', 'quantity'], 'integer'],
			[['target_date'], 'safe'],
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
		$query = OemPlan::find()->joinWith('model');

		// add conditions that should always apply here

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'sort' => ['defaultOrder' => ['target_date' => SORT_DESC]]
		]);

		$this->load($params);

		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			return $dataProvider;
		}

		// grid filtering conditions
		$query->andFilterWhere([
			// 'id' => $this->id,
			'model_id' => $this->model_id,
			'quantity' => $this->quantity,
		]);
		$query->andFilterWhere(['like', 'target_date', $this->target_date]);

		if ($mode == 'excel') {
			$sql = $query->createCommand()->getRawSql();
			$condition = explode('WHERE', $sql);
			if (count($condition) > 1) {
				$condition = 'WHERE ' . $condition[1];
			} else {
				$condition = 'WHERE YEAR(target_date)=' . date('Y');
			}
			$sql = 'SELECT id, description FROM product_model WHERE id in (SELECT DISTINCT model_id FROM oem_plan ' . $condition . ')';
			$modelList = [];
			$models = Yii::$app->db->createCommand($sql)->queryAll();

			$excel_data = [];
			if ($models) {
				foreach ($models as $model) {
					$modelList[] = 'SUM(CASE WHEN model_id = ' . $model['id'] . '  THEN quantity END) \'' . $model['description'].'\'';
				}
				$query = 'SELECT target_date,' . implode(',', $modelList) . " FROM oem_plan $condition GROUP BY target_date ORDER BY target_date";
				$data = Yii::$app->db->createCommand($query)->queryAll();
				$q = 0;

				foreach ($data as $index => $record) {
					$excel_data[$q]['target_date'] = $record['target_date'];
					foreach ($models as $model) {
						$excel_data[$q][$model['description']] = $record[$model['description']];
					}
					$q++;
				}
			}

			$titles = [Yii::t('app', 'Date')];
			foreach ($models as $model) {
				$titles[] = $model['description'];
			}

			$file = Yii::createObject(
				[
					'class' => 'codemix\excelexport\ExcelFile',
					'sheets' => [
						'Plan' => [
							'data' => $excel_data,
							'titles' => $titles,
						],
					]
				]
			);

			return $file;
		} else {
			return $dataProvider;
		}
	}
}
