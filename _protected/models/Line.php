<?php

namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "line".
 *
 * @property int $id
 * @property string $line_name
 * @property string $description
 * @property int $parent_id
 * @property int $factory_id
 * @property int $status
 * @property int $created_at
 * @property int $created_by
 * @property int $updated_at
 * @property int $updated_by
 *
 * @property User $createdBy
 * @property Factory $factory
 * @property Line $parent
 * @property Line[] $lines
 * @property User $updatedBy
 * @property Uloc[] $ulocs
 */
class Line extends \yii\db\ActiveRecord
{
		const STATUS_ACTIVE = 1;
		const STATUS_INACTIVE = 0;

		/**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'line';
    }

		public function behaviors(){
			return [
				TimestampBehavior::className(),
				BlameableBehavior::className(),
			];
		}

		public $statusList = [
			self::STATUS_ACTIVE => 'Актив',
			self::STATUS_INACTIVE => 'Не актив',
		];

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['line_name', 'factory_id'], 'required'],
            [['parent_id', 'factory_id', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['line_name'], 'string', 'max' => 100],
            [['description'], 'string', 'max' => 255],
            [['line_name'], 'unique'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['factory_id'], 'exist', 'skipOnError' => true, 'targetClass' => Factory::className(), 'targetAttribute' => ['factory_id' => 'id']],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Line::className(), 'targetAttribute' => ['parent_id' => 'id']],
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
            'line_name' => Yii::t('app', 'Line'),
            'description' => Yii::t('app', 'Description'),
            'parent_id' => Yii::t('app', 'Parent'),
            'factory_id' => Yii::t('app', 'Factory'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created at'),
            'created_by' => Yii::t('app', 'Created by'),
            'updated_at' => Yii::t('app', 'Updated at'),
            'updated_by' => Yii::t('app', 'Updated by'),
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
    public function getFactory()
    {
        return $this->hasOne(Factory::className(), ['id' => 'factory_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Line::className(), ['id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLines()
    {
        return $this->hasMany(Line::className(), ['parent_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUlocs()
    {
        return $this->hasMany(Uloc::className(), ['line_id' => 'id']);
    }

		public function getUpdatedAtFormatted(){
			return (!empty($this->updated_at)) ? date('d.m.Y H:i', $this->updated_at) : '';
		}

		public function getCreatedAtFormatted(){
			return (!empty($this->created_at)) ? date('d.m.Y H:i', $this->created_at) : '';
		}

		public function getStatusText(){
			return $this->statusList[$this->status];
		}
}
