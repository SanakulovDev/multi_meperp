<?php

namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "line_stop".
 *
 * @property int $id
 * @property int $part_production_monitor_id
 * @property string $start_time
 * @property string|null $end_time
 * @property int $line_stop_reason_id
 * @property string|null $remark
 * @property string|null $fix_list
 * @property int $bypass
 * @property int $status
 * @property int $elapsed_minutes
 * @property string|null $rejection_remark
 * @property int|null $created_by
 * @property int $created_at
 * @property int|null $updated_by
 * @property int|null $updated_at
 *
 * @property LineStopReason $lineStopReason
 * @property PartProductionMonitor $partProductionMonitor
 */
class LineStop extends \yii\db\ActiveRecord
{
  const STATUS_PENDING = 0;
  const STATUS_ACCEPTED = 1;
  const STATUS_REJECTED = 10;

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return "line_stop";
  }

  public function behaviors()
  {
    return [TimestampBehavior::className(), BlameableBehavior::className()];
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [["part_production_monitor_id", "start_time", "line_stop_reason_id"], "required"],
      [
        [
          "part_production_monitor_id",
          "line_stop_reason_id",
          "status",
          "created_by",
          "created_at",
          "updated_by",
          "updated_at",
          "elapsed_minutes",
          "bypass",
        ],
        "integer",
      ],
      [["start_time", "end_time", "auth_item_name", "fix_list"], "safe"],
      [["start_time", "end_time"], "validateOverlap"],
      ["end_time", "compare", "compareAttribute" => "start_time", "operator" => ">"],
      ["bypass", "validateBypass"],
      [["rejection_remark"], "string", "max" => 255],
      [
        ["line_stop_reason_id"],
        "exist",
        "skipOnError" => true,
        "targetClass" => LineStopReason::className(),
        "targetAttribute" => ["line_stop_reason_id" => "id"],
      ],
      [
        ["part_production_monitor_id"],
        "exist",
        "skipOnError" => true,
        "targetClass" => PartProductionMonitor::className(),
        "targetAttribute" => ["part_production_monitor_id" => "id"],
      ],
    ];
  }
  /**
   * @param string $attribute the attribute currently being validated
   * @param mixed $params the value of the "params" given in the rule
   * @param \yii\validators\InlineValidator $validator related InlineValidator instance.
   * This parameter is available since version 2.0.11.
   * @param mixed $current the currently validated value of attribute.
   * This parameter is available since version 2.0.36.
   */
  public function validateOverlap($attribute, $params, $validator)
  {
    if (
      self::find()
        ->andFilterWhere(['<>','id', $this->id])
        ->andWhere(['part_production_monitor_id'=>$this->part_production_monitor_id])
        ->andWhere(["not", ["or", ["<=", "end_time", $this->start_time], [">", "start_time", $this->end_time]]])
        ->exists()
    ) {
      $this->addError($attribute, Yii::t('app', 'The line-stop exists for this period'));
    }
  }

  public function validateBypass($attribute, $params, $validator)
  {
    $elapsed = (strtotime($this->end_time) - strtotime($this->start_time)) / 60;

    if ($this->bypass > $elapsed) {
      $this->addError($attribute, Yii::t('app', '{attribute} must be less then or equal to "{value}"', ['attribute'=>$this->getAttributeLabel($attribute), 'value'=>$elapsed]));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      "id" => Yii::t("app", "ID"),
      "part_production_monitor_id" => Yii::t("app", "Part production monitor"),
      "start_time" => Yii::t("app", "From"),
      "end_time" => Yii::t("app", "To"),
      "elapsed_minutes" => Yii::t("app", "Minut"),
      "line_stop_reason_id" => Yii::t("app", "Reason"),
      "remark" => Yii::t("app", "Remark"),
      "bypass" => Yii::t("app", "By pass"),
      "warehouse" => Yii::t("app", "Warehouse"),
      "fix_list" => Yii::t("app", "Fixes"),
      "status" => Yii::t("app", "Status"),
      "rejection_remark" => Yii::t("app", "Rejection Remark"),
      "created_by" => Yii::t("app", "Created by"),
      "created_at" => Yii::t("app", "Created at"),
      "updated_by" => Yii::t("app", "Updated by"),
      "updated_at" => Yii::t("app", "Updated at"),
    ];
  }

  /**
   * Gets query for [[LineStopReason]].
   *
   * @return \yii\db\ActiveQuery
   */
  public function getLineStopReason()
  {
    return $this->hasOne(LineStopReason::className(), ["id" => "line_stop_reason_id"]);
  }

  /**
   * Gets query for [[PartProductionMonitor]].
   *
   * @return \yii\db\ActiveQuery
   */
  public function getPartProductionMonitor()
  {
    return $this->hasOne(PartProductionMonitor::className(), ["id" => "part_production_monitor_id"]);
  }

  public function getCreatedBy()
  {
    return $this->hasOne(User::className(), ["id" => "created_by"]);
  }

  public function getUpdatedBy()
  {
    return $this->hasOne(User::className(), ["id" => "updated_by"]);
  }

  public function getUpdatedAtFormatted()
  {
    return !empty($this->updated_at) ? date("d.m.Y H:i", $this->updated_at) : "";
  }

  public function getCreatedAtFormatted()
  {
    return !empty($this->created_at) ? date("d.m.Y H:i", $this->created_at) : "";
  }

  public function accept()
  {
    $this->status = self::STATUS_ACCEPTED;
  }

  public function reject()
  {
    $this->status = self::STATUS_REJECTED;
  }

  public function beforeSave($insert)
  {
    $this->elapsed_minutes = (strtotime($this->end_time) - strtotime($this->start_time)) / 60;
    if ($this->lineStopReason->type === LineStopReason::TYPE_PLANNED) {
      $this->status = self::STATUS_ACCEPTED;
    } else {
      if ($this->isAttributeChanged('start_time') || $this->isAttributeChanged('end_time') || $this->isAttributeChanged('line_stop_reason_id')) {
        $this->status = self::STATUS_PENDING;
      }
    }
    return parent::beforeSave($insert);
  }
}
