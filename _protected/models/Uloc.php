<?php

namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "uloc".
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property int $line_id
 * @property int $min_stock
 * @property int $max_stock
 * @property int $actual_stock
 * @property int $status
 * @property int $created_at
 * @property int $created_by
 * @property int $updated_at
 * @property int $updated_by
 *
 * @property User $createdBy
 * @property Line $line
 * @property User $updatedBy
 */
class Uloc extends \yii\db\ActiveRecord
{
		const STATUS_ACTIVE = 1;
		const STATUS_INACTIVE = 0;

		/**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'uloc';
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
            [['title'], 'required'],
            [['line_id', 'min_stock', 'max_stock', 'actual_stock', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['title'], 'string', 'max' => 50],
            [['description'], 'string', 'max' => 255],
            [['title'], 'unique'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['line_id'], 'exist', 'skipOnError' => true, 'targetClass' => Line::className(), 'targetAttribute' => ['line_id' => 'id']],
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
            'title' => Yii::t('app', 'Title'),
            'description' => Yii::t('app', 'Description'),
            'line_id' => Yii::t('app', 'Line'),
            'min_stock' => Yii::t('app', 'Min'),
            'max_stock' => Yii::t('app', 'Max'),
            'actual_stock' => Yii::t('app', 'Actual stock'),
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
    public function getLine()
    {
        return $this->hasOne(Line::className(), ['id' => 'line_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
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
