<?php
	use yii\db\Migration;

	class m190805_052914_production_plan extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%production_plan}}',
				[
					'id' => $this->primaryKey(11)->unsigned(),
					'part_id' => $this->integer(10)->notNull()->comment('Part; semi;FG'),
					'production_date' => $this->date()->notNull(),
					'warehouse_id' => $this->integer(11)->notNull()->comment('location'),
					'shift' => $this->tinyInteger(1)->unsigned()->notNull()->comment('smena'),
					'target_qty' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue(0),
				], $tableOptions
			);
			$this->createIndex('part_wh_shift_dt', '{{%production_plan}}', ['part_id', 'production_date', 'warehouse_id', 'shift'], true);
			$this->createIndex('fk_production_plan_part_id', '{{%production_plan}}', ['part_id'], false);
			$this->createIndex('fk_production_plan_warehouse_id', '{{%production_plan}}', ['warehouse_id'], false);
		}

		public function safeDown(){
			$this->dropIndex('part_wh_shift_dt', '{{%production_plan}}');
			$this->dropIndex('fk_production_plan_part_id', '{{%production_plan}}');
			$this->dropIndex('fk_production_plan_warehouse_id', '{{%production_plan}}');
			$this->dropTable('{{%production_plan}}');
		}
	}
