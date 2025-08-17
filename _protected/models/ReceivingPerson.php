<?php

namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "receiving_person".
 *
 * @property int $id
 * @property string $fullname
 * @property string $doc_number
 * @property string $doc_date
 * @property int $status
 * @property int $created_by
 * @property int $created_at
 * @property int $updated_by
 * @property int $updated_at
 */
class ReceivingPerson extends \yii\db\ActiveRecord
{

		const STATUS_ACTIVE = 1;
		const STATUS_INACTIVE = 0;

		public $statusList = [
			self::STATUS_ACTIVE => 'Актив',
			self::STATUS_INACTIVE => 'Не актив',
		];

		/**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'receiving_person';
    }

		public function behaviors(){
			return [
				TimestampBehavior::className(),
				[
					'class' => BlameableBehavior::class,
					'defaultValue' => 1
				]
			];
		}

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fullname'], 'required'],
            [['doc_date', 'doc_number'], 'safe'],
            [['status', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
            [['fullname', 'doc_number'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'fullname' => Yii::t('app', 'Fullname'),
            'doc_number' => Yii::t('app', 'Attorney letter number'),
            'doc_date' => Yii::t('app', 'Date'),
            'status' => Yii::t('app', 'Status'),
            'created_by' => Yii::t('app', 'Created by'),
            'created_at' => Yii::t('app', 'Created at'),
            'updated_by' => Yii::t('app', 'Updated by'),
            'updated_at' => Yii::t('app', 'Updated at'),
        ];
    }

		public function getCreatedBy(){
			return $this->hasOne(User::className(), ['id' => 'created_by']);
		}

		public function getUpdatedBy(){
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
