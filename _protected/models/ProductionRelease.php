<?php

namespace app\models;

use Yii;
use app\models\Part;
use app\models\ProductionOrder;
/**
 * This is the model class for table "production_release".
 *
 * @property int $id
 * @property int|null $part_id
 * @property string|null $part_name
 * @property int|null $line
 * @property string|null $pr_order_number
 * @property string|null $target_date
 * @property string|null $shift
 * @property string|null $time
 * @property int|null $quantity
 */
class ProductionRelease extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'production_release';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['part_id', 'part_name', 'line', 'pr_order_number', 'target_date', 'shift', 'time', 'quantity'], 'required'],
            [['part_id', 'line', 'quantity'], 'integer'],
            [['target_date'], 'safe'],
            [['part_name', 'pr_order_number', 'shift', 'time'], 'string', 'max' => 255],
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
            'part_name' => Yii::t('app', 'Наименование'),
            'line' => Yii::t('app', 'Line'),
            'pr_order_number' => Yii::t('app', 'Production Order Number'),
            'target_date' => Yii::t('app', 'Target date'),
            'shift' => Yii::t('app', 'Shift'),
            'time' => Yii::t('app', 'Time'),
            'quantity' => Yii::t('app', 'Quantity'),
        ];
    }

    public function getPart()
    {
        return $this->hasOne(Part::className(), ['id' => 'part_id']);
    }


    // 
    public static function selectTimes()
    {

        return [
            0 => 'Секунды',
            1 => 'Минуты',
            2 => 'Часы',
            3 => 'Дни',
            4 => 'Недели',
            5 => 'Месяцы',
            6 => 'Годы',
        ];
    }
}
