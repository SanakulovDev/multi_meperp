<?php
	use yii\db\Migration;

	class m200130_154500_add_regime_columns_to_container_invoice_table extends Migration{

		public function safeUp(){
			
      $this->addColumn('container_invoice','regime',$this->tinyInteger(2)->null());
      $this->addColumn('container_invoice','passed_at',$this->date()->null());
			
		}

		public function safeDown(){
			
			$this->dropColumn('container_invoice', 'regime');
			$this->dropColumn('container_invoice', 'passed_at');
		}

	}

  