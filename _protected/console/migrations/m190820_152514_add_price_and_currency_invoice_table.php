<?php
	use yii\db\Migration;

	class m190820_152514_add_price_and_currency_invoice_table extends Migration{

		public function safeUp(){
			$this->addColumn(
				'{{%invoice}}',
				'currency_id',
				$this->integer()->notNull()->after('supplier_id')
			);
			$this->addForeignKey('fk_invoice_currency_id', 'invoice', 'currency_id', 'currency', 'id');
		}

		public function safeDown(){
			$this->dropForeignKey('fk_invoice_currency_id', 'invoice');
			$this->dropColumn('{{%invoice}}', 'currency_id');
		}

	}
