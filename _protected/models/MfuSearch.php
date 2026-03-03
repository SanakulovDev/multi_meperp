<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * MfuSearch represents the model behind the search form of `app\models\Mfu`.
 */
class MfuSearch extends Mfu
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'part_id', 'ship_mode_id', 'contract_source_id', 'constraint', 'consolidation_type_id', 'created_by', 'created_at', 'updated_by', 'updated_at','moq'], 'integer'],
            [['average', 'capacity', 'transit_time', 'bank'], 'number'],
            [['mfu_code'], 'safe'],
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
        $query = Mfu::find();

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
            'average' => $this->average,
            'capacity' => $this->capacity,
            'transit_time' => $this->transit_time,
            'ship_mode_id' => $this->ship_mode_id,
            'contract_source_id' => $this->contract_source_id,
            'bank' => $this->bank,
            'constraint' => $this->constraint,
            'consolidation_type_id' => $this->consolidation_type_id,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_by' => $this->updated_by,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'mfu_code', $this->mfu_code]);

        return $dataProvider;
    }
}
