<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "mold_machine".
 *
 * @property int $id
 * @property int $mold_id
 * @property int $machine_id
 * @property int $created_by
 * @property int $created_at
 * @property int|null $updated_by
 * @property int|null $updated_at
 *
 * @property User $createdBy
 * @property Machine $machine
 * @property Mold $mold
 * @property User $updatedBy
 * @property Part $partLists
 */
class MoldMachine extends ActiveRecord{
  /**
   * {@inheritdoc}
   */
  public static function tableName(){
    return 'mold_machine';
  }

  /**
   * {@inheritdoc}
   */
  public function rules(){
    return [
      [['mold_id', 'machine_id', 'created_by', 'created_at'], 'required'],
      [['mold_id', 'machine_id', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
      [['mold_id', 'machine_id'], 'unique', 'targetAttribute' => ['mold_id', 'machine_id']],
      [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
      [['machine_id'], 'exist', 'skipOnError' => true, 'targetClass' => Machine::className(), 'targetAttribute' => ['machine_id' => 'id']],
      [['mold_id'], 'exist', 'skipOnError' => true, 'targetClass' => Mold::className(), 'targetAttribute' => ['mold_id' => 'id']],
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
      'machine_id' => Yii::t('app', 'Machine ID'),
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
  public function getMachine(){
    return $this->hasOne(Machine::className(), ['id' => 'machine_id']);
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
  public function getCreatedBy(){
    return $this->hasOne(User::className(), ['id' => 'created_by']);
  }

  /**
   * @return ActiveQuery
   */
  public function getUpdatedBy(){
    return $this->hasOne(User::className(), ['id' => 'updated_by']);
  }

  public function getPartLists(){
    return $this->hasMany(Part::className(), ['id' => 'part_id'])->viaTable('mold_part', ['mold_id' => 'mold_id']);
  }

  public function getMoldParts(){
    return $this->hasMany(MoldPart::className(), ['mold_id' => 'mold_id']);
  }


}
