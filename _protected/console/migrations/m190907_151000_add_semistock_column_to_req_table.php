<?php
	use yii\db\Migration;

	class m190907_151000_add_semistock_column_to_req_table extends Migration{
		/**
			* {@inheritdoc}
			*/
		public function safeUp(){
			$this->addColumn('{{%req}}', 'semistock', $this->decimal(20, 5)->after('linebal'));
		}

		/**
			* {@inheritdoc}
			*/
		public function safeDown(){
			$this->dropColumn('{{%req}}', 'semistock');
		}
	}
