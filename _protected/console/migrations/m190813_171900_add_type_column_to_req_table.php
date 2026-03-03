<?php
	use yii\db\Migration;

	class m190813_171900_add_type_column_to_req_table extends Migration{
		/**
			* {@inheritdoc}
			*/
		public function safeUp(){
			$this->addColumn('{{%req}}', 'type', $this->string(2)->comment('D - Daily, W - Weekly')->after('id'));
		}

		/**
			* {@inheritdoc}
			*/
		public function safeDown(){
			$this->dropColumn('{{%req}}', 'type');
		}
	}
