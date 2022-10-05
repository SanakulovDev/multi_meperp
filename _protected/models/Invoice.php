<?php
	namespace app\models;

	use Yii;
	use yii\behaviors\BlameableBehavior;
	use yii\behaviors\TimestampBehavior;
	use yii\db\ActiveQuery;
	use yii\db\ActiveRecord;

	/**
	 * This is the model class for table "invoice".
	 * @property int                $id
	 * @property string             $invoice_no
	 * //   * @property string $port_of_loading
	 * //   * @property int $package_qty
	 * //   * @property string $cbm
	 * //   * @property string $n_weight
	 * //   * @property string $g_weight
	 * //   * @property string $total_amount
	 * @property int                $supplier_id
	 * @property int                $currency_id
	 * @property int                $created_by
	 * @property int                $created_at
	 * @property int                $updated_by
	 * @property int                $updated_at
	 * @property ContainerInvoice[] $containerInvoices
	 * @property Container[]        $containers
	 * @property User               $createdBy
	 * @property Supplier           $supplier
	 * @property User               $updatedBy
	 */
	class Invoice extends ActiveRecord{

		const STATUS_ACTIVE = 1;
		const STATUS_COMPLETED = 10;

		/**
		 * {@inheritdoc}
		 */
		public static function tableName(){
			return 'invoice';
		}

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
		public function rules(){
			return [
				[['invoice_no', 'currency_id', 'supplier_id', 'created_by', 'created_at'], 'required'],
				[['currency_id', 'supplier_id', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
				[['invoice_no'], 'string', 'max' => 50],
				[['invoice_no'], 'unique'],
				[['invoice_date'], 'safe'],
				[['invoice_amount'], 'number'],
				[['supplier_id'], 'exist', 'skipOnError' => true, 'targetClass' => Supplier::className(), 'targetAttribute' => ['supplier_id' => 'id']],
				[['currency_id'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::className(), 'targetAttribute' => ['currency_id' => 'id']],
				[['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
				[['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels(){
			return [
				'id' => Yii::t('app', 'ID'),
				'invoice_no' => Yii::t('app', 'Invoice no'),
				'invoice_date' => Yii::t('app', 'Date'),
				'invoice_amount' => Yii::t('app', 'Amount'),
				'supplier_id' => Yii::t('app', 'Supplier'),
				'currency_id' => Yii::t('app', 'Currency'),
				'created_by' => Yii::t('app', 'Created by'),
				'created_at' => Yii::t('app', 'Created at'),
				'updated_by' => Yii::t('app', 'Updated by'),
				'updated_at' => Yii::t('app', 'Updated at'),
			];
		}

		/**
		 * @return ActiveQuery
		 */
		public function getContainerInvoices(){
			return $this->hasMany(ContainerInvoice::className(), ['invoice_id' => 'id']);
		}

		public function getContainers(){
			return $this->hasMany(Container::className(), ['id' => 'container_id'])->viaTable('container_invoice', ['invoice_id' => 'id']);
		}

		public function getCurrency(){
			return $this->hasOne(Currency::className(), ['id' => 'currency_id']);
		}

		public function getSupplier(){
			return $this->hasOne(Supplier::className(), ['id' => 'supplier_id']);
		}

		public function getCreatedBy(){
			return $this->hasOne(User::className(), ['id' => 'created_by']);
		}

		public function getUpdatedBy(){
			return $this->hasOne(User::className(), ['id' => 'updated_by']);
		}

		public function getUpdatedAtFormatted() {
			return (!empty($this->updated_at)) ? date('d.m.Y H:i', $this->updated_at) : '';
		}
	}
