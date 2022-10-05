<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\CoverageBalance;

/**
 * CoverageBalanceSearch represents the model behind the search form of `app\models\CoverageBalance`.
 */
class CoverageBalanceSearch extends CoverageBalance
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'supplier_id', 'payment_term_id'], 'integer'],
            [['period','country'], 'safe'],
            [['debt', 'paid'], 'number'],
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
        $query = CoverageBalance::find();
        $query->joinWith('supplier');

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
            'supplier.country_code_id' => $this->country,
            'supplier_id' => $this->supplier_id,
            'payment_term_id' => $this->payment_term_id,
            'period' => $this->period,
            'debt' => $this->debt,
            'paid' => $this->paid,
        ]);

        return $dataProvider;
    }
}
