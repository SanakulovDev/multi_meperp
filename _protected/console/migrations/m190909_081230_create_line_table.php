<?php
	use yii\db\Migration;

	/**
		* Handles the creation of table `{{%line}}`.
		*/
	class m190909_081230_create_line_table extends Migration{
		/**
			* {@inheritdoc}
			*/
		public function safeUp(){
			$tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable('{{%line}}', [
				'id' => $this->primaryKey(),
				'line_name' => $this->string(100)->notNull()->unique(),
				'description' => $this->string(255)->null(),
				'parent_id' => $this->integer()->null(),  // if parent_id is null then it is a main line
				'factory_id' => $this->integer()->notNull(),
				'status' => $this->boolean()->defaultValue(1),
				'created_at' => $this->integer(11)->notNull(),
				'created_by' => $this->integer(11)->notNull(),
				'updated_at' => $this->integer(11)->null()->defaultValue(null),
				'updated_by' => $this->integer(11)->null()->defaultValue(null),
			], $tableOptions);
			$this->addForeignKey('fk_line_parent_id',
			                     '{{%line}}', 'parent_id',
			                     '{{%line}}', 'id',
			                     'SET NULL', 'CASCADE'
			);
			$this->addForeignKey('fk_line_factory_id',
			                     '{{%line}}', 'factory_id',
			                     '{{%factory}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_line_created_by',
			                     '{{%line}}', 'created_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_line_updated_by',
			                     '{{%line}}', 'updated_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
		}

		/**
			* {@inheritdoc}
			*/
		public function safeDown(){
			$this->dropForeignKey('fk_line_parent_id', '{{%line}}');
			$this->dropForeignKey('fk_line_factory_id', '{{%line}}');
			$this->dropForeignKey('fk_line_updated_by', '{{%line}}');
			$this->dropForeignKey('fk_line_created_by', '{{%line}}');
			$this->dropTable('{{%line}}');
		}
	}
