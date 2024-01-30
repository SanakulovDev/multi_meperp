<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%stock_info_sub}}".
 *
 * @property int $id
 * @property int|null $stock_info_id
 * @property int|null $stock_info_wrapper_id
 * @property int|null $p_order_id
 * @property float|null $qty
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property int|null $give_user_id
 *
 * @property User $giveUser
 * @property ProductionOrder $pOrder
 * @property StockInfo $stockInfo
 * @property StockInfoWrapper $stockInfoWrapper
 */
class StockInfoSub extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%stock_info_sub}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['stock_info_id', 'stock_info_wrapper_id', 'p_order_id', 'give_user_id', 'status'], 'integer'],
            [['qty', 'percent'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['give_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['give_user_id' => 'id']],
            [['p_order_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductionOrder::className(), 'targetAttribute' => ['p_order_id' => 'id']],
            [['stock_info_id'], 'exist', 'skipOnError' => true, 'targetClass' => StockInfo::className(), 'targetAttribute' => ['stock_info_id' => 'id']],
            [['stock_info_wrapper_id'], 'exist', 'skipOnError' => true, 'targetClass' => StockInfoWrapper::className(), 'targetAttribute' => ['stock_info_wrapper_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'stock_info_id' => Yii::t('app', 'Stock Info'),
            'stock_info_wrapper_id' => Yii::t('app', 'Stock Info Wrapper'),
            'p_order_id' => Yii::t('app', 'P Order ID'),
            'qty' => Yii::t('app', 'Qty'),
            'created_at' => Yii::t('app', 'Created'),
            'updated_at' => Yii::t('app', 'Updated'),
            'give_user_id' => Yii::t('app', 'User'),
            'percent' => Yii::t('app', 'Percent'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * Gets query for [[GiveUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGiveUser()
    {
        return $this->hasOne(User::className(), ['id' => 'give_user_id']);
    }

    /**
     * Gets query for [[POrder]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPOrder()
    {
        return $this->hasOne(ProductionOrder::className(), ['id' => 'p_order_id']);
    }

    /**
     * Gets query for [[StockInfo]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStockInfo()
    {
        return $this->hasOne(StockInfo::className(), ['id' => 'stock_info_id']);
    }

    /**
     * Gets query for [[StockInfoWrapper]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStockInfoWrapper()
    {
        return $this->hasOne(StockInfoWrapper::className(), ['id' => 'stock_info_wrapper_id']);
    }
}
