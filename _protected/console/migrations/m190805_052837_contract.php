<?php
	use yii\db\Migration;

	class m190805_052837_contract extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%contract}}',
				[
					'id' => $this->primaryKey(11),
					'supplier_id' => $this->integer(11)->notNull(),
					'contract_no' => $this->string(255)->notNull(),
					'contract_date' => $this->date()->notNull(),
					'expiry_date' => $this->date()->notNull(),
					'buyer_id' => $this->integer(11)->notNull(),
					'payment_term_id' => $this->integer(11)->notNull(),
					'delivery_term_id' => $this->integer(11)->notNull(),
					'contract_amount' => $this->decimal(20, 5)->null()->defaultValue(null),
					'contract_subject_id' => $this->integer(11)->notNull(),
					'currency_id' => $this->integer(11)->notNull(),
					'contract_source_id' => $this->integer(11)->notNull(),
					'status' => $this->tinyInteger(1)->notNull()->defaultValue(0),
					'created_by' => $this->integer(11)->notNull(),
					'created_at' => $this->integer(11)->notNull(),
					'updated_by' => $this->integer(11)->null()->defaultValue(null),
					'updated_at' => $this->integer(11)->null()->defaultValue(null),
				], $tableOptions
			);
			$this->createIndex('frk-contract-supplier_id', '{{%contract}}', ['supplier_id'], false);
			$this->createIndex('frk-contract-buyer_id', '{{%contract}}', ['buyer_id'], false);
			$this->createIndex('frk-contract-payment_term_id', '{{%contract}}', ['payment_term_id'], false);
			$this->createIndex('frk-contract-delivery_term_id', '{{%contract}}', ['delivery_term_id'], false);
			$this->createIndex('frk-contract-contract_subject_id', '{{%contract}}', ['contract_subject_id'], false);
			$this->createIndex('frk-contract-currency_id', '{{%contract}}', ['currency_id'], false);
			$this->createIndex('frk-contract-contract_source_id', '{{%contract}}', ['contract_source_id'], false);
			$this->createIndex('frk-contract-created_by', '{{%contract}}', ['created_by'], false);
			$this->createIndex('frk-contract-updated_by', '{{%contract}}', ['updated_by'], false);
		}

		public function safeDown(){
			$this->dropIndex('frk-contract-supplier_id', '{{%contract}}');
			$this->dropIndex('frk-contract-buyer_id', '{{%contract}}');
			$this->dropIndex('frk-contract-payment_term_id', '{{%contract}}');
			$this->dropIndex('frk-contract-delivery_term_id', '{{%contract}}');
			$this->dropIndex('frk-contract-contract_subject_id', '{{%contract}}');
			$this->dropIndex('frk-contract-currency_id', '{{%contract}}');
			$this->dropIndex('frk-contract-contract_source_id', '{{%contract}}');
			$this->dropIndex('frk-contract-created_by', '{{%contract}}');
			$this->dropIndex('frk-contract-updated_by', '{{%contract}}');
			$this->dropTable('{{%contract}}');
		}
	}
