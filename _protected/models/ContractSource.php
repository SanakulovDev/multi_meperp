<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "contract_source".
 *
 * @property int $id
 * @property string $name
 *
 * @property Contract[] $contracts
 */
class ContractSource extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'contract_source';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['description'], 'string', 'max' => 255],
            [['name'], 'string', 'max' => 100],
            [['name'], 'unique'],
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
            'description' => Yii::t('app', 'Description'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContracts()
    {
        return $this->hasMany(Contract::className(), ['contract_source_id' => 'id']);
    }
    
    public static function getContractSources(){
			return \yii\helpers\ArrayHelper::map(self::find()->all(), 'id', 'name');
		}
    
    public static function findOneByName($name){
			return self::find()->where(['name' => $name])->one();
		}
}
