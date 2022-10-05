<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ApiDetail;

/**
 * ApiDetailSearch represents the model behind the search form of `app\models\ApiDetail`.
 */
class ApiDetailSearch extends ApiDetail
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'api_id', 'part_id','uom'], 'integer'],
            [['inventory_qty', 'stock_qty'], 'number'],
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
        
        $query = ApiDetail::find();
        $query->joinWith('api');
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
            'api_detail.id' => $this->id,
            'api_detail.api_id' => $this->api_id,
            'api_detail.part_id' => $this->part_id,
            'api_detail.inventory_qty' => $this->inventory_qty,
            'api_detail.stock_qty' => $this->stock_qty,
            'unit.id' => $this->uom,
        ]);
        
        $dataProvider->sort->attributes['part_id'] = [
            'asc' => ['part.part_no' => SORT_ASC],
            'desc' => ['part.part_no' => SORT_DESC],
        ];
        
        $dataProvider->sort->attributes['uom'] = [
            'asc' => ['unit.unit_value' => SORT_ASC],
            'desc' => ['unit.unit_value' => SORT_DESC],
        ];
        
        return $dataProvider;
    }
}
