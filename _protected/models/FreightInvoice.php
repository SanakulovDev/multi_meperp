<?php
namespace app\models;

use app\enums\FreightInvoiceType;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "freight_invoice".
 *
 * @property int                    $id
 * @property int|null               $invoice_type
 * @property string                 $invoice_no
 * @property string                 $invoice_date
 * @property string                 $contract
 * @property int                    $route_id
 * @property int                    $carrier_id
 * @property int                    $delivery_term_id
 * @property int                    $currency_id
 * @property integer                $created_at
 * @property integer                $created_by
 * @property integer                $updated_at
 * @property integer                $updated_by
 * @property User                   $createdBy
 * @property User                   $updatedBy
 * @property Carrier                $carrier
 * @property DeliveryTerm           $deliveryTerm
 * @property Currency               $currency
 * @property Route                  $route
 * @property FreightInvoiceDetail[] $freightInvoiceDetails
 * @property FreightInvoiceType[]   $freightInvoiceType
 */
class FreightInvoice extends ActiveRecord {

  /**
   * {@inheritdoc}
   */
  public static function tableName() {
    return 'freight_invoice';
  }

  /**
   * {@inheritdoc}
   */
  public function rules() {
    return [
      [['invoice_type', 'route_id', 'carrier_id', 'delivery_term_id', 'currency_id', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
      [['invoice_no', 'invoice_date', 'contract', 'route_id', 'carrier_id', 'delivery_term_id', 'currency_id'], 'required'],
      [['invoice_date'], 'safe'],
      [['invoice_no', 'contract'], 'string', 'max' => 255],
      [['carrier_id'], 'exist', 'skipOnError' => true, 'targetClass' => Carrier::className(), 'targetAttribute' => ['carrier_id' => 'id']],
      [['delivery_term_id'], 'exist', 'skipOnError' => true, 'targetClass' => DeliveryTerm::className(), 'targetAttribute' => ['delivery_term_id' => 'id']],
      [['currency_id'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::className(), 'targetAttribute' => ['currency_id' => 'id']],
      [['route_id'], 'exist', 'skipOnError' => true, 'targetClass' => Route::className(), 'targetAttribute' => ['route_id' => 'id']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels() {
    return [
      'id' => Yii::t('app', 'ID'),
      'invoice_type' => Yii::t('app', 'Invoice type'),
      'invoice_no' => Yii::t('app', 'Invoice no'),
      'invoice_date' => Yii::t('app', 'Invoice date'),
      'contract' => Yii::t('app', 'Contract'),
      'route_id' => Yii::t('app', 'Route'),
      'carrier_id' => Yii::t('app', 'Carrier'),
      'delivery_term_id' => Yii::t('app', 'Delivery term'),
      'currency_id' => Yii::t('app', 'Currency'),
    ];
  }

  /**
   * @return ActiveQuery
   */
  public function getCreatedBy() {
    return $this->hasOne(User::className(), ['id' => 'created_by'])->inverseOf('freightInvoices');
  }

  /**
   * @return ActiveQuery
   */
  public function getUpdatedBy() {
    return $this->hasOne(User::className(), ['id' => 'updated_by'])->inverseOf('freightInvoices');
  }

  /**
   * @return ActiveQuery
   */
  public function getCurrency() {
    return $this->hasOne(Currency::className(), ['id' => 'currency_id'])->inverseOf('freightInvoices');
  }

  /**
   * @return ActiveQuery
   */
  public function getCarrier() {
    return $this->hasOne(Carrier::className(), ['id' => 'carrier_id'])->inverseOf('freightInvoices');
  }

  /**
   * @return ActiveQuery
   */
  public function getDeliveryTerm() {
    return $this->hasOne(DeliveryTerm::className(), ['id' => 'delivery_term_id'])->inverseOf('freightInvoices');
  }

  /**
   * @return ActiveQuery
   */
  public function getRoute() {
    return $this->hasOne(Route::className(), ['id' => 'route_id'])->inverseOf('freightInvoices');
  }

  /**
   * @return ActiveQuery
   */
  public function getFreightInvoiceDetails() {
    return $this->hasMany(FreightInvoiceDetail::className(), ['freight_invoice_id' => 'id'])->inverseOf('freightInvoices');
  }

  public function getFreightInvoiceType() {
    return FreightInvoiceType::name($this->invoice_type);
  }

  public function getInvoiceInfo() {
    return $this->invoice_no . ' (' . $this->invoice_date . ')';
  }

  public function getIsInbound(){
    return ($this->invoice_type == FreightInvoiceType::FREIGHT_TYPE_INBOUND);
  }

  public function getIsOutbound(){
    return ($this->invoice_type == FreightInvoiceType::FREIGHT_TYPE_OUTBOUND);
  }

}
