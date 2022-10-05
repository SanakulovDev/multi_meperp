<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "req".
 *
 * @property int $id
 * @property int $part_id
 * @property string $whbal
 * @property string $linebal
 * @property string $arrive
 * @property string $calc_at
 *
 * @property Part $part
 * @property ReqDetail[] $reqDetails
 */
class Reqt extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public $part_color;
    public $part_name;
    public $unit;
    
    public static function tableName()
    {
        return 'req_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['part_id'], 'required'],
            [['part_id','days_count','doh'], 'integer'],
            [['whbal', 'linebal', 'semistock','fgstock', 'outsourcing', 'pending', 'arrive'], 'number'],
            [['calc_at'], 'safe'],
            [['part_id'], 'exist', 'skipOnError' => true, 'targetClass' => Part::className(), 'targetAttribute' => ['part_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'part_id' => Yii::t('app', 'Part'),
            'whbal' => Yii::t('app', 'WH balance'),
            'linebal' => Yii::t('app', 'Line balance'),
            'semistock' => Yii::t('app', 'Semi balance'),
            'fgstock' => Yii::t('app', 'FG balance'),
            'outsourcing' => Yii::t('app', 'Outsourcing balance'),
            'pending' => Yii::t('app', 'Pending balance'),
            'arrive' => Yii::t('app', 'Arrived'),
            'days_count' => Yii::t('app', 'Days count'),
            'calc_at' => Yii::t('app', 'Calculated at'),
            
            'part_color' => Yii::t('app', 'Part color'),
            'part_name' => Yii::t('app', 'Part name'),
            'unit' => Yii::t('app', 'Unit'),
            'doh' => Yii::t('app', 'DOH'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPart()
    {
        return $this->hasOne(Part::className(), ['id' => 'part_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReqDetails()
    {
        return $this->hasMany(ReqDetail::className(), ['req_id' => 'id']);
    }
    
    public function getReqDetailsWide()
    {
        return $this->hasMany(ReqDetailWide::className(), ['req_id' => 'id']);
    }
    
    public function getTotalstock()
    {
      return $this->whbal + $this->linebal + $this->semistock + $this->outsourcing + $this->arrive + $this->pending;
    }
}
