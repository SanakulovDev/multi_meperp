<?php

namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "lms".
 *
 * @property int $id
 * @property int $part_id
 * @property int $bms
 * @property int $supplier_id
 * @property int $warehouse_id
 * @property string $dloc
 * @property string $minimum
 * @property string $maximum
 * @property string $stack
 * @property string $mpr
 * @property int $high_theft
 * @property int $created_by
 * @property int $created_at
 * @property int $updated_by
 * @property int $updated_at
 *
 * @property User $createdBy
 * @property Part $part
 * @property Supplier $supplier
 * @property User $updatedBy
 * @property Warehouse $warehouse
 */
class Lms extends ActiveRecord
{
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'lms';
  }

  public function behaviors()
  {
    return [
      TimestampBehavior::className(),
      BlameableBehavior::className(),
    ];
  }

  const SIZE_SMALL = 0;
  const SIZE_MEDIUM = 1;
  const SIZE_BULK = 2;

  public function getSizeList()
  {
    return [
      self::SIZE_SMALL => Yii::t('app', 'Small'),
      self::SIZE_MEDIUM => Yii::t('app', 'Medium'),
      self::SIZE_BULK => Yii::t('app', 'Bulk'),
    ];
  }


  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['part_id', 'supplier_id'], 'required'],
      [['part_id', 'bms', 'supplier_id', 'warehouse_id', 'high_theft', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
      [['minimum', 'maximum', 'stack'], 'number'],
      [['dloc', 'mpr'], 'string', 'max' => 50],
      [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
      [['part_id'], 'exist', 'skipOnError' => true, 'targetClass' => Part::className(), 'targetAttribute' => ['part_id' => 'id']],
      [['supplier_id'], 'exist', 'skipOnError' => true, 'targetClass' => Supplier::className(), 'targetAttribute' => ['supplier_id' => 'id']],
      [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
      [['warehouse_id'], 'exist', 'skipOnError' => true, 'targetClass' => Warehouse::className(), 'targetAttribute' => ['warehouse_id' => 'id']],
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
      'supplier_id' => Yii::t('app', 'Supplier'),
      'warehouse_id' => Yii::t('app', 'Warehouse'),
      'dloc' => Yii::t('app', 'Dloc'),
      'bms' => Yii::t('app', 'Bms'),
      'minimum' => Yii::t('app', 'Minimum'),
      'maximum' => Yii::t('app', 'Maximum'),
      'stack' => Yii::t('app', 'Stack'),
      'mpr' => Yii::t('app', 'MRP'),
      'high_theft' => Yii::t('app', 'High theft'),
      'created_by' => Yii::t('app', 'Created by'),
      'created_at' => Yii::t('app', 'Created at'),
      'updated_by' => Yii::t('app', 'Updated by'),
      'updated_at' => Yii::t('app', 'Updated at'),
    ];
  }

  /**
   * @return ActiveQuery
   */
  public function getCreatedBy()
  {
    return $this->hasOne(User::className(), ['id' => 'created_by']);
  }

  /**
   * @return ActiveQuery
   */
  public function getPart()
  {
    return $this->hasOne(Part::className(), ['id' => 'part_id']);
  }

  /**
   * @return ActiveQuery
   */
  public function getSupplier()
  {
    return $this->hasOne(Supplier::className(), ['id' => 'supplier_id']);
  }

  /**
   * @return ActiveQuery
   */
  public function getUpdatedBy()
  {
    return $this->hasOne(User::className(), ['id' => 'updated_by']);
  }

  /**
   * @return ActiveQuery
   */
  public function getWarehouse()
  {
    return $this->hasOne(Warehouse::className(), ['id' => 'warehouse_id']);
  }

  public function highTheftList()
  {
    return [0 => Yii::t('app', 'No'), 1 => Yii::t('app', 'Yes')];
  }

  public function getUpdatedAtFormatted()
  {
    return (!empty($this->updated_at)) ? date('d.m.Y H:i', $this->updated_at) : '';
  }

  public function getCreatedAtFormatted()
  {
    return (!empty($this->created_at)) ? date('d.m.Y H:i', $this->created_at) : '';
  }

  public function getHighTheftFormatted()
  {
    return $this->high_theft == 0 ? Yii::t('app', 'No') : Yii::t('app', 'Yes');
  }
}
