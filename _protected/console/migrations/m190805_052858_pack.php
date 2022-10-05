<?php
	use yii\db\Migration;

	class m190805_052858_pack extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%pack}}',
				[
					'id' => $this->primaryKey(11),
					'part_id' => $this->integer(11)->notNull(),
					'supplier_id' => $this->integer(11)->null()->defaultValue(null),
					'product_model_id' => $this->integer(11)->null()->defaultValue(null),
					'product_line_id' => $this->integer(11)->null()->defaultValue(null),
					'warehouse_id' => $this->integer(11)->null()->defaultValue(null),
					'expandable' => $this->tinyInteger(1)->null()->defaultValue(0),
					'pack_qty' => $this->decimal(20, 5)->null()->defaultValue('1.00000'),
					'piece_weight' => $this->decimal(20, 5)->null()->defaultValue('1.00000'),
					'net_weight' => $this->decimal(20, 5)->null()->defaultValue('1.00000'),
					'gross_weight' => $this->decimal(20, 5)->null()->defaultValue('1.00000'),
					'code' => $this->string(100)->null()->defaultValue(null),
					'description' => $this->string(255)->null()->defaultValue(null),
					'length' => $this->decimal(10, 2)->null()->defaultValue(null),
					'width' => $this->decimal(10, 2)->null()->defaultValue(null),
					'height' => $this->decimal(10, 2)->null()->defaultValue(null),
					'created_by' => $this->integer(11)->null()->defaultValue(null),
					'created_at' => $this->integer(11)->notNull(),
					'updated_by' => $this->integer(11)->null()->defaultValue(null),
					'updated_at' => $this->integer(11)->null()->defaultValue(null),
				], $tableOptions
			);
			$this->createIndex('frk-pack-part_id', '{{%pack}}', ['part_id'], false);
			$this->createIndex('frk-pack-supplier_id', '{{%pack}}', ['supplier_id'], false);
			$this->createIndex('frk-pack-product_model_id', '{{%pack}}', ['product_model_id'], false);
			$this->createIndex('frk-pack-product_line_id', '{{%pack}}', ['product_line_id'], false);
			$this->createIndex('frk-pack-warehouse_id', '{{%pack}}', ['warehouse_id'], false);
			$this->createIndex('frk-pack-created_by', '{{%pack}}', ['created_by'], false);
			$this->createIndex('frk-pack-updated_by', '{{%pack}}', ['updated_by'], false);
		}

		public function safeDown(){
			$this->dropIndex('frk-pack-part_id', '{{%pack}}');
			$this->dropIndex('frk-pack-supplier_id', '{{%pack}}');
			$this->dropIndex('frk-pack-product_model_id', '{{%pack}}');
			$this->dropIndex('frk-pack-product_line_id', '{{%pack}}');
			$this->dropIndex('frk-pack-warehouse_id', '{{%pack}}');
			$this->dropIndex('frk-pack-created_by', '{{%pack}}');
			$this->dropIndex('frk-pack-updated_by', '{{%pack}}');
			$this->dropTable('{{%pack}}');
		}
	}
