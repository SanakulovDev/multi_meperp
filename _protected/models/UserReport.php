<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_report".
 *
 * @property int    $user_id
 * @property int    $report_id
 * @property string $created_at
 *
 * @property Report $report
 * @property User   $user
 */
class UserReport extends ActiveRecord{
	/**
	 * {@inheritdoc}
	 */
	public static function tableName(){
		return 'user_report';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(){
		return [
			[['user_id', 'report_id'], 'required'],
			[['user_id', 'report_id'], 'integer'],
			[['created_at'], 'safe'],
			[['user_id', 'report_id'], 'unique', 'targetAttribute' => ['user_id', 'report_id']],
			[['report_id'], 'exist', 'skipOnError' => true, 'targetClass' => Report::className(), 'targetAttribute' => ['report_id' => 'id']],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(){
		return [
			'user_id'    => Yii::t('app', 'User ID'),
			'report_id'  => Yii::t('app', 'Report ID'),
			'created_at' => Yii::t('app', 'Created at'),
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getReport(){
		return $this->hasOne(Report::className(), ['id' => 'report_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUser(){
		return $this->hasOne(User::className(), ['id' => 'user_id']);
	}
}
