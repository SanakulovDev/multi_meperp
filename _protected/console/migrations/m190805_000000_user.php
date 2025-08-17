<?php
	use yii\db\Migration;

	class m190805_000000_user extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%user}}',
				[
					'id' => $this->primaryKey(11),
					'username' => $this->string(255)->notNull(),
					'tabno' => $this->string(4)->notNull(),
					'fullname' => $this->string(100)->notNull(),
					'account_suffix' => $this->string(100)->notNull(),
					'email' => $this->string(255),
					'password_hash' => $this->string(255)->notNull(),
					'status' => $this->smallInteger(6)->notNull(),
					'auth_key' => $this->string(32)->notNull(),
					'access_token' => $this->string(255)->null()->defaultValue(null),
					'password_reset_token' => $this->string(255)->null()->defaultValue(null),
					'account_activation_token' => $this->string(255)->null()->defaultValue(null),
					'created_at' => $this->integer(11)->notNull(),
					'updated_at' => $this->integer(11)->notNull(),
				], $tableOptions
			);
			$this->createIndex('username', '{{%user}}', ['username'], true);
			$this->createIndex('email', '{{%user}}', ['email'], true);
			$this->createIndex('password_reset_token', '{{%user}}', ['password_reset_token'], true);
			$this->createIndex('account_activation_token', '{{%user}}', ['account_activation_token'], true);
		}

		public function safeDown(){
			$this->dropIndex('username', '{{%user}}');
			$this->dropIndex('email', '{{%user}}');
			$this->dropIndex('password_reset_token', '{{%user}}');
			$this->dropIndex('account_activation_token', '{{%user}}');
			$this->dropTable('{{%user}}');
		}
	}
