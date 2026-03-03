<?php
	namespace app\models;

	use Yii;
	use yii\db\ActiveQuery;
	use yii\db\ActiveRecord;

	/**
	 * This is the model class for table "supplier".
	 * @property int              $id
	 * @property string           $name
	 * @property string           $duns
	 * @property string|null      $alias
	 * @property string|null      $address
	 * @property string|null      $city
	 * @property string|null      $postal
	 * @property int|null         $transit_time vaqt(soat) birligida)
	 * @property int|null         $country_code_id
	 * @property string|null      $contact_name
	 * @property string|null      $contact_position
	 * @property string|null      $contact_email
	 * @property string|null      $contact_phone
	 * @property string|null      $contact_cellular
	 * @property Contract[]       $contracts
	 * @property Document[]       $documents
	 * @property Invoice[]        $invoices
	 * @property Lms[]            $lms
	 * @property PartPacking[]    $partPackings
	 * @property PaymentControl[] $paymentControls
	 * @property CountryCode      $countryCode
	 * @property Warehouse[]      $warehouses
	 */
	class Supplier extends ActiveRecord{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName(){
			return 'supplier';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules(){
			return [
				[['name', 'duns'], 'required'],
				[['country_code_id'], 'integer'],
				[['name', 'city'], 'string', 'max' => 100],
				[['duns', 'postal'], 'string', 'max' => 30],
				[['alias', 'contact_name', 'contact_position', 'contact_email', 'contact_phone', 'contact_cellular'], 'string', 'max' => 50],
				[['address'], 'string', 'max' => 255],
				[['transit_time'], 'integer', 'max' => 999],
				[['duns'], 'unique'],
				[['name'], 'unique'],
				[['country_code_id'], 'exist', 'skipOnError' => true, 'targetClass' => CountryCode::className(), 'targetAttribute' => ['country_code_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels(){
			return [
				'id' => Yii::t('app', 'ID'),
				'name' => Yii::t('app', 'Name'),
				'duns' => Yii::t('app', 'Duns'),
				'alias' => Yii::t('app', 'Alias'),
				'address' => Yii::t('app', 'Address'),
				'city' => Yii::t('app', 'City'),
				'postal' => Yii::t('app', 'Postal'),
				'transit_time' => Yii::t('app', 'Transit time'),
				'country_code_id' => Yii::t('app', 'Country code'),
				'contact_name' => Yii::t('app', 'Contact name'),
				'contact_position' => Yii::t('app', 'Contact position'),
				'contact_email' => Yii::t('app', 'Contact email'),
				'contact_phone' => Yii::t('app', 'Contact phone'),
				'contact_cellular' => Yii::t('app', 'Contact cellular'),
			];
		}

		public function fields(){
			return [
				'id',
				'name',
				'duns',
				'alias'
			];
		}

		/**
		 * @return ActiveQuery
		 */
		public function getContracts(){
			return $this->hasMany(Contract::className(), ['supplier_id' => 'id']);
		}

		/**
		 * @return ActiveQuery
		 */
		public function getDocuments(){
			return $this->hasMany(Document::className(), ['supplier_id' => 'id']);
		}

		/**
		 * @return ActiveQuery
		 */
		public function getInvoices(){
			return $this->hasMany(Invoice::className(), ['supplier_id' => 'id']);
		}

		/**
		 * @return ActiveQuery
		 */
		public function getLms(){
			return $this->hasMany(Lms::className(), ['supplier_id' => 'id']);
		}

		/**
		 * @return ActiveQuery
		 */
		public function getPartPackings(){
			return $this->hasMany(PartPacking::className(), ['supplier_id' => 'id']);
		}

		/**
		 * @return ActiveQuery
		 */
		public function getPaymentControls(){
			return $this->hasMany(PaymentControl::className(), ['supplier_id' => 'id']);
		}

		/**
		 * @return ActiveQuery
		 */
		public function getCountryCode(){
			return $this->hasOne(CountryCode::className(), ['id' => 'country_code_id']);
		}

	}
