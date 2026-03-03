<?php
	use yii\db\Migration;

	class m190913_170529_gtd extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'CHARACTER SET  utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable('{{%gtd}}', [
				'id' => $this->primaryKey(11),
				'gtd_no' => $this->string(100)->notNull(),
				'gtd_dt' => $this->date()->notNull(),
				'post_no' => $this->string(100)->null()->defaultValue(null),
				'created_by' => $this->integer(11)->null(),
				'created_at' => $this->integer(11)->null(),
				'updated_by' => $this->integer(11)->null()->defaultValue(null),
				'updated_at' => $this->integer(11)->null()->defaultValue(null),
			], $tableOptions);
			$this->createIndex('uk_gtd_no_dt', '{{%gtd}}', ['gtd_no', 'gtd_dt'], true);
			$this->addForeignKey(
				'fk_gtd_updated_by',
				'{{%gtd}}', 'updated_by',
				'{{%user}}', 'id',
				'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey(
				'fk_gtd_created_by',
				'{{%gtd}}', 'created_by',
				'{{%user}}', 'id',
				'RESTRICT', 'RESTRICT'
			);
		}

		public function safeDown(){
			$this->dropForeignKey('fk_gtd_updated_by', '{{%gtd}}');
			$this->dropForeignKey('fk_gtd_created_by', '{{%gtd}}');
			$this->dropTable('{{%gtd}}');
		}
	}
