<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Api;

/**
 * ApiSearch represents the model behind the search form of `app\models\Api`.
 */
class ApiSearch extends Api {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'created_by', 'created_at'], 'integer'],
            [['inventory_date', 'stock_date'], 'safe'],
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
    public function search($params, $mode = '') {
        $query = Api::find();

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
            'inventory_date' => $this->inventory_date,
            'stock_date' => $this->stock_date,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
        ]);

        if ($mode == 'excel') {


            $apis = $query->with('apiDetails')->all();
            $arrFile = [];
            $i = 0;
            foreach ($apis as $api) {
                foreach ($api->apiDetails as $detail) {
                    unset($tmpArray);
                    $tmpArray['num'] = ++$i;
                    $tmpArray['api_id'] = $detail->api->id;
                    $tmpArray['inventory_date'] = $detail->api->inventory_date;
                    $tmpArray['stock_date'] = $detail->api->stock_date;

                    $tmpArray['part_number'] = $detail->part->part_no;
                    $tmpArray['part_name'] = $detail->part->part_name;
                    $tmpArray['part_color'] = $detail->part->part_color;
                    $tmpArray['unit'] = $detail->part->unit->unit_value;
                    $tmpArray['inventory_qty'] = $detail->inventory_qty;
                    $tmpArray['stock_qty'] = $detail->stock_qty;

                    $tmpArray['created_by'] = $detail->api->createdBy->fullname;
                    $tmpArray['created_at'] = $detail->api->createdAtFormatted;

                    $arrFile[] = $tmpArray;
                }
            }

            if (empty($arrFile))
                $query->orderBy(['id' => SORT_DESC]);
            $file = \Yii::createObject([
                        'class' => 'codemix\excelexport\ExcelFile',
                        'sheets' => [
                            'api' => [
                                'data' => $arrFile,
                                'titles' => [
                                    0 => Yii::t('app', '#'),
                                    1 => Yii::t('app', 'Inventory ID'),
                                    2 => Yii::t('app', 'Inventory date'),
                                    3 => Yii::t('app', 'Stock date'),
                                    5 => Yii::t('app', 'Part number'),
                                    6 => Yii::t('app', 'Part name'),
                                    4 => Yii::t('app', 'Part color'),
                                    7 => Yii::t('app', 'Unit'),
                                    8 => Yii::t('app', 'Inventory qty'),
                                    9 => Yii::t('app', 'Stock qty'),
                                    10 => Yii::t('app', 'Created by'),
                                    11 => Yii::t('app', 'Created at'),
                                ],
                            ]
                        ]
            ]);
            return $file;
        }else {
            return $dataProvider;
        }
    }

}
