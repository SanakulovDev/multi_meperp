<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ProductionRelease;

/**
 * ProductionReleaseSearch represents the model behind the search form of `app\models\ProductionRelease`.
 */
class ProductionReleaseSearch extends ProductionRelease
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'part_id', 'line', 'quantity'], 'integer'],
            [['part_name', 'pr_order_number', 'target_date', 'shift', 'time'], 'safe'],
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
    public function search($params)
    {
        $query = ProductionRelease::find();

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
            'part_id' => $this->part_id,
            'line' => $this->line,
            'target_date' => $this->target_date,
            'quantity' => $this->quantity,
        ]);

        $query->andFilterWhere(['like', 'pr_order_number', $this->pr_order_number])
            ->andFilterWhere(['like', 'shift', $this->shift])
            ->andFilterWhere(['like', 'time', $this->time]);

        return $dataProvider;
    }
}
