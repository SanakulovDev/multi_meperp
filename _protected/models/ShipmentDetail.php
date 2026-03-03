<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "shipment_detail".
 *
 * @property int $id
 * @property int $shipment_id
 * @property int $part_id
 * @property float|null $pack_size
 * @property float|null $coverage_qty
 * @property float|null $need_qty
 * @property float|null $ready_qty
 * @property float|null $approved_qty
 * @property string|null $comment
 *
 * @property Part $part
 * @property Shipment $shipment
 */
class ShipmentDetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public $part_name, $model, $unit, $diff_ready_need, $diff_appr_ready;
    public static function tableName()
    {
        return 'shipment_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['shipment_id', 'part_id'], 'required'],
            [['shipment_id', 'part_id', 'supplier_id'], 'integer'],
            [['pack_size', 'coverage_qty', 'need_qty', 'ready_qty', 'approved_qty'], 'number'],
            [['disruption_date'], 'safe'],
            [['comment'], 'string'],
            [['part_id'], 'exist', 'skipOnError' => true, 'targetClass' => Part::className(), 'targetAttribute' => ['part_id' => 'id']],
            [['supplier_id'], 'exist', 'skipOnError' => true, 'targetClass' => Supplier::className(), 'targetAttribute' => ['supplier_id' => 'id']],
            [['shipment_id'], 'exist', 'skipOnError' => true, 'targetClass' => Shipment::className(), 'targetAttribute' => ['shipment_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'shipment_id' => Yii::t('app', 'Shipment calculation'),
            'part_id' => Yii::t('app', 'Part'),
            'supplier_id' => Yii::t('app', 'Supplier'),
            'pack_size' => Yii::t('app', 'Pack size (Shipment)'),
            'disruption_date' => Yii::t('app', 'Disruption date'),
            'coverage_qty' => Yii::t('app', 'Coverage quantity'),
            'need_qty' => Yii::t('app', 'Need quantity'),
            'ready_qty' => Yii::t('app', 'Ready quantity'),
            'approved_qty' => Yii::t('app', 'Approved quantity'),
            'comment' => Yii::t('app', 'Comment'),

            'part_name' => Yii::t('app', 'Part name'),
            'model' => Yii::t('app', 'Model'),
            'unit' => Yii::t('app', 'Unit'),
            'diff_ready_need' => Yii::t('app', 'Difference'),
            'diff_appr_ready' => Yii::t('app', 'Difference'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPart()
    {
        return $this->hasOne(Part::className(), ['id' => 'part_id']);
    }

    public function getSupplier()
    {
        return $this->hasOne(Supplier::className(), ['id' => 'supplier_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShipment()
    {
        return $this->hasOne(Shipment::className(), ['id' => 'shipment_id']);
    }

    public function getDiffReadyNeed()
    {
        return $this->ready_qty - $this->need_qty;
    }

    public function getDiffApprReady()
    {
        return $this->approved_qty - $this->ready_qty;
    }
}
