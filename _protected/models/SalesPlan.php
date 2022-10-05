<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sales_plan".
 *
 * @property int $id
 * @property int $part_id Part; semi;FG
 * @property int $customer_id Customer
 * @property string $target_date
 * @property int $target_qty
 */
class SalesPlan extends \yii\db\ActiveRecord
{
  public $partColorId, $partMarkId;
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'sales_plan';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['part_id', 'customer_id', 'target_qty', 'target_date'], 'required'],
      [['part_id', 'customer_id', 'target_qty'], 'integer'],
      [['target_date'], 'safe'],
      [
        ['part_id', 'target_date', 'customer_id'],
        'unique',
        'targetAttribute' => ['part_id', 'target_date', 'customer_id'],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id' => Yii::t('app', 'ID'),
      'part_id' => Yii::t('app', 'Part'),
      'customer_id' => Yii::t('app', 'Customer'),
      'partMarkId' => Yii::t('app', 'Part mark'),
      'partColorId' => Yii::t('app', 'Part color'),
      'target_date' => Yii::t('app', 'Date'),
      'target_qty' => Yii::t('app', 'Quantity'),
    ];
  }

  public function getCustomer()
  {
    return $this->hasOne(Customer::className(), ['id' => 'customer_id']);
  }

  public function getPart()
  {
    return $this->hasOne(Part::className(), ['id' => 'part_id']);
  }
}
