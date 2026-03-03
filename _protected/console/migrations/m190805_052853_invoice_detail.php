<?php
	use yii\db\Migration;

	class m190805_052853_invoice_detail extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%invoice_detail}}',
				[
					'id' => $this->primaryKey(11),
					'part_order_id' => $this->integer(11)->null()->defaultValue(null),
					'contract_id' => $this->integer(11)->null()->defaultValue(null),
					'cont_inv_id' => $this->integer(11)->notNull(),
					'part_id' => $this->integer(11)->notNull(),
					'qty' => $this->decimal(20, 5)->notNull(),
					'remarks' => $this->string(255)->null()->defaultValue(null),
					'created_by' => $this->integer(11)->notNull(),
					'created_at' => $this->integer(11)->notNull(),
					'updated_by' => $this->integer(11)->null()->defaultValue(null),
					'updated_at' => $this->integer(11)->null()->defaultValue(null),
				], $tableOptions
			);
			$this->createIndex('idx-inv_dtl-crt-user_id', '{{%invoice_detail}}', ['created_by'], false);
			$this->createIndex('idx-inv_dtl-updt-user_id', '{{%invoice_detail}}', ['updated_by'], false);
			$this->createIndex('frk-invoice_detail-cont_inv_id', '{{%invoice_detail}}', ['cont_inv_id'], false);
			$this->createIndex('frk-invoice_detail-conrtact_id', '{{%invoice_detail}}', ['contract_id'], false);
			$this->createIndex('frk-invoice_detail-part_order_id', '{{%invoice_detail}}', ['part_order_id'], false);
			$this->createIndex('frk-invoice_detail-part_id', '{{%invoice_detail}}', ['part_id'], false);
		}

		public function safeDown(){
			$this->dropIndex('idx-inv_dtl-crt-user_id', '{{%invoice_detail}}');
			$this->dropIndex('idx-inv_dtl-updt-user_id', '{{%invoice_detail}}');
			$this->dropIndex('frk-invoice_detail-cont_inv_id', '{{%invoice_detail}}');
			$this->dropIndex('frk-invoice_detail-conrtact_id', '{{%invoice_detail}}');
			$this->dropIndex('frk-invoice_detail-part_order_id', '{{%invoice_detail}}');
			$this->dropIndex('frk-invoice_detail-part_id', '{{%invoice_detail}}');
			$this->dropTable('{{%invoice_detail}}');
		}
	}
