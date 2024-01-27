<?php
	namespace app\models;

	use Yii;
	use yii\behaviors\TimestampBehavior;
	use yii\db\ActiveQuery;
	use yii\db\ActiveRecord;

	/**
		* This is the model class for table "warehouse".
		* @property int                  $id
		* @property string               $name
		* @property string               $description
		* @property int                  $is_coverable
		* @property int                  $status
		* @property int                  $warehouse_type
		* @property int                  $warehouse_report_group_id
		* @property int                  $supplier_id
		* @property int                  $created_by
		* @property int                  $created_at
		* @property int                  $updated_by
		* @property int                  $updated_at
		* @property Balance[]            $balances
		* @property Document[]           $documents
		* @property Document[]           $documents0
		* @property DocumentDetailSub[]  $documentDetailSubs
		* @property Factory[]            $factories
		* @property Lms[]                $lms
		* @property Part[]               $parts
		* @property PartPart[]           $partParts
		* @property PartPartWide[]       $partPartWides
		* @property ProductParts[]       $productParts
		* @property ProductionOrderSub[] $productionOrderSubs
		* @property ProductionPlan[]     $productionPlans
		* @property Stock[]              $stocks
		* @property Part[]               $parts0
		* @property UserWarehouse[]      $userWarehouses
		* @property User[]               $users
		* @property User                 $createdBy
		* @property User                 $updatedBy
		* @property WarehouseReportGroup $warehouseReportGroup
		*/
	class Warehouse extends ActiveRecord{

		// the list of status values that can be stored in user table
		const STATUS_ACTIVE    = 1;
		const STATUS_INACTIVE  = 0;
		const TYPE_PHYSICAL    = 0;
		const TYPE_SHOP        = 1;
		const TYPE_VIRTUAL     = 2;
		const TYPE_OUTSOURCING = 3;
    const TYPE_STOCKINFO   = 4;

		const COVERABLE_YES = true;
		const COVERABLE_NO  = false;

		//const STATUS_DELETED  = 0;

		/**
			* List of names for each status.
			* @var array
			*/
		public        $statusList = [
			self::STATUS_ACTIVE => 'Active',
			self::STATUS_INACTIVE => 'Inactive',
			//self::STATUS_DELETED  => 'Deleted'
		];
		public static $typeList   = [
			self::TYPE_PHYSICAL => 'physical',
			self::TYPE_SHOP => 'shop',
			self::TYPE_VIRTUAL => 'virtual',
			self::TYPE_OUTSOURCING => 'outsourcing'
		];

		public $coverableList = [
			self::COVERABLE_NO => 'No',
			self::COVERABLE_YES => 'Yes',
		];

		public function getTypeName(){
			return Yii::t('app', self::$typeList[$this->warehouse_type]);
		}

		public static function getTypeListNames(){
			foreach(self::$typeList as $type_code => $type_name){
				$result[$type_code] = Yii::t('app', $type_name);
			}
			return $result;
		}

		/**
			* Returns a list of behaviors that this component should behave as.
			* @return array
			*/
		public function behaviors(){
			return [
				TimestampBehavior::className(),
				//            BlameableBehavior::className(),  // XxxController actionCreate dan $model->created_by = 1; va actionUpdate dan  $model->updated_by = 1;  ni o`chirish kerak
			];
		}

		/**
			* @inheritdoc
			*/
		public static function tableName(){
			return 'warehouse';
		}

		/**
			* @inheritdoc
			*/
		public function rules(){
			return [
				[['name', 'status', 'warehouse_type', 'warehouse_report_group_id', 'created_by', 'created_at'], 'required'],
				[['status', 'warehouse_type', 'warehouse_report_group_id', 'created_by', 'created_at', 'updated_by', 'updated_at', 'is_coverable', 'supplier_id'], 'integer'],
				[['name'], 'string', 'max' => 50],
				[['description'], 'string', 'max' => 255],
				[['name'], 'unique'],
				[['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
				[['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
				[['supplier_id'], 'exist', 'skipOnError' => true, 'targetClass' => Supplier::className(), 'targetAttribute' => ['supplier_id' => 'id']],
				[['warehouse_report_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => WarehouseReportGroup::className(), 'targetAttribute' => ['warehouse_report_group_id' => 'id']],
			];
		}

		/**
			* @inheritdoc
			*/
		public function attributeLabels(){
			return [
				'id' => Yii::t('app', 'ID'),
				'name' => Yii::t('app', 'Name'),
				'description' => Yii::t('app', 'Description'),
				'status' => Yii::t('app', 'Status'),
				'is_coverable' => Yii::t('app', 'Coverable'),
				'warehouse_type' => Yii::t('app', 'Warehouse type'),
				'warehouse_report_group_id' => Yii::t('app', 'WH report group'),
				'supplier_id' => Yii::t('app', 'Supplier'),
				'created_by' => Yii::t('app', 'Created by'),
				'created_at' => Yii::t('app', 'Created at'),
				'updated_by' => Yii::t('app', 'Updated by'),
				'updated_at' => Yii::t('app', 'Updated at'),
			];
		}

		public function fields()
		{
			return [
				'id',
				'name'
			];
		}

		/**
			* @return ActiveQuery
			*/
		public function getBalances(){
			return $this->hasMany(Balance::className(), ['warehouse_id' => 'id']);
		}

		public function getDocuments(){
			return $this->hasMany(Document::className(), ['from_warehouse_id' => 'id']);
		}

		public function getDocuments0(){
			return $this->hasMany(Document::className(), ['to_warehouse_id' => 'id']);
		}

		public function getStocks(){
			return $this->hasMany(Stock::className(), ['warehouse_id' => 'id']);
		}

		public function getWarehouseReportGroup(){
			return $this->hasOne(WarehouseReportGroup::className(), ['id' => 'warehouse_report_group_id']);
		}

		public function getCreatedBy(){
			return $this->hasOne(User::className(), ['id' => 'created_by']);
		}

		public function getUpdatedBy(){
			return $this->hasOne(User::className(), ['id' => 'updated_by']);
		}

		public function getUpdatedAtFormatted(){
			return (!empty($this->updated_at)) ? date('d.m.Y H:i', $this->updated_at) : '';
		}

		public function getCreatedAtFormatted(){
			return (!empty($this->created_at)) ? date('d.m.Y H:i', $this->created_at) : '';
		}

		public function getUsers(){
			return $this->hasMany(User::className(), ['id' => 'user_id'])->viaTable('user_warehouse', ['warehouse_id' => 'id']);
		}

		public function getMrp(){
			foreach($this->users as $key => $user)
				if($user->roleName == 'mrp') return $this->users[$key];
		}

		public function getStatusText(){
			return $this->statusList[$this->status];
		}

		public function getSupplier(){
			return $this->hasOne(Supplier::className(), ['id' => 'supplier_id']);
		}
    
    public static function getWhNames(){
			return \yii\helpers\ArrayHelper::map(self::find()->where(['status' => self::STATUS_ACTIVE])->all(), 'id', 'name');
		}

		public static function findOneByWhName($wh_name){
			return self::find()->where(['name' => $wh_name])->one();
		}
    
	}
