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
            [['id', 'target_qty'], 'integer'],
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

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'target_date' => $this->target_date,
            'target_qty' => $this->target_qty,
        ])->all();

        $query->andFilterWhere(['OR',
                              ['like', 'part.part_no', $this->part_id],
                              ['like', 'part.part_name', $this->part_id],
                              ['like', 'part.part_color', $this->part_id]
                            ])
          ->andFilterWhere(['like', 'customer.name', $this->customer_id]);

        return $dataProvider;
    }
}
