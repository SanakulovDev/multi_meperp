<?php
	use yii\db\Migration;

	class m190805_052902_part_part extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%part_part}}',
				[
					'id' => $this->primaryKey(11),
					'part_id' => $this->integer(11)->notNull(),
					'sub_part_id' => $this->integer(11)->notNull(),
					'usage_qty' => $this->decimal(25, 10)->notNull()->defaultValue('1.00000'),
					'warehouse_id' => $this->integer(11)->notNull(),
					'remark' => $this->string(255)->null()->defaultValue(null),
					'status' => $this->tinyInteger(1)->notNull()->defaultValue(0),
					'created_by' => $this->integer(11)->null()->defaultValue(null),
					'created_at' => $this->integer(11)->notNull(),
					'updated_by' => $this->integer(11)->null()->defaultValue(null),
					'updated_at' => $this->integer(11)->null()->defaultValue(null),
				], $tableOptions
			);
			$this->createIndex('idx_unique_part_id_sub_part_id_part_part', '{{%part_part}}', ['part_id', 'sub_part_id'], true);
			$this->createIndex('frk-part_part-sub_part_id', '{{%part_part}}', ['sub_part_id'], false);
			$this->createIndex('frk-part_part-created_by', '{{%part_part}}', ['created_by'], false);
			$this->createIndex('frk-part_part-updated_by', '{{%part_part}}', ['updated_by'], false);
			$this->createIndex('frk-part_part-warehouse_id', '{{%part_part}}', ['warehouse_id'], false);
		}

		public function safeDown(){
			$this->dropIndex('idx_unique_part_id_sub_part_id_part_part', '{{%part_part}}');
			$this->dropIndex('frk-part_part-sub_part_id', '{{%part_part}}');
			$this->dropIndex('frk-part_part-created_by', '{{%part_part}}');
			$this->dropIndex('frk-part_part-updated_by', '{{%part_part}}');
			$this->dropIndex('frk-part_part-warehouse_id', '{{%part_part}}');
			$this->dropTable('{{%part_part}}');
		}
	}
