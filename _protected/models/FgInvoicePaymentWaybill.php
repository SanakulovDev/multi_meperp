<?php
namespace app\models;

use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "fg_invoice_payment_waybill".
 *
 * @property int $id
 * @property int $payment_id
 * @property int $waybill_id
 *
 * @property FgInvoicePayment $payment
 * @property Waybill          $waybill
 */
class FgInvoicePaymentWaybill extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'fg_invoice_payment_waybill';
    }

    public function rules()
    {
        return [
            [['payment_id', 'waybill_id'], 'required'],
            [['payment_id', 'waybill_id'], 'integer'],
            [['payment_id'], 'exist', 'skipOnError' => true,
                'targetClass' => FgInvoicePayment::className(),
                'targetAttribute' => ['payment_id' => 'id']],
            [['waybill_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Waybill::className(),
                'targetAttribute' => ['waybill_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'         => Yii::t('app', 'ID'),
            'payment_id' => Yii::t('app', 'Payment'),
            'waybill_id' => Yii::t('app', 'Waybill'),
        ];
    }

    public function getPayment(): ActiveQuery
    {
        return $this->hasOne(FgInvoicePayment::className(), ['id' => 'payment_id']);
    }

    public function getWaybill(): ActiveQuery
    {
        return $this->hasOne(Waybill::className(), ['id' => 'waybill_id']);
    }
}
