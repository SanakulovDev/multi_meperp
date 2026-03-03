<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\PechatProduct;

/**
 * PechatProductSearch represents the model behind the search form of `app\models\PechatProduct`.
 */
class PechatProductSearch extends PechatProduct
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'part_id', 'weight_netto', 'weight_brutto', 'color_id'], 'integer'],
            [['number_lot', 'date', 'comment', 'line'], 'safe'],
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
        $query = PechatProduct::find();

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
            'color_id' => $this->color_id,
            'line' => $this->line,
            'date' => $this->date,
            'weight_netto' => $this->weight_netto,
            'weight_brutto' => $this->weight_brutto,
        ]);
        $query->andFilterWhere(['like', 'comment', $this->comment]);

        $query->andFilterWhere(['like', 'number_lot', $this->number_lot]);

        return $dataProvider;
    }
}
