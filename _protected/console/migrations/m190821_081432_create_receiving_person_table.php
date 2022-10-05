<?php
	use yii\db\Migration;

	/**
		* Handles the creation of table `{{%receiving_person}}`.
		*/
	class m190821_081432_create_receiving_person_table extends Migration{
		/**
			* {@inheritdoc}
			*/
		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable('{{%receiving_person}}', [
				'id' => $this->primaryKey(),
				'fullname' => $this->string(100)->notNull(),
				'doc_number' => $this->string(100)->defaultValue(null),
				'doc_date' => $this->date()->defaultValue(null),
				'status' => $this->tinyInteger(1)->defaultValue(1),
				'created_by' => $this->integer(11)->notNull(),
				'created_at' => $this->integer(11)->notNull(),
				'updated_by' => $this->integer(11)->null()->defaultValue(null),
				'updated_at' => $this->integer(11)->null()->defaultValue(null),
			], $tableOptions);
			$this->createIndex('idx-receiving_person-created_by', '{{%receiving_person}}', ['created_by'], false);
			$this->createIndex('idx-receiving_person-updated_by', '{{%receiving_person}}', ['updated_by'], false);
		}

		/**
			* {@inheritdoc}
			*/
		public function safeDown(){
			$this->dropIndex('idx-receiving_person-created_by', '{{%receiving_person}}');
			$this->dropIndex('idx-receiving_person-updated_by', '{{%receiving_person}}');
			$this->dropTable('{{%receiving_person}}');
		}
	}
