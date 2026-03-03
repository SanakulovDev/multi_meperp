<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Line;

/**
 * LineSearch represents the model behind the search form of `app\models\Line`.
 */
class LineSearch extends Line
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'parent_id', 'factory_id', 'status',  'created_by', 'updated_by'], 'integer'],
            [['line_name', 'description', 'created_at', 'updated_at'], 'safe'],
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
        $query = Line::find()->joinWith(['factory',
																				 'createdBy',
																				 'updatedBy' => function($query){
																					 $query->from(['u2' => User::tableName()]);
																				 },
																				 'parent' => function($query){
																					 $query->from(['parent' => Line::tableName()]);
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
            'parent_id' => $this->parent_id,
            'factory_id' => $this->factory_id,
            'line.status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'line_name', $this->line_name])
						->andFilterWhere(['>=', 'line.created_at', strtotime($this->created_at)])
						->andFilterWhere(['>=', 'line.updated_at', strtotime($this->updated_at)])
            ->andFilterWhere(['like', 'description', $this->description]);

			if($mode == 'excel'){
				$file = Yii::createObject([
																		'class' => 'codemix\excelexport\ExcelFile',
																		'sheets' => [
																			'Lines' => [
																				'class' => 'codemix\excelexport\ActiveExcelSheet',
																				'query' => $query,
																				'attributes' => [
																					'id',
																					'line_name',
																					'description',
																					'parent.line_name',
																					'factory.name',
																					'statusText',
																					'createdBy.fullname',
																					'createdAtFormatted',
																					'updatedBy.fullname',
																					'updatedAtFormatted',
																				],
																				'titles' => [
																					3 => Yii::t('app', 'Parent'),
																					5 => Yii::t('app', 'Status'),
																					6 => Yii::t('app', 'Created by'),
																					7 => Yii::t('app', 'Created at'),
																					8 => Yii::t('app', 'Updated by'),
																					9 => Yii::t('app', 'Updated at'),
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
