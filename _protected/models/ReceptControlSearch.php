<?php
namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ReceptControl;

/**
 * ReceptControlSearch represents the model behind the search form of `app\models\ReceptControl`.
 */
class ReceptControlSearch extends ReceptControl {

  /**
   * {@inheritdoc}
   */
  public function rules() {
    return [
      [['id', 'customer_id', 'payment_term', 'sales_contract_id', 'created_at', 'created_by', 'updated_by', 'updated_at'], 'integer'],
      [['no', 'date'], 'safe'],
      [['amount'], 'number'],
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
    $query = ReceptControl::find()->joinWith(['customer', 'salesContract.currency']);
    $dataProvider = new ActiveDataProvider([
                                             'query' => $query,
                                           ]);
    $this->load($params);
    if (!$this->validate()) {
      return $dataProvider;
    }
    // grid filtering conditions
    $query->andFilterWhere([
                             'id' => $this->id,
                             'date' => $this->date,
                             'customer_id' => $this->customer_id,
                             'payment_term' => $this->payment_term,
                             'amount' => $this->amount,
                             'sales_contract_id' => $this->sales_contract_id,
                             'created_at' => $this->created_at,
                             'created_by' => $this->created_by,
                             'updated_by' => $this->updated_by,
                             'updated_at' => $this->updated_at,
                           ]);
    $query->andFilterWhere(['like', 'no', $this->no]);
    if ($mode == 'excel') {
      $query->joinWith([
                         'customer', 'salesContract', 'createdBy',
                         'updatedBy' => function($query) {
                           $query->from(['u2' => User::tableName()]);
                         }
                       ]);
      $file = \Yii::createObject([
                                   'class' => 'codemix\excelexport\ExcelFile',
                                   'sheets' => [
                                     'Receipt data' => [
                                       'class' => 'codemix\excelexport\ActiveExcelSheet',
                                       'query' => $query,
                                       'attributes' => [
                                         'id',
                                         'no',
                                         'date',
                                         'typeName',
                                         'amount',
                                         'salesContract.contract_no',
                                         'customer.name',
                                         'salesContract.currency.code',
                                         'createdBy.fullname',
                                         'createdAtFormatted',
                                         'updatedBy.fullname',
                                         'updatedAtFormatted',
                                       ],
                                       'titles' => [
                                         3 => Yii::t('app', 'Payment term'),
                                         5 => Yii::t('app', 'Contract'),
                                         6 => Yii::t('app', 'Supplier'),
                                         8 => Yii::t('app', 'Created by'),
                                         9 => Yii::t('app', 'Created at'),
                                         10 => Yii::t('app', 'Updated by'),
                                         11 => Yii::t('app', 'Updated at')
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
