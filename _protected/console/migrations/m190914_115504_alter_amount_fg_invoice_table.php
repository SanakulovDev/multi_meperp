<?php
	use yii\db\Migration;

	class m190914_115504_alter_amount_fg_invoice_table extends Migration{
		public function safeUp(){
			$this->dropColumn('gtd_invoice', 'amount');
			$this->addColumn('gtd_invoice', 'amount',
			                 $this->decimal(20, 5)
			                      ->notNull()
			                      ->comment('Invoice SUMMA')->after('invoice_id')
			);
		}
	}
