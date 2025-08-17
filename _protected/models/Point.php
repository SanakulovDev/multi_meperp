<?php

namespace app\models;

use app\enums\ShipMode;
use Yii;

/**
 * This is the model class for table "point".
 *
 * @property int $id
 * @property int $ship_mode
 * @property string $name
 *
 * @property Route[] $routes
 * @property Route[] $routes0
 */
class Point extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'point';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ship_mode', 'name'], 'required'],
            [['ship_mode'], 'integer'],
            [['description'], 'string'],
            [['name'], 'string', 'max' => 255],
            [['ship_mode', 'name'], 'unique', 'targetAttribute' => ['ship_mode', 'name'], 'message' => Yii::t('app', 'Dublicate data')]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'ship_mode' => Yii::t('app', 'Ship mode'),
            'name' => Yii::t('app', 'Name'),
            'description' => Yii::t('app', 'Description'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoutesWithFromPoint()
    {
        return $this->hasMany(Route::class, ['from_point_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoutesWithToPoint()
    {
        return $this->hasMany(Route::class, ['to_point_id' => 'id']);
    }

    public function getShipModeName(){
        return ShipMode::name($this->ship_mode);
    }
}
