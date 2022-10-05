<?php
namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "health_check_detail".
 *
 * @property int         $id
 * @property int         $health_check_id
 * @property string      $check_date
 * @property int|null    $status      R-red; Y-yellow; G-green;
 * @property string|null $description Status xolatini yozib boriladi
 * @property string      $updated_at
 * @property HealthCheck $healthCheck
 */
class HealthCheckDetail extends ActiveRecord {

  public const STATUS_RED = 'R';
  public const STATUS_YELLOW = 'Y';
  public const STATUS_GREEN = 'G';
  public static $statusList = [
    self::STATUS_RED => 'red',
    self::STATUS_YELLOW => 'yellow',
    self::STATUS_GREEN => 'green',
  ];

  public function getStatusName() {
    return Yii::t('app', self::$statusList[$this->status]);
  }

  /**
   * {@inheritdoc}
   */
  public static function tableName() {
    return 'health_check_detail';
  }

  /**
   * {@inheritdoc}
   */
  public function rules() {
    return [
      [['health_check_id', 'check_date', 'status'], 'required'],
      [['health_check_id',], 'integer'],
      [['check_date', 'updated_at'], 'safe'],
      [['status'], 'string', 'max' => 1],
      [['description'], 'string', 'max' => 250],
      [['health_check_id', 'check_date'], 'unique', 'targetAttribute' => ['health_check_id', 'check_date']],
      [['health_check_id'], 'exist', 'skipOnError' => true, 'targetClass' => HealthCheck::className(), 'targetAttribute' => ['health_check_id' => 'id']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels() {
    return [
      'id' => Yii::t('app', 'ID'),
      'health_check_id' => Yii::t('app', 'Health Check ID'),
      'check_date' => Yii::t('app', 'Check Date'),
      'status' => Yii::t('app', 'Status'),
      'description' => Yii::t('app', 'Description'),
      'updated_at' => Yii::t('app', 'Updated at'),
    ];
  }

  /**
   * @return ActiveQuery
   */
  public function getHealthCheck() {
    return $this->hasOne(HealthCheck::className(), ['id' => 'health_check_id']);
  }

}
