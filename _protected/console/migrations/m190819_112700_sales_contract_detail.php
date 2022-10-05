<?php
	use yii\db\Migration;

	class m190819_112700_sales_contract_detail extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%sales_contract_detail}}',
				[
					'id' => $this->primaryKey(11),
					'sales_contract_id' => $this->integer(11)->notNull(),
					'part_id' => $this->integer(11)->notNull(),
					'delivery_term_id' => $this->integer(11)->notNull(),
					'price' => $this->decimal(20, 5)->notNull(),
					'vat' => $this->decimal(20, 5)->notNull(),
					'excise' => $this->decimal(20, 5)->notNull(),
				], $tableOptions
			);
			$this->addForeignKey('frk-sales_contract_detail-sales_contract_id', '{{%sales_contract_detail}}', ['sales_contract_id'], 'sales_contract', 'id');
			$this->addForeignKey('frk-sales_contract_detail-part_id', '{{%sales_contract_detail}}', ['part_id'], 'part', 'id');
			$this->addForeignKey('frk-sales_contract_detail-delivery_term_id', '{{%sales_contract_detail}}', ['delivery_term_id'], 'delivery_term', 'id');
		}

		public function safeDown(){
			$this->dropForeignKey('frk-sales_contract_detail-sales_contract_id', '{{%sales_contract_detail}}');
			$this->dropForeignKey('frk-sales_contract_detail-part_id', '{{%sales_contract_detail}}');
			$this->dropForeignKey('frk-sales_contract_detail-delivery_term_id', '{{%sales_contract_detail}}');
			$this->dropTable('{{%sales_contract_detail}}');
		}
	}
