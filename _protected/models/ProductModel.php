<?php
namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "product_model".
 *
 * @property int                    $id
 * @property string                 $modelname
 * @property string|null            $description
 * @property int|null               $is_vehicle
 * @property Product[]              $products
 * @property VehicleCoverageInput[] $vehicleCoverageInputs
 */
class ProductModel extends ActiveRecord {

  /**
   * {@inheritdoc}
   */
  public static function tableName() {
    return 'product_model';
  }

  const IS_VEHICLE = 1;
  const IS_NOT_VEHICLE = 0;
  
  /**
   * {@inheritdoc}
   */
  public function rules() {
    return [
      [['modelname'], 'required'],
      [['is_vehicle'], 'integer'],
      [['modelname'], 'string', 'max' => 50],
      [['description'], 'string', 'max' => 255],
      [['modelname'], 'unique'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels() {
    return [
      'id' => Yii::t('app', 'ID'),
      'modelname' => Yii::t('app', 'Product model'),
      'description' => Yii::t('app', 'Description'),
      'is_vehicle' => Yii::t('app', 'Is vehicle'),
    ];
  }

  /**
   * @return ActiveQuery
   */
  public function getProducts() {
    return $this->hasMany(Product::className(), ['product_model_id' => 'id']);
  }

  /**
   * @return ActiveQuery
   */
  public function getVehicleCoverageInputs() {
    return $this->hasMany(VehicleCoverageInput::className(), ['model_id' => 'id']);
  }

  public function getModelinfo() {
    return $this->modelname.' - '.$this->description;
  }

  public static function getModels() {
    return ArrayHelper::map(self::find()->all(), 'id', 'modelname');
  }

  public static function getModelVehicles() {
    return ArrayHelper::map(self::find()->where(['is_vehicle'=>1])->all(), 'id', 'description');
  }

  public static function findOneByName($name) {
    return self::find()->where(['modelname' => $name])->one();
  }

}
