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
class SalesPlanShort   extends \yii\db\ActiveRecord
{
  public $customer_id;
  /**
   * {@inheritdoc}
   */
  
  public function rules()
  {
    return [
      [['customer_id'], 'required'],
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

  
}
