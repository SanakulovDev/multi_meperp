<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "coverage_balance".
 *
 * @property int $id
 * @property int $supplier_id
 * @property string $period
 * @property float|null $debt
 * @property float|null $paid
 *
 * @property Supplier $supplier
 */
class CoverageBalance extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'coverage_balance';
    }

    /**
     * {@inheritdoc}
     */
    public $country;
    public function rules()
    {
        return [
            [['supplier_id', 'payment_term_id', 'period'], 'required'],
            [['supplier_id', 'payment_term_id', 'currency_id'], 'integer'],
            [['period'], 'safe'],
            [['debt', 'paid'], 'number'],
            [['currency_id'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::className(), 'targetAttribute' => ['currency_id' => 'id']],
            [['payment_term_id'], 'exist', 'skipOnError' => true, 'targetClass' => PaymentTerm::className(), 'targetAttribute' => ['payment_term_id' => 'id']],
            [['supplier_id'], 'exist', 'skipOnError' => true, 'targetClass' => Supplier::className(), 'targetAttribute' => ['supplier_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'supplier_id' => Yii::t('app', 'Supplier'),
            'payment_term_id' => Yii::t('app', 'Payment term'),
            'currency_id' => Yii::t('app', 'Currency'),
            'period' => Yii::t('app', 'Month'),
            'debt' => Yii::t('app', 'Required'),
            'paid' => Yii::t('app', 'Paid'),
            'diff' => Yii::t('app', 'For payment'),

            'country' => Yii::t('app', 'Country'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */

    public function getCurrency()
    {
        return $this->hasOne(Currency::className(), ['id' => 'currency_id']);
    }
    
    public function getSupplier()
    {
        return $this->hasOne(Supplier::className(), ['id' => 'supplier_id']);
    }
    public function getPaymentTerm()
    {
        return $this->hasOne(PaymentTerm::className(), ['id' => 'payment_term_id']);
    }

    public function getDiff()
    {
        return $this->debt - $this->paid;
    }
    public function getPeriodMonth()
    {
        return date('m.Y',strtotime($this->period));
    }
}
