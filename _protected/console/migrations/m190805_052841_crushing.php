<?php
	use yii\db\Migration;

	class m190805_052841_crushing extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%crushing}}',
				[
					'id' => $this->primaryKey(11),
					'part_id' => $this->integer(11)->notNull(),
					'qty' => $this->decimal(20, 5)->null()->defaultValue(null),
					'is_processed' => $this->tinyInteger(1)->null()->defaultValue(0),
					'created_by' => $this->integer(11)->notNull(),
					'created_at' => $this->integer(11)->notNull(),
					'updated_by' => $this->integer(11)->null()->defaultValue(null),
					'updated_at' => $this->integer(11)->null()->defaultValue(null),
				], $tableOptions
			);
			$this->createIndex('frk-crushing-part_id', '{{%crushing}}', ['part_id'], false);
			$this->createIndex('frk-crushing-created_by', '{{%crushing}}', ['created_by'], false);
			$this->createIndex('frk-crushing-updated_by', '{{%crushing}}', ['updated_by'], false);
		}

		public function safeDown(){
			$this->dropIndex('frk-crushing-part_id', '{{%crushing}}');
			$this->dropIndex('frk-crushing-created_by', '{{%crushing}}');
			$this->dropIndex('frk-crushing-updated_by', '{{%crushing}}');
			$this->dropTable('{{%crushing}}');
		}
	}
