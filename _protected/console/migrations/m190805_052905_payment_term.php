<?php
	use yii\db\Migration;

	class m190805_052905_payment_term extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%payment_term}}',
				[
					'id' => $this->primaryKey(11),
					'name' => $this->string(255)->notNull(),
				], $tableOptions
			);
			$this->createIndex('name', '{{%payment_term}}', ['name'], true);
		}

		public function safeDown(){
			$this->dropIndex('name', '{{%payment_term}}');
			$this->dropTable('{{%payment_term}}');
		}
	}
