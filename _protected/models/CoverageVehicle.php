<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "coverage_vehicle".
 *
 * @property int $id
 * @property string|null $type D - Daily, W - Weekly
 * @property int $model_id
 * @property int|null $stock
 * @property int|null $intransit
 * @property int|null $orders
 * @property int|null $doh
 * @property string|null $stock_out
 * @property string|null $calc_at
 *
 * @property ProductModel $model
 * @property CoverageVehicleDetail[] $coverageVehicleDetails
 */
class CoverageVehicle extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'coverage_vehicle';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['model_id'], 'required'],
            [['model_id', 'stock', 'uamstock', 'intransit', 'orders', 'doh'], 'integer'],
            [['stock_out', 'calc_at'], 'safe'],
            [['type'], 'string', 'max' => 2],
            [['model_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductModel::className(), 'targetAttribute' => ['model_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'type' => Yii::t('app', 'Type'),
            'model_id' => Yii::t('app', 'Model ID'),
            'stock' => Yii::t('app', 'Stock'),
            'uamstock' => Yii::t('app', 'Stock in UzAutoM'),
            'intransit' => Yii::t('app', 'Intransit'),
            'orders' => Yii::t('app', 'Orders'),
            'doh' => Yii::t('app', 'Doh'),
            'stock_out' => Yii::t('app', 'Stock Out'),
            'calc_at' => Yii::t('app', 'Calc At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModel()
    {
        return $this->hasOne(ProductModel::className(), ['id' => 'model_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCoverageVehicleDetails()
    {
        return $this->hasMany(CoverageVehicleDetail::className(), ['coverage_vehicle_id' => 'id']);
    }
}
