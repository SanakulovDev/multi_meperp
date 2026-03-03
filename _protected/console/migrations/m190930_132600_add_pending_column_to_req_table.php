<?php
	use yii\db\Migration;

	class m190930_132600_add_pending_column_to_req_table extends Migration{
		/**
			* {@inheritdoc}
			*/
		public function safeUp(){
			$this->addColumn('{{%req}}', 'pending', $this->decimal(20, 5)->after('outsourcing'));
		}

		/**
			* {@inheritdoc}
			*/
		public function safeDown(){
			$this->dropColumn('{{%req}}', 'pending');
		}
	}
