<?php
	use yii\db\Migration;

	class m191205_143200_alter_vat_excise_scd_table extends Migration{
		public function safeUp(){
			
      $this->alterColumn('sales_contract_detail', 'vat', $this->decimal(20, 5));
      $this->alterColumn('sales_contract_detail', 'excise', $this->decimal(20, 5));
      
		}
	}
