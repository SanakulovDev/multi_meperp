<?php
	use yii\db\Migration;

	class m191127_155100_add_additional_columns_to_report_table extends Migration{

		public function safeUp(){
			
      $this->addColumn('report','list_order',$this->integer()->null());
      $this->addColumn('report','style',$this->string()->null());
			
		}

		public function safeDown(){
			
			$this->dropColumn('report', 'list_order');
			$this->dropColumn('report', 'style');
		}

	}
