<?php
	use yii\db\Migration;

	class m190805_052847_document_detail extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%document_detail}}',
				[
					'id' => $this->primaryKey(11),
					'document_id' => $this->integer(11)->notNull(),
					'part_id' => $this->integer(11)->notNull(),
					'veh' => $this->decimal(20, 5)->null()->defaultValue(null),
					'qty' => $this->decimal(20, 5)->notNull(),
					'price' => $this->decimal(20, 5)->null()->defaultValue(null),
					'currency' => $this->string(20)->null()->defaultValue(null),
					'remarks' => $this->string(255)->null()->defaultValue(null),
					'sub' => $this->integer(11)->null()->defaultValue(null),
				], $tableOptions
			);
			$this->createIndex('frk-document_detail-document_id', '{{%document_detail}}', ['document_id'], false);
			$this->createIndex('frk-document_detail-part_id', '{{%document_detail}}', ['part_id'], false);
		}

		public function safeDown(){
			$this->dropIndex('frk-document_detail-document_id', '{{%document_detail}}');
			$this->dropIndex('frk-document_detail-part_id', '{{%document_detail}}');
			$this->dropTable('{{%document_detail}}');
		}
	}
