<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "currency_rate".
 *
 * @property int $id
 * @property string $rate_date
 * @property int $currency_id
 * @property string $uzs_value
 * @property int $created_by
 * @property int $created_at
 * @property int $updated_by
 * @property int $updated_at
 *
 * @property User $createdBy
 * @property Currency $currency
 * @property User $updatedBy
 */
class CurrencyRate extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'currency_rate';
    }

    /**
     * {@inheritdoc}
     */
    protected $type;
    public function rules()
    {
        return [
            [['rate_date', 'currency_id', 'uzs_value'], 'required'],
            [['rate_date'], 'safe'],
            [['currency_id', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
            [['uzs_value'], 'number'],
            [['rate_date', 'currency_id'], 'unique', 'targetAttribute' => ['rate_date', 'currency_id'], 'message' => Yii::t('app', 'Duplicating data')],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['currency_id'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::className(), 'targetAttribute' => ['currency_id' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'rate_date' => Yii::t('app', 'Дата курса'),
            'currency_id' => Yii::t('app', 'Currency'),
            'uzs_value' => Yii::t('app', 'В сум'),
            'created_by' => Yii::t('app', 'Created by'),
            'created_at' => Yii::t('app', 'Created at'),
            'updated_by' => Yii::t('app', 'Updated by'),
            'updated_at' => Yii::t('app', 'Updated at'),
            
            'type' => Yii::t('app', 'Type of record'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCurrency()
    {
        return $this->hasOne(Currency::className(), ['id' => 'currency_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }
    
    public function getType()
    {
        if(empty($this->created_by) and empty($this->updated_by)){
          return 'Auto';
        }else{
          return 'Manual';
        }
    }
    
    public static function currentRate($currency_id, $date = null){
      $date = (empty($date)) ? date('Y-m-d') : $date;
      $currRate = CurrencyRate::find()->where([
        'and',
        ['currency_id' => $currency_id],
        ['<=','rate_date',$date]
      ])->orderBy(['rate_date' => SORT_DESC])->one();
      return $currRate->uzs_value ?? null;
    }


    
}
