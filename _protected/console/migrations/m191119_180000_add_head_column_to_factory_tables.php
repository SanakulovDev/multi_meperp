<?php
	use yii\db\Migration;

	class m191119_180000_add_head_column_to_factory_tables extends Migration{

		public function safeUp(){
			$this->addColumn('{{%factory}}', 'head',
			                 $this->string(255)->after('name')
			);
		}

		public function safeDown(){
			$this->dropColumn('{{%factory}}', 'head');
		}
	}
