<?php
namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "freight_invoice_detail_invoice".
 *
 * @property int                  $id
 * @property int                  $freight_invoice_detail_id
 * @property int                  $invoice_id
 * @property FreightInvoiceDetail $freightInvoiceDetail
 * @property Invoice              $invoice
 */
class FreightInvoiceDetailInvoice extends ActiveRecord {

  /**
   * {@inheritdoc}
   */
  public static function tableName() {
    return 'freight_invoice_detail_invoice';
  }

  /**
   * {@inheritdoc}
   */
  public function rules() {
    return [
      [['freight_invoice_detail_id', 'invoice_id'], 'required'],
      [['freight_invoice_detail_id', 'invoice_id'], 'integer'],
      [['freight_invoice_detail_id'], 'exist', 'skipOnError' => true, 'targetClass' => FreightInvoiceDetail::className(), 'targetAttribute' => ['freight_invoice_detail_id' => 'id']],
      [['invoice_id'], 'exist', 'skipOnError' => true, 'targetClass' => Invoice::className(), 'targetAttribute' => ['invoice_id' => 'id']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels() {
    return [
      'id' => Yii::t('app', 'ID'),
      'freight_invoice_detail_id' => Yii::t('app', 'Freight Invoice Detail ID'),
      'invoice_id' => Yii::t('app', 'Invoice ID'),
    ];
  }

  /**
   * Gets query for [[FreightInvoiceDetail]].
   *
   * @return ActiveQuery
   */
  public function getFreightInvoiceDetail() {
    return $this->hasOne(FreightInvoiceDetail::className(), ['id' => 'freight_invoice_detail_id']);
  }

  /**
   * Gets query for [[Invoice]].
   *
   * @return ActiveQuery
   */
  public function getInvoice() {
    return $this->hasOne(Invoice::className(), ['id' => 'invoice_id']);
  }

  public function getContInv() {
    return ContainerInvoice::find()->where([
      'container_id' => $this->freightInvoiceDetail->container_id,
      'invoice_id' => $this->invoice_id,
    ])->one();
  }

}
