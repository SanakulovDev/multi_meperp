<?php
	use yii\db\Migration;

	class m190805_052913_production_order_sub extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%production_order_sub}}',
				[
					'id' => $this->primaryKey(11),
					'production_order_id' => $this->integer(11)->notNull(),
					'sub_part_id' => $this->integer(11)->notNull(),
					'qty' => $this->decimal(20, 5)->notNull(),
					'warehouse_id' => $this->integer(11)->notNull(),
				], $tableOptions
			);
			$this->createIndex('frk-production_order_sub-production_order_id', '{{%production_order_sub}}', ['production_order_id'], false);
			$this->createIndex('frk-production_order_sub-sub_part_id', '{{%production_order_sub}}', ['sub_part_id'], false);
			$this->createIndex('frk-production_order_sub-warehouse_id', '{{%production_order_sub}}', ['warehouse_id'], false);
		}

		public function safeDown(){
			$this->dropIndex('frk-production_order_sub-production_order_id', '{{%production_order_sub}}');
			$this->dropIndex('frk-production_order_sub-sub_part_id', '{{%production_order_sub}}');
			$this->dropIndex('frk-production_order_sub-warehouse_id', '{{%production_order_sub}}');
			$this->dropTable('{{%production_order_sub}}');
		}
	}
