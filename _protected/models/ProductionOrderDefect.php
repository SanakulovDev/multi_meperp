<?php
namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "production_order_defect".
 * @property int             $id
 * @property int             $production_order_id
 * @property int             $serial_number
 * @property int             $defect_id
 * @property int             $qty
 * @property int             $created_by
 * @property int             $created_at
 * @property User            $createdBy
 * @property Defect          $defect
 * @property ProductionOrder $productionOrder
 */
class ProductionOrderDefect extends \yii\db\ActiveRecord{
	public $serial_number,$filter_from, $filter_to;

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(){
		return 'production_order_defect';
	}

	public function behaviors(){
		return [
			[
				'class'              => TimestampBehavior::className(),
				'createdAtAttribute' => 'created_at',
				'updatedAtAttribute' => null,
			],
			[
				'class'              => BlameableBehavior::className(),
				'createdByAttribute' => 'created_by',
				'updatedByAttribute' => null,
			]
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(){
		return [
			[['production_order_id', 'defect_id'], 'required'],
			[['production_order_id', 'defect_id', 'qty', 'created_by', 'created_at'], 'integer'],
			[['serial_number'], 'string', 'max' => 50],
			[['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
			[['defect_id'], 'exist', 'skipOnError' => true, 'targetClass' => Defect::className(), 'targetAttribute' => ['defect_id' => 'id']],
			[['production_order_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductionOrder::className(), 'targetAttribute' => ['production_order_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(){
		return [
			'id'                  => Yii::t('app', 'ID'),
			'serial_number'       => Yii::t('app', 'Serial number'),
			'production_order_id' => Yii::t('app', 'Production Order'),
			'defect_id'           => Yii::t('app', 'Defect'),
			'qty'                 => Yii::t('app', 'Quantity'),
			'created_by'          => Yii::t('app', 'Created by'),
			'created_at'          => Yii::t('app', 'Created at'),
			'filter_from' => Yii::t('app', 'From'),
			'filter_to' => Yii::t('app', 'To'),
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getCreatedBy(){
		return $this->hasOne(User::className(), ['id' => 'created_by']);
	}

	public function getCreatedAtFormatted() {
		return (!empty($this->created_at))?date('d.m.Y H:i',$this->created_at):'';
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getDefect(){
		return $this->hasOne(Defect::className(), ['id' => 'defect_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getProductionOrder(){
		return $this->hasOne(ProductionOrder::className(), ['id' => 'production_order_id']);
	}
}
