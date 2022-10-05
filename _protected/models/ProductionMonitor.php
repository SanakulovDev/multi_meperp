<?php
namespace app\models;

use Yii;
use yii\base\InvalidArgumentException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "production_monitor".
 *
 * @property int                     $id
 * @property int                     $warehouse_id
 * @property string                  $production_date
 * @property int                     $shift
 * @property int                     $status
 * @property string|null             $quality_confirmed_at
 * @property int|null                $quality_confirmed_by
 * @property string|null             $production_completed_at
 * @property int|null                $production_completed_by
 * @property PartProductionMonitor[] $partProductionMonitors
 * @property User                    $productionCompletedBy
 * @property User                    $qualityConfirmedBy
 * @property Warehouse               $warehouse
 */
class ProductionMonitor extends ActiveRecord {

  const STATUS_ENABLED = 1;   // new record, produced qty insertion
  const STATUS_CONFIRMED = 2; // Quality side confirmtion status
  const STATUS_COMPLETED = 3; // Closing the day, compilation

  /**
   * {@inheritdoc}
   */
  public static function tableName() {
    return 'production_monitor';
  }

  /**
   * {@inheritdoc}
   */
  public function rules() {
    return [
      [['warehouse_id', 'production_date', 'shift', 'status'], 'required'],
      [['warehouse_id', 'shift', 'status', 'quality_confirmed_by', 'production_completed_by'], 'integer'],
      [['production_date', 'quality_confirmed_at', 'production_completed_at'], 'safe'],
      [['warehouse_id', 'production_date', 'shift'], 'unique', 'targetAttribute' => ['warehouse_id', 'production_date', 'shift']],
      [['production_completed_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['production_completed_by' => 'id']],
      [['quality_confirmed_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['quality_confirmed_by' => 'id']],
      [['warehouse_id'], 'exist', 'skipOnError' => true, 'targetClass' => Warehouse::className(), 'targetAttribute' => ['warehouse_id' => 'id']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels() {
    return [
      'id' => Yii::t('app', 'ID'),
      'warehouse_id' => Yii::t('app', 'Warehouse ID'),
      'production_date' => Yii::t('app', 'Production Date'),
      'shift' => Yii::t('app', 'Shift'),
      'status' => Yii::t('app', 'Status'),
      'quality_confirmed_at' => Yii::t('app', 'Quality Confirmed At'),
      'quality_confirmed_by' => Yii::t('app', 'Quality Confirmed By'),
      'production_completed_at' => Yii::t('app', 'Production Completed At'),
      'production_completed_by' => Yii::t('app', 'Production Completed By'),
    ];
  }

  /**
   * Gets query for [[PartProductionMonitors]].
   *
   * @return ActiveQuery
   */
  public function getPartProductionMonitors() {
    return $this->hasMany(PartProductionMonitor::className(), ['production_monitor_id' => 'id']);
  }

  /**
   * Gets query for [[ProductionCompletedBy]].
   *
   * @return ActiveQuery
   */
  public function getProductionCompletedBy() {
    return $this->hasOne(User::className(), ['id' => 'production_completed_by']);
  }

  /**
   * Gets query for [[QualityConfirmedBy]].
   *
   * @return ActiveQuery
   */
  public function getQualityConfirmedBy() {
    return $this->hasOne(User::className(), ['id' => 'quality_confirmed_by']);
  }

  /**
   * Gets query for [[Warehouse]].
   *
   * @return ActiveQuery
   */
  public function getWarehouse() {
    return $this->hasOne(Warehouse::className(), ['id' => 'warehouse_id']);
  }

  // New record adding, producing process logic
  public static function write($warehouse_id, $production_date, $shift) {
    $prodMonitor = ProductionMonitor::find()->where([
                                      'warehouse_id' => $warehouse_id,
                                      'production_date' => $production_date,
                                      'shift' => $shift
                                     ])->one();
    if ($prodMonitor) {
      return ['status'=>1, 'data' => $prodMonitor];
    }

    $prodMonitor = new self;
    $prodMonitor->warehouse_id = $warehouse_id;
    $prodMonitor->production_date = $production_date;
    $prodMonitor->shift = $shift;
    $prodMonitor->status = self::STATUS_ENABLED;

    if($prodMonitor->save()) {
      return ['status'=>1, 'data' => $prodMonitor];
    } else {
      return ['status'=>0, 'errors' => $prodMonitor->errors ];
    }
  }

  // Quality confirmation logic
  // $type ==> (1-confirm, 0-unconfirm)
  public static function confirm($id, $userId, $type) {
    $prodMonitor = self::findOne($id);
    if(!$prodMonitor) return false;
//    echo "<pre>"; var_dump($type);echo "</pre>";
    if($type === '1') {
//      echo "<pre>"; print_r($type);echo "</pre>";
      $prodMonitor->status = self::STATUS_CONFIRMED;
      $prodMonitor->quality_confirmed_at = date('Y-m-d H:i:s');
      $prodMonitor->quality_confirmed_by = $userId;
    } else {
      $prodMonitor->status = self::STATUS_ENABLED;
      $prodMonitor->quality_confirmed_at = null;
      $prodMonitor->quality_confirmed_by = null;
    }
    if($prodMonitor->save()) {
      return true;
    }
    return false;
  }

  // Quality confirmation logic
  // $type ==> (1-complete, 0-uncomplete)
  public static function complete($id, $userId, $type) {
    $prodMonitor = self::findOne($id);
    if(!$prodMonitor) return false;
    if($type === '1') {
      $prodMonitor->status = self::STATUS_COMPLETED;
      $prodMonitor->production_completed_at = date('Y-m-d H:i:s');
      $prodMonitor->production_completed_by = $userId;
    } else {
      $prodMonitor->status = self::STATUS_CONFIRMED;
      $prodMonitor->production_completed_at = null;
      $prodMonitor->production_completed_by = null;
    }
    if($prodMonitor->save()) {
      return true;
    }
    return false;
  }

  public function isAllLineStopNotConfirmed() {
    $productionMonitorId = $this->id;
    $query = LineStop::find()
                   ->joinWith(
                     [
                       'partProductionMonitor' => function($query) use ($productionMonitorId) {
                         $query->where(['production_monitor_id' => $productionMonitorId]);
                       },
                       'lineStopReason' => function($q) {
                          $q->from(["ls_reason" => LineStopReason::tableName()]);
                          $q->where(['ls_reason.type' => LineStopReason::TYPE_NOTPLANNED]);
                       }
                     ]
                   );
  //    return $query->createCommand()->rawSql;

    $stops = $query->all();
    foreach ($stops as $stop) {
      if($stop->status !== LineStop::STATUS_ACCEPTED) {
        return false;
      }
    }

    return true;
  }

}
