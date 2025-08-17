<?php
	use yii\db\Migration;

	/**
		* Class m191010_110100_add_uniq_key_part_order_detail_table
		*/
	class m191010_110100_add_uniq_key_part_order_detail_table extends Migration{
		public function safeUp(){
			$this->createIndex('uk_pt_order_id_pt_id', '{{%part_order_detail}}', ['part_order_id', 'part_id'], true);
		}
	}
