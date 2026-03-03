<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ProductionPower;

/**
 * ProductionPowerSearch represents the model behind the search form of `app\models\ProductionPower`.
 */
class ProductionPowerSearch extends ProductionPower
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'part_id', 'line', 'shift', 'unitId'], 'integer'],
            [['part_name', 'test_pr', 'target_date', 'plan_power', 'max_power', 'special', 'time'], 'safe'],
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
        $query = ProductionPower::find();

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
            'target_date' => $this->target_date,
            'line' => $this->line,
            'test_pr' => $this->test_pr,
            'unitId' => $this->unitId,
            'plan_power' => $this->plan_power,
            'max_power' => $this->max_power,
            'time' => $this->time,
        ]);

        $query->andFilterWhere(['like', 'part_name', $this->part_name])
            ->andFilterWhere(['like', 'special', $this->special]);

        return $dataProvider;
    }
}
