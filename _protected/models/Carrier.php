<?php
namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "carrier".
 *
 * @property int         $id
 * @property string      $company_name
 * @property string      $duns
 * @property string|null $address
 * @property int|null    $country_code_id
 * @property string|null $city
 * @property string|null $postal
 * @property string|null $contact_name
 * @property string|null $contact_position
 * @property string|null $contact_email
 * @property string|null $contact_phone
 * @property string|null $contact_cellular
 * @property CountryCode $countryCode
 * @property FreightInvoice[] $freightInvoices
 */
class Carrier extends ActiveRecord {

  /**
   * {@inheritdoc}
   */
  public static function tableName() {
    return 'carrier';
  }

  /**
   * {@inheritdoc}
   */
  public function rules() {
    return [
      [['company_name', 'duns'], 'required'],
      [['country_code_id'], 'integer'],
      [['company_name', 'city'], 'string', 'max' => 100],
      [['duns', 'postal'], 'string', 'max' => 30],
      [['address'], 'string', 'max' => 255],
      [['contact_name', 'contact_position', 'contact_email', 'contact_phone', 'contact_cellular'], 'string', 'max' => 50],
      [['country_code_id'], 'exist', 'skipOnError' => true, 'targetClass' => CountryCode::className(), 'targetAttribute' => ['country_code_id' => 'id']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels() {
    return [
      'id' => Yii::t('app', 'ID'),
      'company_name' => Yii::t('app', 'Company name'),
      'duns' => Yii::t('app', 'Duns'),
      'address' => Yii::t('app', 'Address'),
      'country_code_id' => Yii::t('app', 'Country code'),
      'city' => Yii::t('app', 'City'),
      'postal' => Yii::t('app', 'Postal'),
      'contact_name' => Yii::t('app', 'Contact name'),
      'contact_position' => Yii::t('app', 'Contact position'),
      'contact_email' => Yii::t('app', 'Contact email'),
      'contact_phone' => Yii::t('app', 'Contact phone'),
      'contact_cellular' => Yii::t('app', 'Contact cellular'),
    ];
  }

  /**
   * Gets query for [[CountryCode]].
   *
   * @return ActiveQuery
   */
  public function getCountryCode() {
    return $this->hasOne(CountryCode::className(), ['id' => 'country_code_id']);
  }

  public function getFreightInvoices() {
    return $this->hasMany(FreightInvoice::className(), ['carrier_id' => 'id']);
  }

}
