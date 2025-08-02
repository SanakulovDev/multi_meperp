<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Waybill;

/**
 * WaybillSearch represents the model behind the search form of `app\models\Waybill`.
 */
class WaybillSearch extends Waybill
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'factory_id', 'created_at', 'updated_at'], 'integer'],
            [['waybill_no', 'waybill_date', 'asn', 'created_by', 'updated_by', 'driver', 'truck', 'manager', 'account', 'sender'], 'safe'],
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
        $query = Waybill::find()->orderBy(['id' => SORT_DESC])->joinWith(['factory', 'createdBy',
            'updatedBy' => function($query) {
                $query->from(['u2' => User::tableName()]);
            }]);

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
            'factory_id' => $this->factory_id,
            'waybill_date' => $this->waybill_date,
            'created_at' => $this->created_at,
            //'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'waybill_no', $this->waybill_no])
            ->andFilterWhere(['like', 'asn', $this->asn])
            ->andFilterWhere(['like', 'driver', $this->driver])
            ->andFilterWhere(['like', 'truck', $this->truck])
            ->andFilterWhere(['like', 'manager', $this->manager])
            ->andFilterWhere(['like', 'user.fullname', $this->created_by])
            ->andFilterWhere(['like', 'account', $this->account])
            ->andFilterWhere(['like', 'sender', $this->sender]);

        return $dataProvider;
    }
}
