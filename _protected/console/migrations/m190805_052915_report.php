<?php
	use yii\db\Migration;

	class m190805_052915_report extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%report}}',
				[
					'id' => $this->primaryKey(11),
					'action' => $this->string(50)->notNull(),
					'title' => $this->string(255)->null()->defaultValue(null),
					'description' => $this->string(255)->null()->defaultValue(null),
					'created_by' => $this->integer(11)->notNull(),
					'created_at' => $this->integer(11)->notNull(),
					'updated_by' => $this->integer(11)->null()->defaultValue(null),
					'updated_at' => $this->integer(11)->null()->defaultValue(null),
				], $tableOptions
			);
			$this->createIndex('action', '{{%report}}', ['action'], true);
			$this->createIndex('idx-report-updt-user_id', '{{%report}}', ['updated_by'], false);
			$this->createIndex('idx-report-crt-user_id', '{{%report}}', ['created_by'], false);
		}

		public function safeDown(){
			$this->dropIndex('action', '{{%report}}');
			$this->dropIndex('idx-report-updt-user_id', '{{%report}}');
			$this->dropIndex('idx-report-crt-user_id', '{{%report}}');
			$this->dropTable('{{%report}}');
		}
	}
