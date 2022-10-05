<?php
	use yii\db\Migration;

	class m191023_102300_production_plan_sub extends Migration{

		public function safeUp(){
			$this->createTable('production_plan_sub',[
        
          'plandate' => $this->date()->notNull(),
					'part_id' => $this->integer()->notNull(),
					'qty' => $this->decimal(20,5),
        
      ]);
      
			$this->createIndex('idx_production_plan_sub_plandate', 'production_plan_sub', 'plandate');
			$this->addForeignKey('fk_production_plan_sub_part_id', 'production_plan_sub', 'part_id','part','id');
		}

		public function safeDown(){
      
			$this->dropIndex('idx_production_plan_sub_plandate', 'production_plan_sub');
			$this->dropForeignKey('fk_production_plan_sub_part_id', 'production_plan_sub');
			$this->dropTable('production_plan_sub');
      
		}
	}
