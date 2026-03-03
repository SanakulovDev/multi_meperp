<?php
	use yii\db\Migration;

	class m190805_052904_part_type extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%part_type}}',
				[
					'id' => $this->primaryKey(11),
					'typename' => $this->string(50)->notNull(),
				], $tableOptions
			);
			$this->createIndex('typename', '{{%part_type}}', ['typename'], true);
		}

		public function safeDown(){
			$this->dropIndex('typename', '{{%part_type}}');
			$this->dropTable('{{%part_type}}');
		}
	}
