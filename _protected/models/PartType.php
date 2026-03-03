<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "product_type".
 *
 * @property int $id
 * @property string $typename
 *
 * @property Product[] $products
 */
class PartType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'part_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['typename'], 'required'],
            [['typename'], 'string', 'max' => 50],
            [['description'], 'string', 'max' => 255],
            [['typename'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'typename' => Yii::t('app', 'Part type'),
            'description' => Yii::t('app', 'Description'),
        ];
    }
    
    public static function getPartTypes(){
			return \yii\helpers\ArrayHelper::map(self::find()->all(), 'id', 'typename');
		}
    
    public static function findOneByName($name){
			return self::find()->where(['typename' => $name])->one();
		}
}
