<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "container_invoice".
 *
 * @property int             $id
 * @property int             $container_id
 * @property int             $invoice_id
 * @property string          $app_arr_at
 * @property string          $shipped_at
 * @property int             $shipped_by
 * @property date            $need_at
 * @property int             $current_locate
 * @property int             $current_at
 * @property string          $arrived_at
 * @property int             $arrived_by
 * @property string          $received_at
 * @property int             $received_by
 * @property int             $ship_mode_id
 * @property int             $delivery_term_id
 * @property User            $arrivedBy
 * @property Container       $container
 * @property Invoice         $invoice
 * @property User            $receivedBy
 * @property User            $shippedBy
 * @property ShipMode        $shipMode
 * @property InvoiceDetail[] $invoiceDetails
 */
class ContainerInvoice extends ActiveRecord {

  public $invoice_no;
  public $supplier;
  public $currency;
  public $container_no;
  public $container_type;
  public $err;
  // CONTAINER TYPE LIST
  const CONTAINER_TYPE_20FT = '20ft';
  const CONTAINER_TYPE_40FT = '40ft';
  const CONTAINER_TYPE_40HC = '40hc';

  // the list of customs regimes
  const REGIME_40 = 40;
  const REGIME_70 = 70;
  const REGIME_74 = 74;
  public static $regimeList = [
    self::REGIME_40 => '40 - Выпуск для свободного обращения (импорт)',
    self::REGIME_70 => '70 - Временное хранение',
    self::REGIME_74 => '74 - Таможенный склад'
  ];

  /**
   * @inheritdoc
   */
  public static function tableName() {
    return 'container_invoice';
  }

  /**
   * @inheritdoc
   */
  public function rules() {
    return [
      [['container_id', 'invoice_id', 'app_arr_at', 'shipped_at', 'shipped_by', 'ship_mode_id', 'delivery_term_id'], 'required'],
      [['supplier', 'container_id', 'invoice_id', 'shipped_by', 'arrived_by'], 'integer'],
      [['invoice_no'], 'string', 'max' => 50],
      [['container_type'], 'string'],
      [['container_id', 'container_type', 'invoice_id', 'ship_mode_id', 'shipped_at', 'app_arr_at', 'document_id', 'need_at', 'current_locate', 'current_at', 'arrived_at', 'regime', 'passed_at', 'net_weight', 'gross_weight', 'cbm', 'station_date', 'cargo_type'], 'safe'],
      [['container_id', 'invoice_id', 'shipped_at'], 'unique', 'targetAttribute' => ['container_id', 'invoice_id', 'shipped_at']],
      [['document_id'], 'exist', 'skipOnError' => true, 'targetClass' => Document::className(), 'targetAttribute' => ['document_id' => 'id']],
      [['container_id'], 'exist', 'skipOnError' => true, 'targetClass' => Container::className(), 'targetAttribute' => ['container_id' => 'id']],
      [['invoice_id'], 'exist', 'skipOnError' => true, 'targetClass' => Invoice::className(), 'targetAttribute' => ['invoice_id' => 'id']],
      [['shipped_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['shipped_by' => 'id']],
      [['arrived_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['arrived_by' => 'id']],
      [['received_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['received_by' => 'id']],
      [['ship_mode_id'], 'exist', 'skipOnError' => true, 'targetClass' => ShipMode::className(), 'targetAttribute' => ['ship_mode_id' => 'id']],
      [['regime', 'passed_at'], 'validateRegime', 'except' => ['create', 'update-awb']],
    ];
  }

  public function validateRegime($attributes, $params) {
    // Regime va passed_at fieldlari birgalikda to'ldirilishi kk
    if((!empty($this->regime) and empty($this->passed_at)) or (empty($this->regime) and !empty($this->passed_at))) {
      $this->addError('regime', Yii::t('app', 'Regime and passed date must be filled together'));
      $this->addError('passed_at', Yii::t('app', 'Regime and passed date must be filled together'));
    }
    // Agar arrdt kiritilmagan bo'lsa, regime kiritiilmaydi
    if(!empty($this->regime) and empty($this->arrived_at)) {
      $this->addError('arrived_at', Yii::t('app', 'Arrive date not filled'));
    }
  }

  /**
   * @inheritdoc
   */
  public function attributeLabels() {
    return [
      'id' => Yii::t('app', 'ID'),
      'container_id' => Yii::t('app', 'Container no'),
      'invoice_id' => Yii::t('app', 'Invoice no'),
      'container_type' => Yii::t('app', 'Container type'),
      'delivery_term_id' => Yii::t('app', 'Delivery term'),
      'app_arr_at' => Yii::t('app', 'Approximate arrival date'),
      'shipped_at' => Yii::t('app', 'Shipped at'),
      'shipped_by' => Yii::t('app', 'Shipped by'),
      'document_id' => Yii::t('app', 'Document number'),
      'need_at' => Yii::t('app', 'Need date'),
      'current_locate' => Yii::t('app', 'Current location'),
      'current_at' => Yii::t('app', 'Current date'),
      'arrived_at' => Yii::t('app', 'Arrived at'),
      'arrived_by' => Yii::t('app', 'Arrived by'),
      'received_at' => Yii::t('app', 'Received at'),
      'received_by' => Yii::t('app', 'Received by'),
      'ship_mode_id' => Yii::t('app', 'Ship mode'),
      'regime' => Yii::t('app', 'Customs regime'),
      'passed_at' => Yii::t('app', 'Passed at'),
      'net_weight' => Yii::t('app', 'Net weight'),
      'gross_weight' => Yii::t('app', 'Gross weight'),
      'cbm' => Yii::t('app', 'CBM'),
      'station_date' => Yii::t('app', 'Station date'),
      'cargo_type' => Yii::t('app', 'Cargo type'),
    ];
  }

  public function getDeliveryTerm() {
    return $this->hasOne(DeliveryTerm::className(), ['id' => 'delivery_term_id']);
  }

  public function getContainer() {
    return $this->hasOne(Container::className(), ['id' => 'container_id']);
  }

  public function getInvoice() {
    return $this->hasOne(Invoice::className(), ['id' => 'invoice_id']);
  }

  public function getDocument() {
    return $this->hasOne(Document::className(), ['id' => 'document_id']);
  }

  public function getShippedBy() {
    return $this->hasOne(User::className(), ['id' => 'shipped_by']);
  }

  public function getArrivedBy() {
    return $this->hasOne(User::className(), ['id' => 'arrived_by']);
  }

  public function getReceivedBy() {
    return $this->hasOne(User::className(), ['id' => 'received_by']);
  }

  public function getShipMode() {
    return $this->hasOne(ShipMode::className(), ['id' => 'ship_mode_id']);
  }

  public function getInvoiceDetails() {
    return $this->hasMany(InvoiceDetail::className(), ['cont_inv_id' => 'id']);
  }

  public function getParts() {
    return $this->hasMany(Part::className(), ['id' => 'part_id'])
                ->viaTable('invoice_detail', ['cont_inv_id' => 'id']);
  }

}
