<?php
	use yii\db\Migration;

	class m190805_052910_production_order extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%production_order}}',
				[
					'id' => $this->primaryKey(11),
					'part_id' => $this->integer(11)->notNull(),
					'current_event' => $this->char(3)->notNull()->defaultValue('100'),
					'current_seq' => $this->integer(11)->notNull(),
					'is_printed' => $this->tinyInteger(1)->notNull()->defaultValue(0),
					'quantity' => $this->integer(6)->notNull()->unsigned()->defaultValue(1),
					'created_by' => $this->integer(11)->notNull(),
					'updated_by' => $this->integer(11)->null()->defaultValue(null),
					'created_at' => $this->integer(11)->notNull(),
					'updated_at' => $this->integer(11)->null()->defaultValue(null),
					'serial_number' => $this->string(50)->null()->defaultValue(null),
				], $tableOptions
			);
			$this->createIndex('frk-production_order-part_id', '{{%production_order}}', ['part_id'], false);
			$this->createIndex('frk-production_order-created_by', '{{%production_order}}', ['created_by'], false);
			$this->createIndex('frk-production_order-updated_by', '{{%production_order}}', ['updated_by'], false);
		}

		public function safeDown(){
			$this->dropIndex('frk-production_order-part_id', '{{%production_order}}');
			$this->dropIndex('frk-production_order-created_by', '{{%production_order}}');
			$this->dropIndex('frk-production_order-updated_by', '{{%production_order}}');
			$this->dropTable('{{%production_order}}');
		}
	}
