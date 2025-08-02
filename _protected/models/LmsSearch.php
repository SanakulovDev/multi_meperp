<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Lms;

/**
 * LmsSearch represents the model behind the search form of `app\models\Lms`.
 */
class LmsSearch extends Lms
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'part_id', 'supplier_id', 'warehouse_id', 'high_theft','bms'], 'integer'],
            [['dloc', 'mpr', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'safe'],
            [['minimum', 'maximum', 'stack'], 'number'],
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
        $query = Lms::find()->joinWith(['part', 'supplier', 'warehouse']);

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
            'lms.id' => $this->id,
            'part_id' => $this->part_id,
            'bms' => $this->bms,
            'lms.supplier_id' => $this->supplier_id,
            'lms.warehouse_id' => $this->warehouse_id,
            'high_theft' => $this->high_theft,
            'lms.created_by' => $this->created_by,
            'lms.created_at' => $this->created_at,
            'lms.updated_by' => $this->updated_by,
            'lms.updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'dloc', $this->dloc])
	        	->andFilterWhere(['>=', 'minimum', $this->minimum])
	        	->andFilterWhere(['>=', 'maximum', $this->maximum])
	        	->andFilterWhere(['>=', 'stack', $this->stack])
            ->andFilterWhere(['like', 'mpr', $this->mpr]);

	    if($mode == 'excel'){
		    $query->orderBy([
			                    'part_id' => SORT_ASC
		                    ]);
		    $file = \Yii::createObject([
			                               'class' => 'codemix\excelexport\ExcelFile',
			                               'sheets' => [
				                               'Lms data' => [
					                               'class' => 'codemix\excelexport\ActiveExcelSheet',
					                               'query' => $query,
					                               'attributes' => [
						                               'id',
						                               'part.part_no',
						                               'part.part_name',
						                               'supplier.duns',
						                               'part.part_color',
						                               'supplier.name',
						                               'warehouse.name',
						                               'dloc',
						                               'minimum',
						                               'maximum',
						                               'stack',
						                               'mpr',
						                               'highTheftFormatted',
						                               'createdBy.fullname',
						                               'createdAtFormatted',
						                               'updatedBy.fullname',
						                               'updatedAtFormatted',
					                               ],
					                               'titles' => [
						                               1 => Yii::t('app', 'Part No'),
						                               2 => Yii::t('app', 'Part name'),
						                               3 => Yii::t('app', 'DUNS'),
						                               4 => Yii::t('app', 'Part color'),
						                               5 => Yii::t('app', 'Supplier'),
						                               6 => Yii::t('app', 'Warehouse'),
						                               12 => Yii::t('app', 'High theft'),
						                               13 => Yii::t('app', 'Created by'),
						                               14 => Yii::t('app', 'Created at'),
						                               15 => Yii::t('app', 'Updated by'),
						                               16 => Yii::t('app', 'Updated at')
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
