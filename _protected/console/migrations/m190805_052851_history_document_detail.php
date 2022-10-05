<?php
	use yii\db\Migration;

	class m190805_052851_history_document_detail extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%history_document_detail}}',
				[
					'id' => $this->primaryKey(11),
					'history_document_id' => $this->integer(11)->null()->defaultValue(null),
					'document_detail_id' => $this->integer(11)->null()->defaultValue(null),
					'document_id' => $this->integer(11)->null()->defaultValue(null),
					'part_id' => $this->integer(11)->null()->defaultValue(null),
					'veh' => $this->decimal(20, 5)->null()->defaultValue(null),
					'qty' => $this->decimal(20, 5)->null()->defaultValue(null),
					'price' => $this->decimal(20, 5)->null()->defaultValue(null),
					'currency' => $this->string(20)->null()->defaultValue(null),
					'remarks' => $this->string(255)->null()->defaultValue(null),
				], $tableOptions
			);
			$this->createIndex('frk-history_document_detail-history_document_id', '{{%history_document_detail}}', ['history_document_id'], false);
		}

		public function safeDown(){
			$this->dropIndex('frk-history_document_detail-history_document_id', '{{%history_document_detail}}');
			$this->dropTable('{{%history_document_detail}}');
		}
	}
