<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "mold".
 *
 * @property int $id
 * @property string $mold_no
 * @property string|null $production_date
 * @property string|null $project_name
 * @property string|null $company_name
 * @property string $part_number
 * @property string|null $part_name
 * @property int $created_by
 * @property int $created_at
 * @property int|null $updated_by
 * @property int|null $updated_at
 *
 * @property Machine[] $machines
 * @property User $createdBy
 * @property User $updatedBy
 * @property MoldMachine[] $moldMachines
 * @property Machine[] $machines0
 * @property MoldPart[] $moldParts
 * @property Part[] $parts
 */
class Mold extends ActiveRecord{
  /**
   * {@inheritdoc}
   */
  public static function tableName(){
    return 'mold';
  }

  /**
   * {@inheritdoc}
   */
  public function rules(){
    return [
      [['mold_no', 'part_number'], 'required'],				
      [['created_by', 'created_at'], 'required', 'on' => 'update'],
      [['production_date'], 'safe'],
      [['created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
      [['mold_no', 'part_number'], 'string', 'max' => 50],
      [['project_name', 'company_name', 'part_name'], 'string', 'max' => 100],
      [['mold_no'], 'unique'],
      [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
      [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels(){
    return [
      'id' => Yii::t('app', 'ID'),
      'mold_no' => Yii::t('app', 'Mold No'),
      'production_date' => Yii::t('app', 'Production Date'),
      'project_name' => Yii::t('app', 'Project Name'),
      'company_name' => Yii::t('app', 'Company Name'),
      'part_number' => Yii::t('app', 'Part Number'),
      'part_name' => Yii::t('app', 'Part Name'),
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
  public function getMachines(){
    return $this->hasMany(Machine::className(), ['mold_id' => 'id']);
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

  /**
   * @return ActiveQuery
   */
  public function getMoldMachines(){
    return $this->hasMany(MoldMachine::className(), ['mold_id' => 'id']);
  }

  /**
   * @return ActiveQuery
   */
  public function getMachines0(){
    return $this->hasMany(Machine::className(), ['id' => 'machine_id'])->viaTable('mold_machine', ['mold_id' => 'id']);
  }

  /**
   * @return ActiveQuery
   */
  public function getMoldParts(){
    return $this->hasMany(MoldPart::className(), ['mold_id' => 'id']);
  }

  /**
   * @return ActiveQuery
   */
  public function getParts(){
    return $this->hasMany(Part::className(), ['id' => 'part_id'])->viaTable('mold_part', ['mold_id' => 'id']);
  }

  public function getCreatedAtFormatted(){
    return (!empty($this->created_at)) ? date('d.m.Y H:i', $this->created_at) : '';
  }
}
