<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\FormulationBase;

/**
 * FormulationBaseSearch represents the model behind the search form of `app\models\FormulationBase`.
 */
class FormulationBaseSearch extends FormulationBase
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'part_id', 'version', 'status'], 'integer'],
            [['pack', 'std_rate'], 'number'],
            [['items', 'specifications', 'instructions'], 'safe'],
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
        $query = FormulationBase::find();

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
            'pack' => $this->pack,
            'version' => $this->version,
            'status' => $this->status,
            'std_rate' => $this->std_rate,
        ]);

        $query->andFilterWhere(['like', 'items', $this->items])
            ->andFilterWhere(['like', 'specifications', $this->specifications])
            ->andFilterWhere(['like', 'instructions', $this->instructions]);

        return $dataProvider;
    }
}
