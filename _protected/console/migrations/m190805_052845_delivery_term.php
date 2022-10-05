<?php
	use yii\db\Migration;

	class m190805_052845_delivery_term extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%delivery_term}}',
				[
					'id' => $this->primaryKey(11),
					'name' => $this->string(255)->notNull(),
				], $tableOptions
			);
			$this->createIndex('name', '{{%delivery_term}}', ['name'], true);
		}

		public function safeDown(){
			$this->dropIndex('name', '{{%delivery_term}}');
			$this->dropTable('{{%delivery_term}}');
		}
	}
