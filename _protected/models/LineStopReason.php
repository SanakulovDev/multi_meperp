<?php
namespace app\models;

use app\rbac\models\AuthItem;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "line_stop_reason".
 *
 * @property int        $id
 * @property string     $name
 * @property int $type
 * @property string     $auth_item_name
 * @property string     $fix_list
 * @property LineStop[] $lineStops
 */
class LineStopReason extends ActiveRecord {

  const TYPE_NOTPLANNED = 1;
  const TYPE_PLANNED = 0;

  /**
   * {@inheritdoc}
   */
  public static function tableName() {
    return 'line_stop_reason';
  }

  /**
   * {@inheritdoc}
   */
  public function rules() {
    return [
      [['name', 'auth_item_name', 'type'], 'required'],
      [['type'], 'integer'],
      [['name'], 'string', 'max' => 255],
      [['fix_list'], 'string'],
      [['auth_item_name'], 'string', 'max' => 100],
      [['auth_item_name'], 'exist', 'skipOnError' => true, 'targetClass' => AuthItem::className(), 'targetAttribute' => ['auth_item_name' => 'name']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels() {
    return [
      'id' => Yii::t('app', 'ID'),
      'name' => Yii::t('app', 'Name'),
      'type' => Yii::t('app', 'Type'),
      'fix_list' => Yii::t('app', 'List'),
      'auth_item_name' => Yii::t('app', 'Role'),
    ];
  }

  public function getTypes() {
    return [
      self::TYPE_PLANNED => Yii::t('app', 'Planned line stop'),
      self::TYPE_NOTPLANNED => Yii::t('app', 'Not planned line stop')
    ];
  }

  /**
   * Gets query for [[LineStops]].
   *
   * @return ActiveQuery
   */
  public function getLineStops() {
    return $this->hasMany(LineStop::className(), ['line_stop_reason_id' => 'id']);
  }

}
