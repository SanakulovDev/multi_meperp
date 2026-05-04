<?php
namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * FgInvoicePaymentSearch represents the model behind the search form of FgInvoicePayment.
 */
class FgInvoicePaymentSearch extends FgInvoicePayment
{
    /** @var int filter by customer (virtual — maps to sales_contract.customer_id) */
    public $customer_id;

    public function rules()
    {
        return [
                        [['id', 'sales_contract_id', 'currency_id', 'waybill_id', 'customer_id',
              'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['no', 'date'], 'safe'],
            [['amount'], 'number'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search(array $params, string $mode = '')
    {
        $query = FgInvoicePayment::find()
            ->joinWith(['salesContract.customer', 'waybill']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => ['defaultOrder' => ['date' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'fg_invoice_payment.id'                => $this->id,
            'fg_invoice_payment.sales_contract_id' => $this->sales_contract_id,
            'fg_invoice_payment.currency_id'       => $this->currency_id,
            'fg_invoice_payment.waybill_id'        => $this->waybill_id,
            'fg_invoice_payment.amount'            => $this->amount,
            'sales_contract.customer_id'           => $this->customer_id,
        ]);

        $query->andFilterWhere(['like', 'fg_invoice_payment.no',   $this->no])
              ->andFilterWhere(['like', 'fg_invoice_payment.date',  $this->date]);

        if ($mode === 'excel') {
            $query->joinWith(['createdBy', 'updatedBy' => function ($q) {
                $q->from(['u2' => User::tableName()]);
            }]);

            return \Yii::createObject([
                'class'  => 'codemix\excelexport\ExcelFile',
                'sheets' => [
                    'Payment data' => [
                        'class'      => 'codemix\excelexport\ActiveExcelSheet',
                        'query'      => $query,
                        'attributes' => [
                            'id',
                            'salesContract.customer.name',
                            'salesContract.contract_no',
                            'no',
                            'date',
                            'waybill.waybill_no',
                            'amount',
                            'createdBy.fullname',
                            'createdAtFormatted',
                            'updatedBy.fullname',
                            'updatedAtFormatted',
                        ],
                        'titles' => [
                            1  => Yii::t('app', 'Customer'),
                            2  => Yii::t('app', 'Contract'),
                            5  => Yii::t('app', 'Waybill (TTN)'),
                            7  => Yii::t('app', 'Created by'),
                            8  => Yii::t('app', 'Created at'),
                            9  => Yii::t('app', 'Updated by'),
                            10 => Yii::t('app', 'Updated at'),
                        ],
                    ],
                ],
            ]);
        }

        return $dataProvider;
    }
}
