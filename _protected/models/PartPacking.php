<?php
	namespace app\models;

	use Yii;
	use yii\behaviors\BlameableBehavior;
	use yii\behaviors\TimestampBehavior;
	use yii\db\ActiveQuery;
	use yii\db\ActiveRecord;

	/**
	 * This is the model class for table "part_packing".
	 * @property int        $id
	 * @property int        $part_id
	 * @property int|null   $supplier_id
	 * @property int|null   $returnable
	 * @property float|null $pack_qty
	 * @property float|null $piece_weight
	 * @property int|null   $pack_id
	 * @property int|null   $created_by
	 * @property int        $created_at
	 * @property int|null   $updated_by
	 * @property int|null   $updated_at
	 * @property User       $createdBy
	 * @property Pack       $pack
	 * @property Part       $part
	 * @property Supplier   $supplier
	 * @property User       $updatedBy
	 */
	class PartPacking extends ActiveRecord{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName(){
			return 'part_packing';
		}

		public function behaviors(){
			return [
				TimestampBehavior::className(),
				BlameableBehavior::className(),
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules(){
			return [
				[['part_id'], 'required'],
				[['part_id', 'supplier_id', 'returnable', 'pack_id', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
				[['pack_qty', 'piece_weight'], 'number'],
				[['part_id', 'pack_id'], 'unique', 'targetAttribute' => ['part_id', 'pack_id']],
				[['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
				[['pack_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pack::className(), 'targetAttribute' => ['pack_id' => 'id']],
				[['part_id'], 'exist', 'skipOnError' => true, 'targetClass' => Part::className(), 'targetAttribute' => ['part_id' => 'id']],
				[['supplier_id'], 'exist', 'skipOnError' => true, 'targetClass' => Supplier::className(), 'targetAttribute' => ['supplier_id' => 'id']],
				[['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels(){
			return [
				'id' => Yii::t('app', 'ID'),
				'part_id' => Yii::t('app', 'Part No'),
				'supplier_id' => Yii::t('app', 'Supplier'),
				'returnable' => Yii::t('app', 'Returnable'),
				'pack_qty' => Yii::t('app', 'Standard pack'),
				'netto' => Yii::t('app', 'Net weight (kg)'),
				'brutto' => Yii::t('app', 'Gross weight (kg)'),
				'piece_weight' => Yii::t('app', 'Part weight (kg)'),
				'pack_id' => Yii::t('app', 'Pack'),
				'created_by' => Yii::t('app', 'Created by'),
				'created_at' => Yii::t('app', 'Created at'),
				'updated_by' => Yii::t('app', 'Updated by'),
				'updated_at' => Yii::t('app', 'Updated at'),
			];
		}

		/**
		 * @return ActiveQuery
		 */
		public function getNetto(){
			return $this->pack_qty*$this->piece_weight + 0;
		}

		public function getBrutto(){
			return $this->pack ? $this->pack->weight + $this->netto + 0 : '';
		}

		public function getCreatedBy(){
			return $this->hasOne(User::className(), ['id' => 'created_by']);
		}

		/**
		 * @return ActiveQuery
		 */
		public function getPack(){
			return $this->hasOne(Pack::className(), ['id' => 'pack_id']);
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
		public function getSupplier(){
			return $this->hasOne(Supplier::className(), ['id' => 'supplier_id']);
		}

		/**
		 * @return ActiveQuery
		 */
		public function getUpdatedBy(){
			return $this->hasOne(User::className(), ['id' => 'updated_by']);
		}

		public function getUpdatedAtFormatted(){
			return (!empty($this->updated_at)) ? date('d.m.Y H:i', $this->updated_at) : '';
		}

		public function getCreatedAtFormatted(){
			return (!empty($this->created_at)) ? date('d.m.Y H:i', $this->created_at) : '';
		}

		public function getReturnableFormatted(){
			$returnables = [0 => Yii::t('app', 'N'), 1 => Yii::t('app', 'Y')];
			return $returnables[$this->returnable];
		}
	}
