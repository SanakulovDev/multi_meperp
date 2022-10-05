<?php
	use yii\db\Migration;

	class m190805_052832_bom_log extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%bom_log}}',
				[
					'id' => $this->primaryKey(11),
					'fullname' => $this->string(100)->notNull(),
					'subject' => $this->string(50)->notNull(),
					'action' => $this->string(255)->notNull(),
					'comment' => $this->string(255)->null()->defaultValue(null),
					'created_at' => $this->integer(11)->notNull(),
				], $tableOptions
			);
		}

		public function safeDown(){
			$this->dropTable('{{%bom_log}}');
		}
	}
