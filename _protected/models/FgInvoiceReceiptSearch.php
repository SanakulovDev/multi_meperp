<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\FgInvoiceReceipt;

/**
 * FgInvoiceReceiptSearch represents the model behind the search form of `app\models\FgInvoiceReceipt`.
 */
class FgInvoiceReceiptSearch extends FgInvoiceReceipt
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'fg_invoice_id', 'recept_control_id', 'created_at', 'created_by', 'updated_by', 'updated_at'], 'integer'],
            [['customer', 'contract', 'paymentNo', 'paymentDate'], 'safe'],
            [['amount'], 'number'],
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
        $query = FgInvoiceReceipt::find()->joinWith(['fgInvoice.customer', 'receptControl.salesContract.currency']);

        // add conditions that should always apply here

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
            'fg_invoice_id' => $this->fg_invoice_id,
            'recept_control_id' => $this->recept_control_id,
            'amount' => $this->amount,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'updated_at' => $this->updated_at,
        ]);

      $query->andFilterWhere(['like', 'customer.name', $this->customer])
            ->andFilterWhere(['like', 'sales_contract.contract_no', $this->contract])
            ->andFilterWhere(['like', 'recept_control.no', $this->paymentNo])
            ->andFilterWhere(['like', 'recept_control.date', $this->paymentDate]);
      if ($mode == 'excel') {
        $query->joinWith(['createdBy', 'updatedBy' => function($query) {
                             $query->from(['u2' => User::tableName()]);
                           }]);
        $file = \Yii::createObject([
                                     'class' => 'codemix\excelexport\ExcelFile',
                                     'sheets' => [
                                       'Receipt data' => [
                                         'class' => 'codemix\excelexport\ActiveExcelSheet',
                                         'query' => $query,
                                         'attributes' => [
                                           'id',
                                           'fgInvoice.customer.name',
                                           'receptControl.salesContract.contract_no',
                                           'receptControl.no',
                                           'amount',
                                           'receptControl.date',
                                           'fgInvoice.invoice_no',
                                           'receptControl.salesContract.currency.code',
                                           'createdBy.fullname',
                                           'createdAtFormatted',
                                           'updatedBy.fullname',
                                           'updatedAtFormatted',
                                         ],
                                         'titles' => [
                                           1 => Yii::t('app', 'Customer'),
                                           2 => Yii::t('app', 'Contract'),
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
