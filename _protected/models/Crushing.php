<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "crushing".
 *
 * @property int    $id
 * @property int    $part_id
 * @property string $qty
 * @property int    $is_processed
 * @property int    $created_by
 * @property int    $created_at
 * @property int    $updated_by
 * @property int    $updated_at
 * @property User   $createdBy
 * @property Part   $part
 * @property User   $updatedBy
 */
class Crushing extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public $filter_from;
    public $filter_to;

    // Original code

    public static function tableName()
    {
        return 'crushing';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['part_id'], 'required'],
            [['part_id', 'is_processed', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
            [['qty'], 'number'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['part_id'], 'exist', 'skipOnError' => true, 'targetClass' => Part::className(), 'targetAttribute' => ['part_id' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
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
            'qty' => Yii::t('app', 'Quantity'),
            'is_processed' => Yii::t('app', 'Processed'),
            'created_by' => Yii::t('app', 'Created by'),
            'created_at' => Yii::t('app', 'Created at'),
            'updated_by' => Yii::t('app', 'Updated by'),
            'updated_at' => Yii::t('app', 'Updated at'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPart()
    {
        return $this->hasOne(Part::className(), ['id' => 'part_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    public function getUpdatedAtFormatted()
    {
        return (!empty($this->updated_at)) ? date('d.m.Y H:i', $this->updated_at) : '';
    }

    public function getCreatedAtFormatted()
    {
        return (!empty($this->created_at)) ? date('d.m.Y H:i', $this->created_at) : '';
    }

    public function getIsProcessedText()
    {
        return ($this->is_processed == 1) ? Yii::t('app', 'Yes') : Yii::t('app', 'No');
    }

    public function getQtyFormatted()
    {
        return number_format($this->qty);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->created_by = Yii::$app->user->identity->id;
                $this->created_at = time();
            } else {
                $this->updated_by = Yii::$app->user->identity->id;
                $this->updated_at = time();
            }

            return true;
        } else {
            return false;
        }
    }
}
