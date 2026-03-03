<?php
	namespace app\models;

	use Yii;
	use yii\db\ActiveRecord;

	/**
	 * This is the model class for table "part_part_version".
	 * @property int      $id
	 * @property int      $version
	 * @property string   string $action [+] - Add, [-] - remove
	 * @property int      $part_id
	 * @property int      $sub_part_id
	 * @property float    $usage_qty
	 * @property int      $warehouse_id
	 * @property int|null $remark
	 * @property int      $status
	 * @property int      $created_by
	 * @property int      $created_at
	 * @property int|null $updated_by
	 * @property int|null $updated_at
	 * @property int|null $deleted_by
	 * @property int|null $deleted_at
	 */
	class PartPartVersion extends ActiveRecord{

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    public $statusList = [
      self::STATUS_ACTIVE => 'Актив',
      self::STATUS_INACTIVE => 'Не актив',
    ];

		/* yangi ma`lumot kiritish*/
		public const  ADDED = "+";
		/* ma`lumotni o`chirish*/
		public const REMOVED = "-";

		/**
		 * {@inheritdoc}
		 */
		public static function tableName(){
			return 'part_part_version';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules(){
			return [
				[['version', 'action', 'part_id', 'sub_part_id', 'usage_qty', 'warehouse_id', 'status', 'created_by', 'created_at'], 'required'],
				[['version', 'part_id', 'sub_part_id', 'warehouse_id', 'status', 'created_by', 'created_at', 'updated_by', 'updated_at', 'deleted_by', 'deleted_at'], 'integer'],
				[['usage_qty'], 'number'],
				[['action'], 'string', 'max' => 1],
				[['remark'], 'string', 'max' => 255],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels(){
			return [
				'id' => Yii::t('app', 'ID'),
				'version' => Yii::t('app', 'Version'),
				'action' => Yii::t('app', 'Action'),
				'part_id' => Yii::t('app', 'Part ID'),
				'sub_part_id' => Yii::t('app', 'Sub Part ID'),
				'usage_qty' => Yii::t('app', 'Usage qty'),
				'warehouse_id' => Yii::t('app', 'Uloc'),
				'remark' => Yii::t('app', 'Remark'),
				'status' => Yii::t('app', 'Status'),
				'created_by' => Yii::t('app', 'Created by'),
				'created_at' => Yii::t('app', 'Created at'),
				'updated_by' => Yii::t('app', 'Updated by'),
				'updated_at' => Yii::t('app', 'Updated at'),
				'deleted_by' => Yii::t('app', 'Deleted by'),
				'deleted_at' => Yii::t('app', 'Deleted at'),
			];
		}

		public function getPart(){
			return $this->hasOne(Part::className(), ['id' => 'part_id']);
		}

		public function getSubPart(){
			return $this->hasOne(Part::className(), ['id' => 'sub_part_id']);
		}

		public function getCreatedBy(){
			return $this->hasOne(User::className(), ['id' => 'created_by']);
		}

		public function getUpdatedBy(){
			return $this->hasOne(User::className(), ['id' => 'updated_by']);
		}

		public function getDeletedBy(){
			return $this->hasOne(User::className(), ['id' => 'deleted_by']);
		}

		/**
		 * Chage BOM version
		 * Bu funksiya BOM change bo'lganda uning
		 * versiyasini o'zgartirish uchun xizmat qiladi.
		 * @param string $changeType :<br>
		 *                           self::ADDED or "+" // yangi ma'lumot kiritish;<br>
		 *                           self::REMOVED or "-" // ma`lumotni o'chirish<br>
		 * @param object $partPart   changed model
		 * @param int    $version    new version
		 * @return $msg = array('sts', 'msg')
		 */
		public static function changeBomVersion(string $changeType = null, $partPart = null, int $version = 0){
			if($changeType == null){
				return ['sts' => 'BAD',
				        'msg' => Yii::t('app',
				                        '<strong>{nameAttribute}</strong> cannot be blank.',
				                        ['nameAttribute' => '[changeType]',])
				];
			}
			if($partPart == null){
				return ['sts' => 'BAD',
				        'msg' => Yii::t('app',
				                        '<strong>{nameAttribute}</strong> cannot be blank.',
				                        ['nameAttribute' => '[partPart Model]'])
				];
			}
			$modelBomVersion = new PartPartVersion();
			$modelBomVersion->version = $version;
			$modelBomVersion->action = $changeType;
			$modelBomVersion->part_id = $partPart->part_id;
			$modelBomVersion->sub_part_id = $partPart->sub_part_id;
			$modelBomVersion->usage_qty = $partPart->usage_qty;
			$modelBomVersion->warehouse_id = $partPart->warehouse_id;
			$modelBomVersion->remark = $partPart->remark;
			$modelBomVersion->status = $partPart->status;
			$modelBomVersion->created_by = $partPart->created_by;
			$modelBomVersion->created_at = $partPart->created_at;
			$modelBomVersion->updated_by = $partPart->updated_by;
			$modelBomVersion->updated_at = $partPart->updated_at;
			if($changeType==self::REMOVED){
			  $modelBomVersion->deleted_by = Yii::$app->user->id;
        $modelBomVersion->deleted_at = time();
      }

			if(!$modelBomVersion->save()){
				return ['sts' => 'BAD',
				        'msg' => Yii::t('app', 'Error-changeBomVersion:<br>'.$modelBomVersion->errors)
				];
			}
			return ['sts' => 'OK'];
		}
	}
