<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "bom_log".
 *
 * @property int $id
 * @property string $fullname
 * @property string $subject
 * @property string $action
 * @property string $comment
 * @property int $created_at
 */
class BomLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bom_log';
    }

	public function behaviors(){
		return [
			[
				'class' => TimestampBehavior::className(),
				'createdAtAttribute' => 'created_at',
				'updatedAtAttribute' => false,
			],
		];
	}

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fullname', 'subject', 'action'], 'required'],
            [['created_at'], 'integer'],
            [['fullname'], 'string', 'max' => 100],
            [['subject'], 'string', 'max' => 50],
            [['action', 'comment'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'fullname' => Yii::t('app', 'Full name'),
            'subject' => Yii::t('app', 'Subject'),
            'action' => Yii::t('app', 'Action'),
            'comment' => Yii::t('app', 'Comment'),
            'created_at' => Yii::t('app', 'Created at'),
        ];
    }
}
