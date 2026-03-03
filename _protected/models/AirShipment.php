<?php

namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "air_shipment".
 *
 * @property int $id
 * @property int $supplier_id
 * @property float $volume
 * @property float $cost
 * @property string $period
 * @property int|null $air_shipment_reason_id
 * @property string|null $remark
 * @property int|null $created_by
 * @property int $created_at
 * @property int|null $updated_by
 * @property int|null $updated_at
 *
 * @property User $createdBy
 * @property AirShipmentReason $airShipmentReason
 * @property Supplier $supplier
 * @property User $updatedBy
 */
class AirShipment extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'air_shipment';
    }

    public function behaviors()
    {
      return [
        TimestampBehavior::className(),
        BlameableBehavior::className(),
      ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['supplier_id', 'volume', 'cost', 'period'], 'required'],
            [['supplier_id', 'air_shipment_reason_id', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
            [['volume', 'cost'], 'number'],
            [['period'], 'string', 'max' => 10],
            [['remark'], 'string', 'max' => 191],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['air_shipment_reason_id'], 'exist', 'skipOnError' => true, 'targetClass' => AirShipmentReason::className(), 'targetAttribute' => ['air_shipment_reason_id' => 'id']],
            [['supplier_id'], 'exist', 'skipOnError' => true, 'targetClass' => Supplier::className(), 'targetAttribute' => ['supplier_id' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'supplier_id' => Yii::t('app', 'Supplier'),
            'volume' => Yii::t('app', 'Volume (kg)'),
            'cost' => Yii::t('app', 'Cost (USD)'),
            'period' => Yii::t('app', 'Month'),
            'air_shipment_reason_id' => Yii::t('app', 'Reason'),
            'remark' => Yii::t('app', 'Comment'),
            'created_by' => Yii::t('app', 'Created by'),
            'created_at' => Yii::t('app', 'Created at'),
            'updated_by' => Yii::t('app', 'Updated by'),
            'updated_at' => Yii::t('app', 'Updated at'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAirShipmentReason()
    {
        return $this->hasOne(AirShipmentReason::className(), ['id' => 'air_shipment_reason_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSupplier()
    {
        return $this->hasOne(Supplier::className(), ['id' => 'supplier_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    public function getUpdatedAtFormatted()
    {
        return (!empty($this->updated_at)) ? date('d.m.Y H:i', $this->updated_at) : '';
    }

    public function getCreatedAtFormatted()
    {
        return (!empty($this->created_at)) ? date('d.m.Y H:i', $this->created_at) : '';
    }
}
