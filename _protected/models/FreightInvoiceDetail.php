<?php
namespace app\models;

use app\components\Helpers;
use app\enums\CargoType;
use app\enums\ContainerType;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "freight_invoice_detail".
 *
 * @property int                           $id
 * @property int                           $freight_invoice_id
 * @property int                           $container_id
 * @property string|null                   $comment
 * @property summCost                      $summCost
 * @property Container                     $container
 * @property FreightInvoice                $freightInvoice
 * @property FreightInvoiceDetailCost[]    $freightInvoiceDetailCosts
 * @property FreightInvoiceDetailInvoice[] $freightInvoiceDetailInvoices
 */
class FreightInvoiceDetail extends ActiveRecord {

  /**
   * {@inheritdoc}
   */
  public $isNeededOutbound = 1;
  public $outInvoice;

  public static function tableName() {
    return 'freight_invoice_detail';
  }

  /**
   * {@inheritdoc}
   */
  public function rules() {
    return [
      [['freight_invoice_id', 'container_id'], 'required'],
      [['freight_invoice_id', 'container_id'], 'integer'],
      [['comment'], 'string'],
      [['container_id'], 'exist', 'skipOnError' => true, 'targetClass' => Container::className(), 'targetAttribute' => ['container_id' => 'id']],
      [['freight_invoice_id'], 'exist', 'skipOnError' => true, 'targetClass' => FreightInvoice::className(), 'targetAttribute' => ['freight_invoice_id' => 'id']],
      [['isNeededOutbound', 'outInvoice'], 'safe'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels() {
    return [
      'id' => Yii::t('app', 'ID'),
      'freight_invoice_id' => Yii::t('app', 'Freight invoice'),
      'container_id' => Yii::t('app', 'Container'),
      'quantity' => Yii::t('app', 'Quantity'),
      'comment' => Yii::t('app', 'Comment'),
      'isNeededOutbound' => Yii::t('app', 'Do you want to select Outbound freight invoice ?'),
      'outInvoice' => Yii::t('app', 'Outbound invoice'),
      'outbound_id' => Yii::t('app', 'Outbound invoice'),
    ];
  }

  /**
   * Gets query for [[Container]].
   *
   * @return ActiveQuery
   */
  public function getContainer() {
    return $this->hasOne(Container::className(), ['id' => 'container_id']);
  }



  /**
   * Gets query for [[FreightInvoice]].
   *
   * @return ActiveQuery
   */
  public function getFreightInvoice() {
    return $this->hasOne(FreightInvoice::className(), ['id' => 'freight_invoice_id'])->inverseOf('freightInvoiceDetails');
  }

  public function getContainerInfo() {
    $cont = $this->container;
    return ($cont->container_type) ? $cont->container_no . ' - ' . $cont->container_type : $cont->container_no; 
  }

  public function getOutboundInvoiceDetail() {
    return $this->hasOne(FreightInvoiceDetail::className(), ['id' => 'outbound_id']);
  }

  public function getInboundInvoiceDetail() {
    return FreightInvoiceDetail::find()->where(['outbound_id' => $this->id])->one();
  }

  /**
   * Gets query for [[FreightInvoiceDetailCosts]].
   *
   * @return ActiveQuery
   */
  public function getFreightInvoiceDetailCosts() {
    return $this->hasMany(FreightInvoiceDetailCost::className(), ['freight_invoice_detail_id' => 'id'])->inverseOf('freightInvoiceDetail');
  }

  /**
   * Gets query for [[FreightInvoiceDetailInvoices]].
   *
   * @return ActiveQuery
   */
  public function getFreightInvoiceDetailInvoices() {
    return $this->hasMany(FreightInvoiceDetailInvoice::className(), ['freight_invoice_detail_id' => 'id'])->inverseOf('freightInvoiceDetail');
  }

  public function summCost() {
    return FreightInvoiceDetailCost::find()->where(['freight_invoice_detail_id' => $this->id])->sum('value');
  }

  public function getCostInCurrency($currency = 'USD') {
      
    return Helpers::convertCurrency($this->summCost(), $this->freightInvoice->currency->code, $currency);

  }

  public function supplierList() {
    $invoiceList = $this->freightInvoiceDetailInvoices;
    $invoice = [];
    foreach($invoiceList as $item) {
      $invoice[] = $item->invoice->supplier->name;
    }
    $invoice = array_unique($invoice);
    if(count($invoice) > 0) {
      return trim(Helpers::arrayToStringRecursive($invoice, ",&emsp;"), ",&emsp;");
    }
  }

  public function getContInvData(){
    
    $cbm = 0; $grossWeight = 0; $suppliers = []; 
    foreach ($this->freightInvoiceDetailInvoices as $fidInv) {
      $contInv = $fidInv->contInv;
      $shipMode = $contInv->shipMode->name ?? '';
      $delTerm = $contInv->deliveryTerm->name ?? '';
      $cbm += $contInv->cbm ?? 0;
      $grossWeight += $contInv->gross_weight ?? 0;
      $suppliers[] = $fidInv->invoice->supplier->name ?? '';
      $cargoType = CargoType::name($contInv->cargo_type ?? '') ?? '';
      $shippingDate = $contInv->shipped_at ?? '';
      $stationDate = $contInv->station_date ?? '';
      $arriveDate = $contInv->arrived_at ?? '';
    }

    return [$shipMode, $delTerm, $cbm, $grossWeight, $suppliers, $cargoType, $shippingDate, $stationDate, $arriveDate]; 

  }

  public function calcCu($containerType, $cbm, $grossWeight){

    $capacity = ContainerType::$capacity[$containerType] ?? 0; 
    $load = ContainerType::$load[$containerType] ?? 0; 
    
    $capacity = $capacity * 0.9; 
    $load = $load * 0.9; 

    $cuCBM = ($capacity > 0 ) ? $cbm / $capacity * 100 : 0;
    $cuWeight = ($load > 0 ) ? $grossWeight / $load * 100 : 0;

    return [$capacity, $load, $cuCBM, $cuWeight];

  }

}
