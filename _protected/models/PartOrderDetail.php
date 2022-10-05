<?php
	namespace app\models;

	use Yii;
	use yii\db\ActiveQuery;
	use yii\db\ActiveRecord;

	/**
		* This is the model class for table "part_order_detail".
		* @property int       $id
		* @property int       $part_id
		* @property int       $part_order_id
		* @property int       $qty          Qty
		* @property int       $exwrk_plan   EXWRK_PLAN
		* @property int       $exwrk_actual EXWRK_ACTUAL
	  * @property string|null $comment
	  * @property int       $updated_at
		* @property int       $created_by
		* @property int       $created_at
		* @property int       $updated_by
		* @property User      $createdBy
		* @property Part      $part
		* @property PartOrder $partOrder
		* @property User      $updatedBy
		*/
	class PartOrderDetail extends ActiveRecord{
		/**
			* {@inheritdoc}
			*/
		public static function tableName(){
			return 'part_order_detail';
		}

		/**
			* {@inheritdoc}
			*/
		public function rules(){
			return [
				[['part_id', 'part_order_id', 'qty', 'created_by', 'created_at'], 'required'],
				//				[['exwrk_plan', 'exwrk_actual'], 'date'],
				[['comment'], 'string', 'max' => 255],
				[['part_id', 'part_order_id', 'qty', 'updated_at', 'created_by', 'created_at', 'updated_by'], 'integer'],
				[['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
				[['part_id'], 'exist', 'skipOnError' => true, 'targetClass' => Part::className(), 'targetAttribute' => ['part_id' => 'id']],
				[['part_order_id'], 'exist', 'skipOnError' => true, 'targetClass' => PartOrder::className(), 'targetAttribute' => ['part_order_id' => 'id']],
				[['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
			];
		}

		/**
			* {@inheritdoc}
			*/
		public function attributeLabels(){
			return [
				'id' => Yii::t('app', 'ID'),
				'part_id' => Yii::t('app', 'Part ID'),
				'part_order_id' => Yii::t('app', 'Part Order ID'),
				'qty' => Yii::t('app', 'Qty'),
				'comment' => Yii::t('app', 'Comment'),
				'exwrk_plan' => Yii::t('app', 'exwrk_plan'),
				'exwrk_actual' => Yii::t('app', 'exwrk_actual'),
				'updated_at' => Yii::t('app', 'Updated at'),
				'created_by' => Yii::t('app', 'Created by'),
				'created_at' => Yii::t('app', 'Created at'),
				'updated_by' => Yii::t('app', 'Updated by'),
				'price' => Yii::t('app', 'Price'),
				'amount' => Yii::t('app', 'Amount'),
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
		public function getPart(){
			return $this->hasOne(Part::className(), ['id' => 'part_id']);
		}

		/**
			* @return ActiveQuery
			*/
		public function getPartOrder(){
			return $this->hasOne(PartOrder::className(), ['id' => 'part_order_id']);
		}

		/**
			* @return ActiveQuery
			*/
		public function getUpdatedBy(){
			return $this->hasOne(User::className(), ['id' => 'updated_by']);
		}

		public function getPrice(){
			// $contr_detail =  ContractDetail::find()
			//                      ->where([
			// 	                             'contract_id' => $this->partOrder->contract_id,
			// 	                             'part_id' => $this->part_id,
			// 	                             'delivery_term_id' => $this->partOrder->delivery_term_id,
			//                              ])
			//                      ->one();
			// return $contr_detail->price ?? null;
			$actualContract = $this->part->getActualContract();
			return $actualContract->price ?? 0;
			
		}

		public function getAmount(){
			return $this->qty * ($this->price ?? null);
		}

		public function getInvoiceAmount() {
			$amount = InvoiceDetail::find()
				->where(['part_order_id' => $this->part_order_id, 'part_id' => $this->part_id])
				->sum('qty * price');
			return $amount ? $amount : 0;
		}

		public function getInvoiceQty() {
			$amount = InvoiceDetail::find()
				->where(['part_order_id' => $this->part_order_id, 'part_id' => $this->part_id])
				->sum('qty');
			return $amount ? $amount : 0;
		}

	}
