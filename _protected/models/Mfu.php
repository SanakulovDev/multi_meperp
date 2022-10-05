<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mfu".
 *
 * @property int $id
 * @property int $part_id
 * @property string $average
 * @property string $capacity
 * @property string $transit_time
 * @property int $ship_mode_id
 * @property string $mfu_code
 * @property int $contract_source_id
 * @property string $bank
 * @property int $constraint
 * @property int $consolidation_type_id
 * @property int $created_by
 * @property int $created_at
 * @property int $updated_by
 * @property int $updated_at
 *
 * @property ConsolidationType $consolidationType
 * @property ContractSource $contractSource
 * @property User $createdBy
 * @property Part $part
 * @property ShipMode $shipMode
 * @property User $updatedBy
 */
class Mfu extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mfu';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['part_id','moq'], 'required'],
            [['part_id', 'ship_mode_id', 'contract_source_id', 'constraint', 'consolidation_type_id', 'created_by', 'created_at', 'updated_by', 'updated_at','moq'], 'integer'],
            [['average', 'capacity', 'transit_time', 'bank'], 'number'],
            [['mfu_code'], 'string', 'max' => 10],
            [['part_id'], 'unique'],
            [['consolidation_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ConsolidationType::className(), 'targetAttribute' => ['consolidation_type_id' => 'id']],
            [['contract_source_id'], 'exist', 'skipOnError' => true, 'targetClass' => ContractSource::className(), 'targetAttribute' => ['contract_source_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['part_id'], 'exist', 'skipOnError' => true, 'targetClass' => Part::className(), 'targetAttribute' => ['part_id' => 'id']],
            [['ship_mode_id'], 'exist', 'skipOnError' => true, 'targetClass' => ShipMode::className(), 'targetAttribute' => ['ship_mode_id' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'part_id' => Yii::t('app', 'Part'),
            'average' => Yii::t('app', 'Average monthly requirement'),
            'capacity' => Yii::t('app', 'Montly supplier capacity'),
            'transit_time' => Yii::t('app', 'Transit time'),
            'ship_mode_id' => Yii::t('app', 'Ship mode'),
            'mfu_code' => Yii::t('app', 'MFU Code'),
            'contract_source_id' => Yii::t('app', 'Supply type'),
            'bank' => Yii::t('app', 'Bank'),
            'constraint' => Yii::t('app', 'Constraint list'),
            'consolidation_type_id' => Yii::t('app', 'Consolidation type'),
            'moq' => Yii::t('app', 'MOQ'),
            'created_by' => Yii::t('app', 'Created by'),
            'created_at' => Yii::t('app', 'Created at'),
            'updated_by' => Yii::t('app', 'Updated by'),
            'updated_at' => Yii::t('app', 'Updated at'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConsolidationType()
    {
        return $this->hasOne(ConsolidationType::className(), ['id' => 'consolidation_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContractSource()
    {
        return $this->hasOne(ContractSource::className(), ['id' => 'contract_source_id']);
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
    public function getPart()
    {
        return $this->hasOne(Part::className(), ['id' => 'part_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShipMode()
    {
        return $this->hasOne(ShipMode::className(), ['id' => 'ship_mode_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    public function getUpdatedAtFormatted() {
        return (!empty($this->updated_at))?date('d.m.Y H:i',$this->updated_at):'';
    }
    public function getCreatedAtFormatted() {
        return (!empty($this->created_at))?date('d.m.Y H:i',$this->created_at):'';
    }

    public function beforeSave($insert){
        if (parent::beforeSave($insert)) {
             if($this->isNewRecord) {
                    $this->created_by = Yii::$app->user->identity->id;
                    $this->created_at = time();
                } else {
                    $this->updated_by = Yii::$app->user->identity->id;
                    $this->updated_at = time();
                }
            return true;
        } else {
            return false;
        }
    }
}
