<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "product_group".
 *
 * @property int $id
 * @property string $title
 *
 * @property Part[] $parts
 */
class ProductGroup extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_group';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['title'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Title'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParts()
    {
        return $this->hasMany(Part::className(), ['product_group_id' => 'id']);
    }
    
    public static function getGroupes(){
			return \yii\helpers\ArrayHelper::map(self::find()->all(), 'id', 'title');
		}
    
    public static function findOneByName($name){
			return self::find()->where(['title' => $name])->one();
		}
}
