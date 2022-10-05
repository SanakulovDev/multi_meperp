<?php
namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "freight_invoice_detail_cost".
 *
 * @property int                  $id
 * @property int                  $freight_invoice_detail_id
 * @property int                  $cost_type
 * @property float                $value
 * @property string|null          $comment
 * @property FreightInvoiceDetail $freightInvoiceDetail
 */
class FreightInvoiceDetailCost extends ActiveRecord {

  /**
   * {@inheritdoc}
   */
  public static function tableName() {
    return 'freight_invoice_detail_cost';
  }

  /**
   * {@inheritdoc}
   */
  public function rules() {
    return [
      [['freight_invoice_detail_id', 'cost_type', 'value'], 'required'],
      [['freight_invoice_detail_id', 'cost_type'], 'integer'],
      [['value'], 'number'],
      [['comment'], 'string'],
      [['freight_invoice_detail_id'], 'exist', 'skipOnError' => true, 'targetClass' => FreightInvoiceDetail::className(), 'targetAttribute' => ['freight_invoice_detail_id' => 'id']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels() {
    return [
      'id' => Yii::t('app', 'ID'),
      'freight_invoice_detail_id' => Yii::t('app', 'Freight Invoice Detail'),
      'cost_type' => Yii::t('app', 'Cost type'),
      'value' => Yii::t('app', 'Value'),
      'comment' => Yii::t('app', 'Comment'),
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

}
