<?php
	use app\models\Customer;
	use yii\db\Migration;

	/**
		* Handles the creation of table `{{%customer}}`.
		*/
	class m190812_043257_create_customer_table extends Migration{
		/**
			* {@inheritdoc}
			*/
		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable('{{%customer}}', [
				'id' => $this->primaryKey(),
				'name' => $this->string(255)->notNull(),
				'duns' => $this->string(30)->null()->defaultValue(null),
				'alias' => $this->string(50)->null()->defaultValue(null),
				'address' => $this->string(255)->null()->defaultValue(null),
				'city' => $this->string(100)->null()->defaultValue(null),
				'postal' => $this->string(30)->null()->defaultValue(null),
				'country' => $this->string(100)->null()->defaultValue(null),
				'country_code' => $this->string(10)->null()->defaultValue(null),
				'contact_name' => $this->string(50)->null()->defaultValue(null),
				'contact_position' => $this->string(50)->null()->defaultValue(null),
				'contact_email' => $this->string(50)->null()->defaultValue(null),
				'contact_phone' => $this->string(50)->null()->defaultValue(null),
				'contact_cellular' => $this->string(50)->null()->defaultValue(null),
				'customer_type_id' => $this->integer(11)->notNull(),
				'vat' => $this->string(30)->null()->defaultValue(null)->comment('QQS'),  // НДС
				'tin' => $this->string(30)->null()->defaultValue(null)->comment('Taxpayer Identification Number'),  // ИНН
				'status' => $this->tinyInteger(1)->defaultValue(Customer::STATUS_ACTIVE),
				'created_by' => $this->integer(11)->notNull(),
				'created_at' => $this->integer(11)->notNull(),
				'updated_by' => $this->integer(11)->null()->defaultValue(null),
				'updated_at' => $this->integer(11)->null()->defaultValue(null),
			], $tableOptions);
			$this->createIndex('idx-customer-duns', '{{%customer}}', ['duns'], true);
			$this->createIndex('idx-customer-tin', '{{%customer}}', ['tin'], true);
			$this->createIndex('idx-customer-customer_type_id', '{{%customer}}', ['customer_type_id'], false);
			$this->createIndex('idx-customer-created_by', '{{%customer}}', ['created_by'], false);
			$this->createIndex('idx-customer-updated_by', '{{%customer}}', ['updated_by'], false);
		}

		/**
			* {@inheritdoc}
			*/
		public function safeDown(){
			$this->dropIndex('idx-customer-duns', '{{%customer}}');
			$this->dropIndex('idx-customer-tin', '{{%customer}}');
			$this->dropIndex('idx-customer-customer_type_id', '{{%customer}}');
			$this->dropIndex('idx-customer-created_by', '{{%customer}}');
			$this->dropIndex('idx-customer-updated_by', '{{%customer}}');
			$this->dropTable('{{%customer}}');
		}
	}
