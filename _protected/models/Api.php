<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "api".
 *
 * @property int $id
 * @property string $inventory_date
 * @property string $stock_date
 * @property int $created_by
 * @property int $created_at
 *
 * @property User $createdBy
 * @property ApiDetail[] $apiDetails
 * @property Part[] $parts
 */
class Api extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'api';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['inventory_date'], 'required'],
            [['inventory_date', 'stock_date'], 'safe'],
            [['created_by', 'created_at'], 'integer'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'inventory_date' => Yii::t('app', 'Inventory date'),
            'stock_date' => Yii::t('app', 'Stock date'),
            'created_by' => Yii::t('app', 'Created by'),
            'created_at' => Yii::t('app', 'Created at'),
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
    public function getApiDetails()
    {
        return $this->hasMany(ApiDetail::className(), ['api_id' => 'id']);
    }
    
    public function getInvinfo()
    {
        return $this->id . ' / ' . $this->inventory_date;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParts()
    {
        return $this->hasMany(Part::className(), ['id' => 'part_id'])->viaTable('api_detail', ['api_id' => 'id']);
    }
    
    public function getCreatedAtFormatted(){
        return ( !empty($this->created_at) ) ? date('d.m.Y H:i', $this->created_at) : '';
    }
    
    public function beforeSave($insert){
            if(parent::beforeSave($insert)){
                    if($this->isNewRecord){
                            $this->created_by = Yii::$app->user->identity->id;
                            $this->created_at = time();
                    }
                    return true;
            }else{
                    return false;
            }
    }
}
