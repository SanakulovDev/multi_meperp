<?php
	use app\models\Factory;
	use yii\db\Migration;

	/**
		* Handles the creation of table `{{%factory}}`.
		*/
	class m190819_053214_create_factory_table extends Migration{
		/**
			* {@inheritdoc}
			*/
		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable('{{%factory}}', [
				'id' => $this->primaryKey(),
				'name' => $this->string(255)->notNull()->unique(),
				'address' => $this->string(255)->notNull(),
				'tin' => $this->string(30)->null()->defaultValue(null),
				'vat' => $this->string(30)->null()->defaultValue(null),
				'duns' => $this->string(30)->null()->defaultValue(null),
				'status' => $this->tinyInteger(1)->defaultValue(Factory::STATUS_ACTIVE),
				'created_by' => $this->integer(11)->notNull(),
				'created_at' => $this->integer(11)->notNull(),
				'updated_by' => $this->integer(11)->null()->defaultValue(null),
				'updated_at' => $this->integer(11)->null()->defaultValue(null),
			], $tableOptions);
			$this->createIndex('idx-factory-created_by', '{{%factory}}', ['created_by'], false);
			$this->createIndex('idx-factory-updated_by', '{{%factory}}', ['updated_by'], false);
		}

		/**
			* {@inheritdoc}
			*/
		public function safeDown(){
			$this->dropIndex('idx-factory-created_by', '{{%factory}}');
			$this->dropIndex('idx-factory-updated_by', '{{%factory}}');
			$this->dropTable('{{%factory}}');
		}
	}
