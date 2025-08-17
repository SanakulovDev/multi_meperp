<?php
	use yii\db\Migration;

	class m191204_182300_alter_quantity_prod_order_table extends Migration{
		public function safeUp(){
			
      $this->alterColumn('production_order', 'quantity', 
                        $this->decimal(20, 10)
                            ->notNull()
                            ->unsigned()
                            ->defaultValue(1)
                            ->after('is_printed'));
		}
	}
