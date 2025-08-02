<?php
	use yii\db\Migration;

	class m190805_052848_document_detail_sub extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%document_detail_sub}}',
				[
					'id' => $this->primaryKey(11),
					'document_id' => $this->integer(11)->notNull(),
					'part_id' => $this->integer(11)->notNull(),
					'sub_part_id' => $this->integer(11)->notNull(),
					'qty' => $this->decimal(20, 5)->notNull(),
					'warehouse_id' => $this->integer(11)->notNull(),
				], $tableOptions
			);
			$this->createIndex('frk-document_detail_sub-document_id', '{{%document_detail_sub}}', ['document_id'], false);
			$this->createIndex('frk-document_detail_sub-part_id', '{{%document_detail_sub}}', ['part_id'], false);
			$this->createIndex('frk-document_detail_sub-sub_part_id', '{{%document_detail_sub}}', ['sub_part_id'], false);
			$this->createIndex('frk-document_detail_sub-warehouse_id', '{{%document_detail_sub}}', ['warehouse_id'], false);
		}

		public function safeDown(){
			$this->dropIndex('frk-document_detail_sub-document_id', '{{%document_detail_sub}}');
			$this->dropIndex('frk-document_detail_sub-part_id', '{{%document_detail_sub}}');
			$this->dropIndex('frk-document_detail_sub-sub_part_id', '{{%document_detail_sub}}');
			$this->dropIndex('frk-document_detail_sub-warehouse_id', '{{%document_detail_sub}}');
			$this->dropTable('{{%document_detail_sub}}');
		}
	}
