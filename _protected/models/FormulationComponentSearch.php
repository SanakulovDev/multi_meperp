<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\FormulationComponent;

/**
 * FormulationComponentSearch represents the model behind the search form of `app\models\FormulationComponent`.
 */
class FormulationComponentSearch extends FormulationComponent
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'formulation_id', 'part_id'], 'integer'],
            [['std_value', 'actual_value'], 'number'],
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
        $query = FormulationComponent::find();

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
            'formulation_id' => $this->formulation_id,
            'part_id' => $this->part_id,
            'std_value' => $this->std_value,
            'actual_value' => $this->actual_value,
        ]);

        return $dataProvider;
    }
}
