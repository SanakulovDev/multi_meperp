<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "api_detail".
 *
 * @property int $id
 * @property int $api_id
 * @property int $part_id
 * @property string $inventory_qty
 * @property string $stock_qty
 *
 * @property Api $api
 * @property Part $part
 */
class ApiDetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public $uom;
    public static function tableName()
    {
        return 'api_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['api_id', 'part_id', 'inventory_qty'], 'required'],
            [['api_id', 'part_id'], 'integer'],
            [['inventory_qty', 'stock_qty'], 'number'],
            ['part_id', 'unique', 'targetAttribute' => ['api_id', 'part_id'],  'message' => Yii::t('app', 'Duplicating data')],
            [['api_id'], 'exist', 'skipOnError' => true, 'targetClass' => Api::className(), 'targetAttribute' => ['api_id' => 'id']],
            [['part_id'], 'exist', 'skipOnError' => true, 'targetClass' => Part::className(), 'targetAttribute' => ['part_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'api_id' => Yii::t('app', 'Inventory ID'),
            'part_id' => Yii::t('app', 'Part'),
            'inventory_qty' => Yii::t('app', 'Inventory qty'),
            'stock_qty' => Yii::t('app', 'Stock qty'),
            'uom' => Yii::t('app', 'Unit'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApi()
    {
        return $this->hasOne(Api::className(), ['id' => 'api_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPart()
    {
        return $this->hasOne(Part::className(), ['id' => 'part_id']);
    }
}
