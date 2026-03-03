<?php
	use yii\db\Migration;

	class m190819_112600_sales_contract extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%sales_contract}}',
				[
					'id' => $this->primaryKey(11),
					'customer_id' => $this->integer(11)->notNull(),
					'contract_no' => $this->string(255)->notNull(),
					'contract_date' => $this->date()->notNull(),
					'expiry_date' => $this->date()->null(),
					'seller_id' => $this->integer(11)->notNull(),
					'payment_term_id' => $this->integer(11)->notNull(),
					'contract_amount' => $this->decimal(20, 5)->null()->defaultValue(null),
					'contract_subject_id' => $this->integer(11),
					'currency_id' => $this->integer(11)->notNull(),
					'status' => $this->tinyInteger(1)->notNull()->defaultValue(0),
					'created_by' => $this->integer(11)->notNull(),
					'created_at' => $this->integer(11)->notNull(),
					'updated_by' => $this->integer(11)->null()->defaultValue(null),
					'updated_at' => $this->integer(11)->null()->defaultValue(null),
				], $tableOptions
			);
			$this->addForeignKey('frk-sales_contract-customer_id', '{{%sales_contract}}', ['customer_id'], 'customer', 'id');
			$this->addForeignKey('frk-sales_contract-seller_id', '{{%sales_contract}}', ['seller_id'], 'user', 'id');
			$this->addForeignKey('frk-sales_contract-payment_term_id', '{{%sales_contract}}', ['payment_term_id'], 'payment_term', 'id');
			$this->addForeignKey('frk-sales_contract-contract_subject_id', '{{%sales_contract}}', ['contract_subject_id'], 'contract_subject', 'id');
			$this->addForeignKey('frk-sales_contract-currency_id', '{{%sales_contract}}', ['currency_id'], 'currency', 'id');
			$this->addForeignKey('frk-sales_contract-created_by', '{{%sales_contract}}', ['created_by'], 'user', 'id');
			$this->addForeignKey('frk-sales_contract-updated_by', '{{%sales_contract}}', ['updated_by'], 'user', 'id');
		}

		public function safeDown(){
			$this->dropForeignKey('frk-sales_contract-customer_id', '{{%sales_contract}}');
			$this->dropForeignKey('frk-sales_contract-seller_id', '{{%sales_contract}}');
			$this->dropForeignKey('frk-sales_contract-payment_term_id', '{{%sales_contract}}');
			$this->dropForeignKey('frk-sales_contract-contract_subject_id', '{{%sales_contract}}');
			$this->dropForeignKey('frk-sales_contract-currency_id', '{{%sales_contract}}');
			$this->dropForeignKey('frk-sales_contract-created_by', '{{%sales_contract}}');
			$this->dropForeignKey('frk-sales_contract-updated_by', '{{%sales_contract}}');
			$this->dropTable('{{%sales_contract}}');
		}
	}
