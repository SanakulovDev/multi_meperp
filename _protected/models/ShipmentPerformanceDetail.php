<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "shipment_performance_detail".
 *
 * @property int $id
 * @property int $shipment_performance_id
 * @property int $part_id
 * @property float|null $less_doh_qty
 * @property float|null $shipped_qty
 * @property string|null $shipped_at
 * @property float|null $over_doh_qty
 *
 * @property Part $part
 * @property ShipmentPerformance $shipmentPerformance
 */
class ShipmentPerformanceDetail extends \yii\db\ActiveRecord
{
    const STATUS_UNDER = 0;
    const STATUS_OK = 1;
    const STATUS_OVER = 2;
    const STATUS_NOT_SHIPPED = 3;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shipment_performance_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['shipment_performance_id', 'part_id'], 'required'],
            [['shipment_performance_id', 'part_id', 'doh'], 'integer'],
            [['less_doh_qty', 'shipped_qty', 'over_doh_qty'], 'number'],
            [['shipped_at'], 'safe'],
            [['part_id'], 'exist', 'skipOnError' => true, 'targetClass' => Part::className(), 'targetAttribute' => ['part_id' => 'id']],
            [['shipment_performance_id'], 'exist', 'skipOnError' => true, 'targetClass' => ShipmentPerformance::className(), 'targetAttribute' => ['shipment_performance_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'shipment_performance_id' => Yii::t('app', 'Shipment Performance ID'),
            'part_id' => Yii::t('app', 'Part ID'),
            'less_doh_qty' => Yii::t('app', 'Less Doh Qty'),
            'shipped_qty' => Yii::t('app', 'Shipped Qty'),
            'shipped_at' => Yii::t('app', 'Shipped At'),
            'over_doh_qty' => Yii::t('app', 'Over Doh Qty'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPart()
    {
        return $this->hasOne(Part::className(), ['id' => 'part_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShipmentPerformance()
    {
        return $this->hasOne(ShipmentPerformance::className(), ['id' => 'shipment_performance_id']);
    }

    public function getStatus(){
        if($this->shipped_qty != 0){
            if($this->less_doh_qty == $this->shipped_qty){
                return SELF::STATUS_OK; // ok
            }elseif($this->less_doh_qty > $this->shipped_qty){
                return self::STATUS_UNDER; // under
            }else{
                return self::STATUS_OVER; // over
            }
        }else{
            return self::STATUS_NOT_SHIPPED; // not shipped
        }
        
    }
}
