<?php
namespace app\models;

use Exception;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "production_plan".
 *
 * @property int       $id
 * @property int       $part_id      Part;semi;FG
 * @property string    $production_date
 * @property int       $warehouse_id location
 * @property int       $shift        smena
 * @property int       $target_qty
 * @property string    $comment
 * @property int       $line
 * @property Part      $part
 * @property Warehouse $warehouse
 */
class ProductionDailyPlan extends ActiveRecord {

  public $smena = [
    1 => '1',
    2 => '2',
  ];

  public $comment;

  /**
   * {@inheritdoc}
   */
  public static function tableName() {
    return 'production_daily_plan';
  }

  /**
   * {@inheritdoc}
   */
  public function rules() {
    return [
      [['part_id', 'warehouse_id'], 'required'],
      [['part_id', 'warehouse_id', 'shift', 'target_qty', 'line', 'type'], 'integer'],
      [['part_id', 'production_date', 'warehouse_id', 'shift'], 'unique',
        'targetAttribute' => ['part_id', 'production_date', 'warehouse_id', 'shift']
      ],
      [['production_date', 'comment'], 'safe'],
      [['remark'], 'string'],
      [['part_id'], 'exist', 'skipOnError' => true, 'targetClass' => Part::className(), 'targetAttribute' => ['part_id' => 'id']],
      [['warehouse_id'], 'exist', 'skipOnError' => true, 'targetClass' => Warehouse::className(), 'targetAttribute' => ['warehouse_id' => 'id']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels() {
    return [
      'id' => Yii::t('app', 'ID'),
      'part_id' => Yii::t('app', 'Part ID'),
      'production_date' => Yii::t('app', 'Production date'),
      'warehouse_id' => Yii::t('app', 'Location'),
      'shift' => Yii::t('app', 'Shift'),
      'target_qty' => Yii::t('app', 'Target qty'),
      'comment' => Yii::t('app', 'Comment'),
      'line' => Yii::t('app', 'Line'),
      'remark' => Yii::t('app', 'Remark'),
    ];
  }

  /**
   * @return ActiveQuery
   */
  public function getPart() {
    return $this->hasOne(Part::className(), ['id' => 'part_id']);
  }

  public function getWarehouse() {
    return $this->hasOne(Warehouse::className(), ['id' => 'warehouse_id']);
  }

  public function getPlanComment() {
    return $this->hasOne(ProductionPlanComment::className(), ['production_plan_id' => 'id']);
  }

  public static function allowEdit($shift, $dateForEdit) {
    $allowEdit = 0;
    $today = date('Y-m-d', time());
    if((Yii::$app->params['plan_freeze_time'] == 0)) {
      $allowEdit = 1;
    } elseif(
      (
        $shift == 1 &&
        (
          (
            $today == $dateForEdit &&
            time() >= strtotime($today.' '.Yii::$app->params['shifts']['1']['0']) &&
            time() < (strtotime($today.' '.Yii::$app->params['shifts']['1']['0']) + Yii::$app->params['plan_freeze_time']*60*60)
          )
          or $today < $dateForEdit
        )
      )
      or
      (
        $shift == 2 &&
        (
          (
            $today <= $dateForEdit &&
            time() >= strtotime($today.' '.Yii::$app->params['shifts']['1']['0']) &&
            time() < strtotime($today.' '.Yii::$app->params['shifts']['2']['0']['0']) + Yii::$app->params['plan_freeze_time']*60*60
          )
          or
          (
            time() >= strtotime($today.' '.Yii::$app->params['shifts']['2']['1']['0']) &&
            time() < strtotime($today.' '.Yii::$app->params['shifts']['2']['1']['1']) &&
            time() < strtotime('-1 day', strtotime($today.' '.Yii::$app->params['shifts']['2']['0']['0'])) + Yii::$app->params['plan_freeze_time']*60*60
          )
        )
      )
    ) {
      $allowEdit = 1;
    }

    return $allowEdit;
  }

  public static function deletePlanInactivePart($partId, $sanaInt) {
    try {
      ProductionMonthlyPlan::deleteAll(
        "part_id=".$partId." and production_date >='".date("Y-m-d", $sanaInt)."'"
      );

      return $err = 0;
    }
    catch(Exception $e) {
      return $err = 1;
    }
  }

}
