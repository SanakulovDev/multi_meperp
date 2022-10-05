<?php
	namespace app\models;

	use Yii;
	use yii\db\ActiveQuery;
	use yii\db\ActiveRecord;

	/**
	 * This is the model class for table "contract_detail".
	 * @property int          $id
	 * @property int          $contract_id
	 * @property int          $part_id
	 * @property float        $price
	 * @property int|null     $delivery_term_id
	 * @property float|null   $weekly_capacity
	 * @property string|null  $cnfea
	 * @property int|null     $sub_source
	 * @property int|null     $lead_time Tayyorlash vaqti(kun)
	 * @property Contract     $contract
	 * @property Part         $part
	 * @property DeliveryTerm $deliveryTerm
	 * @property int $is_primary_price Values: 0 or 1, Default: 0
	 */
	class ContractDetail extends ActiveRecord{
		/**
		 * {@inheritdoc}
		 */
		public $part_name, $part_color, $part_no;
		public const SUB_SOURCE_IH = 1;
		public const SUB_SOURCE_IM = 2;
		public const SUB_SOURCE_LP = 3;

		public $subSourceList = [
			self::SUB_SOURCE_IH => 'IH',
			self::SUB_SOURCE_IM => 'IM',
			self::SUB_SOURCE_LP => 'LP',
		];

		public static function tableName(){
			return 'contract_detail';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules(){
			return [
				[['contract_id', 'part_id', 'price'], 'required'],
				[['delivery_term_id'], 'required', 'except' => 'primary-price'],
				[['contract_id', 'part_id', 'delivery_term_id', 'cnfea', 'sub_source', 'is_primary_price'], 'integer'],
				[['price', 'weekly_capacity'], 'number'],
				[['part_name'], 'safe'],
				[['cnfea'], 'is10NumbersOnly'],
				[['lead_time'], 'integer', 'max' => 999],
				['price', 'compare', 'compareValue' => 0, 'operator' => '>', 'message' => Yii::t('app', 'Price must be greater than zero')],
				[['contract_id', 'part_id', 'delivery_term_id'], 'unique', 'targetAttribute' => ['contract_id', 'part_id', 'delivery_term_id'], 'message' => Yii::t('app', 'Duplicating data')],
				[['delivery_term_id'], 'exist', 'skipOnError' => true, 'targetClass' => DeliveryTerm::className(), 'targetAttribute' => ['delivery_term_id' => 'id']],
				[['contract_id'], 'exist', 'skipOnError' => true, 'targetClass' => Contract::className(), 'targetAttribute' => ['contract_id' => 'id']],
				[['part_id'], 'exist', 'skipOnError' => true, 'targetClass' => Part::className(), 'targetAttribute' => ['part_id' => 'id']],
			];
		}

		public function is10NumbersOnly($attribute){
			if(!preg_match('/^[0-9]{10}$/', $this->$attribute)){
				$this->addError($attribute, Yii::t('app', 'CN FEA Code must contain 10 digits.'));
			}
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels(){
			return [
				'id' => Yii::t('app', 'ID'),
				'contract_id' => Yii::t('app', 'Contract'),
				'part_id' => Yii::t('app', 'Part'),
				'price' => Yii::t('app', 'Price'),
				'delivery_term_id' => Yii::t('app', 'Delivery term'),
				'part_name' => Yii::t('app', 'Part name'),
				'part_color' => Yii::t('app', 'Part color'),
				'part_no' => Yii::t('app', 'Part(DY)'),
				'weekly_capacity' => Yii::t('app', 'Weekly capacity'),
				'cnfea' => Yii::t('app', 'CNFEA Code'),
				'sub_source' => Yii::t('app', 'Sub source'),
				'lead_time' => Yii::t('app', 'Lead time (DAY)'),
				'is_primary_price' => Yii::t('app', 'Primary price'),
			];
		}

		/**
		 * @return ActiveQuery
		 */
		public function getContract(){
			return $this->hasOne(Contract::className(), ['id' => 'contract_id']);
		}

		/**
		 * @return ActiveQuery
		 */
		public function getPart(){
			return $this->hasOne(Part::className(), ['id' => 'part_id']);
		}

		public function getDeliveryTerm(){
			return $this->hasOne(DeliveryTerm::className(), ['id' => 'delivery_term_id']);
		}

		public function getSubSourceText(){
			return ($this->sub_source) ? $this->subSourceList[$this->sub_source] : '';
		}
		
		public function getIsPrimaryPriceText(){
			return ($this->is_primary_price == 1) ? Yii::t('app','Yes') : Yii::t('app','No');
		}

		public static function getSubSourceIdByText($sub_source){
			switch($sub_source){
				case 'IH' :
					return self::SUB_SOURCE_IH;
					break;
				case 'IM' :
					return self::SUB_SOURCE_IM;
					break;
				case 'LP' :
					return self::SUB_SOURCE_LP;
					break;
			}
		}

	}
