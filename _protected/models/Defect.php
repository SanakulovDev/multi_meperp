<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "defect".
 *
 * @property int $id
 * @property string $code
 * @property string $description
 * @property integer $category
 *
 * @property ProductionOrderDefect[] $productionOrderDefects
 */
class Defect extends \yii\db\ActiveRecord
{
    const CATEGORY_REWORK = 1;
    const CATEGORY_SCRAP = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'defect';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code', 'category'], 'required'],
            [['category'], 'integer'],
            [['code'], 'string', 'max' => 50],
            [['description'], 'string', 'max' => 255],
            [['code', 'category'], 'unique', 'targetAttribute' => ['code', 'category']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'code' => Yii::t('app', 'Code'),
            'category' => Yii::t('app', 'Category'),
            'description' => Yii::t('app', 'Description'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductionOrderDefects()
    {
        return $this->hasMany(ProductionOrderDefect::className(), ['defect_id' => 'id']);
    }

    public function getCategoryText() {
      return $this->category === self::CATEGORY_REWORK ? 'Rework' : 'Scrap';
    }

    public function categoryList() {
      return [
        self::CATEGORY_REWORK => 'Rework',
        self::CATEGORY_SCRAP => 'Scrap'
      ];
    }
}
