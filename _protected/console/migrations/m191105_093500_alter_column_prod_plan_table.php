<?php
	use yii\db\Migration;

	/**
		* Class m190914_115504_alter_column_fg_invoice_table
		*/
	class m191105_093500_alter_column_prod_plan_table extends Migration{
		public function safeUp(){

			$this->alterColumn('production_plan', 'target_qty',
			                 $this->integer(10)->unsigned()->notNull()->defaultValue(0)
			);

		}
	}
