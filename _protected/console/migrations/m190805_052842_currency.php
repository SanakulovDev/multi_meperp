<?php
	use yii\db\Migration;

	class m190805_052842_currency extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%currency}}',
				[
					'id' => $this->primaryKey(11),
					'code' => $this->string(10)->notNull(),
					'name' => $this->string(50)->null()->defaultValue(null),
				], $tableOptions
			);
			$this->createIndex('code', '{{%currency}}', ['code'], true);
		}

		public function safeDown(){
			$this->dropIndex('code', '{{%currency}}');
			$this->dropTable('{{%currency}}');
		}
	}
