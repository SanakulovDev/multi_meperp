<?php
	use yii\db\Migration;

	class m190805_052926_warehouse extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%warehouse}}',
				[
					'id' => $this->primaryKey(11),
					'name' => $this->string(50)->notNull(),
					'description' => $this->string(255)->null()->defaultValue(null),
					'status' => $this->integer(11)->notNull(),
					'warehouse_type' => $this->tinyInteger(3)->null()->defaultValue(0),
					'created_by' => $this->integer(11)->notNull(),
					'created_at' => $this->integer(11)->notNull(),
					'updated_by' => $this->integer(11)->null()->defaultValue(null),
					'updated_at' => $this->integer(11)->null()->defaultValue(null),
				], $tableOptions
			);
			$this->createIndex('name', '{{%warehouse}}', ['name'], true);
			$this->createIndex('idx-wh-crt-user_id', '{{%warehouse}}', ['created_by'], false);
			$this->createIndex('idx-wh-updt-user_id', '{{%warehouse}}', ['updated_by'], false);
		}

		public function safeDown(){
			$this->dropIndex('name', '{{%warehouse}}');
			$this->dropIndex('idx-wh-crt-user_id', '{{%warehouse}}');
			$this->dropIndex('idx-wh-updt-user_id', '{{%warehouse}}');
			$this->dropTable('{{%warehouse}}');
		}
	}
