<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;


class SalesContractDetailSearch extends SalesContractDetail
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'delivery_term_id'], 'integer'],
            [['price','vat','excise'], 'number'],
            [['part_name', 'part_no', 'part_color', 'sales_contract_id', 'part_id'], 'safe'],
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
        $query = SalesContractDetail::find();
        $query->joinWith('part');
        $query->joinWith('deliveryTerm');
        $query->joinWith('salesContract');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
        
        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'delivery_term_id'    => $this->delivery_term_id,
            'price' => $this->price,
            'vat' => $this->vat,
            'excise' => $this->excise,
        ]);

        $query->andFilterWhere(['like', 'contract_no', $this->sales_contract_id])
              ->andFilterWhere(['like', 'part.part_color', $this->part_color])
              ->andFilterWhere(['like', 'part.part_name', $this->part_name])
              ->andFilterWhere(['like', 'part.part_no', $this->part_no]);

        $dataProvider->sort->attributes['delivery_term_id'] = [
            'asc' => ['delivery_term.name' => SORT_ASC],
            'desc' => ['delivery_term.name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['sales_contract_id'] = [
            'asc' => ['contract_no' => SORT_ASC],
            'desc' => ['contract_no' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['part_name'] = [
            'asc' => ['part.part_name' => SORT_ASC],
            'desc' => ['part.part_name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['part_no'] = [
            'asc' => ['part.part_no' => SORT_ASC],
            'desc' => ['part.part_no' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['part_color'] = [
            'asc' => ['part.part_color' => SORT_ASC],
            'desc' => ['part.part_color' => SORT_DESC],
        ];

        return $dataProvider;
    }
}
