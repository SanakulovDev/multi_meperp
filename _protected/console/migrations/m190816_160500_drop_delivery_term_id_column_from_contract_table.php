<?php
	use yii\db\Migration;

	class m190816_160500_drop_delivery_term_id_column_from_contract_table extends Migration{
		/**
			* {@inheritdoc}
			*/
		public function safeUp(){
			$this->dropForeignKey('fk_contract_delivery_term_id', 'contract');
			$this->dropColumn('{{%contract}}', 'delivery_term_id');
		}

		/**
			* {@inheritdoc}
			*/
		public function safeDown(){
			$this->addColumn('{{%contract}}', 'delivery_term_id', $this->integer());
			$this->addForeignKey('fk_contract_delivery_term_id', 'contract', 'delivery_term_id', 'delivery_term', 'id');
		}
	}
