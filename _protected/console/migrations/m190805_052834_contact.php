<?php
	use yii\db\Migration;

	class m190805_052834_contact extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%contact}}',
				[
					'id' => $this->primaryKey(11),
					'name' => $this->string(255)->notNull(),
					'functionality' => $this->string(255)->null()->defaultValue(null),
					'department' => $this->string(255)->null()->defaultValue(null),
					'team' => $this->string(100)->null()->defaultValue(null),
					'responsibility' => $this->string(255)->null()->defaultValue(null),
					'mrp_code' => $this->string(100)->null()->defaultValue(null),
					'office_phone' => $this->string(50)->null()->defaultValue(null),
					'mobile_phone' => $this->string(50)->null()->defaultValue(null),
					'email' => $this->string(50)->null()->defaultValue(null),
					'mfu_code' => $this->string(50)->null()->defaultValue(null),
				], $tableOptions
			);
		}

		public function safeDown(){
			$this->dropTable('{{%contact}}');
		}
	}
