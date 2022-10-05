<?php

namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "pack_level".
 *
 * @property int $id
 * @property int $pack_id
 * @property int $part_id
 * @property int $in_pack_id
 * @property int $quantity
 * @property int $level
 * @property int $version
 * @property int|null $created_by
 * @property int $created_at
 * @property int|null $updated_by
 * @property int|null $updated_at
 *
 * @property Pack $inPack
 * @property Pack $pack
 */
class PackLevel extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pack_level';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pack_id', 'part_id', 'in_pack_id', 'level'], 'required'],
            [['pack_id', 'part_id', 'in_pack_id', 'quantity', 'level', 'version', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
            [['part_id', 'pack_id', 'in_pack_id', 'version'], 'unique', 'targetAttribute' => ['pack_id', 'in_pack_id', 'version']],
            [['in_pack_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pack::className(), 'targetAttribute' => ['in_pack_id' => 'id']],
            [['pack_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pack::className(), 'targetAttribute' => ['pack_id' => 'id']],
            [['part_id'], 'exist', 'skipOnError' => true, 'targetClass' => Part::className(), 'targetAttribute' => ['part_id' => 'id']],
            ['pack_id', 'compare', 'compareAttribute' => 'in_pack_id', 'operator' => '!=', 'type' => 'number'],
        ];
    }

    public function behaviors(){
        return [
            TimestampBehavior::className(),
            BlameableBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'part_id' => Yii::t('app', 'Part'),
            'pack_id' => Yii::t('app', 'Pack'),
            'in_pack_id' => Yii::t('app', 'In Pack'),
            'quantity' => Yii::t('app', 'Quantity'),
            'level' => Yii::t('app', 'Step (1/2)'),
            'version' => Yii::t('app', 'Version'),
            'created_by' => Yii::t('app', 'Created by'),
            'created_at' => Yii::t('app', 'Created at'),
            'updated_by' => Yii::t('app', 'Updated by'),
            'updated_at' => Yii::t('app', 'Updated at'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInPack()
    {
        return $this->hasOne(Pack::className(), ['id' => 'in_pack_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPack()
    {
        return $this->hasOne(Pack::className(), ['id' => 'pack_id']);
    }

    public function getPart()
    {
      return $this->hasOne(Part::className(), ['id' => 'part_id']);
    }

    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    public function getUpdatedAtFormatted(){
        return (!empty($this->updated_at)) ? date('d.m.Y H:i', $this->updated_at) : '';
    }

    public function getCreatedAtFormatted(){
        return (!empty($this->created_at)) ? date('d.m.Y H:i', $this->created_at) : '';
    }
}
