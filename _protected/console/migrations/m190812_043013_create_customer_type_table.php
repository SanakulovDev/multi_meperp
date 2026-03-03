<?php
	use app\models\CustomerType;
	use yii\db\Migration;

	/**
		* Handles the creation of table `{{%customer_type}}`.
		*/
	class m190812_043013_create_customer_type_table extends Migration{
		/**
			* {@inheritdoc}
			*/
		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable('{{%customer_type}}', [
				'id' => $this->primaryKey(),
				'name' => $this->string(50)->notNull(),
				'description' => $this->string(255)->null(),
				'status' => $this->tinyInteger(1)->notNull()->defaultValue(CustomerType::STATUS_ACTIVE),
				'created_by' => $this->integer(11)->notNull(),
				'created_at' => $this->integer(11)->notNull(),
				'updated_by' => $this->integer(11)->null()->defaultValue(null),
				'updated_at' => $this->integer(11)->null()->defaultValue(null),
			], $tableOptions);
			$this->createIndex('idx-customer_type-name', '{{%customer_type}}', ['name'], true);
			$this->createIndex('idx-customer_type-created_by', '{{%customer_type}}', ['created_by'], false);
			$this->createIndex('idx-customer_type-updated_by', '{{%customer_type}}', ['updated_by'], false);
		}

		/**
			* {@inheritdoc}
			*/
		public function safeDown(){
			$this->dropIndex('idx-customer_type-name', '{{%customer_type}}');
			$this->dropIndex('idx-customer_type-created_by', '{{%customer_type}}');
			$this->dropIndex('idx-customer_type-updated_by', '{{%customer_type}}');
			$this->dropTable('{{%customer_type}}');
		}
	}
