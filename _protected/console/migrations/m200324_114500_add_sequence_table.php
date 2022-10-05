<?php
	use yii\db\Migration;

	class m200324_114500_add_sequence_table extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'ENGINE=InnoDB';

			$this->createTable('{{%sequence_type}}', [
				'id' => $this->primaryKey(10)->unsigned(),
				'name' => $this->string(30)->notNull(),
				'description' => $this->string(250)->notNull(),
			], $tableOptions);
			$this->addCommentOnTable('{{%sequence_type}}', 'Sequence type');
			$this->createIndex('name', '{{%sequence_type}}', ['name'], true);

			$this->createTable('{{%sequence}}', [
				'id' => $this->primaryKey(10)->unsigned(),
				'sequence_type_id' => $this->integer(10)->unsigned()->notNull(),
				'last_seq' => $this->integer(6)->unsigned()->notNull(),
			], $tableOptions);
			$this->addCommentOnTable('{{%sequence}}', 'Last sequence');
			$this->createIndex('seq_type', '{{%sequence}}', ['sequence_type_id'], true);
			$this->addForeignKey(
				'fk_sequence_sequence_type_id',
				'{{%sequence}}', 'sequence_type_id',
				'{{%sequence_type}}', 'id',
				'RESTRICT', 'CASCADE'
			);

		}

		public function safeDown(){
			$this->dropForeignKey('fk_sequence_sequence_type_id', '{{%sequence}}');
			$this->dropTable('{{%sequence}}');
			$this->dropTable('{{%sequence_type}}');
		}
	}