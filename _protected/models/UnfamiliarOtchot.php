<?php

namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "{{%unfamiliar_otchot}}".
 *
 * @property int $id
 * @property int|null $user_id
 * @property int $part_id
 * @property int|null $quantity
 * @property string|null $location
 * @property string|null $status
 * @property string|null $expected_arrival_date
 * @property string|null $remark
 * @property string $created_at
 * @property string $updated_at
 */
class UnfamiliarOtchot extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        return [
            [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'user_id',
                'updatedByAttribute' => false,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%unfamiliar_otchot}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'part_id', 'quantity'], 'integer'],
            [['part_id', 'quantity', 'expected_arrival_date'], 'required'],
            [['expected_arrival_date', 'created_at', 'updated_at'], 'safe'],
            [['location', 'remark'], 'string', 'max' => 255],
            [['status'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User'),
            'part_id' => Yii::t('app', 'Part'),
            'quantity' => Yii::t('app', 'Quantity'),
            'location' => Yii::t('app', 'Местоположение груза'),
            'status' => Yii::t('app', 'Статус груза '),
            'expected_arrival_date' => Yii::t('app', 'Ожидемая дата прибытия'),
            'remark' => Yii::t('app', 'Remark'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    public function getPart()
    {
        return $this->hasOne(Part::class, ['id'=>'part_id']);
    }
    
    public function getUser()
    {
        return $this->hasOne(User::class, ['id'=>'user_id']);
    }
}
