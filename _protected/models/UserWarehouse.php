<?php
namespace app\models;

use Yii;

/**
 * This is the model class for table "user_warehouse".
 *
 * @property int       $user_id
 * @property int       $warehouse_id
 *
 * @property User      $user
 * @property Warehouse $warehouse
 */
class UserWarehouse extends \yii\db\ActiveRecord{
	/**
	 * {@inheritdoc}
	 */
	public static function tableName(){
		return 'user_warehouse';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(){
		return [
			[['user_id', 'warehouse_id'], 'required'],
			[['user_id', 'warehouse_id'], 'integer'],
			[['user_id', 'warehouse_id'], 'unique', 'targetAttribute' => ['user_id', 'warehouse_id']],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
			[['warehouse_id'], 'exist', 'skipOnError' => true, 'targetClass' => Warehouse::className(), 'targetAttribute' => ['warehouse_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(){
		return [
			'user_id'      => Yii::t('app', 'User ID'),
			'warehouse_id' => Yii::t('app', 'Warehouse ID'),
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUser(){
		return $this->hasOne(User::className(), ['id' => 'user_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getWarehouse(){
		return $this->hasOne(Warehouse::className(), ['id' => 'warehouse_id']);
	}
}
