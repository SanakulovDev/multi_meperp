<?php
	namespace app\models;

	use Yii;
	use yii\db\ActiveQuery;
	use yii\db\ActiveRecord;

	/**
	 * This is the model class for table "gtd_invoice".
	 * @property int     $id
	 * @property int     $gtd_id
	 * @property int     $invoice_id
	 * @property string  $amount
	 * @property int     $created_by
	 * @property int     $created_at
	 * @property int     $updated_by
	 * @property int     $updated_at
	 * @property User    $createdBy
	 * @property Gtd     $gtd
	 * @property Invoice $invoice
	 * @property User    $updatedBy
	 */
	class GtdInvoice extends ActiveRecord{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName(){
			return 'gtd_invoice';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules(){
			return [
				[['gtd_id', 'invoice_id', 'amount', 'created_by', 'created_at'], 'required'],
				[['gtd_id', 'invoice_id', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
				[['amount'], 'number'],
				[['gtd_id', 'invoice_id'], 'unique', 'targetAttribute' => ['gtd_id', 'invoice_id']],
				[['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
				[['gtd_id'], 'exist', 'skipOnError' => true, 'targetClass' => Gtd::className(), 'targetAttribute' => ['gtd_id' => 'id']],
				[['invoice_id'], 'exist', 'skipOnError' => true, 'targetClass' => Invoice::className(), 'targetAttribute' => ['invoice_id' => 'id']],
				[['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels(){
			return [
				'id' => Yii::t('app', 'ID'),
				'gtd_id' => Yii::t('app', 'GTD no'),
				'invoice_id' => Yii::t('app', 'Invoice'),
				'amount' => Yii::t('app', 'Amount'),
				'created_by' => Yii::t('app', 'Created by'),
				'created_at' => Yii::t('app', 'Created at'),
				'updated_by' => Yii::t('app', 'Updated by'),
				'updated_at' => Yii::t('app', 'Updated at'),
			];
		}

		public function getGtd(){
			return $this->hasOne(Gtd::className(), ['id' => 'gtd_id']);
		}

		public function getInvoice(){
			return $this->hasOne(Invoice::className(), ['id' => 'invoice_id']);
		}

		public function getCreatedBy(){
			return $this->hasOne(User::className(), ['id' => 'created_by']);
		}
		public function getCreatedAtFormatted(){
			return (!empty($this->created_at)) ? date('d.m.Y H:i', $this->created_at) : '';
		}

		public function getUpdatedBy(){
			return $this->hasOne(User::className(), ['id' => 'updated_by']);
		}
		public function getUpdatedAtFormatted(){
			return (!empty($this->updated_at)) ? date('d.m.Y H:i', $this->updated_at) : '';
		}
	}
