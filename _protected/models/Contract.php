<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "contract".
 *
 * @property int              $id
 * @property int              $supplier_id
 * @property string           $contract_no
 * @property string           $contract_date
 * @property string           $expiry_date
 * @property int              $buyer_id
 * @property int              $payment_term_id
 * @property string           $contract_amount
 * @property int              $contract_subject_id
 * @property int              $currency_id
 * @property int              $contract_source_id
 * @property int              $status
 * @property int              $created_by
 * @property int              $created_at
 * @property int              $updated_by
 * @property int              $updated_at
 *
 * @property User             $buyer
 * @property ContractSource   $contractSource
 * @property ContractSubject  $contractSubject
 * @property User             $createdBy
 * @property Currency         $currency
 * @property PaymentTerm      $paymentTerm
 * @property Supplier         $supplier
 * @property User             $updatedBy
 * @property ContractDetail[] $contractDetails
 */
class Contract extends ActiveRecord{
	// the list of status values that can be stored in user table
	const STATUS_ACTIVE = 1;
	const STATUS_INACTIVE = 0;

	/**
	 * List of names for each status.
	 * @var array
	 */
	public $statusList = [
		self::STATUS_ACTIVE => 'Актив',
		self::STATUS_INACTIVE => 'Не актив',
	];

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(){
		return 'contract';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(){
		return [
			[['supplier_id', 'contract_no', 'contract_date', 'expiry_date', 'buyer_id', 'payment_term_id',  'contract_subject_id', 'currency_id', 'contract_source_id'], 'required'],
			[['supplier_id', 'buyer_id', 'payment_term_id',  'contract_subject_id', 'currency_id', 'contract_source_id', 'status', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
			[['contract_date', 'expiry_date'], 'safe'],
			[['contract_amount'], 'number'],
			[['contract_no'], 'string', 'max' => 255],
			[['buyer_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['buyer_id' => 'id']],
			[['contract_source_id'], 'exist', 'skipOnError' => true, 'targetClass' => ContractSource::className(), 'targetAttribute' => ['contract_source_id' => 'id']],
			[['contract_subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => ContractSubject::className(), 'targetAttribute' => ['contract_subject_id' => 'id']],
			[['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
			[['currency_id'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::className(), 'targetAttribute' => ['currency_id' => 'id']],
			[['payment_term_id'], 'exist', 'skipOnError' => true, 'targetClass' => PaymentTerm::className(), 'targetAttribute' => ['payment_term_id' => 'id']],
			[['supplier_id'], 'exist', 'skipOnError' => true, 'targetClass' => Supplier::className(), 'targetAttribute' => ['supplier_id' => 'id']],
			[['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(){
		return [
			'id'                  => Yii::t('app', 'ID'),
			'supplier_id'         => Yii::t('app', 'Supplier'),
			'contract_no'         => Yii::t('app', 'Contract no'),
			'contract_date'       => Yii::t('app', 'Contract date'),
			'expiry_date'         => Yii::t('app', 'Expiry date'),
			'buyer_id'            => Yii::t('app', 'Buyer'),
			'payment_term_id'     => Yii::t('app', 'Payment term'),
			'contract_amount'     => Yii::t('app', 'Contract amount'),
			'contract_subject_id' => Yii::t('app', 'Contract subject'),
			'currency_id'         => Yii::t('app', 'Currency'),
			'contract_source_id'  => Yii::t('app', 'Contract source'),
			'status'              => Yii::t('app', 'Status'),
			'created_by'          => Yii::t('app', 'Created by'),
			'created_at'          => Yii::t('app', 'Created at'),
			'updated_by'          => Yii::t('app', 'Updated by'),
			'updated_at'          => Yii::t('app', 'Updated at'),
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getBuyer(){
		return $this->hasOne(User::className(), ['id' => 'buyer_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getContractSource(){
		return $this->hasOne(ContractSource::className(), ['id' => 'contract_source_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getContractSubject(){
		return $this->hasOne(ContractSubject::className(), ['id' => 'contract_subject_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getCreatedBy(){
		return $this->hasOne(User::className(), ['id' => 'created_by']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getCurrency(){
		return $this->hasOne(Currency::className(), ['id' => 'currency_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getPaymentTerm(){
		return $this->hasOne(PaymentTerm::className(), ['id' => 'payment_term_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getSupplier(){
		return $this->hasOne(Supplier::className(), ['id' => 'supplier_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUpdatedBy(){
		return $this->hasOne(User::className(), ['id' => 'updated_by']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getContractDetails(){
		return $this->hasMany(ContractDetail::className(), ['contract_id' => 'id']);
	}

	public function getUpdatedAtFormatted(){
		return ( !empty($this->updated_at) ) ? date('d.m.Y H:i', $this->updated_at) : '';
	}

	public function getCreatedAtFormatted(){
		return ( !empty($this->created_at) ) ? date('d.m.Y H:i', $this->created_at) : '';
	}

	public function getStatusText(){
		return ($this->status == 1) ? Yii::t('app','Active') : Yii::t('app','Inactive');
	}

	public function getContractDateFormatted() {
        return (!empty($this->contract_date))?date('d.m.Y',strtotime($this->contract_date)):'';
	}

	public function getExpiryDateFormatted() {
        return (!empty($this->expiry_date))?date('d.m.Y',strtotime($this->expiry_date)):'';
	}

	public function getContractInfo(){
		return $this->contract_no . ' (' . $this->supplier->name . ')';
	}


	public function getIsActive(){
		return ($this->status == Contract::STATUS_ACTIVE and $this->contract_date <= date('Y-m-d') and $this->expiry_date >= date('Y-m-d'));
	}

	public function beforeSave($insert){
		if(parent::beforeSave($insert)){
			if($this->isNewRecord){
				$this->created_by = Yii::$app->user->identity->id;
				$this->created_at = time();
			}else{
				$this->updated_by = Yii::$app->user->identity->id;
				$this->updated_at = time();
			}

			return true;
		}else{
			return false;
		}
	}
}
