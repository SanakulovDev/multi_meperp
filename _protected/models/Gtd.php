<?php
	namespace app\models;

	use Yii;
	use yii\db\ActiveQuery;
	use yii\db\ActiveRecord;

	/**
	 * This is the model class for table "gtd".
	 * @property int          $id
	 * @property string       $gtd_no
	 * @property string       $gtd_dt
	 * @property string       $post_no
	 * @property int          $created_by
	 * @property int          $created_at
	 * @property int          $updated_by
	 * @property int          $updated_at
	 * @property User         $createdBy
	 * @property User         $updatedBy
	 * @property GtdInvoice[] $gtdInvoices
	 * @property Invoice[]    $invoices
	 */
	class Gtd extends ActiveRecord{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName(){
			return 'gtd';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules(){
			return [
				[['gtd_no', 'gtd_dt'], 'required'],
				[['gtd_dt'], 'safe'],
				[['created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
				[['gtd_no', 'post_no'], 'string', 'max' => 100],
				[['gtd_no', 'gtd_dt'], 'unique', 'targetAttribute' => ['gtd_no', 'gtd_dt']],
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
				'gtd_no' => Yii::t('app', 'Document number'),
				'gtd_dt' => Yii::t('app', 'Date'),
				'post_no' => Yii::t('app', 'Post number'),
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

		public function getGtdInvoices(){
			return $this->hasMany(GtdInvoice::className(), ['gtd_id' => 'id']);
		}

		public function getInvoices(){
			return $this->hasMany(Invoice::className(), ['id' => 'invoice_id'])
									->viaTable('gtd_invoice', ['gtd_id' => 'id']);
		}
	}
