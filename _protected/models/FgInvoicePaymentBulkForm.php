<?php
namespace app\models;

use Yii;
use yii\base\Model;

class FgInvoicePaymentBulkForm extends Model
{
    public $customer_id;
    public $no;
    public $date;
    public $selected_keys = [];

    public function rules()
    {
        return [
            [['customer_id', 'no', 'date'], 'required'],
            [['customer_id'], 'integer'],
            [['date'], 'safe'],
            [['no'], 'string', 'max' => 100],
            [['selected_keys'], 'required'],
            [['selected_keys'], 'each', 'rule' => ['string']],
            [['customer_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Customer::className(),
                'targetAttribute' => ['customer_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'customer_id' => Yii::t('app', 'Customer'),
            'no' => Yii::t('app', 'Receipt number'),
            'date' => Yii::t('app', 'Date'),
            'selected_keys' => Yii::t('app', 'Waybill (TTN)'),
        ];
    }
}
