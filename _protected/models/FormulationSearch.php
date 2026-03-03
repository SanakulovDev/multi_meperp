<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Formulation;

/**
 * FormulationSearch represents the model behind the search form of `app\models\Formulation`.
 */
class FormulationSearch extends Formulation
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'formulation_base_id', 'customer_id', 'order_no', 'ulock'], 'integer'],
            [['amount', 'act_rate'], 'number'],
            [['due_at', 'start_at', 'finish_at', 'grind', 'packages'], 'safe'],
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
        $query = Formulation::find();

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
            'formulation_base_id' => $this->formulation_base_id,
            'amount' => $this->amount,
            'customer_id' => $this->customer_id,
            'order_no' => $this->order_no,
            'ulock' => $this->ulock,
            'due_at' => $this->due_at,
            'start_at' => $this->start_at,
            'finish_at' => $this->finish_at,
            'act_rate' => $this->act_rate,
        ]);

        $query->andFilterWhere(['like', 'grind', $this->grind])
            ->andFilterWhere(['like', 'packages', $this->packages]);

        return $dataProvider;
    }
}
