<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CustomerSearch represents the model behind the search form of `app\models\Customer`.
 */
class CustomerSearch extends Customer
{
  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['id', 'customer_type_id', 'status'], 'integer'],
      [['name', 'duns', 'alias', 'address', 'city', 'postal', 'country_code_id',
        'contact_name', 'contact_position', 'contact_email', 'contact_phone', 'contact_cellular',
        'created_by', 'created_at', 'updated_by', 'updated_at', 'vat', 'tin'], 'safe'],
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
    $query = Customer::find()->joinWith(['customerType', 'createdBy', 'countryCode',
      'updatedBy' => function ($query) {
        $query->from(['u2' => User::tableName()]);
      }]);

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
      'customer.id' => $this->id,
      'customer_type_id' => $this->customer_type_id,
      'customer.status' => $this->status,
      'country_code_id' => $this->country_code_id,
      //'created_at' => $this->created_at,
      //'updated_by' => $this->updated_by,
      //'updated_at' => $this->updated_at,
    ]);

    $query->andFilterWhere(['like', 'customer.name', $this->name])
      ->andFilterWhere(['like', 'duns', $this->duns])
      ->andFilterWhere(['like', 'vat', $this->vat])
      ->andFilterWhere(['like', 'tin', $this->tin])
      ->andFilterWhere(['like', 'alias', $this->alias])
      ->andFilterWhere(['like', 'address', $this->address])
      ->andFilterWhere(['like', 'city', $this->city])
      ->andFilterWhere(['like', 'postal', $this->postal])
//      ->andFilterWhere(['like', 'country', $this->country])
//      ->andFilterWhere(['like', 'country_code', $this->country_code])
      ->andFilterWhere(['like', 'contact_name', $this->contact_name])
      ->andFilterWhere(['like', 'contact_position', $this->contact_position])
      ->andFilterWhere(['like', 'contact_email', $this->contact_email])
      ->andFilterWhere(['like', 'contact_phone', $this->contact_phone])
      ->andFilterWhere(['like', 'u2.fullname', $this->updated_by])
      ->andFilterWhere(['>=', 'customer.created_at', strtotime($this->created_at)])
      ->andFilterWhere(['>=', 'customer.updated_at', strtotime($this->updated_at)])
      ->andFilterWhere(['like', 'contact_cellular', $this->contact_cellular]);

    if ($mode == 'excel') {
      $file = \Yii::createObject([
        'class' => 'codemix\excelexport\ExcelFile',
        'sheets' => [
          'Customers' => [
            'class' => 'codemix\excelexport\ActiveExcelSheet',
            'query' => $query,
            'attributes' => [
              'id',
              'name',
              'duns',
              'alias',
              'tin',
              'vat',
              'address',
//              'country',
//              'country_code',
              'countryCode.name',
              'city',
              'postal',

              'contact_name',
              'contact_position',
              'contact_email',
              'contact_phone',
              'contact_cellular',
              'customerType.name',
              'statusText',
              'createdBy.fullname',
              'createdAtFormatted',
              'updatedBy.fullname',
              'updatedAtFormatted',

            ],
            'titles' => [
              16 => Yii::t('app', 'Status'),
              17 => Yii::t('app', 'Created by'),
              18 => Yii::t('app', 'Created at'),
              19 => Yii::t('app', 'Updated by'),
              20 => Yii::t('app', 'Updated at'),
            ],
          ],
        ]
      ]);
      return $file;
    } else {
      return $dataProvider;
    }
  }
}
