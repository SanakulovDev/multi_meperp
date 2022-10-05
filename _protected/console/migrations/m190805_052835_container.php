<?php
	use yii\db\Migration;

	class m190805_052835_container extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%container}}',
				[
					'id' => $this->primaryKey(11),
					'container_no' => $this->string(255)->notNull(),
					'created_by' => $this->integer(11)->notNull(),
					'created_at' => $this->integer(11)->notNull(),
					'updated_by' => $this->integer(11)->null()->defaultValue(null),
					'updated_at' => $this->integer(11)->null()->defaultValue(null),
				], $tableOptions
			);
			$this->createIndex('container_no', '{{%container}}', ['container_no'], true);
			$this->createIndex('idx-container-created_by', '{{%container}}', ['created_by'], false);
			$this->createIndex('idx-container-updated_by', '{{%container}}', ['updated_by'], false);
		}

		public function safeDown(){
			$this->dropIndex('container_no', '{{%container}}');
			$this->dropIndex('idx-container-created_by', '{{%container}}');
			$this->dropIndex('idx-container-updated_by', '{{%container}}');
			$this->dropTable('{{%container}}');
		}
	}
