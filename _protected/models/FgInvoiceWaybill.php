<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "fg_invoive_waybill".
 *
 * @property int $id
 * @property int $fg_invoice_id
 * @property int $waybill_id
 *
 * @property FgInvoice $fgInvoice
 * @property Waybill $waybill
 */
class FgInvoiceWaybill extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fg_invoice_waybill';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fg_invoice_id', 'waybill_id'], 'required'],
            [['fg_invoice_id', 'waybill_id'], 'integer'],
            [['fg_invoice_id'], 'exist', 'skipOnError' => true, 'targetClass' => FgInvoice::className(), 'targetAttribute' => ['fg_invoice_id' => 'id']],
            [['waybill_id'], 'exist', 'skipOnError' => true, 'targetClass' => Waybill::className(), 'targetAttribute' => ['waybill_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'fg_invoice_id' => Yii::t('app', 'Fg Invoice ID'),
            'waybill_id' => Yii::t('app', 'Waybill ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFgInvoice()
    {
        return $this->hasOne(FgInvoice::className(), ['id' => 'fg_invoice_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWaybill()
    {
        return $this->hasOne(Waybill::className(), ['id' => 'waybill_id']);
    }
}
