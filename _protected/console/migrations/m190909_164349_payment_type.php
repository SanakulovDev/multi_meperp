<?php
	use yii\db\Migration;

	class m190909_164349_payment_type extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable('{{%payment_type}}', [
				'id' => $this->primaryKey(11),
				'title' => $this->string(50)->notNull(),
				'description' => $this->string(100)->notNull(),
				'created_by' => $this->integer(11)->notNull(),
				'created_at' => $this->integer(11)->notNull(),
				'updated_by' => $this->integer(11)->null()->defaultValue(null),
				'updated_at' => $this->integer(11)->null()->defaultValue(null),
			], $tableOptions);
			$this->createIndex('fk_payment_type_created_by', '{{%payment_type}}', ['created_by'], false);
			$this->createIndex('fk_payment_type_updated_by', '{{%payment_type}}', ['updated_by'], false);
			$this->addForeignKey(
				'fk_payment_type_created_by',
				'{{%payment_type}}', 'created_by',
				'{{%user}}', 'id',
				'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey(
				'fk_payment_type_updated_by',
				'{{%payment_type}}', 'updated_by',
				'{{%user}}', 'id',
				'RESTRICT', 'RESTRICT'
			);
		}

		public function safeDown(){
			$this->dropForeignKey('fk_payment_type_created_by', '{{%payment_type}}');
			$this->dropForeignKey('fk_payment_type_updated_by', '{{%payment_type}}');
			$this->dropTable('{{%payment_type}}');
		}
	}
