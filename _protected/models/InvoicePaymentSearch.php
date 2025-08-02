<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\InvoicePayment;

/**
 * InvoicePaymentSearch represents the model behind the search form of `app\models\InvoicePayment`.
 */
class InvoicePaymentSearch extends InvoicePayment
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'invoice_id', 'payment_control_id'], 'integer'],
            [['amount'], 'number'],
            [['updated_by','updated_at'], 'safe']
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
        $query = InvoicePayment::find()->joinWith(['invoice','paymentControl','updatedBy']);

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
            'invoice_id' => $this->invoice_id,
            'payment_control_id' => $this->payment_control_id,
            'amount' => $this->amount,
        ]);

        $query->andFilterWhere(['like', 'user.fullname', $this->updated_by])
					->andFilterWhere(['>', 'invoice_payment.updated_at', strtotime($this->updated_at)]);
                    
        if($mode == 'excel'){
            $file = \Yii::createObject([
                'class' => 'codemix\excelexport\ExcelFile',
                'sheets' => [
                    'Lms data' => [
                        'class' => 'codemix\excelexport\ActiveExcelSheet',
                        'query' => $query,
                        'attributes' => [
                            'id',
                            'payment.no',
                            'invoice.invoice_no',
                            'amount',
                            'updatedBy.fullname',
                            'updatedAtFormatted',
                        ],
                        'titles' => [
                            1 => Yii::t('app', 'Payment type'),
                            2 => Yii::t('app', 'Contract'),
                            4 => Yii::t('app', 'Updated by'),
                            5 => Yii::t('app', 'Updated at')
                        ],
                    ],
                ]
            ]);
            return  $file;
        } else { return $dataProvider; }		
    }
}
