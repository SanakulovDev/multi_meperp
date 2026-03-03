<?php

namespace app\models;

use Yii;
use yii\base\Model;
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
class ProductionPowerDynamic extends Model
{
    public  $part_id;
    public  $part_name;
    public  $test_pr;
    public  $target_date;
    public function rules()
    {
        return [
            [['part_id', 'target_date', 'part_name', 'test_pr'], 'required'],
            [['part_id'], 'integer'],
            [['target_date'], 'safe'],
            [['part_name', 'test_pr'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'part_id' => Yii::t('app', 'Part ID'),
            'part_name' => Yii::t('app', 'Наименование'),
            'test_pr' => Yii::t('app', 'Test Pr'),
            'target_date' => Yii::t('app', 'Target Date'),
        ];
    }
}
