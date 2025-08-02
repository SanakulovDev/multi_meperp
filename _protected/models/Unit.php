<?php
namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "unit".
 *
 * @property int    $id
 * @property string $unit_value
 * @property string $description
 *
 * @property FgInvoiceDetail[] $fgInvoiceDetails
 * @property FreightInvoiceDetail[] $freightInvoiceDetails
 * @property Part[] $parts
 */
class Unit extends ActiveRecord {

  /**
   * {@inheritdoc}
   */
  public static function tableName() {
    return 'unit';
  }

  /**
   * {@inheritdoc}
   */
  public function rules() {
    return [
      [['unit_value'], 'required'],
      [['unit_value'], 'string', 'max' => 10],
      [['description'], 'string', 'max' => 150],
      [['unit_value'], 'unique'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels() {
    return [
      'id' => Yii::t('app', 'ID'),
      'unit_value' => Yii::t('app', 'Unit value'),
      'description' => Yii::t('app', 'Description'),
    ];
  }

  public static function getUnitNames() {
    return ArrayHelper::map(self::find()->all(), 'id', 'unit_value');
  }

  public static function findOneByName($name) {
    return self::find()->where(['unit_value' => $name])->one();
  }

  /**
   * Gets query for [[FgInvoiceDetails]].
   *
   * @return ActiveQuery
   */
  public function getFgInvoiceDetails() {
    return $this->hasMany(FgInvoiceDetail::className(), ['unit_id' => 'id']);
  }

  /**
   * Gets query for [[FreightInvoiceDetails]].
   *
   * @return ActiveQuery
   */
  public function getFreightInvoiceDetails() {
    return $this->hasMany(FreightInvoiceDetail::className(), ['unit_id' => 'id']);
  }

  /**
   * Gets query for [[Parts]].
   *
   * @return ActiveQuery
   */
  public function getParts() {
    return $this->hasMany(Part::className(), ['unit_id' => 'id']);
  }

}
