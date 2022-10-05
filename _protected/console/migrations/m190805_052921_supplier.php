<?php
	use yii\db\Migration;

	class m190805_052921_supplier extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%supplier}}',
				[
					'id' => $this->primaryKey(11),
					'name' => $this->string(100)->notNull(),
					'duns' => $this->string(30)->notNull(),
					'alias' => $this->string(50)->null()->defaultValue(null),
					'address' => $this->string(255)->null()->defaultValue(null),
					'city' => $this->string(100)->null()->defaultValue(null),
					'postal' => $this->string(30)->null()->defaultValue(null),
					'country' => $this->string(100)->null()->defaultValue(null),
					'country_code' => $this->string(30)->null()->defaultValue(null),
					'contact_name' => $this->string(50)->null()->defaultValue(null),
					'contact_position' => $this->string(50)->null()->defaultValue(null),
					'contact_email' => $this->string(50)->null()->defaultValue(null),
					'contact_phone' => $this->string(50)->null()->defaultValue(null),
					'contact_cellular' => $this->string(50)->null()->defaultValue(null),
				], $tableOptions
			);
			$this->createIndex('duns', '{{%supplier}}', ['duns'], true);
		}

		public function safeDown(){
			$this->dropIndex('duns', '{{%supplier}}');
			$this->dropTable('{{%supplier}}');
		}
	}
