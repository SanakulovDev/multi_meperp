<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "document_detail".
 *
 * @property int $id
 * @property int $document_id
 * @property int $part_id
 * @property string $veh
 * @property string $qty
 * @property string $price
 * @property string $currency
 * @property string $remarks
 *
 * @property Document $document
 * @property Part $part
 */
class DocumentDetail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'document_detail';
    }

    public function fields()
    {
        return [
            'id',
            'part'=> function(){ 
                return $this->isRelationPopulated('part') ? $this->part : ['id'=>$this->part_id]; 
            },
            'qty'
        ];
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['document_id', 'part_id', 'qty'], 'required'],
            [['document_id', 'part_id'], 'integer'],
            [['veh', 'qty', 'price'], 'number'],
            ['qty', 'compare', 'compareValue' => 0, 'operator' => '>','message'=>Yii::t('app', 'Quantity must be greater than zero')],
            [['currency'], 'string', 'max' => 20],
            [['remarks'], 'string', 'max' => 255],
            //[['document_id', 'part_id'], 'unique', 'targetAttribute' => ['document_id', 'part_id'],  'message' => Yii::t('app', 'Duplicating data')],
            [['document_id'], 'exist', 'skipOnError' => true, 'targetClass' => Document::className(), 'targetAttribute' => ['document_id' => 'id']],
            [['part_id'], 'exist', 'skipOnError' => true, 'targetClass' => Part::className(), 'targetAttribute' => ['part_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'document_id' => Yii::t('app', 'Document'),
            'part_id' => Yii::t('app', 'Detail'),
            'veh' => Yii::t('app', 'Veh'),
            'qty' => Yii::t('app', 'Quantity'),
            'price' => Yii::t('app', 'Price'),
            'currency' => Yii::t('app', 'Currency'),
            'remarks' => Yii::t('app', 'Remarks'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDocument()
    {
        return $this->hasOne(Document::className(), ['id' => 'document_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPart()
    {
        return $this->hasOne(Part::className(), ['id' => 'part_id']);
    }
}
