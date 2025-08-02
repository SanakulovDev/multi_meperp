<?php
	use yii\db\Migration;

	class m190910_120600_add_fgstock_column_to_req_table extends Migration{
		/**
			* {@inheritdoc}
			*/
		public function safeUp(){
			$this->addColumn('{{%req}}', 'fgstock', $this->decimal(20, 5)->after('semistock'));
		}

		/**
			* {@inheritdoc}
			*/
		public function safeDown(){
			$this->dropColumn('{{%req}}', 'fgstock');
		}
	}
