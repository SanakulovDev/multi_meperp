<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "document_detail_sub".
 *
 * @property int $id
 * @property int $document_id
 * @property int $part_id
 * @property int $sub_part_id
 * @property string $qty
 * @property int $warehouse_id
 *
 * @property Document $document
 * @property Part $part
 * @property Part $subPart
 * @property Warehouse $warehouse
 */
class DocumentDetailSub extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'document_detail_sub';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['document_id', 'part_id', 'sub_part_id', 'qty', 'warehouse_id'], 'required'],
            [['document_id', 'part_id', 'sub_part_id', 'warehouse_id'], 'integer'],
            [['qty'], 'number'],
            [['document_id'], 'exist', 'skipOnError' => true, 'targetClass' => Document::className(), 'targetAttribute' => ['document_id' => 'id']],
            [['part_id'], 'exist', 'skipOnError' => true, 'targetClass' => Part::className(), 'targetAttribute' => ['part_id' => 'id']],
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
            'document_id' => Yii::t('app', 'Document ID'),
            'part_id' => Yii::t('app', 'Part ID'),
            'sub_part_id' => Yii::t('app', 'Sub Part ID'),
            'qty' => Yii::t('app', 'Qty'),
            'warehouse_id' => Yii::t('app', 'Warehouse ID'),
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
