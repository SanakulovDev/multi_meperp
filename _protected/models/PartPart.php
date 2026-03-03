<?php
	namespace app\models;

	use Yii;
	use yii\behaviors\BlameableBehavior;
	use yii\behaviors\TimestampBehavior;
	use yii\db\ActiveQuery;
	use yii\db\ActiveRecord;

	/**
	 * This is the model class for table "part_part".
	 * @property int       $id
	 * @property int       $part_id
	 * @property int       $sub_part_id
	 * @property string    $usage_qty
	 * @property int       $warehouse_id
	 * @property string    $remark
	 * @property int       $status
	 * @property int       $created_by
	 * @property int       $created_at
	 * @property int       $updated_by
	 * @property int       $updated_at
	 * @property User      $createdBy
	 * @property Part      $part
	 * @property Warehouse $warehouse
	 * @property Part      $subPart
	 * @property User      $updatedBy
	 * @property int       $childCount
	 */
	class PartPart extends ActiveRecord{
		public $part_nm;
		public $sub_part_nm;
		public $part_color;

		/**
		 * {@inheritdoc}
		 */
		public static function tableName(){
			return 'part_part';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules(){
			return [
				[['part_id', 'sub_part_id', 'warehouse_id', 'usage_qty', 'created_by'], 'required'],
				[['remark'], 'required', 'on' => 'update'],
				[['part_id', 'sub_part_id', 'warehouse_id', 'status', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
				[['usage_qty'], 'number'],
				[['remark', 'unit_value'], 'string', 'max' => 255],
				[['part_id', 'sub_part_id'], 'unique', 'targetAttribute' => ['part_id', 'sub_part_id']],
				[['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
				[['part_id'], 'exist', 'skipOnError' => true, 'targetClass' => Part::className(), 'targetAttribute' => ['part_id' => 'id']],
				[['sub_part_id'], 'exist', 'skipOnError' => true, 'targetClass' => Part::className(), 'targetAttribute' => ['sub_part_id' => 'id']],
				[['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
				[['warehouse_id'], 'exist', 'skipOnError' => true, 'targetClass' => Warehouse::className(), 'targetAttribute' => ['warehouse_id' => 'id']],
				[['part_id'], 'checkState'],
			];
		}

	public function fields(){
		return [
			'id',
			'part' => function () {
				return $this->isRelationPopulated('part') ? $this->part : ['id'=>$this->part_id];
			},
			'subPart' => function () {
				return $this->isRelationPopulated('subPart') ? $this->part : ['id'=>$this->sub_part_id];
			},
			'usage_qty',
			'warehouse' => function () {
				return $this->isRelationPopulated('warehouse') ? $this->warehouse : ['id'=>$this->warehouse_id];
			},
		];
	}

	public function checkState($attributes, $params){
			$part = Part::findOne($this->part_id);
			$subPart = Part::findOne($this->sub_part_id);
			// 1. Agar birinchi ustunda Siryo tanlansa xato beramiz
			if($part->state == Part::STATE_RAW){
				$this->addError('part_id', Yii::t('app', 'Part should not be a raw material'));
			}
			// 2. Agar ikkinchi ustunda Produkt tanlansa xato beramiz
			if($subPart->state == Part::STATE_FINISHED){
				$this->addError('sub_part_id', Yii::t('app', 'Sub part should not be a product'));
			}
			// 3. Agar ikkala part bir xil tanlansa xato beramiz
			if($this->part_id == $this->sub_part_id){
				$this->addError('part_id', Yii::t('app', 'Part and sub part should not be equal'));
				$this->addError('sub_part_id', Yii::t('app', 'Part and sub part should not be equal'));
			}
		}

	const STATUS_ACTIVE = 1;
	const STATUS_INACTIVE = 0;

	public $statusList = [
		self::STATUS_ACTIVE => 'Актив',
		self::STATUS_INACTIVE => 'Не актив',
		//		self::STATUS_DELETED  => 'Удален'
	];

	public $unit_value;

	/**
	 * Returns a list of behaviors that this component should behave as.
	 * @return array
	 */
	public function behaviors(){
		return [
			TimestampBehavior::className(),
			BlameableBehavior::className(),
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'id' => Yii::t('app', 'ID'),
			'part_nm' => Yii::t('app', 'Part name'),
			'sub_part_nm' => Yii::t('app', 'Sub part name'),
			'part_id' => Yii::t('app', 'Part ID'),
			'sub_part_id' => Yii::t('app', 'Sub Part ID'),
			'usage_qty' => Yii::t('app', 'Usage qty'),
			'warehouse_id' => Yii::t('app', 'Uloc'),
			'remark' => Yii::t('app', 'Remark'),
			'unit_value' => Yii::t('app', 'Unit value'),
			'status' => Yii::t('app', 'Status'),
			'created_by' => Yii::t('app', 'Created by'),
			'created_at' => Yii::t('app', 'Created at'),
			'updated_by' => Yii::t('app', 'Updated by'),
			'updated_at' => Yii::t('app', 'Updated at'),
			'part_color' => Yii::t('app', 'Color'),
		];
	}

		/**
		 * @return ActiveQuery
		 */
		public function getCreatedBy(){
			return $this->hasOne(User::className(), ['id' => 'created_by']);
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
		public function getWarehouse(){
			return $this->hasOne(Warehouse::className(), ['id' => 'warehouse_id']);
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
		public function getUpdatedBy(){
			return $this->hasOne(User::className(), ['id' => 'updated_by']);
		}

		public function getUpdatedAtFormatted(){
			return (!empty($this->updated_at)) ? date('d.m.Y H:i', $this->updated_at) : '';
		}

		public function getCreatedAtFormatted(){
			return (!empty($this->created_at)) ? date('d.m.Y H:i', $this->created_at) : '';
		}

		public function getStatusText(){
			return $this->statusList[$this->status];
		}

    public static function getChildCount($partId){
      return PartPart::find()->where(['part_id'=>$partId])->count();
    }

	}
