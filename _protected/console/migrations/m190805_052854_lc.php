<?php
	use yii\db\Migration;

	class m190805_052854_lc extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%lc}}',
				[
					'id' => $this->primaryKey(11),
					'pay_no' => $this->string(50)->notNull(),
					'pay_amount' => $this->decimal(10, 2)->null()->defaultValue(null),
					'pay_date' => $this->date()->null()->defaultValue(null),
					'exp_date' => $this->date()->null()->defaultValue(null),
					'ship_date' => $this->date()->null()->defaultValue(null),
					'bank' => $this->string(50)->null()->defaultValue(null),
					'contract_id' => $this->integer(11)->null()->defaultValue(null),
					'part_order_id' => $this->integer(11)->null()->defaultValue(null),
					'created_by' => $this->integer(11)->null()->defaultValue(null),
					'created_at' => $this->integer(11)->notNull(),
					'updated_by' => $this->integer(11)->null()->defaultValue(null),
					'updated_at' => $this->integer(11)->null()->defaultValue(null),
				], $tableOptions
			);
			$this->createIndex('frk-lc-contract_id', '{{%lc}}', ['contract_id'], false);
			$this->createIndex('frk-lc-part_order_id', '{{%lc}}', ['part_order_id'], false);
			$this->createIndex('frk-lc-created_by', '{{%lc}}', ['created_by'], false);
			$this->createIndex('frk-lc-updated_by', '{{%lc}}', ['updated_by'], false);
		}

		public function safeDown(){
			$this->dropIndex('frk-lc-contract_id', '{{%lc}}');
			$this->dropIndex('frk-lc-part_order_id', '{{%lc}}');
			$this->dropIndex('frk-lc-created_by', '{{%lc}}');
			$this->dropIndex('frk-lc-updated_by', '{{%lc}}');
			$this->dropTable('{{%lc}}');
		}
	}
