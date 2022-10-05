<?php
	use yii\db\Migration;

	class m190805_052855_lms extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%lms}}',
				[
					'id' => $this->primaryKey(11),
					'part_id' => $this->integer(11)->notNull(),
					'supplier_id' => $this->integer(11)->null()->defaultValue(null),
					'warehouse_id' => $this->integer(11)->null()->defaultValue(null),
					'dloc' => $this->string(50)->null()->defaultValue(null),
					'minimum' => $this->decimal(10, 2)->null()->defaultValue(null),
					'maximum' => $this->decimal(10, 2)->null()->defaultValue(null),
					'stack' => $this->decimal(10, 2)->null()->defaultValue(null),
					'mpr' => $this->string(50)->null()->defaultValue(null),
					'high_theft' => $this->tinyInteger(1)->null()->defaultValue(0),
					'created_by' => $this->integer(11)->null()->defaultValue(null),
					'created_at' => $this->integer(11)->notNull(),
					'updated_by' => $this->integer(11)->null()->defaultValue(null),
					'updated_at' => $this->integer(11)->null()->defaultValue(null),
				], $tableOptions
			);
			$this->createIndex('frk-lms-part_id', '{{%lms}}', ['part_id'], false);
			$this->createIndex('frk-lms-supplier_id', '{{%lms}}', ['supplier_id'], false);
			$this->createIndex('frk-lms-warehouse_id', '{{%lms}}', ['warehouse_id'], false);
			$this->createIndex('frk-lms-created_by', '{{%lms}}', ['created_by'], false);
			$this->createIndex('frk-lms-updated_by', '{{%lms}}', ['updated_by'], false);
		}

		public function safeDown(){
			$this->dropIndex('frk-lms-part_id', '{{%lms}}');
			$this->dropIndex('frk-lms-supplier_id', '{{%lms}}');
			$this->dropIndex('frk-lms-warehouse_id', '{{%lms}}');
			$this->dropIndex('frk-lms-created_by', '{{%lms}}');
			$this->dropIndex('frk-lms-updated_by', '{{%lms}}');
			$this->dropTable('{{%lms}}');
		}
	}
