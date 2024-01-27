<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%stock_info}}".
 *
 * @property int $id
 * @property int|null $part_id
 * @property int|null $warehouse_id
 * @property float|null $qty
 * @property int|null $give_user_id
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class StockInfo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public $part_name;
    public $part_status;
    public $unit;
    public $part_color;
    public $part_state;
    public static function tableName()
    {
        return '{{%stock_info}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['part_id', 'warehouse_id', 'give_user_id', 'type_id','stock_wrapper_id'], 'integer'],
            [['qty'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'part_id' => Yii::t('app', 'Part ID'),
            'warehouse_id' => Yii::t('app', 'Warehouse ID'),
            'qty' => Yii::t('app', 'Qty'),
            'give_user_id' => Yii::t('app', 'Give User ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'type_id' => Yii::t('app', 'Type'),
        ];
    }
    /**
   * @return ActiveQuery
   */
  public function getPart() {
    return $this->hasOne(Part::className(), ['id' => 'part_id']);
  }

  /**
   * @return ActiveQuery
   */
  public function getWarehouse() {
    return $this->hasOne(Warehouse::className(), ['id' => 'warehouse_id']);
  }
  
  public function getStockInfoWrapper() {
    return $this->hasOne(StockInfoWrapper::className(), ['id' => 'warehouse_id']);
  }

  // get stock
  public static function getStockPart($part_id)
  {
    // sum
    $sum = self::find()->where(['part_id' => $part_id])->sum('qty');
    return $sum;
  }
}
