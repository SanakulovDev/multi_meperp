<?php
	use yii\db\Migration;

	class m190816_160000_add_delivery_term_id_column_to_contract_detail_table extends Migration{
		/**
			* {@inheritdoc}
			*/
		public function safeUp(){
			$this->addColumn('{{%contract_detail}}', 'delivery_term_id', $this->integer());
			$this->addForeignKey('frk-contract_detail-delivery_term_id', 'contract_detail', 'delivery_term_id', 'delivery_term', 'id');
		}

		/**
			* {@inheritdoc}
			*/
		public function safeDown(){
			$this->dropForeignKey('frk-contract_detail-delivery_term_id', 'contract_detail');
			$this->dropColumn('{{%contract_detail}}', 'delivery_term_id');
		}
	}
