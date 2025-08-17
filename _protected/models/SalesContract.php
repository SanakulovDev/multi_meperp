<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "sales_contract".
 *
 * @property int              $id
 * @property int              $customer_id
 * @property string           $contract_no
 * @property string           $contract_date
 * @property string           $expiry_date
 * @property int              $seller_id
 * @property int              $payment_term_id
 * @property string           $contract_amount
 * @property int              $contract_subject_id
 * @property int              $contract_source_id
 * @property int              $status
 * @property int              $created_by
 * @property int              $created_at
 * @property int              $updated_by
 * @property int              $updated_at
 *
 * @property User             $seller
 * @property ContractSource   $contractSource
 * @property ContractSubject  $contractSubject
 * @property User             $createdBy
 * @property PaymentTerm      $paymentTerm
 * @property Customer         $customer
 * @property User             $updatedBy
 * @property ContractDetail[] $contractDetails
 */
class SalesContract extends ActiveRecord{
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
		return 'sales_contract';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(){
		return [
			[['customer_id', 'contract_no', 'contract_date', 'seller_id', 'payment_term_id','currency_id'], 'required'],
			[['customer_id', 'seller_id', 'payment_term_id',  'contract_subject_id',  'status', 'created_by', 'created_at', 'updated_by', 'updated_at','currency_id'], 'integer'],
			[['contract_date', 'expiry_date'], 'safe'],
			[['contract_amount'], 'number'],
			[['contract_no'], 'unique'],
			[['contract_no'], 'string', 'max' => 255],
			[['seller_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['seller_id' => 'id']],
			[['contract_subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => ContractSubject::className(), 'targetAttribute' => ['contract_subject_id' => 'id']],
			[['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
			[['payment_term_id'], 'exist', 'skipOnError' => true, 'targetClass' => PaymentTerm::className(), 'targetAttribute' => ['payment_term_id' => 'id']],
			[['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::className(), 'targetAttribute' => ['customer_id' => 'id']],
      [['currency_id'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::className(), 'targetAttribute' => ['currency_id' => 'id']],
			[['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(){
		return [
			'id'                  => Yii::t('app', 'ID'),
			'customer_id'         => Yii::t('app', 'Customer'),
			'contract_no'         => Yii::t('app', 'Contract no'),
			'contract_date'       => Yii::t('app', 'Contract date'),
			'expiry_date'         => Yii::t('app', 'Expiry date'),
			'seller_id'            => Yii::t('app', 'Seller'),
			'payment_term_id'     => Yii::t('app', 'Payment term'),
			'contract_amount'     => Yii::t('app', 'Contract amount'),
			'contract_subject_id' => Yii::t('app', 'Contract subject'),
			'currency_id'    => Yii::t('app', 'Currency'),
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
	public function getSeller(){
		return $this->hasOne(User::className(), ['id' => 'seller_id']);
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
	public function getPaymentTerm(){
		return $this->hasOne(PaymentTerm::className(), ['id' => 'payment_term_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getCustomer(){
		return $this->hasOne(Customer::className(), ['id' => 'customer_id']);
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
	public function getSalesContractDetails(){
		return $this->hasMany(SalesContractDetail::className(), ['sales_contract_id' => 'id']);
	}

	public function getUpdatedAtFormatted(){
		return ( !empty($this->updated_at) ) ? date('d.m.Y H:i', $this->updated_at) : '';
	}

	public function getCreatedAtFormatted(){
		return ( !empty($this->created_at) ) ? date('d.m.Y H:i', $this->created_at) : '';
	}

	public function getStatusText(){
		//return ($this->status == 1) ? Yii::t('app','Active') : Yii::t('app','Inactive');
		return Yii::t('app','Active');
	}

	public function getContractDateFormatted() {
        return (!empty($this->contract_date))?date('d.m.Y',strtotime($this->contract_date)):'';
	}

	public function getExpiryDateFormatted() {
        return (!empty($this->expiry_date))?date('d.m.Y',strtotime($this->expiry_date)):'';
	}

	public function getContractInfo(){
		return $this->contract_no . ' (' . $this->customer->name . ')';
	}

  public function getCurrency(){
      return $this->hasOne(Currency::className(), ['id' => 'currency_id']);
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
