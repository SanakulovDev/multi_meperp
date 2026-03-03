<?php
	use yii\db\Migration;

	class m190828_145500_api_detail extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%api_detail}}',
				[
					'id' => $this->primaryKey(11),
					'api_id' => $this->integer(11)->notNull(),
					'part_id' => $this->integer(11)->notNull(),
					'inventory_qty' => $this->decimal(20, 5)->notNull(),
					'stock_qty' => $this->decimal(20, 5),
				], $tableOptions
			);
			$this->createIndex('ind-api_detail-api_id-part_id', '{{%api_detail}}', ['api_id', 'part_id'], true);
			$this->addForeignKey('frk-api_detail-api_id', '{{%api_detail}}', ['api_id'], 'api', 'id', 'cascade', 'cascade');
			$this->addForeignKey('frk-api_detail-part_id', '{{%api_detail}}', ['part_id'], 'part', 'id');
		}

		public function safeDown(){
			$this->dropIndex('ind-api_detail-api_id-part_id', '{{%api_detail}}');
			$this->dropForeignKey('frk-api_detail-api_id', '{{%api_detail}}');
			$this->dropForeignKey('frk-api_detail-part_id', '{{%api_detail}}');
			$this->dropTable('{{%api_detail}}');
		}
	}
