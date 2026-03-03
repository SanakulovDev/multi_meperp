<?php
	use yii\db\Migration;

	class m190805_052852_invoice extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%invoice}}',
				[
					'id' => $this->primaryKey(11),
					'invoice_no' => $this->string(50)->notNull(),
					'supplier_id' => $this->integer(11)->null()->defaultValue(null),
					//					'port_of_loading' => $this->string(255)->notNull(),
					//					'package_qty' => $this->integer(11)->notNull(),
					//					'cbm' => $this->decimal(20, 5)->null()->defaultValue(null),
					//					'n_weight' => $this->decimal(20, 5)->null()->defaultValue(null),
					//					'g_weight' => $this->decimal(20, 5)->null()->defaultValue(null),
					//					'total_amount' => $this->decimal(20, 5)->null()->defaultValue(null),
					'created_by' => $this->integer(11)->notNull(),
					'created_at' => $this->integer(11)->notNull(),
					'updated_by' => $this->integer(11)->null()->defaultValue(null),
					'updated_at' => $this->integer(11)->null()->defaultValue(null),
				], $tableOptions
			);
			$this->createIndex('invoice_no', '{{%invoice}}', ['invoice_no'], true);
			$this->createIndex('idx-invoice-created_by', '{{%invoice}}', ['created_by'], false);
			$this->createIndex('idx-invoice-updated_by', '{{%invoice}}', ['updated_by'], false);
			$this->createIndex('idx-invoice-supplier_id', '{{%invoice}}', ['supplier_id'], false);
		}

		public function safeDown(){
			$this->dropIndex('invoice_no', '{{%invoice}}');
			$this->dropIndex('idx-invoice-created_by', '{{%invoice}}');
			$this->dropIndex('idx-invoice-updated_by', '{{%invoice}}');
			$this->dropIndex('idx-invoice-supplier_id', '{{%invoice}}');
			$this->dropTable('{{%invoice}}');
		}
	}
