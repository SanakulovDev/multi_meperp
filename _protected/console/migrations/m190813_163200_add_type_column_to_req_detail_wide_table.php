<?php
	use yii\db\Migration;

	class m190813_163200_add_type_column_to_req_detail_wide_table extends Migration{
		/**
			* {@inheritdoc}
			*/
		public function safeUp(){
			$this->addColumn('{{%req_detail_wide}}', 'type', $this->string(2)->comment('D - Daily, W - Weekly')->after('req_id'));
		}

		/**
			* {@inheritdoc}
			*/
		public function safeDown(){
			$this->dropColumn('{{%req_detail_wide}}', 'type');
		}
	}
