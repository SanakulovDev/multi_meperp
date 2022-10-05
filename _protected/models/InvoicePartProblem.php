<?php
	namespace app\models;

	use Yii;
	use yii\db\ActiveQuery;
	use yii\db\ActiveRecord;

	/**
		* This is the model class for table "invoice_part_problem".
		* @property int           $id
		* @property int           $inv_detail_id
		* @property string        $part_order_no
		* @property string        $contract_no
		* @property int           $created_by
		* @property int           $created_at
		* @property int           $updated_by
		* @property int           $updated_at
		* @property User          $createdBy
		* @property InvoiceDetail $invDetail
		* @property User          $updatedBy
		*/
	class InvoicePartProblem extends ActiveRecord{
		/**
			* {@inheritdoc}
			*/
		public static function tableName(){
			return 'invoice_part_problem';
		}

		/**
			* {@inheritdoc}
			*/
		public function rules(){
			return [
				[['inv_detail_id', 'created_by', 'created_at'], 'required'],
				[['inv_detail_id', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
				[['part_order_no', 'contract_no'], 'string', 'max' => 100],
				[['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
				[['inv_detail_id'], 'exist', 'skipOnError' => true, 'targetClass' => InvoiceDetail::className(), 'targetAttribute' => ['inv_detail_id' => 'id']],
				[['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
			];
		}

		/**
			* {@inheritdoc}
			*/
		public function attributeLabels(){
			return [
				'id' => Yii::t('app', 'ID'),
				'inv_detail_id' => Yii::t('app', 'Inv detail ID'),
				'part_order_no' => Yii::t('app', 'Part order no'),
				'contract_no' => Yii::t('app', 'Contract no'),
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
		public function getInvDetail(){
			return $this->hasOne(InvoiceDetail::className(), ['id' => 'inv_detail_id']);
		}

		/**
			* @return ActiveQuery
			*/
		public function getUpdatedBy(){
			return $this->hasOne(User::className(), ['id' => 'updated_by']);
		}
	}
