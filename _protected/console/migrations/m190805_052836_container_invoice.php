<?php
	use yii\db\Migration;

	class m190805_052836_container_invoice extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%container_invoice}}',
				[
					'id' => $this->primaryKey(11),
					'container_id' => $this->integer(11)->null()->defaultValue(null),
					'invoice_id' => $this->integer(11)->null()->defaultValue(null),
					'delivery_term_id' => $this->integer(11)->notNull()->defaultValue(4),
					'app_arr_at' => $this->date()->null()->defaultValue(null),
					'shipped_at' => $this->date()->null()->defaultValue(null),
					'shipped_by' => $this->integer(11)->notNull(),
					'document_id' => $this->integer(11)->null()->defaultValue(null),
					'need_at' => $this->date()->null()->defaultValue(null),
					'current_locate' => $this->string(255)->null()->defaultValue(null),
					'current_at' => $this->date()->null()->defaultValue(null),
					'arrived_at' => $this->date()->null()->defaultValue(null),
					'arrived_by' => $this->integer(11)->null()->defaultValue(null),
					'received_at' => $this->date()->null()->defaultValue(null),
					'received_by' => $this->integer(11)->null()->defaultValue(null),
					'ship_mode_id' => $this->integer(11)->notNull(),
				], $tableOptions
			);
			$this->createIndex('idx_unique_cont_inv_ship', '{{%container_invoice}}', ['container_id', 'invoice_id', 'shipped_at'], true);
			$this->createIndex('frk-container_invoice-invoice_id', '{{%container_invoice}}', ['invoice_id'], false);
			$this->createIndex('frk-container_invoice-shipped_by', '{{%container_invoice}}', ['shipped_by'], false);
			$this->createIndex('frk-container_invoice-arrived_by', '{{%container_invoice}}', ['arrived_by'], false);
			$this->createIndex('frk-container_invoice-received_by', '{{%container_invoice}}', ['received_by'], false);
			$this->createIndex('frk-container_invoice-track_type_id', '{{%container_invoice}}', ['ship_mode_id'], false);
			$this->createIndex('frk-container_invoice-document_id', '{{%container_invoice}}', ['document_id'], false);
			$this->createIndex('fk-container_invoice-delivery_term_id', '{{%container_invoice}}', ['delivery_term_id'], false);
		}

		public function safeDown(){
			$this->dropIndex('idx_unique_cont_inv_ship', '{{%container_invoice}}');
			$this->dropIndex('frk-container_invoice-invoice_id', '{{%container_invoice}}');
			$this->dropIndex('frk-container_invoice-shipped_by', '{{%container_invoice}}');
			$this->dropIndex('frk-container_invoice-arrived_by', '{{%container_invoice}}');
			$this->dropIndex('frk-container_invoice-received_by', '{{%container_invoice}}');
			$this->dropIndex('frk-container_invoice-track_type_id', '{{%container_invoice}}');
			$this->dropIndex('frk-container_invoice-document_id', '{{%container_invoice}}');
			$this->dropIndex('fk-container_invoice-delivery_term_id', '{{%container_invoice}}');
			$this->dropTable('{{%container_invoice}}');
		}
	}
