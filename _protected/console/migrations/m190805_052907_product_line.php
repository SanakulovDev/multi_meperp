<?php
	use yii\db\Migration;

	class m190805_052907_product_line extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%product_line}}',
				[
					'id' => $this->primaryKey(11),
					'linename' => $this->string(50)->notNull(),
				], $tableOptions
			);
			$this->createIndex('linename', '{{%product_line}}', ['linename'], true);
		}

		public function safeDown(){
			$this->dropIndex('linename', '{{%product_line}}');
			$this->dropTable('{{%product_line}}');
		}
	}
