<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\CustomerType;

/**
 * CustomerTypeSearch represents the model behind the search form of `app\models\CustomerType`.
 */
class CustomerTypeSearch extends CustomerType
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status'], 'integer'],
            [['name', 'description', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'safe'],
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
        $query = CustomerType::find();

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
				$query->joinWith(['createdBy', 'updatedBy' => function($query){
					$query->from(['u2' => User::tableName()]);
				}]);
        // grid filtering conditions
        $query->andFilterWhere([
            'customer_type.id' => $this->id,
            'customer_type.status' => $this->status,
            'customer_type.created_by' => $this->created_by,
            //'created_at' => $this->created_at,
            // 'updated_by' => $this->updated_by,
            //'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
						->andFilterWhere(['like', 'u2.fullname', $this->updated_by])
						->andFilterWhere(['>=', 'customer_type.created_at', strtotime($this->created_at)])
						->andFilterWhere(['>=', 'customer_type.updated_at', strtotime($this->updated_at)])
            ->andFilterWhere(['like', 'description', $this->description]);

			if($mode == 'excel'){

				$file = \Yii::createObject([
																		 'class'  => 'codemix\excelexport\ExcelFile',
																		 'sheets' => [
																			 'Customer types' => [
																				 'class'      => 'codemix\excelexport\ActiveExcelSheet',
																				 'query'      => $query,
																				 'attributes' => [
																					 'id',
																					 'name',
																					 'description',
																					 'createdBy.fullname',
																					 'createdAtFormatted',
																					 'updatedBy.fullname',
																					 'updatedAtFormatted',
																					 'statusText',
																				 ],
																				 'titles'     => [
																					 3 => Yii::t('app', 'Created by'),
																					 4 => Yii::t('app', 'Created at'),
																					 5 => Yii::t('app', 'Updated by'),
																					 6 => Yii::t('app', 'Updated at'),
																					 7 => Yii::t('app', 'Status'),
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
