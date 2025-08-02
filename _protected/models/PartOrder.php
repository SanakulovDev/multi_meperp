<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "part_order".
 *
 * @property int               $id
 * @property string            $order_no
 * @property int               $order_type 1-Regular; 2-Urgent; 3-Additional;
 * @property string            $iss_dt     issued date
 * @property string            $mr_dt      material required date
 * @property int               $contract_id
 * @property int               $created_by
 * @property int               $created_at
 * @property int               $updated_by
 * @property int               $updated_at
 * @property int               $delivery_term_id
 * @property InvoiceDetail[]   $invoiceDetails
 * @property Lc[]              $lcs
 * @property Contract          $contract
 * @property User              $createdBy
 * @property User              $updatedBy
 * @property DeliveryTerm      $deliveryTerm
 * @property PartOrderDetail[] $partOrderDetails
 * @property Part[]            $parts
 */
class PartOrder extends ActiveRecord {

  const REGULAR = 1;
  const URGENT = 2;
  const ADDITIONAL = 3;

  public $orderTypeList = [
    self::REGULAR => 'Regular',
    self::URGENT => 'Urgent',
    self::ADDITIONAL => 'Additional',
  ];

  public static function tableName() {
    return 'part_order';
  }

  public function rules() {
    return [
      [['order_no', 'iss_dt', 'mr_dt', 'contract_id', 'created_by', 'created_at'], 'required'],
      [['order_type', 'contract_id', 'created_by', 'created_at', 'updated_by', 'updated_at', 'delivery_term_id'], 'integer'],
      [['iss_dt', 'mr_dt', 'for_month'], 'safe'],
      [['order_no'], 'string', 'max' => 100],
      [['order_no', 'iss_dt', 'contract_id'], 'unique', 'targetAttribute' => ['order_no', 'iss_dt', 'contract_id']],
      [['contract_id'], 'exist', 'skipOnError' => true, 'targetClass' => Contract::className(), 'targetAttribute' => ['contract_id' => 'id']],
      [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
      [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
      [['delivery_term_id'], 'exist', 'skipOnError' => true, 'targetClass' => DeliveryTerm::className(), 'targetAttribute' => ['delivery_term_id' => 'id']],
    ];
  }

  public function attributeLabels() {
    return [
      'id' => Yii::t('app', 'ID'),
      'order_no' => Yii::t('app', 'Order no'),
      'order_type' => Yii::t('app', 'Order type'),
      'iss_dt' => Yii::t('app', 'Issued date'),
      'mr_dt' => Yii::t('app', 'Material required date'),
      'for_month' => Yii::t('app', 'For month'),
      'contract_id' => Yii::t('app', 'Contract no'),
      'created_by' => Yii::t('app', 'Created by'),
      'created_at' => Yii::t('app', 'Created at'),
      'updated_by' => Yii::t('app', 'Updated by'),
      'updated_at' => Yii::t('app', 'Updated at'),
      'amount' => Yii::t('app', 'Order amount'),
      'delivery_term_id' => Yii::t('app', 'Delivery term'),
    ];
  }

  public function getOrderTypeTextById($id) {
    return $this->orderTypeList[$id];
  }

  public function getOrderTypeText() {
    return $this->orderTypeList[$this->order_type];
  }

  public function getInvoiceDetails() {
    return $this->hasMany(InvoiceDetail::className(), ['part_order_id' => 'id']);
  }

  public function getContract() {
    return $this->hasOne(Contract::className(), ['id' => 'contract_id']);
  }

  public function getCreatedBy() {
    return $this->hasOne(User::className(), ['id' => 'created_by']);
  }

  public function getUpdatedBy() {
    return $this->hasOne(User::className(), ['id' => 'updated_by']);
  }

  public function getPartOrderDetails() {
    return $this->hasMany(PartOrderDetail::className(), ['part_order_id' => 'id']);
  }

  public function getDeliveryTerm() {
    return $this->hasOne(DeliveryTerm::className(), ['id' => 'delivery_term_id']);
  }

  public function getAmount() {
    $amount = 0;
    foreach($this->partOrderDetails as $orderDetail) {
      $part_contract = ContractDetail::find() 
                                     ->where([
                                       'contract_id' => $this->contract_id,
                                       'part_id' => $orderDetail->part_id,
                                       'delivery_term_id' => $this->delivery_term_id
                                     ])
                                     ->one();
      if(isset($part_contract)) {
        $amount += $orderDetail->qty * $part_contract->price;
      }
    }
    return $amount;
  }

  public static function getInvoiceAmount($orderId) {
    $queryInvoiceDetail = InvoiceDetail::find()
                                       ->select('cont_inv_id')
                                       ->where(['part_order_id' => $orderId])
                                       ->groupBy('cont_inv_id');
    $queryContainerInvoice = ContainerInvoice::find()
                                             ->select('invoice_id')
                                             ->where(['in', 'id', $queryInvoiceDetail])
                                             ->groupBy('invoice_id');
    $invoice = Invoice::find()
                      ->where(['in', 'id', $queryContainerInvoice])
                      ->sum('invoice_amount');
    return $amount = (isset($invoice)) ? $invoice : 0;
  }

  public static function getInvoiceDetailAmount($orderId) {
    $invoiceDetail = InvoiceDetail::find()
                                  ->where(['part_order_id' => $orderId])
                                  ->sum('qty * price');
    return $amount = (isset($invoiceDetail)) ? $invoiceDetail : 0;
  }

  public static function getInvoiceDetailPartAmount($orderId, $partId, $contractId) {
    $invoiceDetail = InvoiceDetail::find()
                                  ->where([
                                    'part_order_id' => $orderId,
                                    'part_id' => $partId,
                                    'contract_id' => $contractId,
                                  ])
                                  ->sum('qty * price');
    return $amount = (isset($invoiceDetail)) ? $invoiceDetail : 0;
  }

  public function getParts() {
    return $this->hasMany(Part::className(), ['id' => 'part_id'])->viaTable('part_order_detail', ['part_order_id' => 'id']);
  }

  public function getLcs() {
    return $this->hasMany(Lc::className(), ['part_order_id' => 'id']);
  }

  public static function getMonths($monthsCount = 12, $monthKey = true){
    $months = [];
    for ($i = 0; $i < $monthsCount; $i++){
      $month = date('Y-m', strtotime(date('Y-m-01') .  ' +' . $i . ' months'));
      if($monthKey){
        $months[$month] = $month;
      }else{
        $months[] = $month;
      }
      
    }  
    return $months;
  }

}
