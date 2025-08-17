<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * LineStopSearch represents the model behind the search form of `app\models\LineStop`.
 * @property int $type
 * @property string $auth_item_name
 */
class LineStopSearch extends LineStop
{
    public $type;
    public $warehouse;
    public $auth_item_name;
    public $production_date;
    public $shift;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'part_production_monitor_id', 'line_stop_reason_id', 'status', 'created_by', 'created_at', 'updated_by', 'updated_at', 'elapsed_minutes', 'type', 'bypass', 'shift'], 'integer'],
            [['fix_list', 'start_time', 'end_time', 'remark', 'rejection_remark', 'auth_item_name', 'warehouse', 'production_date'], 'safe'],
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
        $query = LineStop::find()->joinWith(['lineStopReason','partProductionMonitor.productionMonitor.warehouse']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['created_at' => SORT_DESC]],
        ]);

        $dataProvider->sort->attributes['warehouse'] = [
          'asc' => ['warehouse.name' => SORT_ASC],
          'desc' => ['warehouse.name' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'part_production_monitor_id' => $this->production_date ? null : $this->part_production_monitor_id,
            'elapsed_minutes' => $this->elapsed_minutes,
            'line_stop_reason_id' => $this->line_stop_reason_id,
            'status' => $this->status,
            'production_date' => $this->production_date,
            'shift' => $this->shift,
            'line_stop_reason.type' => $this->type,
            'line_stop_reason.auth_item_name' => $this->auth_item_name,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_by' => $this->updated_by,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'remark', $this->remark])
            ->andFilterWhere(['like', 'warehouse.name', $this->warehouse])
            ->andFilterWhere(['>=', 'line_stop.start_time', $this->start_time])
            ->andFilterWhere(['<=', 'line_stop.end_time', $this->end_time])
            ->andFilterWhere(['like', 'line_stop.fix_list', $this->fix_list])
            ->andFilterWhere(['like', 'rejection_remark', $this->rejection_remark]);

        return $dataProvider;
    }
}
