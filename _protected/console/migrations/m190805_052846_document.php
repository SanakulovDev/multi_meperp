<?php
	use yii\db\Migration;

	class m190805_052846_document extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%document}}',
				[
					'id' => $this->primaryKey(11),
					'docnum' => $this->string(255)->notNull(),
					'docdate' => $this->date()->notNull(),
					'document_type_id' => $this->integer(11)->notNull(),
					'from_warehouse_id' => $this->integer(11)->notNull(),
					'to_warehouse_id' => $this->integer(11)->notNull(),
					'supplier_id' => $this->integer(11)->null()->defaultValue(null),
					'series' => $this->string(255)->null()->defaultValue(null),
					'status' => $this->tinyInteger(1)->notNull()->defaultValue(0),
					'comment' => $this->string(1000)->null()->defaultValue(null),
					'serial_number' => $this->string(50)->null()->defaultValue(null),
					'created_by' => $this->integer(11)->notNull(),
					'created_at' => $this->integer(11)->notNull(),
					'updated_by' => $this->integer(11)->null()->defaultValue(null),
					'updated_at' => $this->integer(11)->null()->defaultValue(null),
				], $tableOptions
			);
			$this->createIndex('idx-document-document_type_id', '{{%document}}', ['document_type_id'], false);
			$this->createIndex('idx-document-from_warehouse_id', '{{%document}}', ['from_warehouse_id'], false);
			$this->createIndex('idx-document-to_warehouse_id', '{{%document}}', ['to_warehouse_id'], false);
			$this->createIndex('idx-document-crt-user_id', '{{%document}}', ['created_by'], false);
			$this->createIndex('idx-document-updt-user_id', '{{%document}}', ['updated_by'], false);
			$this->createIndex('frk-document-supplier_id', '{{%document}}', ['supplier_id'], false);
		}

		public function safeDown(){
			$this->dropIndex('idx-document-document_type_id', '{{%document}}');
			$this->dropIndex('idx-document-from_warehouse_id', '{{%document}}');
			$this->dropIndex('idx-document-to_warehouse_id', '{{%document}}');
			$this->dropIndex('idx-document-crt-user_id', '{{%document}}');
			$this->dropIndex('idx-document-updt-user_id', '{{%document}}');
			$this->dropIndex('frk-document-supplier_id', '{{%document}}');
			$this->dropTable('{{%document}}');
		}
	}
