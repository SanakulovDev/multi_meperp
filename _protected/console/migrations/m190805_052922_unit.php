<?php
	use yii\db\Migration;

	class m190805_052922_unit extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%unit}}',
				[
					'id' => $this->primaryKey(11),
					'unit_value' => $this->string(10)->notNull(),
					'description' => $this->string(150)->null()->defaultValue(null),
				], $tableOptions
			);
			$this->createIndex('unit_value', '{{%unit}}', ['unit_value'], true);
		}

		public function safeDown(){
			$this->dropIndex('unit_value', '{{%unit}}');
			$this->dropTable('{{%unit}}');
		}
	}
