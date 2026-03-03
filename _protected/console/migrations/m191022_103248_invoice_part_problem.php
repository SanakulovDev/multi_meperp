<?php
	use yii\db\Migration;

	class m191022_103248_invoice_part_problem extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable('{{%invoice_part_problem}}', [
				'id' => $this->primaryKey(11),
				'inv_detail_id' => $this->integer(11)->notNull(),
				'part_order_no' => $this->string(100)->null()->defaultValue(null),
				'contract_no' => $this->string(100)->null()->defaultValue(null),
				'created_by' => $this->integer(11)->notNull(),
				'created_at' => $this->integer(11)->notNull(),
				'updated_by' => $this->integer(11)->null()->defaultValue(null),
				'updated_at' => $this->integer(11)->null()->defaultValue(null),
			], $tableOptions);
			$this->createIndex('fk_invoice_part_problem_created_by', '{{%invoice_part_problem}}', ['created_by'], false);
			$this->createIndex('frk_invoice_part_problem_updated_by', '{{%invoice_part_problem}}', ['updated_by'], false);
			$this->createIndex('frk_invoice_part_problem_inv_detail_id', '{{%invoice_part_problem}}', ['inv_detail_id'], false);
			$this->addForeignKey(
				'fk_invoice_part_problem_created_by',
				'{{%invoice_part_problem}}', 'created_by',
				'{{%user}}', 'id',
				'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey(
				'fk_invoice_part_problem_inv_detail_id',
				'{{%invoice_part_problem}}', 'inv_detail_id',
				'{{%invoice_detail}}', 'id',
				'CASCADE', 'CASCADE'
			);
			$this->addForeignKey(
				'fk_invoice_part_problem_updated_by',
				'{{%invoice_part_problem}}', 'updated_by',
				'{{%user}}', 'id',
				'RESTRICT', 'RESTRICT'
			);
		}

		public function safeDown(){
			$this->dropForeignKey('fk_invoice_part_problem_created_by', '{{%invoice_part_problem}}');
			$this->dropForeignKey('fk_invoice_part_problem_inv_detail_id', '{{%invoice_part_problem}}');
			$this->dropForeignKey('fk_invoice_part_problem_updated_by', '{{%invoice_part_problem}}');
			$this->dropTable('{{%invoice_part_problem}}');
		}
	}
