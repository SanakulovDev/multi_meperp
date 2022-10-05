<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "shipment_performance".
 *
 * @property int $id
 * @property string $report_date
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property ShipmentPerformanceDetail[] $shipmentPerformanceDetails
 */
class ShipmentPerformance extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shipment_performance';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['report_date'], 'required'],
            [['report_date', 'created_at', 'updated_at'], 'safe'],
            [['report_date'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'report_date' => Yii::t('app', 'Report Date'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShipmentPerformanceDetails()
    {
        return $this->hasMany(ShipmentPerformanceDetail::className(), ['shipment_performance_id' => 'id']);
    }

    public function getCalendarWeek(){
        return date("W", strtotime($this->report_date));
    }
}
