<?php
	use yii\db\Migration;

	class m191010_133300_add_sub_source_contract_detail_table extends Migration{

		public function safeUp(){
			$this->addColumn(
				'{{%contract_detail}}', 'sub_source', $this->tinyInteger(2)->null()
			);
		}

		public function safeDown(){
			$this->dropColumn('{{%contract_detail}}', 'sub_source');
		}

	}
