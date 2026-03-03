<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "product_line".
 *
 * @property int $id
 * @property string $linename
 * @property string|null $description
 * @property int|null $is_zone
 *
 * @property Machine[] $machines
 * @property Product[] $products
 */
class ProductLine extends ActiveRecord{

  const STATUS_ACTIVE = 1;
  const STATUS_INACTIVE = 0;

  public function statusList(){
    return [
      self::STATUS_ACTIVE => Yii::t('app','Yes'),
      self::STATUS_INACTIVE => Yii::t('app','No'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function tableName(){
    return 'product_line';
  }

  /**
   * {@inheritdoc}
   */
  public function rules(){
    return [
      [['linename'], 'required'],
      [['description'], 'string', 'max' => 255],
      [['is_zone'], 'integer', 'max' =>1],
      [['linename'], 'string', 'max' => 50],
      [['linename'], 'unique'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels(){
    return [
      'id' => Yii::t('app', 'ID'),
      'linename' => Yii::t('app', 'The production line'),
      'description' => Yii::t('app', 'Description'),
      'is_zone' => Yii::t('app', 'Is zone'),
    ];
  }

  /**
   * @return ActiveQuery
   */
  public function getProducts(){
    return $this->hasMany(Product::className(), ['product_line_id' => 'id']);
  }

  public static function getLines(){
    return ArrayHelper::map(self::find()->all(), 'id', 'linename');
  }

  public static function findOneByName($name){
    return self::find()->where(['linename' => $name])->one();
  }
}
