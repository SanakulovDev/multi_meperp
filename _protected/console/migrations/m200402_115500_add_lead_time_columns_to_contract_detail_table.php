<?php
	use yii\db\Migration;

	class m200402_115500_add_lead_time_columns_to_contract_detail_table extends Migration{

		public function safeUp(){
			$this->addColumn(
				'{{%contract_detail}}', 'lead_time',
				$this->integer(3)
				     ->null()
				     ->defaultValue(null)
				     ->after('sub_source')
				->comment('Tayyorlash vaqti(kun)')
			);
		}

	}
