<?php
namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * FreightInvoiceSearch represents the model behind the search form of `app\models\FreightInvoice`.
 */
class FreightInvoiceSearch extends FreightInvoice {

  /**
   * {@inheritdoc}
   */
  public function rules() {
    return [
      [['id', 'route_id', 'carrier_id', 'delivery_term_id', 'currency_id'], 'integer'],
      [['invoice_no', 'invoice_date', 'contract', 'route_id', 'carrier_id', 'delivery_term_id', 'currency_id'], 'safe'],
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
  public function search($params) {
    $query = FreightInvoice::find();
    // add conditions that should always apply here
    $dataProvider = new ActiveDataProvider([
      'query' => $query,
    ]);
    $this->load($params);
    if(!$this->validate()) {
      // uncomment the following line if you do not want to return any records when validation fails
      // $query->where('0=1');
      return $dataProvider;
    }
    $query->joinWith('currency');
    $query->joinWith('deliveryTerm');
    $query->joinWith('carrier');
    $query->joinWith('route');
    // grid filtering conditions
    $query->andFilterWhere([
      'id' => $this->id,
      'invoice_date' => $this->invoice_date,
      'route_id' => $this->route_id,
      'carrier_id' => $this->carrier_id,
      'delivery_term_id' => $this->delivery_term_id,
      'currency_id' => $this->currency_id,
    ]);
    $query->andFilterWhere(['like', 'invoice_no', $this->invoice_no])
          ->andFilterWhere(['like', 'contract', $this->contract]);
    return $dataProvider;
  }

}
