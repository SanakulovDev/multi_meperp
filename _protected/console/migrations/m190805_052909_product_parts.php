<?php
	use yii\db\Migration;

	class m190805_052909_product_parts extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%product_parts}}',
				[
					'id' => $this->primaryKey(11),
					'product_id' => $this->integer(11)->notNull(),
					'part_id' => $this->integer(11)->notNull(),
					'usage_qty' => $this->decimal(20, 5)->notNull()->defaultValue('1.00000'),
					'warehouse_id' => $this->integer(11)->notNull(),
					'remark' => $this->string(255)->null()->defaultValue(null),
					'begin_at' => $this->date()->null()->defaultValue(null),
					'end_at' => $this->date()->null()->defaultValue(null),
					'status' => $this->tinyInteger(1)->notNull()->defaultValue(0),
					'created_by' => $this->integer(11)->notNull(),
					'created_at' => $this->integer(11)->notNull(),
					'updated_by' => $this->integer(11)->null()->defaultValue(null),
					'updated_at' => $this->integer(11)->null()->defaultValue(null),
				], $tableOptions
			);
			$this->createIndex('idx-product_parts-product_id_part_id', '{{%product_parts}}', ['product_id', 'part_id'], true);
			$this->createIndex('frk-product_parts-part_id', '{{%product_parts}}', ['part_id'], false);
			$this->createIndex('frk-product_parts-warehouse_id', '{{%product_parts}}', ['warehouse_id'], false);
		}

		public function safeDown(){
			$this->dropIndex('idx-product_parts-product_id_part_id', '{{%product_parts}}');
			$this->dropIndex('frk-product_parts-part_id', '{{%product_parts}}');
			$this->dropIndex('frk-product_parts-warehouse_id', '{{%product_parts}}');
			$this->dropTable('{{%product_parts}}');
		}
	}
