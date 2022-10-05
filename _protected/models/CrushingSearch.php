<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Crushing;

/**
 * CrushingSearch represents the model behind the search form of `app\models\Crushing`.
 */
class CrushingSearch extends Crushing
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'part_id', 'is_processed', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
            [['qty'], 'number'],
            [['filter_from', 'filter_to'], 'safe'],
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
    public function search($params,$mode='')
    {
        $query = Crushing::find();
        
        $query->joinWith('part');
        $query->joinWith('createdBy as u1');
        $query->joinWith('updatedBy as u2');

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
        
        $filter_from = (!empty($this->filter_from)) ? $this->filter_from . ' 00:00:00' : '1970-01-01 00:00:00';
        $filter_to = (!empty($this->filter_to)) ? $this->filter_to . ' 23:59:59' : '9999-12-31 23:59:59';
        
        $query->andFilterWhere(['between', 'from_unixtime(crushing.created_at)', $filter_from, $filter_to]);

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'part_id' => $this->part_id,
            'qty' => $this->qty,
            'is_processed' => $this->is_processed,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);
        
        $dataProvider->sort->attributes['part_id'] = [
            'asc' => ['part.part_no' => SORT_ASC],
            'desc' => ['part.part_no' => SORT_DESC],
        ]; 
        
        $dataProvider->sort->attributes['created_by'] = [
            'asc' => ['u1.fullname' => SORT_ASC],
            'desc' => ['u1.fullname' => SORT_DESC],
        ]; 
        
        $dataProvider->sort->attributes['updated_by'] = [
            'asc' => ['u2.fullname' => SORT_ASC],
            'desc' => ['u2.fullname' => SORT_DESC],
        ]; 

        if($mode == 'excel'){
            
            $file = \Yii::createObject([
                'class' => 'codemix\excelexport\ExcelFile',
                'sheets' => [
                    'Crushing' => [
                        'class' => 'codemix\excelexport\ActiveExcelSheet',
                        'query' => $query,  
                        'attributes' => [
                            'id',
                            'part.part_no',
                            'qty',
                            'isProcessedText',
                            'createdBy.fullname',
                            'createdAtFormatted',
                            'updatedBy.fullname',
                            'updatedAtFormatted',
                        ],
                        'titles' => [
                            1 => Yii::t('app', 'Product'),
                            2 => Yii::t('app', 'Quantity'),
                            3 => Yii::t('app', 'Processed'),
                            4 => Yii::t('app', 'Created by'),
                            5 => Yii::t('app', 'Created at'),
                            6 => Yii::t('app', 'Updated by'),
                            7 => Yii::t('app', 'Updated at'),
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
