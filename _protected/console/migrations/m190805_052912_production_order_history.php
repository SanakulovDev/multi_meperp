<?php
	use yii\db\Migration;

	class m190805_052912_production_order_history extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%production_order_history}}',
				[
					'id' => $this->primaryKey(11),
					'production_order_id' => $this->integer(11)->notNull(),
					'current_event' => $this->char(3)->notNull()->defaultValue('100'),
					'station' => $this->string(50)->null()->defaultValue(null),
					'created_by' => $this->integer(11)->notNull(),
					'created_at' => $this->integer(11)->notNull(),
				], $tableOptions
			);
			$this->createIndex('frk-production_order_history-production_order_id', '{{%production_order_history}}', ['production_order_id'], false);
			$this->createIndex('frk-production_order_history-created_by', '{{%production_order_history}}', ['created_by'], false);
		}

		public function safeDown(){
			$this->dropIndex('frk-production_order_history-production_order_id', '{{%production_order_history}}');
			$this->dropIndex('frk-production_order_history-created_by', '{{%production_order_history}}');
			$this->dropTable('{{%production_order_history}}');
		}
	}
