<?php
namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "part_production_monitor".
 *
 * @property int                $id
 * @property int                $production_monitor_id
 * @property int                $part_id
 * @property string|null        $start_time
 * @property string|null        $end_time
 * @property float              $produced_qty production label create da update qilinadi
 * @property float|null         $repaired_qty
 * @property float|null         $broken_qty
 * @property int|null           $actual_production_time
 * @property int                $created_by
 * @property int                $created_at
 * @property int|null           $updated_by
 * @property int|null           $updated_at
 * @property User               $createdBy
 * @property LineStop[]         $lineStops
 * @property Part               $part
 * @property ProductionDefect[] $productionDefects
 * @property ProductionMonitor  $productionMonitor
 * @property User               $updatedBy
 */
class PartProductionMonitor extends ActiveRecord
{
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return "part_production_monitor";
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [["production_monitor_id", "part_id", "produced_qty", "created_at"], "required"],
      [
        [
          "production_monitor_id",
          "part_id",
          "actual_production_time",
          "created_at",
          "created_by",
          "updated_at",
          "updated_by",
        ],
        "integer",
      ],
      [["start_time", "end_time"], "safe"],
      [["start_time", "end_time"], "validateOverlap"],
      ["end_time", "compare", "compareAttribute" => "start_time", "operator" => ">"],
      ["actual_production_time", "validateActualProductionTime"],
      [["produced_qty", "repaired_qty", "broken_qty"], "number"],
      [
        ["part_id"],
        "exist",
        "skipOnError" => true,
        "targetClass" => Part::className(),
        "targetAttribute" => ["part_id" => "id"],
      ],
      [
        ["production_monitor_id"],
        "exist",
        "skipOnError" => true,
        "targetClass" => ProductionMonitor::className(),
        "targetAttribute" => ["production_monitor_id" => "id"],
      ],
      [
        ["created_by"],
        "exist",
        "skipOnError" => true,
        "targetClass" => User::className(),
        "targetAttribute" => ["created_by" => "id"],
      ],
      [
        ["updated_by"],
        "exist",
        "skipOnError" => true,
        "targetClass" => User::className(),
        "targetAttribute" => ["updated_by" => "id"],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      "id" => Yii::t("app", "ID"),
      "production_monitor_id" => Yii::t("app", "Production monitor"),
      "part_id" => Yii::t("app", "Part"),
      "start_time" => Yii::t("app", "Start time"),
      "end_time" => Yii::t("app", "End time"),
      "produced_qty" => Yii::t("app", "Produced qty"),
      "repaired_qty" => Yii::t("app", "Repaired qty"),
      "broken_qty" => Yii::t("app", "Broken qty"),
      "actual_production_time" => Yii::t("app", "Actual production time"),
      "created_by" => Yii::t("app", "Created by"),
      "created_at" => Yii::t("app", "Created at"),
      "updated_by" => Yii::t("app", "Updated by"),
      "updated_at" => Yii::t("app", "Updated at"),
    ];
  }

  /**
   * Gets query for [[CreatedBy]].
   *
   * @return ActiveQuery
   */
  public function getCreatedBy()
  {
    return $this->hasOne(User::className(), ["id" => "created_by"]);
  }

  /**
   * Gets query for [[LineStops]].
   *
   * @return ActiveQuery
   */
  public function getLineStops()
  {
    return $this->hasMany(LineStop::className(), ["part_production_monitor_id" => "id"]);
  }

  /**
   * Gets query for [[Part]].
   *
   * @return ActiveQuery
   */
  public function getPart()
  {
    return $this->hasOne(ProductionMonitor::className(), ["id" => "part_id"]);
  }

  /**
   * Gets query for [[ProductionMonitor]].
   *
   * @return ActiveQuery
   */
  public function getProductionMonitor()
  {
    return $this->hasOne(ProductionMonitor::className(), ["id" => "production_monitor_id"]);
  }

  /**
   * Gets query for [[UpdatedBy]].
   *
   * @return ActiveQuery
   */
  public function getUpdatedBy()
  {
    return $this->hasOne(User::className(), ["id" => "updated_by"]);
  }

  public function validateActualProductionTime($attribute, $params, $validator)
  {
    if ($this->start_time > 0 && $this->end_time > 0) {
      $elapsed = (strtotime($this->end_time) - strtotime($this->start_time)) / 60;
      if ($elapsed < 0) {
        $this->addError(
          $attribute,
          Yii::t("app", '{attribute} must be less then or equal to "{value}"', [
            "attribute" => $this->getAttributeLabel($attribute),
            "value" => $elapsed,
          ])
        );
      }
    }
  }

  public function validateOverlap($attribute, $params, $validator)
  {
    if (
    self::find()
        ->where(["<>", "id", $this->id])
        ->andWhere(["production_monitor_id" => $this->production_monitor_id])
        ->andWhere(["part_id" => $this->part_id])
        ->andWhere(["not", ["or", ["<=", "end_time", $this->start_time], [">", "start_time", $this->end_time]]])
        ->exists()
    ) {
      $this->addError($attribute, Yii::t("app", "The line-stop exists for this period"));
    }
  }

  public function beforeSave($insert)
  {
    if (parent::beforeSave($insert)) {
      $startTime = $this->start_time ?: 0;
      $endTime = $this->end_time ?: 0;

      //      echo "<pre>"; print_r("S: ",$startTime);echo "</pre>";
      //      echo "<pre>"; print_r("E: ",$endTime);echo "</pre>";

      if ($this->start_time == 0) {
        $this->actual_production_time = 0;
        $this->start_time = null;
        $this->end_time = null;
      } elseif ($this->start_time > 0 && (strtotime($endTime) - strtotime($startTime) > 0)) {
        $diff = (strtotime($endTime) - strtotime($startTime)) / 60;
        $this->actual_production_time = $diff;
      } else {
        $this->actual_production_time = 0;
      }

      if ($this->isNewRecord) {
        $this->created_by = isset(Yii::$app->user) ? Yii::$app->user->identity->id : 1;
        $this->created_at = time();
      } else {
        $this->updated_by = isset(Yii::$app->user) ? Yii::$app->user->identity->id : 1;
        $this->updated_at = time();
      }
      return true;
    } else {
      return false;
    }

    return false;
  }

  // New record adding, producing process logic
  public static function setProduced($productionMonitorId, $partId, $qty, $userId = null, $addOrDelete = "add")
  {
    $model = self::find()
                 ->where([
                           "production_monitor_id" => $productionMonitorId,
                           "part_id" => $partId,
                         ])
                 ->one();
    if ($model) {
      $model->produced_qty =
        $addOrDelete === "add" ? $model->produced_qty + (float) $qty : $model->produced_qty - (float) $qty;
    } else {
      $model = new self();
      $model->production_monitor_id = $productionMonitorId;
      $model->part_id = $partId;
      $model->produced_qty = $qty;
      $model->created_at = time();
      $model->created_by = $userId;
    }
    if ($model->save()) {
      return $model;
    }
    return false;
  }
}