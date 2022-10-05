<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Uloc;

/**
 * UlocSearch represents the model behind the search form of `app\models\Uloc`.
 */
class UlocSearch extends Uloc
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'line_id', 'min_stock', 'max_stock', 'actual_stock', 'status',  'created_by', 'updated_by'], 'integer'],
            [['title', 'created_at', 'updated_at', 'description'], 'safe'],
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
        $query = Uloc::find()->joinWith(['line',
																					'createdBy',
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
            'line_id' => $this->line_id,
            'min_stock' => $this->min_stock,
            'max_stock' => $this->max_stock,
            'actual_stock' => $this->actual_stock,
            'uloc.status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
						->andFilterWhere(['>=', 'uloc.created_at', strtotime($this->created_at)])
						->andFilterWhere(['>=', 'uloc.updated_at', strtotime($this->updated_at)])
            ->andFilterWhere(['like', 'description', $this->description]);

			if($mode == 'excel'){
				$file = Yii::createObject([
																		'class' => 'codemix\excelexport\ExcelFile',
																		'sheets' => [
																			'Ulocs' => [
																				'class' => 'codemix\excelexport\ActiveExcelSheet',
																				'query' => $query,
																				'attributes' => [
																					'id',
																					'title',
																					'description',
																					'min_stock',
																					'max_stock',
																					'actual_stock',
																					'line.line_name',
																					'statusText',
																					'createdBy.fullname',
																					'createdAtFormatted',
																					'updatedBy.fullname',
																					'updatedAtFormatted',
																				],
																				'titles' => [
																					7 => Yii::t('app', 'Status'),
																					8 => Yii::t('app', 'Created by'),
																					9 => Yii::t('app', 'Created at'),
																					10 => Yii::t('app', 'Updated by'),
																					11 => Yii::t('app', 'Updated at'),
																				],
																			],
																		]
																	]);
				return $file;
			}else{
				return $dataProvider;
			}
    }
}
