<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Defect;

/**
 * DefectSearch represents the model behind the search form of `app\models\Defect`.
 */
class DefectSearch extends Defect
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id','category'], 'integer'],
            [['code', 'description'], 'safe'],
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
        $query = Defect::find();

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
            'category' => $this->category,
        ]);

        $query->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'description', $this->description]);

			if($mode == 'excel'){
				$file = \Yii::createObject([
																		 'class'  => 'codemix\excelexport\ExcelFile',
																		 'sheets' => [
																			 'Defects' => [
																				 'class'      => 'codemix\excelexport\ActiveExcelSheet',
																				 'query'      => $query,
																				 'attributes' => [
																					 'id',
																					 'code',
																					 'categoryText',
																					 'description',
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
