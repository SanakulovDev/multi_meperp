<?php
	use yii\db\Migration;

	class m190805_052843_defect extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%defect}}',
				[
					'id' => $this->primaryKey(11),
					'code' => $this->string(50)->notNull(),
					'description' => $this->string(255)->null()->defaultValue(null),
				], $tableOptions
			);
			$this->createIndex('code', '{{%defect}}', ['code'], true);
		}

		public function safeDown(){
			$this->dropIndex('code', '{{%defect}}');
			$this->dropTable('{{%defect}}');
		}
	}
