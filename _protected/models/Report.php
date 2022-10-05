<?php
	namespace app\models;

	use Yii;
	use yii\db\ActiveRecord;

	/**
		* This is the model class for table "report".
		* @property int          $id
		* @property string       $action
		* @property string       $title
		* @property string       $description
		* @property UserReport[] $userReports
		* @property User[]       $users
		*/
	class Report extends ActiveRecord{
		/**
			* {@inheritdoc}
			*/
		public static
		function tableName(){
			return 'report';
		}

		/**
			* {@inheritdoc}
			*/
		public
		function rules(){
			return [
				[['action'], 'required'],
				[['list_order', 'report_group_id'], 'integer'],
				[['action', 'title', 'description'], 'string', 'max' => 255],
				[['action'], 'unique'],
				[['report_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => ReportGroup::className(), 'targetAttribute' => ['report_group_id' => 'id']],
			];
		}

		/**
			* {@inheritdoc}
			*/
		public
		function attributeLabels(){
			return [
				'id'           => Yii::t('app', 'ID'),
				'action'       => Yii::t('app', 'Action'),
				'title'        => Yii::t('app', 'Title'),
				'description'  => Yii::t('app', 'Description'),
				'report_lists' => Yii::t('app', 'Report lists'),
				'report_group_id' => Yii::t('app', 'Report group'),
			];
		}

		/**
			* @return \yii\db\ActiveQuery
			*/
		public
		function getUserReports(){
			return $this->hasMany(UserReport::className(), ['report_id' => 'id']);
		}

		/**
			* @return \yii\db\ActiveQuery
			*/
		public
		function getUsers(){
			return $this->hasMany(User::className(), ['id' => 'user_id'])->viaTable('user_report', ['report_id' => 'id']);
		}

		public function getReportGroup()
    {
        return $this->hasOne(ReportGroup::className(), ['id' => 'report_group_id']);
    }
	}
