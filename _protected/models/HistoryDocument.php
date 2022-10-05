<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "history_document".
 *
 * @property int $id
 * @property string $his_action
 * @property int $his_user_id
 * @property string $his_date
 * @property int $document_id
 * @property string $docnum
 * @property string $docdate
 * @property int $document_type_id
 * @property int $from_warehouse_id
 * @property int $to_warehouse_id
 * @property string $series
 * @property string $product_name
 * @property int $status
 * @property int $created_by
 * @property int $created_at
 * @property int $updated_by
 * @property int $updated_at
 *
 * @property User $createdBy
 * @property Document $document
 * @property DocumentType $documentType
 * @property Warehouse $fromWarehouse
 * @property User $hisUser
 * @property Warehouse $toWarehouse
 * @property User $updatedBy
 * @property HistoryDocumentDetail[] $historyDocumentDetails
 */
class HistoryDocument extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     *
     *
     */

    const ADJ_RECEIPT = 1;
    const ADJ_ISSUE = 0;

    public  static $adjList = [
        self::ADJ_RECEIPT => 'Receipt',
        self::ADJ_ISSUE  => 'Issue',
    ];

    public static function tableName()
    {
        return 'history_document';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['his_user_id', 'document_id', 'document_type_id', 'from_warehouse_id', 'to_warehouse_id', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
            [['his_date', 'docdate'], 'safe'],
            [['his_action'], 'string', 'max' => 50],
            [['docnum', 'series', 'product_name'], 'string', 'max' => 255],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['document_id'], 'exist', 'skipOnError' => true, 'targetClass' => Document::className(), 'targetAttribute' => ['document_id' => 'id']],
            [['document_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentType::className(), 'targetAttribute' => ['document_type_id' => 'id']],
            [['from_warehouse_id'], 'exist', 'skipOnError' => true, 'targetClass' => Warehouse::className(), 'targetAttribute' => ['from_warehouse_id' => 'id']],
            [['his_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['his_user_id' => 'id']],
            [['to_warehouse_id'], 'exist', 'skipOnError' => true, 'targetClass' => Warehouse::className(), 'targetAttribute' => ['to_warehouse_id' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'his_action' => Yii::t('app', 'Action'),
            'his_user_id' => Yii::t('app', 'User'),
            'his_date' => Yii::t('app', 'Date'),
            'document_id' => Yii::t('app', 'Document'),

            'docnum' => Yii::t('app', 'Document number'),
            'docdate' => Yii::t('app', 'Document date'),
            'document_type_id' => Yii::t('app', 'Document type'),
            'from_warehouse_id' => Yii::t('app', 'Warehouse A'),
            'to_warehouse_id' => Yii::t('app', 'Warehouse B'),
            'series' => Yii::t('app', 'Series'),
            'product_name' => Yii::t('app', 'Product name'),
            'status' => Yii::t('app', 'Status'),
            'created_by' => Yii::t('app', 'Created by'),
            'created_at' => Yii::t('app', 'Created at'),
            'updated_by' => Yii::t('app', 'Updated by'),
            'updated_at' => Yii::t('app', 'Updated at'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
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
    public function getDocumentType()
    {
        return $this->hasOne(DocumentType::className(), ['id' => 'document_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFromWarehouse()
    {
        return $this->hasOne(Warehouse::className(), ['id' => 'from_warehouse_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHisUser()
    {
        return $this->hasOne(User::className(), ['id' => 'his_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getToWarehouse()
    {
        return $this->hasOne(Warehouse::className(), ['id' => 'to_warehouse_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHistoryDocumentDetails()
    {
        return $this->hasMany(HistoryDocumentDetail::className(), ['history_document_id' => 'id']);
    }

    public function getStatusName()
    {
        return ($this->status == 0) ? Yii::t('app', 'Pending') : Yii::t('app', 'Confirmed');
    }


    public function getAdjWhId()
    {
        if($this->to_warehouse_id == 99){
            return $this->from_warehouse_id;
        }elseif($this->from_warehouse_id == 99){
            return $this->to_warehouse_id;
        }

    }

    public function getAdjWhName()
    {
        return Warehouse::findOne($this->adjWhId)->name;
    }

    public function getAdjStatus()
    {
        if($this->to_warehouse_id == 99){
            return 0;
        }elseif($this->from_warehouse_id == 99){
            return 1;
        }

    }


    public function getAdjName(){
        return Yii::t('app',self::$adjList[$this->adjStatus]);
    }


    public function getActionName()
    {
        switch($this->his_action){
            case 'create' : return Yii::t('app', 'Creating');break;
            case 'update' : return Yii::t('app', 'Changing');break;
            case 'delete' : return Yii::t('app', 'Deleting');break;
            case 'confirm' : return Yii::t('app', 'Confirmation');break;
        }

    }


}
