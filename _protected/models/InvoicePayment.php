<?php

namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "invoice_payment".
 *
 * @property int $id
 * @property int $invoice_id
 * @property int $payment_control_id
 * @property float $amount
 *
 * @property Invoice $invoice
 * @property PaymentControl $paymentControl
 */
class InvoicePayment extends \yii\db\ActiveRecord {
	/**
	 * {@inheritdoc}
	 */
	public static function tableName() {
		return 'invoice_payment';
	}

	public function behaviors() {
		return [
			[
				'class' => TimestampBehavior::className(),
				'createdAtAttribute' => null,
				'updatedAtAttribute' => 'updated_at',
			],
			[
				'class' => BlameableBehavior::className(),
				'createdByAttribute' => null,
				'updatedByAttribute' => 'updated_by',
			],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules() {
		return [
			[['invoice_id', 'payment_control_id', 'amount'], 'required'],
			[['invoice_id', 'payment_control_id', 'updated_by', 'updated_at'], 'integer'],
			[['amount'], 'number'],
			[['amount'], 'validateAmount'],
			[['invoice_id'], 'exist', 'skipOnError' => true, 'targetClass' => Invoice::className(), 'targetAttribute' => ['invoice_id' => 'id']],
			[['payment_control_id'], 'exist', 'skipOnError' => true, 'targetClass' => PaymentControl::className(), 'targetAttribute' => ['payment_control_id' => 'id']],
		];
	}

	public function validateAmount($attribute, $params, $validator) {
        // check for payment
        $payment = PaymentControl::findOne($this->payment_control_id);
        $totalSpent = InvoicePayment::find()
            ->where(['payment_control_id' => $this->payment_control_id])
            ->andWhere(['<>', 'id', $this->id])
            ->sum('amount');
        if($totalSpent + $this->$attribute > $payment->amount) {
            $this->addError($attribute, 'The amount exceeds payment amount.');
        }

        $invoice = Invoice::findOne($this->invoice_id);
		$totalPayed = InvoicePayment::find()
        ->where(['invoice_id' => $this->invoice_id])
        ->andWhere(['<>', 'id', $this->id])
		->sum('amount');

		if($invoice->invoice_amount && $totalPayed + $this->$attribute > $invoice->invoice_amount) {
            $this->addError($attribute, 'The amount exceeds invoice amount".');
        }
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels() {
		return [
			'id' => Yii::t('app', 'ID'),
			'invoice_id' => Yii::t('app', 'Invoice'),
			'payment_control_id' => Yii::t('app', 'Payment'),
			'amount' => Yii::t('app', 'Amount'),
			'updated_at' => Yii::t('app', 'Updated at'),
			'updated_by' => Yii::t('app', 'Updated by'),
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getInvoice() {
		return $this->hasOne(Invoice::className(), ['id' => 'invoice_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getPaymentControl() {
		return $this->hasOne(PaymentControl::className(), ['id' => 'payment_control_id']);
	}

	public function getUpdatedBy() {
		return $this->hasOne(User::className(), ['id' => 'updated_by']);
	}

	public function getUpdatedAtFormatted() {
		return (!empty($this->updated_at)) ? date('d.m.Y H:i', $this->updated_at) : '';
	}
}
