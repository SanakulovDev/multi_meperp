<?php
namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "fg_invoice_detail".
 *
 * @property int $id
 * @property int $fg_invoice_id
 * @property string $part_no
 * @property string $part_name
 * @property float $qty
 * @property float $price
 * @property int $unit_id
 * @property int $source 1-FG WH; 0-From production line;
 * @property int $created_at
 * @property int $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 *
 * @property FgInvoice $fgInvoice
 * @property Unit $unit
 * @property User $createdBy
 * @property User $updatedBy
 */
class FgInvoiceDetail extends ActiveRecord {


  /** 0-From Production line; 1-From FG WH; */
  public const SOURCE_PRODUCTION = 0;
  public const SOURCE_WH = 1;

  public function getSourceName() {
    return [
      self::SOURCE_PRODUCTION => Yii::t('app', 'SOURCE_PRODUCTION'),
      self::SOURCE_WH => Yii::t('app', 'SOURCE_WH'),
    ];
  }
  public static function getSourceText(int $descNumber=0) {
    switch ($descNumber) {
      case 0: $txt = Yii::t('app', 'SOURCE_PRODUCTION');
        break;
      case 1: $txt = Yii::t('app', 'SOURCE_WH');
        break;
    }
    return $txt;
  }


  public static function tableName() {
    return 'fg_invoice_detail';
  }

  /**
   * {@inheritdoc}
   */
  public function rules() {
    return [
      [['fg_invoice_id', 'part_no', 'part_name', 'qty', 'price', 'unit_id', 'created_at', 'created_by'], 'required'],
      [['fg_invoice_id', 'unit_id', 'source', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
      [['qty', 'price'], 'number'],
      [['part_no'], 'string', 'max' => 50],
      [['part_name'], 'string', 'max' => 100],
      [['fg_invoice_id'], 'exist', 'skipOnError' => true, 'targetClass' => FgInvoice::className(), 'targetAttribute' => ['fg_invoice_id' => 'id']],
      [['unit_id'], 'exist', 'skipOnError' => true, 'targetClass' => Unit::className(), 'targetAttribute' => ['unit_id' => 'id']],
      [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
      [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels() {
    return [
      'id' => Yii::t('app', 'ID'),
      'fg_invoice_id' => Yii::t('app', 'Fg Invoice ID'),
      'part_no' => Yii::t('app', 'Part No'),
      'part_name' => Yii::t('app', 'Part name'),
      'qty' => Yii::t('app', 'Qty'),
      'price' => Yii::t('app', 'Price'),
      'unit_id' => Yii::t('app', 'Unit'),
      'source' => Yii::t('app', 'Source'),
      'created_at' => Yii::t('app', 'Created at'),
      'created_by' => Yii::t('app', 'Created by'),
      'updated_at' => Yii::t('app', 'Updated at'),
      'updated_by' => Yii::t('app', 'Updated by'),
    ];
  }

  public function fields() {
    return [
      'id',
      'part_no',
      'part_name',
      'qty',
      'price',
      'unit' => function() {
        return $this->isRelationPopulated('unit') ? $this->unit->unit_value : ['id' => $this->unit_id];
      },
      'source' => function() {
        return self::getSourceText($this->source);
      }
    ];
  }

  /**
   * @return ActiveQuery
   */
  public function getFgInvoice() {
    return $this->hasOne(FgInvoice::className(), ['id' => 'fg_invoice_id']);
  }

  /**
   * @return ActiveQuery
   */
  public function getUnit() {
    return $this->hasOne(Unit::className(), ['id' => 'unit_id']);
  }

  /**
   * @return ActiveQuery
   */
  public function getPart() {
    return $this->hasOne(Part::className(), ['part_no' => 'part_no']);
  }

  /**
   * @return ActiveQuery
   */
  public function getCreatedBy() {
    return $this->hasOne(User::className(), ['id' => 'created_by']);
  }

  /**
   * @return ActiveQuery
   */
  public function getUpdatedBy() {
    return $this->hasOne(User::className(), ['id' => 'updated_by']);
  }

}
