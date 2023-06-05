<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sales_contract_detail".
 *
 * @property int $id
 * @property int $sales_contract_id
 * @property int $part_id
 * @property string $price
 *
 * @property Contract $contract
 * @property Part $part
 * @property  $qty
 */
class SalesContractDetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */

    public $part_name,$part_color,$part_no;

    public static function tableName()
    {
        return 'sales_contract_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sales_contract_id', 'part_id', 'price','delivery_term_id', 'qty'], 'required'],
            [['sales_contract_id', 'part_id', 'delivery_term_id'], 'integer'],
            [['price', 'vat', 'excise', 'qty'], 'number'],
            [['part_name'], 'safe'],
            ['price', 'compare', 'compareValue' => 0, 'operator' => '>','message'=>Yii::t('app', 'Price must be greater than zero')],
            [['sales_contract_id', 'part_id', 'delivery_term_id'], 'unique', 'targetAttribute' => ['sales_contract_id', 'part_id', 'delivery_term_id'],  'message' => Yii::t('app', 'Duplicating data')],
            [['delivery_term_id'], 'exist', 'skipOnError' => true, 'targetClass' => DeliveryTerm::className(), 'targetAttribute' => ['delivery_term_id' => 'id']],
            [['sales_contract_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalesContract::className(), 'targetAttribute' => ['sales_contract_id' => 'id']],
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
            'sales_contract_id' => Yii::t('app', 'Sales contract'),
            'part_id' => Yii::t('app', 'Part'),
            'price' => Yii::t('app', 'Price'),
            'vat' => Yii::t('app', 'VAT'),
            'excise' => Yii::t('app', 'Excise'),
            'delivery_term_id'    => Yii::t('app', 'Delivery term'),

            'part_name' => Yii::t('app', 'Part name'),
            'part_color' => Yii::t('app', 'Part color'),
            'part_no' => Yii::t('app', 'Part(DY)'),
            'qty' => Yii::t('app', 'Qty'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */

    public function getSalesContract()
    {
        return $this->hasOne(SalesContract::className(), ['id' => 'sales_contract_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPart()
    {
        return $this->hasOne(Part::className(), ['id' => 'part_id']);
    }

    public function getDeliveryTerm(){
        return $this->hasOne(DeliveryTerm::className(), ['id' => 'delivery_term_id']);
    }


}
