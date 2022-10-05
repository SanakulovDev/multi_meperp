<?php
	use yii\db\Migration;

	class m190805_052920_stock extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%stock}}',
				[
					'id' => $this->primaryKey(11),
					'part_id' => $this->integer(11)->notNull(),
					'warehouse_id' => $this->integer(11)->notNull(),
					'qty' => $this->decimal(20, 5)->notNull(),
					'created_at' => $this->integer(11)->notNull(),
					'updated_at' => $this->integer(11)->null()->defaultValue(null),
				], $tableOptions
			);
			$this->createIndex('uniq_idx-stock-part_id-warehouse_id', '{{%stock}}', ['part_id', 'warehouse_id'], true);
			$this->createIndex('idx-stock-part_id', '{{%stock}}', ['part_id'], false);
			$this->createIndex('idx-stock-warehouse_id', '{{%stock}}', ['warehouse_id'], false);
		}

		public function safeDown(){
			$this->dropIndex('uniq_idx-stock-part_id-warehouse_id', '{{%stock}}');
			$this->dropIndex('idx-stock-part_id', '{{%stock}}');
			$this->dropIndex('idx-stock-warehouse_id', '{{%stock}}');
			$this->dropTable('{{%stock}}');
		}
	}
