<?php
	use yii\db\Migration;

	class m190819_152514_add_order_type_partOrer_table extends Migration{

		public function safeUp(){
			$this->addColumn(
				'{{%part_order}}',
				'order_type',
				$this->tinyInteger(4)
				     ->after('order_no')
				     ->notNull()
				     ->defaultValue(1)
				     ->comment('1-Regular; 2-Urgent; 3-Additional;')
			);
		}

		public function safeDown(){
			$this->dropColumn('{{%part_order}}', 'order_type');
		}

	}
