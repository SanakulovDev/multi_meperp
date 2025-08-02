<?php
namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "vehicle_coverage_input".
 *
 * @property int          $id
 * @property int          $model_id
 * @property float|null   $quantity
 * @property string|null  $for_date
 * @property int          $description 1-Current stock; 2-Paid not shippen order volume; 3-Intransit ETA;
 * @property int          $created_at
 * @property int          $created_by
 * @property User         $createdBy
 * @property ProductModel $model
 */
class VehicleCoverageInput extends ActiveRecord {

  /** 1-Current stock; 2-Intransit ETA; 3- Paid not shipped order volume; */
  public const CURRENT_STOCK = 1;
  public const PAID_NOT_SHIPPED_ORDER = 2;
  public const INTRANSIT_ETA = 3;
  public const UAM_STOCK = 4;

  public function getDescriptionName() {
    return [
      self::CURRENT_STOCK => Yii::t('app', 'CURRENT_STOCK'),
      self::INTRANSIT_ETA => Yii::t('app', 'INTRANSIT_ETA'),
      self::PAID_NOT_SHIPPED_ORDER => Yii::t('app', 'PAID_NOT_SHIPPED_ORDER'),
      self::UAM_STOCK => Yii::t('app', 'UZAUTOMOTORS_STOCK'),
    ];
  }
  public static function getDescriptionText(int $descNumber=0) {
    switch ($descNumber) {
    case 1: $txt = Yii::t('app', 'CURRENT_STOCK');
        break;
    case 2: $txt = Yii::t('app', 'PAID_NOT_SHIPPED_ORDER');
        break;
    case 3: $txt = Yii::t('app', 'INTRANSIT_ETA');
        break;
    case 4: $txt = Yii::t('app', 'UZAUTOMOTORS_STOCK');
        break;
    }
    return $txt;
  }

  /**
   * {@inheritdoc}
   */
  public static function tableName() {
    return 'vehicle_coverage_input';
  }

  /**
   * {@inheritdoc}
   */
  public function rules() {
    return [
      [['model_id', 'description', 'created_at', 'created_by'], 'required'],
      [['model_id', 'description', 'created_at', 'created_by'], 'integer'],
      [['quantity'], 'number'],
      [['for_date'], 'safe'],
      [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
      [['model_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductModel::className(), 'targetAttribute' => ['model_id' => 'id']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels() {
    return [
      'id' => Yii::t('app', 'ID'),
      'model_id' => Yii::t('app', 'Model'),
      'quantity' => Yii::t('app', 'Quantity'),
      'for_date' => Yii::t('app', 'Date'),
      'description' => Yii::t('app', 'Description'),
      'created_at' => Yii::t('app', 'Created at'),
      'created_by' => Yii::t('app', 'Created by'),
    ];
  }

  /**
   * @return ActiveQuery
   */
  public function getCreatedBy() {
    return $this->hasOne(User::className(), ['id' => 'created_by']);
  }

  /**
   * @return ActiveQuery
   */
  public function getModel() {
    return $this->hasOne(ProductModel::className(), ['id' => 'model_id']);
  }

  public static function getLastCoverageDate(){
    
    $lastCovDate = self::find()
			->select('max(coverage_date) as coverage_date')
			->where(['description' => VehicleCoverageInput::CURRENT_STOCK])
      ->one()->coverage_date;

    return ($lastCovDate) ? $lastCovDate : date('Y-m-d');

  }

  public static function getExpiredData(){
    
    $vcis = self::find()
			->where([
        'and',
        ['description' => VehicleCoverageInput::INTRANSIT_ETA],
        ['<', 'for_date', date('Y-m-d')]
        ])
      ->all();

    return ($vcis) ? $vcis : [];

  }

}
