<?php
	namespace app\models;

	use Yii;
	use yii\behaviors\TimestampBehavior;

	/**
	 * This is the model class for table "document_type".
	 *
	 * @property int $id
	 * @property string $code
	 * @property string $name
	 * @property string $description
	 * @property int $yyyy
	 * @property int $sequence
	 *
	 * @property Document[] $documents
	 */
	class DocumentType extends \yii\db\ActiveRecord{

		/**
		 * Returns a list of behaviors that this component should behave as.
		 *
		 * @return array
		 */
		public function behaviors(){
			return [
				//TimestampBehavior::className(),
				//            BlameableBehavior::className(),  // XxxController actionCreate dan $model->created_by = 1; va actionUpdate dan  $model->updated_by = 1;  ni o`chirish kerak
			];
		}

		/**
		 * @inheritdoc
		 */
		public static function tableName(){
			return 'document_type';
		}

		/**
		 * @inheritdoc
		 */
		public function rules(){
			return [
				[['code', 'name'], 'required'],
				[['yyyy', 'sequence'], 'integer'],
				[['code'], 'string', 'max' => 3],
				[['name'], 'string', 'max' => 50],
				[['description'], 'string', 'max' => 255],
				[['name'], 'unique'],
			];
		}

		/**
		 * @inheritdoc
		 */
		public function attributeLabels(){
			return [
				'id'          => Yii::t('app', 'ID'),
				'code'        => Yii::t('app', 'Code'),
				'name'        => Yii::t('app', 'Name'),
				'description' => Yii::t('app', 'Description'),
				'yyyy'        => Yii::t('app', 'Yyyy'),
				'sequence'    => Yii::t('app', 'Sequence'),
			];
		}

		public function fields()
		{
			return [
				'id',
				'code',
				'name'
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getDocuments(){
			return $this->hasMany(Document::className(), ['document_type_id' => 'id']);
		}

		

		public static function generateDocnum($id){

			$docType = self::findOne($id);
			if($docType->yyyy == date('Y')){
				$seq = $docType->sequence + 1;
				$docType->sequence = $seq;
				$docType->save();
			}else{
				$docType->yyyy = date('Y');
				$docType->sequence = 1;
				$docType->save();
			}
			return $docType->code . '-' . $docType->yyyy . '-' . $docType->sequence;
		}

	}
