<?php
	namespace app\models;

	use Yii;
	use yii\db\ActiveQuery;
	use yii\db\ActiveRecord;

	/**
	 * This is the model class for table "part_part_wide".
	 *
	 * @property int $id
	 * @property int $part_id
	 * @property int $sub_part_id
	 * @property float $usage_qty
	 * @property int $warehouse_id
	 * @property int|null $level
	 *
	 * @property Part $part
	 * @property Part $subPart
	 * @property Warehouse $warehouse
	 */
	class PartPartWide extends ActiveRecord{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName(){
			return 'part_part_wide';
		}

		const TYPE_RAW = 'R';
		const TYPE_CONSIGNMENT = 'C';
		const TYPE_SEMI = 'S';

		/**
		 * {@inheritdoc}
		 */
		public function rules(){
			return [
				[['part_id', 'sub_part_id', 'warehouse_id', 'type'], 'required'],
				[['part_id', 'sub_part_id', 'warehouse_id', 'level'], 'integer'],
				[['usage_qty'], 'number'],
				[['part_id'], 'exist', 'skipOnError' => true, 'targetClass' => Part::className(), 'targetAttribute' => ['part_id' => 'id']],
				[['sub_part_id'], 'exist', 'skipOnError' => true, 'targetClass' => Part::className(), 'targetAttribute' => ['sub_part_id' => 'id']],
				[['warehouse_id'], 'exist', 'skipOnError' => true, 'targetClass' => Warehouse::className(), 'targetAttribute' => ['warehouse_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels(){
			return [
				'id' => Yii::t('app', 'ID'),
				'type' => Yii::t('app', 'Type'),
				'part_id' => Yii::t('app', 'Part ID'),
				'sub_part_id' => Yii::t('app', 'Sub Part ID'),
				'usage_qty' => Yii::t('app', 'Usage Qty'),
				'warehouse_id' => Yii::t('app', 'Warehouse ID'),
				'level' => Yii::t('app', 'Level'),
			];
		}

		/**
		 * @return ActiveQuery
		 */
		public function getPart(){
			return $this->hasOne(Part::className(), ['id' => 'part_id']);
		}

		/**
		 * @return ActiveQuery
		 */
		public function getSubPart(){
			return $this->hasOne(Part::className(), ['id' => 'sub_part_id']);
		}

		/**
		 * @return ActiveQuery
		 */
		public function getWarehouse(){
			return $this->hasOne(Warehouse::className(), ['id' => 'warehouse_id']);
		}
	}
