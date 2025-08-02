<?php
	use yii\db\Migration;

	class m191010_170000_add_delivery_term_id_column_to_part_order_table extends Migration{
		public function safeUp(){
			$this->execute("SET foreign_key_checks = 0;");
			$this->addColumn('{{%part_order}}', 'delivery_term_id', $this->integer()->notNull()->after('contract_id'));
			$this->addForeignKey('frk-part_order-delivery_term_id', 'part_order', 'delivery_term_id',
			                     'delivery_term', 'id');
			$this->execute("SET foreign_key_checks = 1;");
		}
	}
