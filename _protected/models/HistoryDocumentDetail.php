<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "history_document_detail".
 *
 * @property int $id
 * @property int $history_document_id
 * @property int $document_detail_id
 * @property int $document_id
 * @property int $part_id
 * @property string $veh
 * @property string $qty
 * @property string $price
 * @property string $currency
 * @property string $remarks
 *
 * @property DocumentDetail $documentDetail
 * @property Document $document
 * @property HistoryDocument $historyDocument
 * @property Part $part
 */
class HistoryDocumentDetail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'history_document_detail';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['history_document_id', 'document_detail_id', 'document_id', 'part_id'], 'integer'],
            [['veh', 'qty', 'price'], 'number'],
            [['currency'], 'string', 'max' => 20],
            [['remarks'], 'string', 'max' => 255],
            [['document_detail_id'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentDetail::className(), 'targetAttribute' => ['document_detail_id' => 'id']],
            [['document_id'], 'exist', 'skipOnError' => true, 'targetClass' => Document::className(), 'targetAttribute' => ['document_id' => 'id']],
            [['history_document_id'], 'exist', 'skipOnError' => true, 'targetClass' => HistoryDocument::className(), 'targetAttribute' => ['history_document_id' => 'id']],
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
            
            'history_document_id' => Yii::t('app', 'History'),
            
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
    public function getDocumentDetail()
    {
        return $this->hasOne(DocumentDetail::className(), ['id' => 'document_detail_id']);
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
    public function getHistoryDocument()
    {
        return $this->hasOne(HistoryDocument::className(), ['id' => 'history_document_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPart()
    {
        return $this->hasOne(Part::className(), ['id' => 'part_id']);
    }
}
