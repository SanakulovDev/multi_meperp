<?php
	use yii\db\Migration;

	class m190805_052911_production_order_defect extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%production_order_defect}}',
				[
					'id' => $this->primaryKey(11),
					'production_order_id' => $this->integer(11)->notNull(),
					'defect_id' => $this->integer(11)->notNull(),
					'qty' => $this->tinyInteger(3)->null()->defaultValue(1),
					'created_by' => $this->integer(11)->notNull(),
					'created_at' => $this->integer(11)->notNull(),
				], $tableOptions
			);
			$this->createIndex('frk-production_order_defect-production_order_id', '{{%production_order_defect}}', ['production_order_id'], false);
			$this->createIndex('frk-production_order_defect-defect_id', '{{%production_order_defect}}', ['defect_id'], false);
			$this->createIndex('frk-production_order_defect-created_by', '{{%production_order_defect}}', ['created_by'], false);
		}

		public function safeDown(){
			$this->dropIndex('frk-production_order_defect-production_order_id', '{{%production_order_defect}}');
			$this->dropIndex('frk-production_order_defect-defect_id', '{{%production_order_defect}}');
			$this->dropIndex('frk-production_order_defect-created_by', '{{%production_order_defect}}');
			$this->dropTable('{{%production_order_defect}}');
		}
	}
