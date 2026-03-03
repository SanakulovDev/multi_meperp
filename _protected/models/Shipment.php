<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "shipment".
 *
 * @property int $id
 * @property string $report_date
 * @property string $created_at
 *
 * @property ShipmentDetail[] $shipmentDetails
 */
class Shipment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shipment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['report_date', 'created_at'], 'required'],
            [['report_date', 'created_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'report_date' => Yii::t('app', 'Report date'),
            'created_at' => Yii::t('app', 'Created at'),
            'days' => Yii::t('app', 'Days'),
            'title' => Yii::t('app', 'Calculation'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShipmentDetails()
    {
        return $this->hasMany(ShipmentDetail::className(), ['shipment_id' => 'id']);
    }

    public function getDays(){
        return round((strtotime($this->report_date) - strtotime($this->created_at)) / (60 * 60 * 24));
    }

    public function getTitle(){
        return Yii::t('app', 'Calculation') . ' ' . $this->id;
    }
}
