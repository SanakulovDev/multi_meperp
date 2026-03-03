<?php
	use yii\db\Migration;

	class m190805_052859_part extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%part}}',
				[
					'id' => $this->primaryKey(11),
					'state' => $this->tinyInteger(1)->notNull()->comment('0-raw material, 1-semi, 2-FG'),
					'part_no' => $this->string(50)->notNull(),
					'unit_id' => $this->integer(11)->null()->defaultValue(null),
					'part_name' => $this->string(255)->null()->defaultValue(null),
					'part_color' => $this->string(50)->null(),
					'part_type_id' => $this->integer(11)->null()->defaultValue(0),
					'status' => $this->integer(11)->notNull(),
					'contract_source_id' => $this->integer(11)->null()->defaultValue(null),
					'pack_size' => $this->string(255)->null()->defaultValue(null),
					'warehouse_id' => $this->integer(11)->null()->defaultValue(null),
//					'fg_warehouse_id' => $this->integer(11)->null()->defaultValue(null),
//					'side' => $this->char(2)->null()->defaultValue(null),
//					'is_bulk' => $this->tinyInteger(1)->null()->defaultValue(0),
					'remark' => $this->string(255)->null()->defaultValue(null),
					'created_by' => $this->integer(11)->notNull(),
					'created_at' => $this->integer(11)->notNull(),
					'updated_by' => $this->integer(11)->null()->defaultValue(null),
					'updated_at' => $this->integer(11)->null()->defaultValue(null),
				], $tableOptions
			);
      
			$this->createIndex('part_no', '{{%part}}', ['part_no'], true);
			$this->createIndex('idx-pt-crt-user_id', '{{%part}}', ['created_by'], false);
			$this->createIndex('idx-pt-updt-user_id', '{{%part}}', ['updated_by'], false);
			$this->createIndex('idx-pt-unit_id', '{{%part}}', ['unit_id'], false);
			$this->createIndex('frk-part-contract_source_id', '{{%part}}', ['contract_source_id'], false);
			$this->createIndex('frk-part-warehouse_id', '{{%part}}', ['warehouse_id'], false);
		}

		public function safeDown(){
			$this->dropIndex('part_no', '{{%part}}');
			$this->dropIndex('idx-pt-crt-user_id', '{{%part}}');
			$this->dropIndex('idx-pt-updt-user_id', '{{%part}}');
			$this->dropIndex('idx-pt-unit_id', '{{%part}}');
			$this->dropIndex('frk-part-contract_source_id', '{{%part}}');
			$this->dropIndex('frk-part-warehouse_id', '{{%part}}');
			$this->dropTable('{{%part}}');
		}
	}
