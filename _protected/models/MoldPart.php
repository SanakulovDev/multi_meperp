<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "mold_part". *
 * @property int $id
 * @property int $mold_id
 * @property int $part_id
 * @property int $quantity Qancha chiqishi
 * @property int $created_by
 * @property int $created_at
 * @property int|null $updated_by
 * @property int|null $updated_at
 *
 * @property User $createdBy
 * @property Mold $mold
 * @property Part $part
 * @property User $updatedBy
 */
class MoldPart extends ActiveRecord{
  /**
   * {@inheritdoc}
   */
  public static function tableName(){
    return 'mold_part';
  }

  /**
   * {@inheritdoc}
   */
  public function rules(){
    return [
      [['mold_id', 'part_id'], 'required'],
      [['mold_id', 'part_id', 'quantity', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
      [['mold_id', 'part_id'], 'unique', 'targetAttribute' => ['mold_id', 'part_id']],
      [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
      [['mold_id'], 'exist', 'skipOnError' => true, 'targetClass' => Mold::className(), 'targetAttribute' => ['mold_id' => 'id']],
      [['part_id'], 'exist', 'skipOnError' => true, 'targetClass' => Part::className(), 'targetAttribute' => ['part_id' => 'id']],
      [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels(){
    return [
      'id' => Yii::t('app', 'ID'),
      'mold_id' => Yii::t('app', 'Mold ID'),
      'part_id' => Yii::t('app', 'Part ID'),
      'quantity' => Yii::t('app', 'Qancha chiqishi'),
      'created_by' => Yii::t('app', 'Created by'),
      'created_at' => Yii::t('app', 'Created at'),
      'updated_by' => Yii::t('app', 'Updated by'),
      'updated_at' => Yii::t('app', 'Updated at'),
    ];
  }

  /**
   * Returns a list of behaviors that this component should behave as.
   * @return array
   */
  public function behaviors(){
    return [
      TimestampBehavior::className(),
    ];
  }

  /**
   * @return ActiveQuery
   */
  public function getMold(){
    return $this->hasOne(Mold::className(), ['id' => 'mold_id']);
  }

  /**
   * @return ActiveQuery
   */
  public function getPart(){
    return $this->hasOne(Part::className(), ['id' => 'part_id']);
  }

  /**
   * @return ActiveQuery
   */
  public function getCreatedBy(){
    return $this->hasOne(User::className(), ['id' => 'created_by']);
  }

  /**
   * @return ActiveQuery
   */
  public function getUpdatedBy(){
    return $this->hasOne(User::className(), ['id' => 'updated_by']);
  }
}
