<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "contact".
 *
 * @property int    $id
 * @property string $name
 * @property string $functionality
 * @property string $department
 * @property string $team
 * @property string $responsibility
 * @property string $mrp_code
 * @property string $office_phone
 * @property string $mobile_phone
 * @property string $email
 * @property string $mfu_code
 */
class Contact extends ActiveRecord{
	/**
	 * {@inheritdoc}
	 */
	public static function tableName(){
		return 'contact';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(){
		return [
			[['name'], 'required'],
			[['name', 'functionality', 'department', 'responsibility'], 'string', 'max' => 255],
			[['team', 'mrp_code'], 'string', 'max' => 100],
			[['office_phone', 'mobile_phone', 'email', 'mfu_code'], 'string', 'max' => 50],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(){
		return [
			'id'             => Yii::t('app', 'ID'),
			'name'           => Yii::t('app', 'Name'),
			'functionality'  => Yii::t('app', 'Functionality'),
			'department'     => Yii::t('app', 'Department'),
			'team'           => Yii::t('app', 'Team'),
			'responsibility' => Yii::t('app', 'Responsibility'),
			'mrp_code'       => Yii::t('app', 'Mrp Code'),
			'office_phone'   => Yii::t('app', 'Office Phone'),
			'mobile_phone'   => Yii::t('app', 'Mobile Phone'),
			'email'          => Yii::t('app', 'Email'),
			'mfu_code'       => Yii::t('app', 'Mfu Code'),
		];
	}
}
