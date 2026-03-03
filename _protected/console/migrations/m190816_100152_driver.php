<?php
	use yii\db\Migration;

	class m190816_100152_driver extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable('{{%driver}}', [
				'id' => $this->primaryKey(11),
				'first_name' => $this->string(50)->notNull(),
				'last_name' => $this->string(50)->notNull(),
				'middle_name' => $this->string(50)->notNull(),
				'emp_no' => $this->string(10)->null()->defaultValue(null),
				'created_by' => $this->integer(11)->notNull(),
				'created_at' => $this->integer(11)->notNull(),
				'updated_by' => $this->integer(11)->null()->defaultValue(null),
				'updated_at' => $this->integer(11)->null()->defaultValue(null),
			], $tableOptions);
			$this->createIndex('emp_no', '{{%driver}}', ['emp_no'], true);
			$this->addForeignKey(
				'fk_driver_created_by',
				'{{%driver}}', 'created_by',
				'{{%user}}', 'id',
				'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey(
				'fk_driver_updated_by',
				'{{%driver}}', 'updated_by',
				'{{%user}}', 'id',
				'RESTRICT', 'RESTRICT'
			);
		}

		public function safeDown(){
			$this->dropForeignKey('fk_driver_created_by', '{{%driver}}');
			$this->dropForeignKey('fk_driver_updated_by', '{{%driver}}');
			$this->dropTable('{{%driver}}');
		}
	}
