<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\InvoicePartProblem;

/**
 * InvoicePartProblemSearch represents the model behind the search form of `app\models\InvoicePartProblem`.
 */
class InvoicePartProblemSearch extends InvoicePartProblem
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'inv_detail_id', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
            [['part_order_no', 'contract_no'], 'safe'],
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
        $query = InvoicePartProblem::find();

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
            'inv_detail_id' => $this->inv_detail_id,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_by' => $this->updated_by,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'part_order_no', $this->part_order_no])
            ->andFilterWhere(['like', 'contract_no', $this->contract_no]);

        return $dataProvider;
    }
}
