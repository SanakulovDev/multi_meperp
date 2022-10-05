<?php
namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "part_active_log".
 *
 * @property int    $id
 * @property string $part_no
 * @property string $begin_date
 * @property string $end_date
 * @property int    $status
 * @property Part   $part
 */
class PartActiveLog extends ActiveRecord {

  /**
   * {@inheritdoc}
   */
  public static function tableName() {
    return 'part_active_log';
  }

  /**
   * {@inheritdoc}
   */
  public function rules() {
    return [
      [['part_no'], 'required'],
      [['status'], 'integer'],
      [['part_no', 'status', 'begin_date', 'end_date'], 'safe'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels() {
    return [
      'id' => Yii::t('app', 'ID'),
      'part_no' => Yii::t('app', 'Part No'),
      'begin_date' => Yii::t('app', 'Begining date'),
      'end_date' => Yii::t('app', 'Expire date'),
      'status' => Yii::t('app', 'Status'),
    ];
  }

  /**
   * Gets query for [[Part]].
   *
   * @return ActiveQuery
   */
  public function getPart() {
    return $this->hasOne(Part::className(), ['part_no' => 'part_no']);
  }

}
