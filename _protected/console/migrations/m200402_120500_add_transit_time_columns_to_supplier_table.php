<?php
	use yii\db\Migration;

	class m200402_120500_add_transit_time_columns_to_supplier_table extends Migration{

		public function safeUp(){
			$this->addColumn(
				'{{%supplier}}', 'transit_time',
				$this->integer(3)
				     ->null()
				     ->defaultValue(null)
				     ->after('postal')
				     ->comment('vaqt(soat) birligida)')
			);
		}

	}
