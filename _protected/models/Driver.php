<?php
	namespace app\models;

	use Yii;
	use yii\db\ActiveQuery;
	use yii\db\ActiveRecord;

	/**
	 * This is the model class for table "driver".
	 * @property int    $id
	 * @property string $first_name
	 * @property string $last_name
	 * @property string $middle_name
	 * @property string $emp_no
	 * @property int    $created_by
	 * @property int    $created_at
	 * @property int    $updated_by
	 * @property int    $updated_at
	 * @property User   $createdBy
	 * @property User   $updatedBy
	 */
	class Driver extends ActiveRecord{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName(){
			return 'driver';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules(){
			return [
				[['first_name', 'last_name', 'middle_name', 'created_by', 'created_at'], 'required'],
				[['created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
				[['first_name', 'last_name', 'middle_name'], 'string', 'max' => 50],
				[['emp_no'], 'default', 'value' => null],
				[['emp_no'], 'string', 'max' => 10],
				[['emp_no'], 'unique'],
				[['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
				[['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels(){
			return [
				'id' => Yii::t('app', 'ID'),
				'first_name' => Yii::t('app', 'First name'),
				'last_name' => Yii::t('app', 'Last name'),
				'middle_name' => Yii::t('app', 'Middle name'),
				'emp_no' => Yii::t('app', 'Emp No'),
				'created_by' => Yii::t('app', 'Created by'),
				'created_at' => Yii::t('app', 'Created at'),
				'updated_by' => Yii::t('app', 'Updated by'),
				'updated_at' => Yii::t('app', 'Updated at'),
			];
		}

		/**
		 * @return ActiveQuery
		 */
		public function getCreatedBy(){
			return $this->hasOne(User::className(), ['id' => 'created_by']);
		}

		/**
		 * @return ActiveQuery
		 */
		public function getUpdatedBy(){
			return $this->hasOne(User::className(), ['id' => 'updated_by']);
		}
	}
