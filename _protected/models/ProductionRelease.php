<?php

namespace app\models;

use Yii;
use app\models\Part;
use app\models\ProductionOrder;
use app\models\ProductionPower;
/**
 * This is the model class for table "production_release".
 *
 * @property int $id
 * @property int|null $part_id
 * @property string|null $part_name
 * @property int|null $line
 * @property string|null $pr_order_number
 * @property string|null $target_date
 * @property string|null $shift
 * @property string|null $time
 * @property int|null $quantity
 */
class ProductionRelease extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'production_release';
    }

    public $planQty = 0;
    public $mixerPlan= 0;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['part_id', 'part_name', 'line', 'pr_order_number', 'target_date', 'shift', 'time', 'quantity'], 'required'],
            [['part_id', 'line', 'quantity', 'created_by', 'status', 'updated_by'], 'integer'],
            [['target_date', 'created', 'updated'], 'safe'],
            [['part_name', 'pr_order_number', 'shift', 'time'], 'string', 'max' => 255],
            [['fact', 'planQty', 'mixerPlan'], 'number']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'part_id' => Yii::t('app', 'Part ID'),
            'part_name' => Yii::t('app', 'Наименование'),
            'line' => Yii::t('app', 'Line'),
            'pr_order_number' => Yii::t('app', 'Production Order Number'),
            'target_date' => Yii::t('app', 'Target date'),
            'shift' => Yii::t('app', 'Shift'),
            'time' => Yii::t('app', 'Time'),
            'quantity' => Yii::t('app', 'Quantity'),
            'fact' => Yii::t('app', 'Fact'),
            'planQty' => Yii::t('app', 'Plan Qty'),
            'mixerPlan' => Yii::t('app', 'Mixer Plan'),
            'status' => Yii::t('app', 'Status'),
            'updated_by' => Yii::t('app', 'Updated By'),
        ];
    }

    public function getPart()
    {
        return $this->hasOne(Part::className(), ['id' => 'part_id']);
    }


    // 
    public static function selectTimes()
    {

        return [
            0 => 'Секунды',
            1 => 'Минуты',
            2 => 'Часы',
            3 => 'Дни',
            4 => 'Недели',
            5 => 'Месяцы',
            6 => 'Годы',
        ];
    }

    // production power integration
    public  function getPowerPlan()
    {
      return $this->hasOne(ProductionPower::className(), ['part_id' => 'part_id', 'line' => 'line']);
    }

    

    // norma rasxod product_specification_item 
    const STATUS_ACTIVE = 1;
    public static function getProductSpecificationItems($part_id,$forma=0, $state = 1)
    {
      $model = ProductSpecification::find()->where(['part_id' => $part_id, 'status' => self::STATUS_ACTIVE])->one();
      $data = [];
      if($model){
        if($forma == 1){
          return $model;
        }
        $items = $model->productSpecificationItems;
        foreach($items as $item){
          
          if($item->part){
            if($state == 1){
              if($item->part->state != 0){
                $data[] = $item;
              }
            }
            elseif($state == 0){
              if($item->part->state == $state){
                $data[] = $item;
              }
            }
            else{
              $data[] = $item;
            }
            // return $item;
          }
        }
        return $data;
      }
      return null;
    }


    public static function  getData($models, $item)
    {
      $mainSpecification = self::getProductSpecificationItems($item->part_id, 1);
      $data = [];
      if(!empty($models)){
        foreach($models as $key => $model){
            $productionReleaseItems = ProductionReleaseItem::find()->where(['release_id' => $item->id])->andWhere(['partId' => $model->part_id])->one();
            $data[$key]['part_id'] = $model->part_id;
            $data[$key]['part_name'] = $model->part? substr($model->part->part_no.'  '.$model->part->part_name, 0, 45) : '';
            $data[$key]['main_qty']  = round($model->usage_qty / $mainSpecification->amount * $item->mixerPlan, 2);
            $data[$key]['unit'] = $item->part->unit->unit_value;
            $data[$key]['protsent'] = round($model->usage_qty / $mainSpecification->amount * 100, 2  );
            $data[$key]['qty'] = $productionReleaseItems? round($productionReleaseItems->qty) : 0;
            $data[$key]['comment'] = $productionReleaseItems? $productionReleaseItems->comment : '';
            $data[$key]['status'] = $productionReleaseItems? $productionReleaseItems->status : 0;
          }
      }
      return $data;
    }

    // get release production_release_item
    public function getReleaseItems()
    {
      return $this->hasMany(ProductionReleaseItem::className(), ['release_id' => 'id']);
    }



    // partId berilganda barcha liniyalardagi releaseId larni olish
    public static function getReleaseId($date)
    {
      $data       = [];
      $lines      = ProductionOrder::getLines();
      $todayDate  = date('Y-m-d');
      foreach($lines as $key => $line){
        $model      = ProductionRelease::find()->where(['line' => $key])->andWhere(['target_date'=>$date])->orderBy(['id'=>SORT_ASC])->one();
        if($model){
          $data[]   = $model->id;
        }
        else{
          $data[]   = 0;
        }
      }
      return $data;
    }
}
