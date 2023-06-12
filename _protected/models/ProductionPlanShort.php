<?php
namespace app\models;

use Exception;
use Yii;
use yii\base\Model;
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
class ProductionPlanShort extends Model {

  public $part_id;
  public $warehouse_id;
  public function rules() {
    return [
      [['part_id', 'warehouse_id'], 'required'],
      [['part_id', 'warehouse_id'], 'integer'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels() {
    return [
      'part_id' => Yii::t('app', 'Part ID'),
      'warehouse_id' => Yii::t('app', 'Location'),
    ];
  }

}
