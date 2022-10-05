<?php
	namespace app\models;

	use Yii;
	use yii\behaviors\BlameableBehavior;
	use yii\behaviors\TimestampBehavior;
	use yii\db\ActiveQuery;
	use yii\db\ActiveRecord;

	/**
	 * This is the model class for table "payment_control".
	 * @property int         $id
	 * @property string      $no
	 * @property string      $date
	 * @property int         $payment_type
	 * @property string      $amount
	 * @property int         $contract_id
	 * @property int         $supplier_id
	 * @property int         $created_at
	 * @property int         $created_by
	 * @property int         $updated_at
	 * @property int         $updated_by
	 * @property string      $expire_date
	 * @property string      $shipment_date
	 * @property int         $part_order_id
	 * @property string      $bank_name
	 * @property int				 $is_spend
	 * @property Invoice[]   $invoices
	 * @property Contract    $contract
	 * @property User        $createdBy
	 * @property PaymentType $paymentType
	 * @property Supplier    $supplier
	 * @property User        $updatedBy
	 */
	class PaymentControl extends ActiveRecord{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName(){
			return 'payment_control';
		}

		const LC_TYPE = 0;
		const POST_TYPE = 1;
		const PRE_TYPE = 2;
		
		const STATUS_ACTIVE = 1;
		const STATUS_COMPLETED = 10;

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
				[['no', 'date', 'payment_type', 'amount', 'supplier_id'], 'required'],
				[['date', 'expire_date', 'shipment_date', 'bank_name'], 'safe'],
				[['payment_type', 'is_spend', 'part_order_id', 'contract_id', 'supplier_id', 'created_at', 'created_by', 'updated_at', 'updated_by','dummy_order'], 'integer'],
				[['amount'], 'number'],
				[['no'], 'string', 'max' => 100],
				[['bank_name'], 'string', 'max' => 191],
				[['no', 'date'], 'unique', 'targetAttribute' => ['no', 'date']],
				[['contract_id'], 'exist', 'skipOnError' => true, 'targetClass' => Contract::className(), 'targetAttribute' => ['contract_id' => 'id']],
				[['part_order_id'], 'exist', 'skipOnError' => true, 'targetClass' => PartOrder::className(), 'targetAttribute' => ['part_order_id' => 'id']],
				[['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
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
				'no' => Yii::t('app', 'Payment number'),
				'date' => Yii::t('app', 'Payment date'),
				'amount' => Yii::t('app', 'Amount'),
				'contract_id' => Yii::t('app', 'Contract no'),
				'supplier_id' => Yii::t('app', 'Supplier'),
				'created_at' => Yii::t('app', 'Created at'),
				'created_by' => Yii::t('app', 'Created by'),
				'updated_at' => Yii::t('app', 'Updated at'),
				'updated_by' => Yii::t('app', 'Updated by'),

				'payment_type' => Yii::t('app', 'Payment type'),
				'expire_date' => Yii::t('app', 'Expiry date'),
				'shipment_date' => Yii::t('app', 'Last shipment date'),
				'part_order_id' => Yii::t('app', 'Order no'),
				'bank_name' => Yii::t('app', 'Bank'),
				'is_spend' => Yii::t('app', 'Spent'),
				'dummy_order' => Yii::t('app', 'Dummy order'),
			];
		}

		/**
		 * @return ActiveQuery
		 */
		public function getInvoices(){
			return $this->hasMany(Invoice::className(), ['payment_control_id' => 'id']);
		}

		public function getTypes(){
			return [
				self::LC_TYPE 	=> Yii::t('app', 'LC'),
				self::POST_TYPE => Yii::t('app', 'Post payment'),
				self::PRE_TYPE 	=> Yii::t('app', 'Prepayment'),
			];
		}

		public function getTypeName() {
			return $this->getTypes()[$this->payment_type];
		}
		/**
		 * @return ActiveQuery
		 */
		public function getContract(){
			return $this->hasOne(Contract::className(), ['id' => 'contract_id']);
		}

		public function getPartOrder(){
			return $this->hasOne(PartOrder::className(), ['id' => 'part_order_id']);
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
		public function getPaymentType(){
			return $this->hasOne(PaymentType::className(), ['id' => 'payment_type_id']);
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

		public function getDummyOrderText(){
			return ($this->dummy_order == 1) ? 'D' : '';
		}

		public static function getPastPayments(){

				$data = self::find()->where([
          'and',
          ['dummy_order' => 1],
          ['<','date', date('Y-m-d')]
        ])->all();
	
			return ($data) ? $data : [];
	
		}
	}
