<?php
namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "container".
 *
 * @property int                $id
 * @property string             $container_no
 * @property string             $container_type
 * @property int                $created_by
 * @property int                $created_at
 * @property int                $updated_by
 * @property int                $updated_at
 * @property User               $createdBy
 * @property User               $updatedBy
 * @property ContainerInvoice[] $containerInvoices
 * @property Invoice[]          $invoices
 * @property InvoiceDetail[]    $invoiceDetails
 */
class Container extends ActiveRecord {

  /**
   * @inheritdoc
   */
  public static function tableName() {
    return 'container';
  }

  /**
   * @inheritdoc
   */
  public function rules() {
    return [
      [['container_no', 'created_by', 'created_at'], 'required'],
      [['created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
      [['container_no'], 'string', 'max' => 100],
      [['container_type'], 'string', 'max' => 10],
      [['container_no'], 'unique'],
      [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
      [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
    ];
  }

  /**
   * @inheritdoc
   */
  public function attributeLabels() {
    return [
      'id' => Yii::t('app', 'ID'),
      'container_no' => Yii::t('app', 'Container No'),
      'container_type' => Yii::t('app', 'Container type'),
      'created_by' => Yii::t('app', 'Created by'),
      'created_at' => Yii::t('app', 'Created at'),
      'updated_by' => Yii::t('app', 'Updated by'),
      'updated_at' => Yii::t('app', 'Updated at'),
    ];
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

  /**
   * @return ActiveQuery
   */
  public function getContainerInvoices() {
    return $this->hasMany(ContainerInvoice::className(), ['container_id' => 'id']);
  }

  /**
   * @return ActiveQuery
   */
  public function getInvoices() {
    return $this->hasMany(Invoice::className(), ['id' => 'invoice_id'])->viaTable('container_invoice', ['container_id' => 'id']);
  }

  /**
   * @return ActiveQuery
   */
  public function getInvoiceDetails() {
    return $this->hasMany(InvoiceDetail::className(), ['container_id' => 'id']);
  }

  /**
   * Gets query for [[FreightInvoiceDetails]].
   *
   * @return ActiveQuery
   */
  public function getFreightInvoiceDetails() {
    return $this->hasMany(FreightInvoiceDetail::className(), ['container_id' => 'id']);
  }

}
