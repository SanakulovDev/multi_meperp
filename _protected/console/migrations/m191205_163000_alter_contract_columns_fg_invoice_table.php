<?php
	use yii\db\Migration;

	class m191205_163000_alter_contract_columns_fg_invoice_table extends Migration{
		public function safeUp(){
			
      $this->alterColumn('fg_invoice',
                         'contract',
	      $this->string(255)
	           ->null()
	           ->defaultValue(null)
	           ->comment('Sales contract of Customer')
      );
		}
	}
