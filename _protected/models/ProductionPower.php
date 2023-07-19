<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "production_power".
 *
 * @property int $id
 * @property int|null $part_id
 * @property string|null $part_name
 * @property string|null $test_pr
 * @property string|null $target_date
 * @property int|null $line
 * @property int|null $shift
 * @property int|null $unitId
 * @property string|null $plan_power
 * @property string|null $max_power
 * @property string|null $special
 */
class ProductionPower extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'production_power';
    }

    /**
     * {@inheritdoc}
     */
    const SCENARIO_DYNAMIC = 'dynamic';
    public function rules()
    {
        return [
            [['part_id', 'line', 'shift', 'unitId','created_by'], 'integer'],
            [['target_date', 'created', 'updated'], 'safe'],
            [['part_name', 'test_pr', 'plan_power', 'max_power', 'special', 'time'], 'string', 'max' => 255],
            [['line', 'unitId', 'plan_power', 'time', 'max_power', 'special'], 'required', 'on' => self::SCENARIO_DYNAMIC],
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
            'part_name' => Yii::t('app', 'Part name'),
            'test_pr' => Yii::t('app', 'Test Pr'),
            'target_date' => Yii::t('app', 'Дата Запуска'),
            'line' => Yii::t('app', 'Line'),
            'shift' => Yii::t('app', 'Shift'),
            'unitId' => Yii::t('app', 'All units'),
            'plan_power' => Yii::t('app', 'Plan Power'),
            'max_power' => Yii::t('app', 'Max Power'),
            'special' => Yii::t('app', 'Mixer кол-во'),
            'Time' => Yii::t('app', 'Time'),
        ];
    }
    public function getPart()
    {
        return $this->hasOne(Part::className(), ['id' => 'part_id']);
    }
    public function getUnit()
    {
        return $this->hasOne(Unit::className(), ['id' => 'unitId']);
    }
}
