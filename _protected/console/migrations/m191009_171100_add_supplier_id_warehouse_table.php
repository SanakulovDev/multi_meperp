<?php
	use yii\db\Migration;

	class m191009_171100_add_supplier_id_warehouse_table extends Migration{

		public function safeUp(){
			$this->addColumn(
				'{{%warehouse}}',
				'supplier_id',
				$this->integer()->null()->after('warehouse_type')
			);
			$this->addForeignKey('frk-warehouse_supplier_id', 'warehouse', 'supplier_id', 'supplier', 'id');
		}

		public function safeDown(){
			$this->dropForeignKey('frk-warehouse_supplier_id', 'warehouse');
			$this->dropColumn('{{%warehouse}}', 'supplier_id');
		}

	}
