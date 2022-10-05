<?php
	use yii\db\Migration;

	class m191024_170000_add_warehouse_report_group_id_column_to_warehouse_table extends Migration{
		public function safeUp(){
			$this->execute("SET foreign_key_checks = 0;");

			$this->addColumn('{{%warehouse}}',
			                 'warehouse_report_group_id',
			                 $this->integer()->notNull()->after('warehouse_type')
			);
			$this->addForeignKey('frk-warehouse-warehouse_report_group_id',
			                     'warehouse',
			                     'warehouse_report_group_id',
			                     'warehouse_report_group',
			                     'id',
			                     'CASCADE','CASCADE');

			$this->execute("SET foreign_key_checks = 1;");
		}

	}
