<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "production_plan_sub".
 *
 * @property string $plandate
 * @property int $part_id
 * @property string $qty
 *
 * @property Part $part
 */
class ProductionPlanSub extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'production_plan_sub';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['plandate', 'part_id'], 'required'],
            [['plandate'], 'safe'],
            [['part_id'], 'integer'],
            [['qty'], 'number'],
            [['part_id'], 'exist', 'skipOnError' => true, 'targetClass' => Part::className(), 'targetAttribute' => ['part_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'plandate' => Yii::t('app', 'Plandate'),
            'part_id' => Yii::t('app', 'Part ID'),
            'qty' => Yii::t('app', 'Qty'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPart()
    {
        return $this->hasOne(Part::className(), ['id' => 'part_id']);
    }
}
