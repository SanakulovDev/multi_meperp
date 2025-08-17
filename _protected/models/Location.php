<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "location".
 *
 * @property int $id
 * @property int $location_type_id
 * @property string $code
 * @property string $name
 * @property string $alias
 * @property int|null $is_main
 * @property string|null $area
 * @property int $conveyor_type_id
 * @property int $parent_id
 * @property string $address
 *
 * @property Location $parent
 * @property Location[] $locations
 */
class Location extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'location';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['location_type_id', 'code', 'name', 'alias'], 'required'],
            [['location_type_id', 'is_main', 'conveyor_type_id', 'parent_id'], 'integer'],
            [['code'], 'string', 'max' => 50],
            [['name', 'alias'], 'string', 'max' => 100],
            [['area', 'address'], 'string', 'max' => 255],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Location::className(), 'targetAttribute' => ['parent_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'location_type_id' => Yii::t('app', 'Location Type ID'),
            'code' => Yii::t('app', 'Code'),
            'name' => Yii::t('app', 'Name'),
            'alias' => Yii::t('app', 'Alias'),
            'is_main' => Yii::t('app', 'Is Main'),
            'area' => Yii::t('app', 'Area'),
            'conveyor_type_id' => Yii::t('app', 'Conveyor Type ID'),
            'parent_id' => Yii::t('app', 'Parent ID'),
            'address' => Yii::t('app', 'Address'),
        ];
    }

	public function getLocationType()
	{
		return $this->hasOne(LocationType::className(), ['id' => 'location_type_id']);
	}

    const MAIN_YES = 1;
    const MAIN_NO = 0;

    public $mainList = [
        self::MAIN_YES => 'Yes',
        self::MAIN_NO => 'No',
    ];

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Location::className(), ['id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocations()
    {
        return $this->hasMany(Location::className(), ['parent_id' => 'id']);
    }

    public function getMainText(){
        return $this->mainList[$this->is_main];
    }
}
