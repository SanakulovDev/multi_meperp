<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "country_code".
 *
 * @property int $id
 * @property string $alpha_2
 * @property string|null $alpha_3
 * @property int|null $numeric_code
 * @property string $name_en
 * @property string $name_ru
 */
class CountryCode extends \yii\db\ActiveRecord
{
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'country_code';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['alpha_2', 'name_en', 'name_ru'], 'required'],
      [['numeric_code'], 'integer'],
      [['alpha_2'], 'string', 'max' => 2],
      [['alpha_3'], 'string', 'max' => 3],
      [['name_en', 'name_ru'], 'string', 'max' => 100],
      [['alpha_2'], 'unique'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id' => Yii::t('app', 'ID'),
      'alpha_2' => Yii::t('app', 'Alpha 2'),
      'alpha_3' => Yii::t('app', 'Alpha 3'),
      'numeric_code' => Yii::t('app', 'Numeric Code'),
      'name_en' => Yii::t('app', 'Name En'),
      'name_ru' => Yii::t('app', 'Name Ru'),
    ];
  }

  public function getName()
  {
    return (Yii::$app->language == 'en' ? $this->name_en : $this->name_ru) . ' (' . $this->alpha_2 . ')';
  }
}
