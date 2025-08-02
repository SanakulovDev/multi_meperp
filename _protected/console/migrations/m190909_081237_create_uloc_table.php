<?php
	use yii\db\Migration;

	/**
		* Handles the creation of table `{{%uloc}}`.
		*/
	class m190909_081237_create_uloc_table extends Migration{
		/**
			* {@inheritdoc}
			*/
		public function safeUp(){
			$tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable('{{%uloc}}', [
				'id' => $this->primaryKey(),
				'title' => $this->string(50)->notNull()->unique(),
				'description' => $this->string(255)->null(),
				'line_id' => $this->integer(),
				'min_stock' => $this->integer()->defaultValue(1),
				'max_stock' => $this->integer()->null(),
				'actual_stock' => $this->integer()->null(),
				'status' => $this->boolean()->defaultValue(1),
				'created_at' => $this->integer(11)->notNull(),
				'created_by' => $this->integer(11)->notNull(),
				'updated_at' => $this->integer(11)->null()->defaultValue(null),
				'updated_by' => $this->integer(11)->null()->defaultValue(null),
			], $tableOptions);
			$this->addForeignKey('fk_uloc_line_id',
			                     '{{%uloc}}', 'line_id',
			                     '{{%line}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_uloc_created_by',
			                     '{{%uloc}}', 'created_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_uloc_updated_by',
			                     '{{%uloc}}', 'updated_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
		}

		/**
			* {@inheritdoc}
			*/
		public function safeDown(){
			$this->dropForeignKey('fk_uloc_line_id', '{{%uloc}}');
			$this->dropForeignKey('fk_uloc_updated_by', '{{%uloc}}');
			$this->dropForeignKey('fk_uloc_created_by', '{{%uloc}}');
			$this->dropTable('{{%uloc}}');
		}
	}
