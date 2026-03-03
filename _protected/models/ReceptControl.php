<?php
namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "recept_control".
 *
 * @property int      $id
 * @property string   $no
 * @property string   $date
 * @property int      $customer_id
 * @property int      $payment_term
 * @property float    $amount
 * @property int      $sales_contract_id
 * @property int      $created_at
 * @property int      $created_by
 * @property int|null $updated_by
 * @property int|null $updated_at
 */
class ReceptControl extends \yii\db\ActiveRecord {

  const LC_TYPE = 0;
  const POST_TYPE = 1;
  const PRE_TYPE = 2;

  /**
   * {@inheritdoc}
   */
  public static function tableName() {
    return 'recept_control';
  }

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
      [['no', 'date', 'customer_id', 'payment_term', 'amount', 'sales_contract_id'], 'required'],
      [['date'], 'safe'],
      [['customer_id', 'payment_term', 'sales_contract_id', 'created_at', 'created_by', 'updated_by', 'updated_at'], 'integer'],
      [['amount'], 'safe'],
      [['no'], 'string', 'max' => 100],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels() {
    return [
      'id' => Yii::t('app', 'ID'),
      'no' => Yii::t('app', 'Receipt number'),
      'date' => Yii::t('app', 'Date'),
      'customer_id' => Yii::t('app', 'Customer'),
      'payment_term' => Yii::t('app', 'Payment term'),
      'amount' => Yii::t('app', 'Amount'),
      'sales_contract_id' => Yii::t('app', 'Sales contract'),
      'created_at' => Yii::t('app', 'Created at'),
      'created_by' => Yii::t('app', 'Created by'),
      'updated_by' => Yii::t('app', 'Updated by'),
      'updated_at' => Yii::t('app', 'Updated at'),
    ];
  }

  public function getTypes() {
    return [
      self::LC_TYPE => Yii::t('app', 'LC'),
      self::POST_TYPE => Yii::t('app', 'Post payment'),
      self::PRE_TYPE => Yii::t('app', 'Prepayment'),
    ];
  }

  public function getTypeName() {
    return $this->getTypes()[$this->payment_term];
  }

  /**
   * @return ActiveQuery
   */
  public function getCustomer() {
    return $this->hasOne(Customer::className(), ['id' => 'customer_id']);
  }

  /**
   * @return ActiveQuery
   */
  public function getSalesContract() {
    return $this->hasOne(SalesContract::className(), ['id' => 'sales_contract_id']);
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
