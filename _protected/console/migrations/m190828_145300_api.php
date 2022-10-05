<?php
	use yii\db\Migration;

	class m190828_145300_api extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%api}}',
				[
					'id' => $this->primaryKey(11),
					'inventory_date' => $this->date()->notNull(),
					'stock_date' => $this->date(),
					'created_by' => $this->integer(11)->notNull(),
					'created_at' => $this->integer(11)->notNull(),
				], $tableOptions
			);
			$this->addForeignKey('frk-api-created_by', '{{%api}}', ['created_by'], 'user', 'id');
		}

		public function safeDown(){
			$this->dropForeignKey('frk-api-created_by', '{{%api}}');
			$this->dropTable('{{%api}}');
		}
	}
