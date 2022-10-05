<?php
	use yii\db\Migration;

	class m200406_132000_add_is_primary_price_column_to_contract_detail_table extends Migration{

		public function safeUp(){
			$this->addColumn(
				'{{%contract_detail}}', 'is_primary_price',
				$this->tinyInteger(1)
				     ->notNull()
				     ->defaultValue(0)
				     ->comment('Values: 0 or 1, Default: 0')
			);
		}

	}
