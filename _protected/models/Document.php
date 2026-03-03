<?php
	namespace app\models;

	use Yii;
	use yii\behaviors\BlameableBehavior;
	use yii\behaviors\TimestampBehavior;
	use yii\db\ActiveQuery;
	use yii\db\ActiveRecord;
	use Da\QrCode\Contracts\ErrorCorrectionLevelInterface;
	use Da\QrCode\QrCode;

	/**
		* This is the model class for table "document".
		* @property int              $id
		* @property string           $docnum
		* @property string           $docdate
		* @property int              $document_type_id
		* @property int              $from_warehouse_id
		* @property int              $to_warehouse_id
		* @property string           $series
		* @property int              $status
		* @property int              $created_by
		* @property int              $created_at
		* @property int              $updated_by
		* @property int              $updated_at
		* @property User             $createdBy
		* @property DocumentType     $documentType
		* @property Warehouse        $fromWarehouse
		* @property Warehouse        $toWarehouse
		* @property User             $updatedBy
		* @property DocumentDetail[] $documentDetails
		*/
	class Document extends ActiveRecord{

		const ADJ_RECEIPT = 1;
		const ADJ_ISSUE   = 0;

		public static $adjList = [
			self::ADJ_RECEIPT => 'Receipt',
			self::ADJ_ISSUE => 'Issue',
		];
		public $action, $adj, $adj_wh_id, $filter_from, $filter_to, $type_id;

		public function fields()
		{
			return [
				'id',
				'docnum',
				'docdate',
				'document_type'=> function(){ 
					return $this->isRelationPopulated('documentType') ? $this->documentType : [ 'id' => $this->document_type_id ]; 
				},
				'from_warehouse'=> function(){ 
					return $this->isRelationPopulated('fromWarehouse') ? $this->fromWarehouse : [ 'id' => $this->from_warehouse_id ]; 
				},
				'to_warehouse'=> function(){ 
					return $this->isRelationPopulated('toWarehouse') ? $this->toWarehouse : [ 'id' => $this->to_warehouse_id ]; 
				},
				'supplier'=> function(){ 
					return $this->isRelationPopulated('supplier') ? $this->supplier : [ 'id' => $this->supplier_id ]; 
				},
				'series',
				'status',
				'comment',
				'serial_number',
				'items'    => function () {
					return $this->isRelationPopulated('documentDetails') ? $this->documentDetails : [];
				},
				'createdBy'    => function () {
					return $this->isRelationPopulated('createdBy') ? $this->createdBy : [ 'id' => $this->created_by ];
				},
				'created_at'=>function(){
					return $this->getCreatedAtFormatted();
				},
				'updatedBy'    => function () {
					return $this->isRelationPopulated('updatedBy') ? $this->updatedBy : [ 'id' => $this->updated_by ];
				},
				'updated_at'=>function(){
					return $this->getUpdatedAtFormatted();
				},
			];
		}

		public static function tableName(){
			return 'document';
		}

		public function behaviors(){
			return [
				TimestampBehavior::className(),
				[
					'class' => BlameableBehavior::class,
					'defaultValue' => 1
				]
			];
		}

		/**
			* @inheritdoc
			*/
		public function rules(){
			return [
				[['docnum', 'docdate', 'document_type_id', 'from_warehouse_id', 'to_warehouse_id'], 'required'],
				[['supplier_id'], 'required', 'on' => 'scenario_req_supp'],
				[['comment'], 'required', 'on' => 'act'],
				[['docdate', 'status', 'action', 'adj_wh_id', 'adj'], 'safe'],
				[['supplier_id', 'document_type_id', 'from_warehouse_id', 'to_warehouse_id', 'created_by', 'created_at', 'updated_by', 'updated_at', 'type_id'], 'integer'],
				[['docnum', 'series'], 'string', 'max' => 255],
				[['comment'], 'string', 'max' => 1000],
				[['serial_number'], 'string', 'max' => 50],
				[['supplier_id'], 'exist', 'skipOnError' => true, 'targetClass' => Supplier::className(), 'targetAttribute' => ['supplier_id' => 'id']],
				[['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
				[['document_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentType::className(), 'targetAttribute' => ['document_type_id' => 'id']],
				[['from_warehouse_id'], 'exist', 'skipOnError' => true, 'targetClass' => Warehouse::className(), 'targetAttribute' => ['from_warehouse_id' => 'id']],
				[['to_warehouse_id'], 'exist', 'skipOnError' => true, 'targetClass' => Warehouse::className(), 'targetAttribute' => ['to_warehouse_id' => 'id']],
				[['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
			];
		}

		/**
			* @inheritdoc
			*/
		public function attributeLabels(){
			return [
				'id' => Yii::t('app', 'ID'),
				'docnum' => Yii::t('app', 'Document number'),
				'docdate' => Yii::t('app', 'Document date'),
				'document_type_id' => Yii::t('app', 'Document type'),
				'from_warehouse_id' => Yii::t('app', 'Warehouse A'),
				'to_warehouse_id' => Yii::t('app', 'Warehouse B'),
				'series' => Yii::t('app', 'Series'),
				'status' => Yii::t('app', 'Status'),
				'comment' => Yii::t('app', 'Comment'),
				'serial_number' => Yii::t('app', 'Serial number'),
				'created_by' => Yii::t('app', 'Created by'),
				'created_at' => Yii::t('app', 'Created at'),
				'updated_by' => Yii::t('app', 'Updated by'),
				'updated_at' => Yii::t('app', 'Updated at'),
				'action' => Yii::t('app', 'Action'),
				'adj' => Yii::t('app', 'Adjustment'),
				'adj_wh_id' => Yii::t('app', 'Warehouse'),
				'supplier_id' => Yii::t('app', 'Supplier'),
				'filter_from' => Yii::t('app', 'From'),
				'filter_to' => Yii::t('app', 'To'),
			];
		}

		/**
			* @return ActiveQuery
			*/
		public function getSupplier(){
			return $this->hasOne(Supplier::className(), ['id' => 'supplier_id']);
		}

		public function getCreatedBy(){
			return $this->hasOne(User::className(), ['id' => 'created_by']);
		}

		/**
			* @return ActiveQuery
			*/
		public function getDocumentType(){
			return $this->hasOne(DocumentType::className(), ['id' => 'document_type_id']);
		}

		/**
			* @return ActiveQuery
			*/
		public function getFromWarehouse(){
			return $this->hasOne(Warehouse::className(), ['id' => 'from_warehouse_id']);
		}

		/**
			* @return ActiveQuery
			*/
		public function getToWarehouse(){
			return $this->hasOne(Warehouse::className(), ['id' => 'to_warehouse_id']);
		}

		public function getAdjWhId(){
			if($this->to_warehouse_id == Yii::$app->params['adjustmentWhId']){
				return $this->from_warehouse_id;
			}elseif($this->from_warehouse_id == Yii::$app->params['adjustmentWhId']){
				return $this->to_warehouse_id;
			}
		}

		public function getAdjWhName(){
			return Warehouse::findOne($this->adjWhId)->name;
		}

		public function getAdjStatus(){
			if($this->to_warehouse_id == Yii::$app->params['adjustmentWhId']){
				return 0;
			}elseif($this->from_warehouse_id == Yii::$app->params['adjustmentWhId']){
				return 1;
			}
		}

		public function getAdjName(){
			return Yii::t('app', self::$adjList[$this->adjStatus]);
		}

		public function getAdjNameForList(){
			if($this->to_warehouse_id == Yii::$app->params['adjustmentWhId']){
				return Yii::t('app', 'Issue');
			}elseif($this->from_warehouse_id == Yii::$app->params['adjustmentWhId']){
				return Yii::t('app', 'Receipt');
			}
		}

		public static function getAdjListNames(){
			foreach(self::$adjList as $adj_code => $adj_name){
				$result[$adj_code] = Yii::t('app', $adj_name);
			}
			return $result;
		}

		/**
			* @return ActiveQuery
			*/
		public function getUpdatedBy(){
			return $this->hasOne(User::className(), ['id' => 'updated_by']);
		}

		/**
			* @return ActiveQuery
			*/
		public function getDocumentDetails(){
			return $this->hasMany(DocumentDetail::className(), ['document_id' => 'id']);
		}

		public function getDocumentDetailsSub(){
			return $this->hasMany(DocumentDetailSub::className(), ['document_id' => 'id']);
		}

		public function getStatusName(){
			return ($this->status == 0) ? Yii::t('app', 'Pending') : Yii::t('app', 'Confirmed');
		}

		public function getActionName(){
			if(in_array($this->to_warehouse_id, Yii::$app->user->identity->warehouseIds)){
				return Yii::t('app', 'Receipt');
			}elseif(in_array($this->from_warehouse_id, Yii::$app->user->identity->warehouseIds)){
				return Yii::t('app', 'Issue');
			}
		}

		public function getActionStatus(){
			if(in_array($this->to_warehouse_id, Yii::$app->user->identity->warehouseIds)){
				return 1;
			}elseif(in_array($this->from_warehouse_id, Yii::$app->user->identity->warehouseIds)){
				return 0;
			}
		}

		public function getIsLocalkd(){
			return ($this->document_type_id == 2 and $this->from_warehouse_id == Yii::$app->params['outsoursingWhId']);
		}

		public function getIsLocal(){
			return ($this->document_type_id == 2 and $this->fromWarehouse->warehouse_type == Warehouse::TYPE_OUTSOURCING);
		}

		public function getIsIntransit(){
			return ($this->document_type_id == 1 and $this->from_warehouse_id == Yii::$app->params['inTransitWhId']);
		}

		public function getIsLocalIssue(){
			return ($this->document_type_id == 2 and $this->toWarehouse->warehouse_type == Warehouse::TYPE_OUTSOURCING);
		}

		public function getIsProdIssue(){
			return $this->to_warehouse_id == Yii::$app->params['deliveryWhId'];
		}

		public function getUpdatedAtFormatted(){
			return (!empty($this->updated_at)) ? date('d.m.Y H:i', $this->updated_at) : '';
		}

		public function getCreatedAtFormatted(){
			return (!empty($this->created_at)) ? date('d.m.Y H:i', $this->created_at) : '';
		}

		public function getDocdateFormatted(){
			return (!empty($this->docdate)) ? date('d.m.Y', strtotime($this->docdate)) : '';
		}

		static function getDocumentBySerial($serial_number){
			return self::find()->where(['serial_number' => $serial_number])->one();
		}

		public function generateQrcode(){
			$qrCode = (new QrCode($this->docnum))
			  ->setErrorCorrectionLevel(ErrorCorrectionLevelInterface::HIGH);
		//			->setSize(250)
		//			->setMargin(1);
			$pngData = $qrCode->writeString();
			return base64_encode(($pngData));
		  }




//    public function beforeSave($insert){
//        if (parent::beforeSave($insert)) {
//             if($this->isNewRecord) {
//                    $this->created_by = Yii::$app->user->identity->id;
//                    $this->created_at = time();
//                } else {
//                    $this->updated_by = Yii::$app->user->identity->id;
//                    $this->updated_at = time();
//                }
//            return true;
//        } else {
//            return false;
//        }
//    }
	}
