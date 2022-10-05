<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\PackLevel;

/**
 * PackLevelSearch represents the model behind the search form of `app\models\PackLevel`.
 */
class PackLevelSearch extends PackLevel
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'quantity', 'version', 'level', 'created_by', 'created_at', 'updated_by'], 'integer'],
            [['quantity'], 'number'],
            [['pack_id', 'part_id', 'in_pack_id', 'updated_at'], 'safe'],
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
    public function search($params, $mode='')
    {
        $query = PackLevel::find()->joinWith(['inPack', 'createdBy', 'part',
            'pack' => function($query){
                $query->from(['mainPack' => Pack::tableName()]);
            },
            'updatedBy' => function($query){
                $query->from(['u2' => User::tableName()]);
            }
        ]);

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
            'version' => $this->version,
            'level' => $this->level,
            'part_id' => $this->part_id,
            'pack_id' => $this->pack_id,
            'in_pack_id' => $this->in_pack_id,
            'pack_level.created_by' => $this->created_by,
            'pack_level.created_at' => $this->created_at,
            'pack_level.updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['>=', 'pack_level.updated_at', strtotime($this->updated_at)])
            ->andFilterWhere(['>=', 'quantity', $this->quantity]);

        if($mode == 'excel'){
            $file = \Yii::createObject([
                'class' => 'codemix\excelexport\ExcelFile',
                'sheets' => [
                    'Packing' => [
                        'class' => 'codemix\excelexport\ActiveExcelSheet',
                        'query' => $query,
                        'attributes' => [
                            'id',
                            'part.part_no',
                            'pack.code',
                            'inPack.code',
                            'quantity',
                            'version',
                            'createdBy.fullname',
                            'createdAtFormatted',
                            'updatedBy.fullname',
                            'updatedAtFormatted',
                        ],
                        'titles' => [
                            2 => Yii::t('app', 'Pack'),
                            3 => Yii::t('app', 'In Pack'),
                            6 => Yii::t('app', 'Created by'),
                            7 => Yii::t('app', 'Created at'),
                            8 => Yii::t('app', 'Updated by'),
                            9 => Yii::t('app', 'Updated at')
                        ],
                    ],

                ]
            ]);
            return  $file;
        }else{
            return $dataProvider;
        }
    }
}
