<?php
namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "stock".
 *
 * @property int       $id
 * @property int       $part_id
 * @property int       $warehouse_id
 * @property string    $qty
 * @property int       $created_at
 * @property int       $updated_at
 * @property Part      $part
 * @property Warehouse $warehouse
 */
class Stock extends ActiveRecord {

  public $part_name;
  public $part_status;
  public $unit;
  public $part_color;
  public $part_state;

  /**
   * {@inheritdoc}
   */
  public function behaviors() {
    return [
      TimestampBehavior::className(),
    ];
  }

  public static function tableName() {
    return 'stock';
  }

  /**
   * {@inheritdoc}
   */
  public function rules() {
    return [
      [['part_id', 'warehouse_id', 'qty'], 'required'],
      [['part_id', 'warehouse_id', 'created_at', 'updated_at'], 'integer'],
      [['qty'], 'number'],
      [['part_id', 'warehouse_id'], 'unique', 'targetAttribute' => ['part_id', 'warehouse_id']],
      [['part_id'], 'exist', 'skipOnError' => true, 'targetClass' => Part::className(), 'targetAttribute' => ['part_id' => 'id']],
      [['warehouse_id'], 'exist', 'skipOnError' => true, 'targetClass' => Warehouse::className(), 'targetAttribute' => ['warehouse_id' => 'id']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels() {
    return [
      'id' => Yii::t('app', 'ID'),
      'part_id' => Yii::t('app', 'Part No'),
      'warehouse_id' => Yii::t('app', 'Warehouse'),
      'qty' => Yii::t('app', 'Quantity'),
      'created_at' => Yii::t('app', 'Created at'),
      'updated_at' => Yii::t('app', 'Updated at'),
      'part_name' => Yii::t('app', 'Part name'),
      'part_color' => Yii::t('app', 'Part color'),
      'unit' => Yii::t('app', 'Unit'),
      'model' => Yii::t('app', 'Model'),
      'part_state' => Yii::t('app', 'State'),
    ];
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
  public function getWarehouse() {
    return $this->hasOne(Warehouse::className(), ['id' => 'warehouse_id']);
  }

  public static function receipt($wh_id, $data, $stock_info = false) {
    $errorlist = [];
    $transaction = Yii::$app->db->beginTransaction();
    foreach($data as $key => $item) {
      if($stock_info || (isset($item['wh_id']) && $item['wh_id'] > 0)){
        $stock_item = Stock::find()->where(['warehouse_id' => $item['wh_id'], 'part_id' => $item['part_id']])->one();
      }else{
        $stock_item = Stock::find()->where(['warehouse_id' => $wh_id, 'part_id' => $item['part_id']])->one();
      }
      if($stock_item) {
        $stock_item->qty = $stock_item->qty + $item['qty'];
        if(!$stock_item->save()) {
          $errorlist[] = 'Stock change problem! Part : '.$stock_item->part->partinfo;
        }
      } else {
        $stock = new Stock();
        $stock->warehouse_id = $wh_id;
        $stock->part_id = $item['part_id'];
        $stock->qty = $item['qty'];
        if(!$stock->save(false)) {
          $errorlist[] = 'Stock insert problem! Part : '.Part::findOne($item['part_id'])->partinfo;
        }
      }
    }
    if(count($errorlist) == 0) {
      $transaction->commit();

      return [
        'success' => true
      ];
    } else {
      $transaction->rollBack();

      return [
        'success' => false,
        'errorlist' => $errorlist
      ];
    }
  }

  public static function issue($wh_id, $data, $stock_info=false) {
    $errorlist = [];
    $transaction = Yii::$app->db->beginTransaction();
    $key = 0;
    foreach($data as $item) {
      $key++;
      if($stock_info || (isset($item['wh_id']) && $item['wh_id'] > 0)){
        $stock_item = Stock::find()->where(['warehouse_id' => $item['wh_id'], 'part_id' => $item['part_id']])->one();
      }
      else{
        $stock_item = Stock::find()->where(['warehouse_id' => $wh_id, 'part_id' => $item['part_id']])->one();
      }
      // vd($stock_item);
      if($stock_item) {
        if(($stock_item->qty - $item['qty']) >= 0) {
          $stock_item->qty = $stock_item->qty - $item['qty'];
          /**
           * Anvar Sanakulov
           * 2024-01-25
           * @sanakulov_Dev
           * StockInfo ga yozadigan joyi
           */
          if($stock_info){
            $stockInfo = new StockInfo();
            $stockInfo->stock_info_wrapper_id = $item['stock_info_wrapper_id'];
            $stockInfo->part_id = $item['part_id'];
            $stockInfo->warehouse_id = $item['wh_id'];
            $stockInfo->qty = $item['qty'];
            $stockInfo->save(false);
          }
          if(!$stock_item->save()) {
            $errorlist[] = 'Stock change problem! Part : '.$stock_item->part->part_no;
          }
        } else {
          $errorlist[] = $stock_item->part->partinfo.', '.Yii::t('app', 'Stock').' : '.($stock_item->qty*1);
        }
      } else {
        $errorlist[] = Part::findOne($item['part_id'])->partinfo.', '.Yii::t('app', 'Stock').' : 0';
      }
    }
    if(count($errorlist) == 0) {
      $transaction->commit();

      return [
        'success' => true
      ];
    } else {
      $transaction->rollBack();

      return [
        'success' => false,
        'errorlist' => $errorlist
      ];
    }
  }

  // bu method skladni ostatkasi yetmasa ham ayiradi, yani minusga haydeydi :)
  public static function issueFromShop($wh_id, $data) {
    $errorlist = [];
    $transaction = Yii::$app->db->beginTransaction();
    $key = 0;
    foreach($data as $item) {
      $key++;
      $stock_item = Stock::find()->where(['warehouse_id' => $wh_id, 'part_id' => $item['part_id']])->one();
      if($stock_item) {
        $stock_qty = ($stock_item) ? $stock_item->qty : 0;
        $stock_item->qty = $stock_qty - $item['qty'];
      } else {
        $stock_item = new Stock();
        $stock_item->warehouse_id = $wh_id;
        $stock_item->part_id = $item['part_id'];
        $stock_item->qty = $item['qty']*(-1);
      }
      if(!$stock_item->save()) {
        $errorlist[] = 'Stock change problem! Part : '.$stock_item->part->partinfo;
      }
    }
    if(count($errorlist) == 0) {
      $transaction->commit();

      return [
        'success' => true
      ];
    } else {
      $transaction->rollBack();

      return [
        'success' => false,
        'errorlist' => $errorlist
      ];
    }
  }

  // Bu funksiya berilgan part ni BOM ga asosan
  // componentlarini liniya qoldig'idan kamaytiradi
  // i/ch gan mahsulot (part_id) qolfig'ini esa oshiradi
  public static function consumption($production_order, $line2out = null, $stock_info = true) {
    $part_id = $production_order->part_id;
    $qty = $production_order->quantity + $production_order->mix_quantity;
    $mix_quantity = $production_order->mix_quantity;
    $errorlist = [];
    if(empty($production_order->part->warehouse_id)) {
      $errorlist[] = 'FLOC not set to ready part. Ready part: '.$production_order->part->part_no;
    }
    if(count($production_order->part->components) == 0 and is_array($production_order->part->components)) {
      $errorlist[] = 'Ready part has no componenets. Ready part: '.$production_order->part->part_no;
    }
    $warehouse_id = $production_order->part->warehouse_id;
    $key = 0;
    //$transaction = Yii::$app->db->beginTransaction();
    if(count($errorlist) == 0) {
      // Partning BOM dagi componentlarini ULOC laridan kamaytirish
      // vd(count($production_order->part->components));
      if($stock_info){
        foreach($production_order->part->components as $component) {
          $key++;
          $stock_item = Stock::find()->where(['warehouse_id' => $component->warehouse_id, 'part_id' => $component->sub_part_id])->one();
          $qty_to_issue =
            (!empty(Yii::$app->params['cutted_coil_part_type_id']) and $production_order->part->part_type_id == Yii::$app->params['cutted_coil_part_type_id'])
              ? $qty
              : $qty*$component->usage_qty;
          if($stock_item) {
            $stock_qty = ($stock_item) ? $stock_item->qty : 0;
            $stock_item->qty = $stock_qty - $qty_to_issue;
          } else {
            $stock_item = new Stock();
            $stock_item->warehouse_id = $component->warehouse_id;
            $stock_item->part_id = $component->sub_part_id;
            $stock_item->qty = $qty_to_issue*(-1);
          }
          if(!$stock_item->save()) {
            $errorlist[] = 'Error in consumption process. Component part: '.$component->subPart->part_no;
          } elseif($line2out != 1) {
            $productionOrderSub = new ProductionOrderSub();
            $productionOrderSub->production_order_id = $production_order->id;
            $productionOrderSub->sub_part_id = $component->sub_part_id;
            $productionOrderSub->qty = $qty_to_issue;
            $productionOrderSub->warehouse_id = $component->warehouse_id;
            if(!$productionOrderSub->save()) {
              echo "<pre>Error111: ";
              print_r($productionOrderSub->errors);
              echo "</pre>";
              $errorlist[] = 'Error in inserting to sub table. Component part: '.$component->subPart->part_no;
            }
          }
        }
      }
      
      if($line2out != 1) {
        // Part ni o'zini ishlab chiqarlgan liniya ostatkasiga qo'shib qo'yish.
        $stock_part = Stock::find()->where(['warehouse_id' => $warehouse_id, 'part_id' => $part_id])->one();
        if($stock_part) {
          $stock_qty = ($stock_part) ? $stock_part->qty : 0;
          $stock_part->qty = $stock_qty + $qty;
        } else {
          $stock_part = new Stock();
          $stock_part->warehouse_id = $warehouse_id;
          $stock_part->part_id = $part_id;
          $stock_part->qty = $qty;
        }
        if(!$stock_part->save()) {
          $errorlist[] = 'Error in consumption process. Ready part: '.$production_order->part->part_no;
        }
      }
    }
    if(count($errorlist) == 0)
      return ['success' => true];
    else
      return ['success' => false, 'errorlist' => $errorlist];
  }

  // Bu funksiya consumption() funksiyasini teskarisi bo'lib,
  // consumption() da qilingan ishlarni ortga qaytaradi
  public static function deconsumption($production_order, $line2out = null, $stock_info = true) {
    $part_id = $production_order->part_id;
    $qty = $production_order->quantity + $production_order->mix_quantity;
    $mix_quantity = $production_order->mix_quantity;
    $errorlist = [];
    if(empty($production_order->part->warehouse_id)) {
      $partNo = Part::findOne($part_id);
      $errorlist[] = 'FLOC not set to ready part. Ready part: '.$partNo->part_no;
    }
    $warehouse_id = $production_order->part->warehouse_id;
    $key = 0;
    if(count($errorlist) == 0) {
      // Partning BOM dagi componentlarini ULOC lariga qo`shadi
      if($stock_info || $mix_quantity > 0){
        foreach($production_order->consumptionDetails as $component) {
          $key++;
          $stock_item = Stock::find()->where(['warehouse_id' => $component->warehouse_id, 'part_id' => $component->sub_part_id])->one();
          if($stock_item) {
            $stock_qty = ($stock_item) ? $stock_item->qty : 0;
            $stock_item->qty = $stock_qty + $component->qty;
          } else {
            $stock_item = new Stock();
            $stock_item->warehouse_id = $component->warehouse_id;
            $stock_item->part_id = $component->sub_part_id;
            $stock_item->qty = $component->qty;
          }
          if(!$stock_item->save()) {
            $errorlist[] = 'Error in consumption process. Component part: '.$component->subPart->part_no;
          }
        }
      }
      
      if($line2out != 1) {
        // Part ni o'zini ishlab chiqarlgan liniya ostatkasidan ayrib qo`yadi.
        $stock_part = Stock::find()->where(['warehouse_id' => $warehouse_id, 'part_id' => $part_id])->one();
        if($stock_part) {
          $stock_qty = ($stock_part) ? $stock_part->qty : 0;
          $stock_part->qty = $stock_qty - $qty;
        } else {
          $stock_part = new Stock();
          $stock_part->warehouse_id = $warehouse_id;
          $stock_part->part_id = $part_id;
          $stock_part->qty = $qty*(-1);
        }
        if(!$stock_part->save()) {
          $errorlist[] = 'Error in consumption process. Ready part: '.$production_order->part->part_no;
        }
      }
    }
    if(count($errorlist) == 0)
      return ['success' => true];
    else
      return ['success' => false, 'errorlist' => $errorlist];
  }

  public function getUpdatedAtFormatted() {
    return (!empty($this->updated_at)) ? date('d.m.Y H:i', $this->updated_at) : '';
  }

  public function getCreatedAtFormatted() {
    return (!empty($this->created_at)) ? date('d.m.Y H:i', $this->created_at) : '';
  }



  // get stock
  public static function getStockPart($part_id)
  {
    // sum
    $sum = Stock::find()->where(['part_id' => $part_id])->sum('qty');
    return $sum;
  }
}
