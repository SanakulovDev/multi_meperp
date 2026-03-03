<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Pack;

/**
 * PackSearch represents the model behind the search form of `app\models\Pack`.
 */
class PackSearch extends Pack
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'created_by', 'created_at', 'updated_by', 'updated_at', 'level'], 'integer'],
            [['quantity', 'weight', 'length', 'width', 'height', 'thickness'], 'number'],
            [['code', 'description', 'construction'], 'safe'],
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
    public function search($params, $mode = '')
    {
        $query = Pack::find();

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
            'weight' => $this->weight,
            'length' => $this->length,
            'width' => $this->width,
            'thickness' => $this->thickness,
            'height' => $this->height,
            'level' => $this->level,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_by' => $this->updated_by,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'construction', $this->construction])
            ->andFilterWhere(['>=', 'quantity', $this->quantity])
            ->andFilterWhere(['like', 'description', $this->description]);

        if ($mode == 'excel') {
            $file = \Yii::createObject([
                'class' => 'codemix\excelexport\ExcelFile',
                'sheets' => [
                    'Packing' => [
                        'class' => 'codemix\excelexport\ActiveExcelSheet',
                        'query' => $query,
                        'attributes' => [
                            'id',
                            'code',
                            'description',
                            'construction',
                            'length',
                            'width',
                            'height',
                            'weight',
                            'thickness',
                            'level',
                            'quantity',
                            'createdBy.fullname',
                            'createdAtFormatted',
                            'updatedBy.fullname',
                            'updatedAtFormatted',
                        ],
                        'titles' => [
                            11 => Yii::t('app', 'Created by'),
                            12 => Yii::t('app', 'Created at'),
                            13 => Yii::t('app', 'Updated by'),
                            14 => Yii::t('app', 'Updated at')
                        ],
                    ],

                ]
            ]);
            return  $file;
        } else {
            return $dataProvider;
        }
    }
}
