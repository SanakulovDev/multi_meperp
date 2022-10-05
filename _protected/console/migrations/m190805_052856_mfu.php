<?php
	use yii\db\Migration;

	class m190805_052856_mfu extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%mfu}}',
				[
					'id' => $this->primaryKey(11),
					'part_id' => $this->integer(11)->notNull(),
					'average' => $this->decimal(20, 5)->null()->defaultValue(null),
					'capacity' => $this->decimal(20, 5)->null()->defaultValue(null),
					'transit_time' => $this->decimal(20, 5)->null()->defaultValue(null),
					'ship_mode_id' => $this->integer(11)->null()->defaultValue(null),
					'mfu_code' => $this->string(10)->null()->defaultValue(null),
					'contract_source_id' => $this->integer(11)->null()->defaultValue(null),
					'bank' => $this->decimal(20, 5)->null()->defaultValue(null),
					'constraint' => $this->tinyInteger(3)->null()->defaultValue(null),
					'consolidation_type_id' => $this->integer(11)->null()->defaultValue(null),
					'created_by' => $this->integer(11)->notNull(),
					'created_at' => $this->integer(11)->notNull(),
					'updated_by' => $this->integer(11)->null()->defaultValue(null),
					'updated_at' => $this->integer(11)->null()->defaultValue(null),
				], $tableOptions
			);
			$this->createIndex('part_id', '{{%mfu}}', ['part_id'], true);
			$this->createIndex('frk-mfu-track_type_id', '{{%mfu}}', ['ship_mode_id'], false);
			$this->createIndex('frk-mfu-contract_source_id', '{{%mfu}}', ['contract_source_id'], false);
			$this->createIndex('frk-mfu-consolidation_type_id', '{{%mfu}}', ['consolidation_type_id'], false);
			$this->createIndex('frk-mfu-created_by', '{{%mfu}}', ['created_by'], false);
			$this->createIndex('frk-mfu-updated_by', '{{%mfu}}', ['updated_by'], false);
		}

		public function safeDown(){
			$this->dropIndex('part_id', '{{%mfu}}');
			$this->dropIndex('frk-mfu-track_type_id', '{{%mfu}}');
			$this->dropIndex('frk-mfu-contract_source_id', '{{%mfu}}');
			$this->dropIndex('frk-mfu-consolidation_type_id', '{{%mfu}}');
			$this->dropIndex('frk-mfu-created_by', '{{%mfu}}');
			$this->dropIndex('frk-mfu-updated_by', '{{%mfu}}');
			$this->dropTable('{{%mfu}}');
		}
	}
