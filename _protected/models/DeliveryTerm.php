<?php
namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "delivery_term".
 *
 * @property int        $id
 * @property string     $name
 * @property Contract[] $contracts
 * @property FreightInvoice[] $freightInvoices
 */
class DeliveryTerm extends ActiveRecord {

  /**
   * {@inheritdoc}
   */
  public static function tableName() {
    return 'delivery_term';
  }

  /**
   * {@inheritdoc}
   */
  public function rules() {
    return [
      [['name'], 'required'],
      [['priority'], 'integer'],
      [['description'], 'string', 'max' => 255],
      [['name'], 'string', 'max' => 50],
      [['name'], 'unique'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels() {
    return [
      'id' => Yii::t('app', 'ID'),
      'name' => Yii::t('app', 'Name'),
      'priority' => Yii::t('app', 'Priority'),
      'description' => Yii::t('app', 'Description'),
    ];
  }

  /**
   * @return ActiveQuery
   */
  public function getContracts() {
    return $this->hasMany(Contract::className(), ['delivery_term_id' => 'id']);
  }

  public function getFreightInvoices() {
    return $this->hasMany(FreightInvoice::className(), ['delivery_term_id' => 'id']);
  }

  public static function getTermNames() {
    return ArrayHelper::map(self::find()->all(), 'id', 'trimname');
  }

  public function getTrimName() {
    return trim($this->name);
  }

  public static function findOneByTermName($term_name) {
    return self::find()->where(['trim(name)' => trim($term_name)])->one();
  }

}
