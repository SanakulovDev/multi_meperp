<?php
	use yii\db\Migration;

	class m200408_183500_add_doh_column_to_req_table extends Migration{
		/**
			* {@inheritdoc}
			*/
		public function safeUp(){
			$this->addColumn('{{%req}}', 'doh', $this->integer());
			$this->addColumn('{{%req_t}}', 'doh', $this->integer());
		}

		/**
			* {@inheritdoc}
			*/
		public function safeDown(){
			$this->dropColumn('{{%req}}', 'doh');
			$this->dropColumn('{{%req_t}}', 'doh');
		}
	}
