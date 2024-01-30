<?php
namespace app\models;

use app\components\Helpers;
use Da\QrCode\Contracts\ErrorCorrectionLevelInterface;
use Da\QrCode\QrCode;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use app\models\StockInfoWrapper;
/**
 * This is the model class for table "production_order".
 *
 * @property int                      $id
 * @property int                      $part_id
 * @property int                      $product_specification_id
 * @property string                   $current_event
 * @property int                      $current_seq
 * @property int                      $is_printed
 * @property int                      $is_label
 * @property int                      $quantity
 * @property int                      $created_by
 * @property int                      $updated_by
 * @property int                      $created_at
 * @property int                      $updated_at
 * @property int                      $line
 * @property User                     $createdBy
 * @property Part                     $part
 * @property User                     $updatedBy
 * @property ProductionOrderDefect[]  $productionOrderDefects
 * @property ProductionOrderHistory[] $productionOrderHistories
 * @property string                   $serial_number [varchar(50)]
 */
class ProductionOrder extends ActiveRecord {

  /**
   * {@inheritdoc}
   */
  const EVENT_INITIAL = '100';
  const EVENT_PRODUCED = '600';
  const EVENT_SHIPPED = '700';
  const EVENT_ARRIVED = '800';

  const LABEL_INDIVIDUAL = 0;
  const LABEL_PACK = 1;
  const LABEL_ACTUAL = 2;
  public $counted;

  public $eventList = [
    self::EVENT_INITIAL => '100',
    self::EVENT_PRODUCED => '600',
    self::EVENT_SHIPPED => '700',
    self::EVENT_ARRIVED => '800'
  ];

  
  public static function isBulkList() {
    return [1 => Yii::t('app', 'Yes'), 0 => Yii::t('app', 'No')];
  }

  public static function stateList() {
    return [
      self::LABEL_INDIVIDUAL => Yii::t('app', 'Individual'),
      self::LABEL_PACK => Yii::t('app', 'Pack')
    ];
  }

  public static function stateListFull() {
    return [
      self::LABEL_INDIVIDUAL => Yii::t('app', 'Individual'),
      self::LABEL_PACK => Yii::t('app', 'Pack'),
      self::LABEL_ACTUAL => Yii::t('app', 'actual'),
    ];
  }

  public $filter_from, $filter_to, $quantity_of_copy, $is_bulk;

  public static function tableName() {
    return 'production_order';
  }

  /**
   * {@inheritdoc}
   */
  public function rules() {
    return [
      [['part_id', 'current_seq', 'quantity', 'line'], 'required'],
      [['quantity_of_copy'], 'required', 'on' => 'create'],
      [['part_id', 'is_bulk', 'product_specification_id', 'current_seq', 'is_printed', 'is_label', 'created_by', 'updated_by', 'created_at', 'updated_at', 'line', 'stock_info_wrapper_id'], 'integer'],
      [['current_event'], 'string', 'max' => 3],
      [['serial_number'], 'string', 'max' => 50],
      ['quantity', 'number'],
      ['quantity', 'compare', 'compareValue' => 0, 'operator' => '>', 'on' => 'create'],
      ['quantity', 'compare', 'compareValue' => 0, 'operator' => '>=', 'on' => 'create-isbulk'],
      [['quantity_of_copy'], 'integer', 'min' => 1, 'max' => 99],
      [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
      [['part_id'], 'exist', 'skipOnError' => true, 'targetClass' => Part::className(), 'targetAttribute' => ['part_id' => 'id']],
      [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels() {
    return [
      'id' => Yii::t('app', 'ID'),
      'part_id' => Yii::t('app', 'Product'),
      'serial_number' => Yii::t('app', 'Serial number'),
      'current_event' => Yii::t('app', 'Current event'),
      'current_seq' => Yii::t('app', 'Sequence'),
      'is_printed' => Yii::t('app', 'Printed'),
      'is_label' => Yii::t('app', 'Label type'),
      'quantity' => Yii::t('app', 'Quantity'),
      'quantity_of_copy' => Yii::t('app', 'Copies'),
      'created_by' => Yii::t('app', 'Created by'),
      'updated_by' => Yii::t('app', 'Updated by'),
      'created_at' => Yii::t('app', 'Created at'),
      'updated_at' => Yii::t('app', 'Updated at'),
      'is_bulk' => Yii::t('app', 'BULK'),
      'product_specification_id' => Yii::t('app', 'Production specification'),
      'line' => Yii::t('app', 'Line'),
      'stock_info_wrapper_id' => Yii::t('app', 'Stock Info Code')
    ];
  }

  /**
   * @return string
   */
  public function generateQrcode() {
    $qrCode = (new QrCode($this->serial_number))
      ->setErrorCorrectionLevel(ErrorCorrectionLevelInterface::HIGH);
    $pngData = $qrCode->writeString();
    return base64_encode(($pngData));
  }

  public function generateSerialNumber() {
    $serial_array = [
      $this->part->part_no,
      date('ymd', $this->created_at),
      $this->current_seq,
      $this->quantity
    ];
    return implode(':', $serial_array);
  }

  public function getCreatedBy() {
    return $this->hasOne(User::className(), ['id' => 'created_by']);
  }

  /**
   * @return ActiveQuery
   */
  public function getPart() {
    return $this->hasOne(Part::className(), ['id' => 'part_id']);
  }

  /**
   * @return ActiveQuery
   */
  public function getUpdatedBy() {
    return $this->hasOne(User::className(), ['id' => 'updated_by']);
  }
  /**
   * getStockInfoWrapper
   */
  public function getStockInfoWrapper()
  {
    return $this->hasOne(StockInfoWrapper::className(), ['id'=>'stock_info_wrapper_id']);
  }
  /**
   * @return ActiveQuery
   */
  public function getProductionOrderDefects() {
    return $this->hasMany(ProductionOrderDefect::className(), ['production_order_id' => 'id']);
  }

  /**
   * @return ActiveQuery
   */
  public function getProductionOrderHistories() {
    return $this->hasMany(ProductionOrderHistory::className(), ['production_order_id' => 'id']);
  }

  public static function getCurrentSeq($part_id) {
    $current_seq = self::find()->select('max(current_seq) as current_seq')
                       ->where(['part_id' => $part_id, 'date(from_unixtime(created_at))' => date('Y-m-d')])
                       ->one()
      ->current_seq;
    return ($current_seq) ? $current_seq : 0;
  }

  public function getUpdatedAtFormatted() {
    return (!empty($this->updated_at)) ? date('d.m.Y H:i', $this->updated_at) : '';
  }

  public function getCreatedAtFormatted() {
    return (!empty($this->created_at)) ? date('d.m.Y H:i', $this->created_at) : '';
  }

  public function getIsPrintedText() {
    return ($this->is_printed == 1) ? Yii::t('app', 'Yes') : Yii::t('app', 'No');
  }

  public function getIsLabelText() {
    return self::stateListFull()[$this->is_label];
  }

  public function getConsumptionDetails() {
    return $this->hasMany(ProductionOrderSub::className(), ['production_order_id' => 'id']);
  }

  public static function getOrderBySerial($serial_number) {
    return self::find()->where(['serial_number' => $serial_number])->one();
  }

  public function beforeSave($insert) {
    if(parent::beforeSave($insert)) {
      if($this->isNewRecord) {
        $this->created_by = Yii::$app->user->identity->id;
        $this->updated_by = Yii::$app->user->identity->id;
        $this->updated_at = time();
      } else {
        $this->updated_by = Yii::$app->user->identity->id;
        $this->updated_at = time();
      }
      return true;
    } else {
      return false;
    }
  }

  public static function writeToMonitor($model, $copyCount, $productionDate = null, $shift= null)
  {
    if(!$productionDate) {
      $currShiftData = Helpers::getShift();
      $productionDate = $currShiftData['productionDate'];
      $shift = $currShiftData['shift'];

    }
    // Dastlab header tablega ma'lumot yozamiz
    $result = ProductionMonitor::write($model->part->warehouse_id, $productionDate, $shift);
    if ($result["status"] === 0) {
      Yii::$app->session->setFlash("error", Helpers::arrayToStringRecursive($result["errors"]));
      return false;
    }
    PartProductionMonitor::setProduced(
      $result["data"]->id,
      $model->part_id,
      $model->quantity * $copyCount,
      Yii::$app->user->identity->id
    );
    return true;
  }


  public static function createProdOrders($post_params, $shift_crt_at, $stock_info = false) {
    $err = 0;
    $err_sms = '';
    $modelPo = new ProductionOrder();
    $modelPo->load($post_params);
    $modelPo->current_event = (isset($post_params['produced'])) ? ProductionOrder::EVENT_PRODUCED : ProductionOrder::EVENT_INITIAL;
    $modelPo->current_seq = $modelPo->getCurrentSeq($modelPo->part_id) + 1;
    $modelPo->created_at = time();
    if(isset($post_params['shift'])) {
      if($post_params['shift'] == 1) {
        $modelPo->created_at = $shift_crt_at;
      }
    }
    $modelPo->serial_number = $modelPo->generateSerialNumber();
    $modelPo->is_label = $modelPo->quantity > 0 ? ProductionOrder::LABEL_ACTUAL : ProductionOrder::LABEL_INDIVIDUAL;
    $spec = ProductSpecification::find()->where(['part_id' => $modelPo->part_id, 'status' => ProductSpecification::STATUS_ACTIVE])->one();
    $modelPo->product_specification_id = $spec ? $spec->id : null;
    if($modelPo->save()) {
      // vd($modelPo); 
      if($stock_info && $modelPo->stock_info_wrapper_id){
        $data['wrapper_id'] = $modelPo->stock_info_wrapper_id;
        $data['p_order_id'] = $modelPo->id;
        $stock_issue = StockInfoWrapper::issue($data, $modelPo->quantity);
        if(!$stock_issue['success']){
          $err = 1;
          $message = '';
          // vd($stock_issue);
          $errors = implode($stock_issue['errorList']);
          $err_sms = Yii::t('app', $message.'<br>'.$errors);
        }
      }
      
      if($modelPo->is_label === ProductionOrder::LABEL_ACTUAL){
        $result = self::writeToMonitor(
          $modelPo,
          $post_params["ProductionOrder"]["quantity_of_copy"]
        );
        if (!$result) {
          $err = 1;
        }
      }

      if($modelPo->current_event == ProductionOrder::EVENT_PRODUCED) {
        $resultCons['success'] = true;
        if(empty($modelPo->stock_info_wrapper_id)){
          $resultCons = Stock::consumption($modelPo);
        }
        if($resultCons['success'] != 1) {
          $err = 1;
          $message = 'Production order not created. Something is wrong.';
          $errors = implode('<br>', $resultCons['errorlist']);
          $err_sms = Yii::t('app', $message.'<br>'.$errors);
        }
      }
      $new_ids[] = $modelPo->id;
    } else {
      $err = 1;
      $message = 'Production order not created.';
      $errors = '';
      foreach($modelPo->errors as $err) {
        foreach($err as $err_text) {
          $errors .= '<br>'.$err_text;
        }
      }
      $err_sms = Yii::t('app', $message.'<br>'.$errors);
    }
    if($err == 0) {
      return ['success' => true];
    } else {
      return ['success' => false, 'errorlist' => $err_sms];
    }
  }



  // lines
  public static function getLines()
  {
    return [
      1 => Yii::t('app', 'Line').' 1',
      2 => Yii::t('app', 'Line').' 2',
      3 => Yii::t('app', 'Line').' 3',
      4 => Yii::t('app', 'Line').' 4',
      5 => Yii::t('app', 'Line').' 5',
      6 => Yii::t('app', 'Line').' 6',
      7 => Yii::t('app', 'Line').' 7',
    ];
  }

  // getShifts
  public static function getShifts()
  {
    return [
      1 => Yii::t('app', 'Shift').' 1',
      2 => Yii::t('app', 'Shift').' 2',
    ];
  }
}
