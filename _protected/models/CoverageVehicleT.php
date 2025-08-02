<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "coverage_vehicle_t".
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
 */
class CoverageVehicleT extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'coverage_vehicle_t';
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
}
