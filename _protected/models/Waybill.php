<?php
namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "waybill".
 *
 * @property int                $id
 * @property int                $factory_id
 * @property string             $waybill_no Increment by factory and year(waybill_date)
 * @property string             $waybill_date
 * @property string             $asn
 * @property string             $driver
 * @property string             $truck
 * @property string             $manager
 * @property string             $account
 * @property string             $sender
 * @property int                $created_at
 * @property int                $created_by
 * @property int                $updated_at
 * @property int                $updated_by
 * @property FgInvoiceWaybill[] $fgInvoiceWaybills
 * @property User               $createdBy
 * @property Factory            $factory
 * @property User               $updatedBy
 */
class Waybill extends ActiveRecord {

  /**
   * {@inheritdoc}
   */
  public static function tableName() {
    return 'waybill';
  }

  public $invoices;

  public function behaviors() {
    return [
      TimestampBehavior::className(),
      BlameableBehavior::className(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function rules() {
    return [
      [['factory_id', 'waybill_no', 'waybill_date', 'invoices'], 'required'],
      [['factory_id', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
      [['waybill_date'], 'safe'],
      [['waybill_no', 'asn', 'driver', 'manager', 'account', 'sender'], 'string', 'max' => 100],
      [['truck'], 'string', 'max' => 30],
      [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
      [['factory_id'], 'exist', 'skipOnError' => true, 'targetClass' => Factory::className(), 'targetAttribute' => ['factory_id' => 'id']],
      [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels() {
    return [
      'id' => Yii::t('app', 'ID'),
      'factory_id' => Yii::t('app', 'Factory'),
      'waybill_no' => Yii::t('app', 'Waybill no'),
      'waybill_date' => Yii::t('app', 'Date'),
      'asn' => Yii::t('app', 'ASN'),
      'driver' => Yii::t('app', 'Driver'),
      'truck' => Yii::t('app', 'Truck'),
      'invoices' => Yii::t('app', 'Invoices'),
      'manager' => Yii::t('app', 'Manager'),
      'account' => Yii::t('app', 'Account'),
      'sender' => Yii::t('app', 'Sender'),
      'created_at' => Yii::t('app', 'Created at'),
      'created_by' => Yii::t('app', 'Created by'),
      'updated_at' => Yii::t('app', 'Updated at'),
      'updated_by' => Yii::t('app', 'Updated by'),
    ];
  }

  /**
   * @return ActiveQuery
   */
  public function getFgInvoiveWaybills() {
    return $this->hasMany(FgInvoiceWaybill::className(), ['waybill_id' => 'id']);
  }

  /**
   * @return ActiveQuery
   */
  public function getCreatedBy() {
    return $this->hasOne(User::className(), ['id' => 'created_by']);
  }

  public function getCreatedAtFormatted() {
    return (!empty($this->created_at)) ? date('d.m.Y H:i', $this->created_at) : '';
  }

  public function getUpdatedBy() {
    return $this->hasOne(User::className(), ['id' => 'updated_by']);
  }

  public function getUpdatedAtFormatted() {
    return (!empty($this->updated_at)) ? date('d.m.Y H:i', $this->updated_at) : '';
  }

  /**
   * @return ActiveQuery
   */
  public function getFactory() {
    return $this->hasOne(Factory::className(), ['id' => 'factory_id']);
  }

  public function getFgInvoiceWaybills() {
    return $this->hasMany(FgInvoiceWaybill::className(), ['waybill_id' => 'id']);
  }

  public function getReceiptWaybills() {
    return $this->hasMany(ReceiptWaybill::className(), ['waybill_id' => 'id']);
  }

}
