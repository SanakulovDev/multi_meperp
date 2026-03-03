<?php
	use yii\db\Migration;

	class m191024_152051_warehouse_report_group extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable('{{%warehouse_report_group}}', [
				'id' => $this->primaryKey(11),
				'title' => $this->string(255)->notNull(),
				'description' => $this->string(255)->null()->defaultValue(null),
				'created_by' => $this->integer(11)->notNull(),
				'created_at' => $this->integer(11)->notNull(),
				'updated_by' => $this->integer(11)->null()->defaultValue(null),
				'updated_at' => $this->integer(11)->null()->defaultValue(null),
			], $tableOptions);
			$this->createIndex('title', '{{%warehouse_report_group}}', ['title'], true);
			$this->createIndex('fk_warehouse_report_group_created_by', '{{%warehouse_report_group}}', ['created_by'], false);
			$this->createIndex('fk_warehouse_report_group_updated_by', '{{%warehouse_report_group}}', ['updated_by'], false);
			$this->addForeignKey(
				'fk_warehouse_report_group_created_by',
				'{{%warehouse_report_group}}', 'created_by',
				'{{%user}}', 'id',
				'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey(
				'fk_warehouse_report_group_updated_by',
				'{{%warehouse_report_group}}', 'updated_by',
				'{{%user}}', 'id',
				'RESTRICT', 'RESTRICT'
			);
		}

		public function safeDown(){
			$this->dropForeignKey('fk_warehouse_report_group_created_by', '{{%warehouse_report_group}}');
			$this->dropForeignKey('fk_warehouse_report_group_updated_by', '{{%warehouse_report_group}}');
			$this->dropTable('{{%warehouse_report_group}}');
		}
	}
