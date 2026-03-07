<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "product_specification_item".
 *
 * @property int $id
 * @property int $product_specification_id
 * @property int $related_specification_id
 * @property int $part_id
 * @property float $usage_qty
 * @property float|null $usage_qty_meter
 * @property int|null $warehouse_id
 *
 * @property Part $part
 * @property ProductSpecification $productSpecification
 */
class ProductSpecificationItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_specification_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_specification_id','usage_qty', 'warehouse_id', 'part_id'], 'required'],
            [['product_specification_id', 'related_specification_id', 'part_id', 'warehouse_id'], 'integer'],
            [['usage_qty', 'usage_qty_meter'], 'number'],
            [['product_specification_id', 'part_id'], 'unique', 'targetAttribute' => ['product_specification_id', 'part_id']],
            [['part_id'], 'exist', 'skipOnError' => true, 'targetClass' => Part::className(), 'targetAttribute' => ['part_id' => 'id']],
            [['product_specification_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductSpecification::className(), 'targetAttribute' => ['product_specification_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'product_specification_id' => Yii::t('app', 'Product specification'),
            'part_id' => Yii::t('app', 'Part'),
            'usage_qty' => Yii::t('app', 'Quantity') . ' (kg)',
            'usage_qty_meter' => Yii::t('app', 'Quantity') . ' (m)',
            'warehouse_id' => Yii::t('app', 'Warehouse'),
            'related_specification_id' => Yii::t('app', 'Related product specification'),
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $part = Part::findOne($this->part_id);
            if ($part && !empty($part->coefficient) && $part->coefficient > 0) {
                $this->usage_qty_meter = $this->usage_qty / ($part->coefficient / 1000);
            } else {
                $this->usage_qty_meter = null;
            }
            return true;
        }
        return false;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPart()
    {
        return $this->hasOne(Part::className(), ['id' => 'part_id']);
    }

    public function getWarehouse()
    {
        return $this->hasOne(Warehouse::className(), ['id' => 'warehouse_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductSpecification()
    {
        return $this->hasOne(ProductSpecification::className(), ['id' => 'product_specification_id']);
    }

    public function getRelatedSpecifications()
    {
        return $this->hasMany(ProductSpecification::className(), ['id' => 'related_specification_id']);
    }
}
