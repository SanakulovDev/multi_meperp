<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * HistoryDocumentSearch represents the model behind the search form of `app\models\HistoryDocument`.
 */
class HistoryDocumentSearch extends HistoryDocument
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'his_user_id', 'document_id', 'document_type_id', 'from_warehouse_id', 'to_warehouse_id', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
            [['his_action', 'his_date', 'docnum', 'docdate', 'series', 'status'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
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
        $query = HistoryDocument::find();

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
            'his_user_id' => $this->his_user_id,
            'his_date' => $this->his_date,
            'document_id' => $this->document_id,
            'docdate' => $this->docdate,
            'document_type_id' => $this->document_type_id,
            'from_warehouse_id' => $this->from_warehouse_id,
            'to_warehouse_id' => $this->to_warehouse_id,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_by' => $this->updated_by,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'his_action', $this->his_action])
            ->andFilterWhere(['like', 'docnum', $this->docnum])
            ->andFilterWhere(['like', 'series', $this->series])
            ->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }
}
