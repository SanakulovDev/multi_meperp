<?php
	namespace app\models;

	use Yii;
	use yii\behaviors\BlameableBehavior;
	use yii\behaviors\TimestampBehavior;
	use yii\db\ActiveQuery;
	use yii\db\ActiveRecord;

	/**
	 * This is the model class for table "factory".
	 * @property int         $id
	 * @property string      $name
	 * @property string      $head
	 * @property string      $alias
	 * @property string      $is_main
	 * @property string      $address
	 * @property string      $tin
	 * @property string      $vat
	 * @property string      $duns
	 * @property string      $remark
	 * @property int         $fg_warehouse_id
	 * @property int         $status
	 * @property int         $created_by
	 * @property int         $created_at
	 * @property int         $updated_by
	 * @property int         $updated_at
	 * @property Warehouse   $fgWarehouse
	 * @property FgInvoice[] $fgInvoices
	 * @property Line[]      $lines
	 */
	class Factory extends ActiveRecord{

		const STATUS_ACTIVE = 1;
		const STATUS_INACTIVE = 0;

		public $statusList = [
			self::STATUS_ACTIVE => 'Актив',
			self::STATUS_INACTIVE => 'Не актив',
		];

		/**
		 * {@inheritdoc}
		 */
		public static function tableName(){
			return 'factory';
		}

		public function behaviors(){
			return [
				TimestampBehavior::className(),
				[
					'class' => BlameableBehavior::class,
					'defaultValue' => 1,
				]
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules(){
			return [
				[['name', 'address', 'fg_warehouse_id', 'fg_warehouse_id', 'alias'], 'required'],
				[['status'], 'integer'],
				[['name'], 'string', 'max' => 150],
				[['alias'], 'string', 'max' => 10],
				[['is_main'], 'integer', 'max' => 1],
				[['head', 'address', 'remark', 'chief_accountant'], 'string', 'max' => 255],
				[['tin', 'vat', 'duns'], 'string', 'max' => 30],
				[['name'], 'unique'],
				[['alias'], 'unique'],
				[['fg_warehouse_id'], 'exist', 'skipOnError' => true, 'targetClass' => Warehouse::className(), 'targetAttribute' => ['fg_warehouse_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels(){
			return [
				'id' => Yii::t('app', 'ID'),
				'name' => Yii::t('app', 'Name'),
				'head' => Yii::t('app', 'Head of factory'),
				'chief_accountant' => Yii::t('app', 'Chief accountant'),
				'alias' => Yii::t('app', 'Alias'),
				'is_main' => Yii::t('app', 'Is main'),
				'address' => Yii::t('app', 'Address'),
				'tin' => Yii::t('app', 'Taxpayer Identification Number'),
				'vat' => Yii::t('app', 'Reg.code(VAT)'),
				'duns' => Yii::t('app', 'DUNS'),
				'fg_warehouse_id' => Yii::t('app', 'FG storage'),
				'remark' => Yii::t('app', 'Remark'),
				'status' => Yii::t('app', 'Status'),
				'created_by' => Yii::t('app', 'Created by'),
				'created_at' => Yii::t('app', 'Created at'),
				'updated_by' => Yii::t('app', 'Updated by'),
				'updated_at' => Yii::t('app', 'Updated at'),
			];
		}

		public function fields(){
			return [
				'id',
				'name',
				'head',
				'chief_accountant',
				'alias',
				'is_main',
				'address',
				'tin',
				'vat',
				'duns',
				'fg_warehouse_id'
			];
		}

		/**
		 * @return ActiveQuery
		 */
		public function getFgWarehouse(){
			return $this->hasOne(Warehouse::className(), ['id' => 'fg_warehouse_id']);
		}

		public function getFgInvoices(){
			return $this->hasMany(FgInvoice::className(), ['factory_id' => 'id']);
		}

		public function getLines(){
			return $this->hasMany(Line::className(), ['factory_id' => 'id']);
		}

		public function getWaybills(){
			return $this->hasMany(Waybill::className(), ['factory_id' => 'id']);
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

		public function getStatusText(){
			return $this->statusList[$this->status];
		}

		public function getFactoryinfo(){
			return $this->name.' - '.$this->fgWarehouse->name;
		}

		public function afterSave($insert, $changedAttributes) {

      if($this->is_main == 1) {
        self::updateAll(['is_main'=>0], 'id<>'.$this->id);
      }
      parent::afterSave($insert, $changedAttributes);
    }

  }
