<?php
	use yii\db\Migration;

	class m190913_171537_gtd_invoice extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable('{{%gtd_invoice}}', [
				'id' => $this->primaryKey(11),
				'gtd_id' => $this->integer(11)->notNull(),
				'invoice_id' => $this->integer(11)->notNull(),
				'amount' => $this->decimal(25)->notNull(),
				'created_by' => $this->integer(11)->notNull(),
				'created_at' => $this->integer(11)->notNull(),
				'updated_by' => $this->integer(11)->null()->defaultValue(null),
				'updated_at' => $this->integer(11)->null()->defaultValue(null),
			], $tableOptions);
			$this->createIndex('gtd_invoice', '{{%gtd_invoice}}', ['gtd_id', 'invoice_id'], true);
			$this->addForeignKey(
				'fk_gtd_invoice_created_by',
				'{{%gtd_invoice}}', 'created_by',
				'{{%user}}', 'id',
				'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey(
				'fk_gtd_invoice_gtd_id',
				'{{%gtd_invoice}}', 'gtd_id',
				'{{%gtd}}', 'id',
				'CASCADE', 'RESTRICT'
			);
			$this->addForeignKey(
				'fk_gtd_invoice_invoice_id',
				'{{%gtd_invoice}}', 'invoice_id',
				'{{%invoice}}', 'id',
				'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey(
				'fk_gtd_invoice_updated_by',
				'{{%gtd_invoice}}', 'updated_by',
				'{{%user}}', 'id',
				'RESTRICT', 'RESTRICT'
			);
		}

		public function safeDown(){
			$this->dropForeignKey('fk_gtd_invoice_created_by', '{{%gtd_invoice}}');
			$this->dropForeignKey('fk_gtd_invoice_gtd_id', '{{%gtd_invoice}}');
			$this->dropForeignKey('fk_gtd_invoice_invoice_id', '{{%gtd_invoice}}');
			$this->dropForeignKey('fk_gtd_invoice_updated_by', '{{%gtd_invoice}}');
			$this->dropTable('{{%gtd_invoice}}');
		}
	}
