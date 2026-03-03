<?php
namespace app\models;

use app\enums\ShipMode;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "route".
 *
 * @property int   $id
 * @property int   $ship_mode
 * @property int   $from_point_id
 * @property int   $to_point_id
 * @property Point $fromPoint
 * @property Point $toPoint
 * @property FreightInvoice[] $freightInvoices
 */
class Route extends ActiveRecord {

  /**
   * {@inheritdoc}
   */
  public static function tableName() {
    return 'route';
  }

  /**
   * {@inheritdoc}
   */
  public function rules() {
    return [
      [['ship_mode', 'from_point_id', 'to_point_id', 'name'], 'required'],
      [['ship_mode', 'from_point_id', 'to_point_id'], 'integer'],
      [['description'], 'string'],
      [['name'], 'string', 'max' => 255],
      [['to_point_id'], 'compare', 'compareAttribute' => 'from_point_id', 'operator' => '!=', 'message' => Yii::t('app', 'Please choose a different points'), 'type' => 'number'],
      [['ship_mode', 'from_point_id', 'to_point_id'], 'unique', 'targetAttribute' => ['ship_mode', 'from_point_id', 'to_point_id'], 'message' => Yii::t('app', 'Dublicate data')],
      [['from_point_id'], 'exist', 'skipOnError' => true, 'targetClass' => Point::class, 'targetAttribute' => ['from_point_id' => 'id']],
      [['to_point_id'], 'exist', 'skipOnError' => true, 'targetClass' => Point::class, 'targetAttribute' => ['to_point_id' => 'id']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels() {
    return [
      'id' => Yii::t('app', 'ID'),
      'ship_mode' => Yii::t('app', 'Ship mode'),
      'from_point_id' => Yii::t('app', 'From point'),
      'to_point_id' => Yii::t('app', 'To point'),
      'name' => Yii::t('app', 'Name'),
      'description' => Yii::t('app', 'Description'),
    ];
  }

  /**
   * @return ActiveQuery
   */
  public function getFromPoint() {
    return $this->hasOne(Point::class, ['id' => 'from_point_id']);
  }

  /**
   * @return ActiveQuery
   */
  public function getToPoint() {
    return $this->hasOne(Point::class, ['id' => 'to_point_id']);
  }

  public function getShipModeName() {
    return ShipMode::name($this->ship_mode);
  }

  public function getFreightInvoices() {
    return $this->hasMany(FreightInvoice::className(), ['route_id' => 'id']);
  }

}
