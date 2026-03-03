<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\CurrencyRate;

/**
 * CurrencyRateSearch represents the model behind the search form of `app\models\CurrencyRate`.
 */
class CurrencyRateSearch extends CurrencyRate
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'currency_id', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
            [['rate_date'], 'safe'],
            [['uzs_value'], 'number'],
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
        $query = CurrencyRate::find();

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
            'rate_date' => $this->rate_date,
            'currency_id' => $this->currency_id,
            'uzs_value' => $this->uzs_value,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_by' => $this->updated_by,
            'updated_at' => $this->updated_at,
        ]);

        return $dataProvider;
    }
}
