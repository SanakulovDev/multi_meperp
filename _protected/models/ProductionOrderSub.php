<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "production_order_sub".
 *
 * @property int $id
 * @property int $production_order_id
 * @property int $sub_part_id
 * @property string $qty
 * @property int $warehouse_id
 *
 * @property ProductionOrder $productionOrder
 * @property Part $subPart
 * @property Warehouse $warehouse
 */
class ProductionOrderSub extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'production_order_sub';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['production_order_id', 'sub_part_id', 'qty', 'warehouse_id'], 'required'],
            [['production_order_id', 'sub_part_id', 'warehouse_id'], 'integer'],
            [['qty'], 'number'],
            [['production_order_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductionOrder::className(), 'targetAttribute' => ['production_order_id' => 'id']],
            [['sub_part_id'], 'exist', 'skipOnError' => true, 'targetClass' => Part::className(), 'targetAttribute' => ['sub_part_id' => 'id']],
            [['warehouse_id'], 'exist', 'skipOnError' => true, 'targetClass' => Warehouse::className(), 'targetAttribute' => ['warehouse_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'production_order_id' => Yii::t('app', 'Production Order ID'),
            'sub_part_id' => Yii::t('app', 'Sub Part ID'),
            'qty' => Yii::t('app', 'Qty'),
            'warehouse_id' => Yii::t('app', 'Warehouse ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductionOrder()
    {
        return $this->hasOne(ProductionOrder::className(), ['id' => 'production_order_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubPart()
    {
        return $this->hasOne(Part::className(), ['id' => 'sub_part_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWarehouse()
    {
        return $this->hasOne(Warehouse::className(), ['id' => 'warehouse_id']);
    }
}
