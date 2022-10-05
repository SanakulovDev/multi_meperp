<?php
	use yii\db\Migration;

	/**
		* Class m190913_150104_alter_column_and_unique_invoice_table
		*/
	class m190913_150104_alter_column_and_unique_invoice_table extends Migration{
		public function safeUp(){
			$this->dropForeignKey('fk_invoice_supplier_id', 'invoice');
			$this->alterColumn('invoice', 'supplier_id', $this->integer(11)->notNull());
			$this->addForeignKey('fk_invoice_supplier_id',
			                     '{{%invoice}}', 'supplier_id',
			                     '{{%supplier}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->dropIndex('invoice_no', 'invoice');
			$this->createIndex('uk_invoice_no_supplier', '{{%invoice}}', ['invoice_no', 'supplier_id'], true);
		}
	}
