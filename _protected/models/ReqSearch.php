<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ReqSearch represents the model behind the search form of `app\models\Req`.
 */
class ReqSearch extends Req {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'part_id'], 'integer'],
            [['whbal', 'linebal', 'arrive'], 'number'],
            [['calc_at', 'part_color', 'part_name', 'unit'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios() {
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
    public function search($params){
        $query = Req::find();

        $query->joinWith('part.unit');

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
            'whbal' => $this->whbal,
            'linebal' => $this->linebal,
            'arrive' => $this->arrive,
            'calc_at' => $this->calc_at,
            'unit.id' => $this->unit,
        ]);

        $query->andFilterWhere(['like', 'part.part_color', $this->part_color])
                ->andFilterWhere(['like', 'part.part_no', $this->part_id])
                ->andFilterWhere(['like', 'part.part_name', $this->part_name])
        ;
        
        $dataProvider->sort->attributes['part_color'] = [
            'asc' => ['part.part_color' => SORT_ASC],
            'desc' => ['part.part_color' => SORT_DESC],
        ];    
        
        $dataProvider->sort->attributes['part_name'] = [
            'asc' => ['part.part_name' => SORT_ASC],
            'desc' => ['part.part_name' => SORT_DESC],
        ]; 

        $dataProvider->sort->attributes['unit'] = [
            'asc' => ['unit.unit_value' => SORT_ASC],
            'desc' => ['unit.unit_value' => SORT_DESC],
        ]; 

        
        
        return $dataProvider;
        
        
        
    }

}
