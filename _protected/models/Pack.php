<?php
namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "pack".
 *
 * @property int    $id
 * @property string $code
 * @property string $description
 * @property string $construction
 * @property string $length
 * @property string $width
 * @property string $height
 * @property string $weight
 * @property string $thickness
 * @property int    $level
 * @property string $quantity
 * @property int    $created_by
 * @property int    $created_at
 * @property int    $updated_by
 * @property int    $updated_at
 * @property User   $createdBy
 * @property User   $updatedBy
 */
class Pack extends \yii\db\ActiveRecord {

  /**
   * {@inheritdoc}
   */
  public static function tableName() {
    return 'pack';
  }

  public function behaviors() {
    return [
      TimestampBehavior::className(),
      BlameableBehavior::className(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function rules() {
    return [
      [['code','length', 'width', 'height', 'weight', 'thickness'], 'required'],
      [['length', 'width', 'height', 'weight', 'thickness', 'quantity'], 'number'],
      [['level', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
      [['code'], 'string', 'max' => 100],
      [['code'], 'unique'],
      [['description', 'construction'], 'string', 'max' => 255],
      [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
      [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels() {
    return [
      'id' => Yii::t('app', 'ID'),
      'code' => Yii::t('app', 'Packaging code'),
      'description' => Yii::t('app', 'Pack description'),
      'construction' => Yii::t('app', 'Packaging material'),
      'length' => Yii::t('app', 'Length (cm)'),
      'width' => Yii::t('app', 'Width (cm)'),
      'height' => Yii::t('app', 'Height (cm)'),
      'weight' => Yii::t('app', 'Weight (kg)'),
      'thickness' => Yii::t('app', 'Thickness (mm)'),
      'level' => Yii::t('app', 'Step (1/2)'),
      'quantity' => Yii::t('app', 'Loop size'),
      'created_by' => Yii::t('app', 'Created by'),
      'created_at' => Yii::t('app', 'Created at'),
      'updated_by' => Yii::t('app', 'Updated by'),
      'updated_at' => Yii::t('app', 'Updated at'),
    ];
  }

  /**
   * @return \yii\db\ActiveQuery
   */
  public function getCreatedBy() {
    return $this->hasOne(User::className(), ['id' => 'created_by']);
  }

  /**
   * @return \yii\db\ActiveQuery
   */
  public function getUpdatedBy() {
    return $this->hasOne(User::className(), ['id' => 'updated_by']);
  }

  public function getUpdatedAtFormatted() {
    return (!empty($this->updated_at)) ? date('d.m.Y H:i', $this->updated_at) : '';
  }

  public function getPackLevels() {
    return $this->hasMany(PackLevel::className(), ['pack_id' => 'id']);
  }

  public function getCreatedAtFormatted() {
    return (!empty($this->created_at)) ? date('d.m.Y H:i', $this->created_at) : '';
  }

}
