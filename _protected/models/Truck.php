<?php
	namespace app\models;

	use Yii;
	use yii\db\ActiveQuery;
	use yii\db\ActiveRecord;

	/**
	 * This is the model class for table "truck".
	 * @property int    $id
	 * @property string $model
	 * @property string $number
	 * @property int    $created_by
	 * @property int    $created_at
	 * @property int    $updated_by
	 * @property int    $updated_at
	 * @property User   $createdBy
	 * @property User   $updatedBy
	 */
	class Truck extends ActiveRecord{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName(){
			return 'truck';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules(){
			return [
				[['model', 'number', 'created_by', 'created_at'], 'required'],
				[['created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
				[['model'], 'string', 'max' => 50],
				[['number'], 'string', 'max' => 20],
				[
					['model', 'number'], 'unique',
					'targetAttribute' => ['model', 'number'],
					'message' => Yii::t('app', 'This combination has already been taken.')
				],
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
				'model' => Yii::t('app', 'Model'),
				'number' => Yii::t('app', 'Number'),
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
