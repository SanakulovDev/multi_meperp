<?php

namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "air_shipment_reason".
 *
 * @property int $id
 * @property string $title
 * @property int $created_at
 * @property int|null $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 *
 * @property AirShipment[] $airShipments
 */
class AirShipmentReason extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'air_shipment_reason';
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
            [['title'], 'required'],
            [['created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['title'], 'string', 'max' => 191],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Title'),
            'created_at' => Yii::t('app', 'Created at'),
            'created_by' => Yii::t('app', 'Created by'),
            'updated_at' => Yii::t('app', 'Updated at'),
            'updated_by' => Yii::t('app', 'Updated by'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAirShipments()
    {
        return $this->hasMany(AirShipment::className(), ['air_shipment_reason_id' => 'id']);
    }

    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

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
