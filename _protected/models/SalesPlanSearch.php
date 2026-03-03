<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\SalesPlan;

/**
 * SalesPlanSearch represents the model behind the search form of `app\models\SalesPlan`.
 */
class SalesPlanSearch extends SalesPlan
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'target_qty', 'status'], 'integer'],
            [['target_date', 'partMarkId', 'partColorId', 'part_id', 'customer_id',], 'safe'],
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
        $query = SalesPlan::find()->joinWith(['customer', 'part']);

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => [
                'defaultOrder' => [
                    'target_date' => SORT_DESC,
                ],
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }
        if(empty($this->status)) {
            $this->status = 1;
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'id'            => $this->id,
            'target_qty'    => $this->target_qty,
            'sales_plan.status'        => $this->status,
        ]);
        // vd($this);

        $query->andFilterWhere(['part.id' => $this->part_id]);
        $query->andFilterWhere(['customer.id'=> $this->customer_id]);
        $query->andFilterWhere(['like', 'target_date', $this->target_date]);

        // vd($query->createCommand()->rawSql);
        return $dataProvider;
    }
}
