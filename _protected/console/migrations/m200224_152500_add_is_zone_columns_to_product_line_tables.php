<?php
	use yii\db\Migration;

	class m200224_152500_add_is_zone_columns_to_product_line_tables extends Migration{

		public function safeUp(){
			$this->addColumn('{{%product_line}}', 'is_zone',
        $this->tinyInteger(1)->null()->defaultValue(null));
		}

		public function safeDown(){
			$this->dropColumn('{{%product_line}}', 'is_zone');
		}
	}
