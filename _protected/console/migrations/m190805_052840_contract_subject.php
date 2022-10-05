<?php
	use yii\db\Migration;

	class m190805_052840_contract_subject extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%contract_subject}}',
				[
					'id' => $this->primaryKey(11),
					'name' => $this->string(255)->notNull(),
				], $tableOptions
			);
			$this->createIndex('name', '{{%contract_subject}}', ['name'], true);
		}

		public function safeDown(){
			$this->dropIndex('name', '{{%contract_subject}}');
			$this->dropTable('{{%contract_subject}}');
		}
	}
