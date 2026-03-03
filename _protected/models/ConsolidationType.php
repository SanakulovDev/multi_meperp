<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "consolidation_type".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 *
 * @property Mfu[] $mfus
 */
class ConsolidationType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'consolidation_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 50],
            [['description'], 'string', 'max' => 255],
            [['name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'description' => Yii::t('app', 'Description'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMfus()
    {
        return $this->hasMany(Mfu::className(), ['consolidation_type_id' => 'id']);
    }
}
