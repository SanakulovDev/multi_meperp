<?php
namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "health_check".
 *
 * @property int                 $id
 * @property int|null            $sort_order
 * @property string              $title
 * @property string              $description
 * @property int                 $status 1-active; 0-inactive;
 * @property HealthCheckDetail[] $healthCheckDetails
 */
class HealthCheck extends ActiveRecord {

  public const STATUS_ACTIVE = 1;
  public const STATUS_INACTIVE = 0;

  public $statusList = [
    self::STATUS_ACTIVE => 'Active',
    self::STATUS_INACTIVE => 'Inactive',
  ];

  public function getStatusName() {
    return Yii::t('app', self::$typeList[$this->status]);
  }

  public static function getStatusListNames() {
    foreach (self::$statusList as $status_code => $status_name) {
      $result[$status_code] = Yii::t('app', $status_name);
    }
    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public static function tableName() {
    return 'health_check';
  }

  /**
   * {@inheritdoc}
   */
  public function rules() {
    return [
      [['sort_order', 'status'], 'integer'],
      [['title', 'description'], 'required'],
      [['description'], 'string'],
      [['title'], 'string', 'max' => 100],
      [['title'], 'unique'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels() {
    return [
      'id' => Yii::t('app', 'ID'),
      'sort_order' => Yii::t('app', 'Sort order'),
      'title' => Yii::t('app', 'Title'),
      'description' => Yii::t('app', 'Description'),
      'status' => Yii::t('app', 'Status'),
    ];
  }

  /**
   * @return ActiveQuery
   */
  public function getHealthCheckDetails() {
    return $this->hasMany(HealthCheckDetail::className(), ['health_check_id' => 'id']);
  }

}
