<?php
	use yii\db\Migration;

	class m191009_151900_alter_contract_detail_table extends Migration{

		public function safeUp(){
			$this->addColumn(
				'{{%contract_detail}}', 'weekly_capacity', $this->decimal(20, 5)->null()
			);
			$this->addColumn(
				'{{%contract_detail}}', 'cnfea', $this->string(10)->null()
			);
		}

		public function safeDown(){
			$this->dropColumn('{{%contract_detail}}', 'weekly_capacity');
			$this->dropColumn('{{%contract_detail}}', 'cnfea');
		}

	}
