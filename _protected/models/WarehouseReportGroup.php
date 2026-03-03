<?php
	namespace app\models;

	use Yii;
	use yii\db\ActiveQuery;
	use yii\db\ActiveRecord;

	/**
		* This is the model class for table "warehouse_report_group".
		* @property int    $id
		* @property string $title
		* @property string $description
		* @property Warehouse[] $warehouses
		*/
	class WarehouseReportGroup extends ActiveRecord{
		/**
			* {@inheritdoc}
			*/
		public static function tableName(){
			return 'warehouse_report_group';
		}

		/**
			* {@inheritdoc}
			*/
		public function rules(){
			return [
				[['title'], 'required'],
				[['sort_order'], 'integer'],
				[['description'], 'string', 'max' => 255],
				[['title'], 'string', 'max' => 100],
				[['title'], 'unique'],
			];
		}

		/**
			* {@inheritdoc}
			*/
		public function attributeLabels(){
			return [
				'id' => Yii::t('app', 'ID'),
				'title' => Yii::t('app', 'Title'),
				'description' => Yii::t('app', 'Description'),
				'sort_order' => Yii::t('app', 'Sort order'),
			];
		}

		/**
			* @return ActiveQuery
			*/
		public function getWarehouses(){
			return $this->hasMany(Warehouse::className(), ['warehouse_report_group_id' => 'id']);
		}

		
	}
