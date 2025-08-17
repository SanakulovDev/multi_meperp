<?php
	namespace app\models;

	use Yii;
	use yii\behaviors\BlameableBehavior;
	use yii\behaviors\TimestampBehavior;
	use yii\db\ActiveQuery;
	use yii\db\ActiveRecord;

	/**
	 * This is the model class for table "payment_type".
	 * @property int              $id
	 * @property string           $title
	 * @property string           $description
	 * @property int              $created_by
	 * @property int              $created_at
	 * @property int              $updated_by
	 * @property int              $updated_at
	 * @property PaymentControl[] $paymentControls
	 * @property User             $createdBy
	 * @property User             $updatedBy
	 */
	class PaymentType extends ActiveRecord{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName(){
			return 'payment_type';
		}

		public function behaviors(){
			return [
				TimestampBehavior::className(),
				BlameableBehavior::className(),
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules(){
			return [
				[['title', 'description'], 'required'],
				[['created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
				[['title'], 'string', 'max' => 50],
				[['description'], 'string', 'max' => 100],
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
				'title' => Yii::t('app', 'Title'),
				'description' => Yii::t('app', 'Description'),
				'created_by' => Yii::t('app', 'Created by'),
				'created_at' => Yii::t('app', 'Created at'),
				'updated_by' => Yii::t('app', 'Updated by'),
				'updated_at' => Yii::t('app', 'Updated at'),
			];
		}

		/**
		 * @return ActiveQuery
		 */
		public function getPaymentControls(){
			return $this->hasMany(PaymentControl::className(), ['payment_type_id' => 'id']);
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

		public function getUpdatedAtFormatted(){
			return (!empty($this->updated_at)) ? date('d.m.Y H:i', $this->updated_at) : '';
		}

		public function getCreatedAtFormatted(){
			return (!empty($this->created_at)) ? date('d.m.Y H:i', $this->created_at) : '';
		}
	}
