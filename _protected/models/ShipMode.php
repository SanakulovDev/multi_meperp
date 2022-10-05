<?php
	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "ship_mode".
	 *
	 * @property int $id
	 * @property string $name
	 * @property string $description
	 *
	 * @property ContainerInvoice[] $containerInvoices
	 */
	class ShipMode extends \yii\db\ActiveRecord{

		/**
		 * @inheritdoc
		 */
		public static function tableName(){
			return 'ship_mode';
		}

		/**
		 * @inheritdoc
		 */
		public function rules(){
			return [
				[['name', 'description'], 'string', 'max' => 255],
			];
		}

		/**
		 * @inheritdoc
		 */
		public function attributeLabels(){
			return [
				'id'          => Yii::t('app', 'ID'),
				'name'        => Yii::t('app', 'Name'),
				'description' => Yii::t('app', 'Description'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getContainerInvoices(){
			return $this->hasMany(ContainerInvoice::className(), ['ship_mode_id' => 'id']);
		}
	}
