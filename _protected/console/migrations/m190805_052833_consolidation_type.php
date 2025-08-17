<?php
	use yii\db\Migration;

	class m190805_052833_consolidation_type extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%consolidation_type}}',
				[
					'id' => $this->primaryKey(11),
					'name' => $this->string(255)->notNull(),
				], $tableOptions
			);
			$this->createIndex('name', '{{%consolidation_type}}', ['name'], true);
		}

		public function safeDown(){
			$this->dropIndex('name', '{{%consolidation_type}}');
			$this->dropTable('{{%consolidation_type}}');
		}
	}
