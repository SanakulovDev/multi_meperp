<?php
namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "waybill_payments".
 *
 * @property int           $id
 * @property int           $fg_invoice_id
 * @property int           $recept_control_id
 * @property float         $amount
 * @property int           $created_at
 * @property int           $created_by
 * @property int|null      $updated_by
 * @property int|null      $updated_at
 * @property ReceptControl $receptControl
 * @property Waybill       $waybill
 */
class FgInvoiceReceipt extends \yii\db\ActiveRecord {

  /**
   * {@inheritdoc}
   */
  public static function tableName() {
    return 'fg_invoice_receipt';
  }

  public function behaviors() {
    return [
      TimestampBehavior::className(),
      BlameableBehavior::className(),
    ];
  }

  public $customer, $contract, $paymentNo, $paymentDate;

  /**
   * {@inheritdoc}
   */
  public function rules() {
    return [
      [['fg_invoice_id', 'recept_control_id', 'amount'], 'required'],
      [['fg_invoice_id', 'recept_control_id', 'created_at', 'created_by', 'updated_by', 'updated_at'], 'integer'],
      [['amount'], 'number'],
      [['customer', 'contract', 'paymentNo', 'paymentDate'], 'safe'],
      [['recept_control_id'], 'exist', 'skipOnError' => true, 'targetClass' => ReceptControl::className(), 'targetAttribute' => ['recept_control_id' => 'id']],
      [['fg_invoice_id'], 'exist', 'skipOnError' => true, 'targetClass' => FgInvoice::className(), 'targetAttribute' => ['fg_invoice_id' => 'id']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels() {
    return [
      'id' => Yii::t('app', 'ID'),
      'fg_invoice_id' => Yii::t('app', 'FG Invoice'),
      'recept_control_id' => Yii::t('app', 'Receipt control'),
      'amount' => Yii::t('app', 'Amount'),
      'created_at' => Yii::t('app', 'Created at'),
      'created_by' => Yii::t('app', 'Created by'),
      'updated_by' => Yii::t('app', 'Updated by'),
      'updated_at' => Yii::t('app', 'Updated at'),
      'customer' => Yii::t('app', 'Customer'),
      'contract' => Yii::t('app', 'Sales contract'),
      'paymentNo' => Yii::t('app', 'Receipt number'),
      'paymentDate' => Yii::t('app', 'Date'),
    ];
  }

  /**
   * Gets query for [[ReceptControl]].
   *
   * @return \yii\db\ActiveQuery
   */
  public function getReceptControl() {
    return $this->hasOne(ReceptControl::className(), ['id' => 'recept_control_id']);
  }

  /**
   * Gets query for [[Waybill]].
   *
   * @return \yii\db\ActiveQuery
   */
  public function getFgInvoice() {
    return $this->hasOne(FgInvoice::className(), ['id' => 'fg_invoice_id']);
  }

  /**
   * @return ActiveQuery
   */
  public function getCreatedBy() {
    return $this->hasOne(User::className(), ['id' => 'created_by']);
  }

  /**
   * @return ActiveQuery
   */
  public function getUpdatedBy() {
    return $this->hasOne(User::className(), ['id' => 'updated_by']);
  }

  public function getUpdatedAtFormatted() {
    return (!empty($this->updated_at)) ? date('d.m.Y H:i', $this->updated_at) : '';
  }

  public function getCreatedAtFormatted() {
    return (!empty($this->created_at)) ? date('d.m.Y H:i', $this->created_at) : '';
  }

}
