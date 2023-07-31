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

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['part_id', 'part_name', 'line', 'pr_order_number', 'target_date', 'shift', 'time', 'quantity'], 'required'],
            [['part_id', 'line', 'quantity', 'created_by'], 'integer'],
            [['target_date', 'created', 'updated'], 'safe'],
            [['part_name', 'pr_order_number', 'shift', 'time'], 'string', 'max' => 255],
            [['fact'], 'number']
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

    // fact
    public function getPowerFact()
    {
      return 0;
    }

    // norma rasxod product_specification_item 
    const STATUS_ACTIVE = 1;
    public static function getProductSpecificationItems($part_id,$forma=0)
    {
      $model = ProductSpecification::find()->where(['part_id' => $part_id, 'status' => self::STATUS_ACTIVE])->one();

      if($model){
        if($forma == 1){
          return $model;
        }
        $items = $model->productSpecificationItems;
        return $items;
      }
      return null;
    }
}
