<?php
	use yii\db\Migration;

	class m190805_052838_contract_detail extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%contract_detail}}',
				[
					'id' => $this->primaryKey(11),
					'contract_id' => $this->integer(11)->notNull(),
					'part_id' => $this->integer(11)->notNull(),
					'price' => $this->decimal(20, 5)->notNull(),
				], $tableOptions
			);
			$this->createIndex('frk-contract_detail-contract_id', '{{%contract_detail}}', ['contract_id'], false);
			$this->createIndex('frk-contract_detail-part_id', '{{%contract_detail}}', ['part_id'], false);
		}

		public function safeDown(){
			$this->dropIndex('frk-contract_detail-contract_id', '{{%contract_detail}}');
			$this->dropIndex('frk-contract_detail-part_id', '{{%contract_detail}}');
			$this->dropTable('{{%contract_detail}}');
		}
	}
