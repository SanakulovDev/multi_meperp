<?php

namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "customer".
 *
 * @property int $id
 * @property string $name
 * @property string $duns
 * @property string $vat
 * @property string $tin
 * @property string $alias
 * @property string $address
 * @property string $city
 * @property string $postal
 * @property string $contact_name
 * @property string $contact_position
 * @property string $contact_email
 * @property string $contact_phone
 * @property string $contact_cellular
 * @property int $customer_type_id
 * @property int $country_code_id
 * @property int $created_by
 * @property int $created_at
 * @property int $updated_by
 * @property int $updated_at
 */
class Customer extends \yii\db\ActiveRecord
{
		const STATUS_ACTIVE = 1;
		const STATUS_INACTIVE = 0;

		public $statusList = [
			self::STATUS_ACTIVE => 'Актив',
			self::STATUS_INACTIVE => 'Не актив',
		];
		/**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'customer';
    }

		public function behaviors(){
			return [
				TimestampBehavior::className(),
				[
					'class' => BlameableBehavior::class,
					'defaultValue' => 1
				]
			];
		}

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'customer_type_id', 'duns','tin'], 'required'],
            [['customer_type_id', 'status', 'country_code_id', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
            [['name', 'address'], 'string', 'max' => 255],
            [['duns', 'postal', 'vat','tin'], 'string', 'max' => 30],
            [['alias', 'contact_name', 'contact_position', 'contact_email', 'contact_phone', 'contact_cellular'], 'string', 'max' => 50],
            [['city'], 'string', 'max' => 100],
            [['duns','tin'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'duns' => Yii::t('app', 'Duns'),
            'tin' => Yii::t('app', 'Taxpayer Identification Number'),
            'vat' => Yii::t('app', 'Reg.code(VAT)'),
            'alias' => Yii::t('app', 'Alias'),
            'address' => Yii::t('app', 'Address'),
            'city' => Yii::t('app', 'City'),
            'postal' => Yii::t('app', 'Postal'),
//            'country' => Yii::t('app', 'Country'),
//            'country_code' => Yii::t('app', 'Country code'),
            'country_code_id' => Yii::t('app', 'Country code'),
            'contact_name' => Yii::t('app', 'Contact name'),
            'contact_position' => Yii::t('app', 'Contact position'),
            'contact_email' => Yii::t('app', 'Contact email'),
            'contact_phone' => Yii::t('app', 'Contact phone'),
            'contact_cellular' => Yii::t('app', 'Contact cellular'),
            'customer_type_id' => Yii::t('app', 'Customer type'),
						'status' => Yii::t('app', 'Status'),
						'created_by' => Yii::t('app', 'Created by'),
            'created_at' => Yii::t('app', 'Created at'),
            'updated_by' => Yii::t('app', 'Updated by'),
            'updated_at' => Yii::t('app', 'Updated at'),
        ];
    }

    public function fields(){
			return [
				'id',
				'name',
				'duns',
				'tin',
				'vat',
				'alias',
				'address',
				'postal',
			];
		}

		public function getCustomerType(){
			return $this->hasOne(CustomerType::className(), ['id' => 'customer_type_id']);
		}

    public function getCountryCode(){
      return $this->hasOne(CountryCode::className(), ['id' => 'country_code_id']);
    }

		public function getCreatedBy(){
			return $this->hasOne(User::className(), ['id' => 'created_by']);
		}

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
}
