<?php
	use yii\db\Migration;

	class m190819_152014_change_car_table extends Migration{

		public function safeUp(){
			$this->dropForeignKey('fk_car_created_by', 'car');
			$this->dropForeignKey('fk_car_updated_by', 'car');
			$this->addForeignKey('fk_truck_created_by',
			                     'car', 'created_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_truck_updated_by',
			                     'car', 'updated_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->renameTable('car', 'truck');
		}

		public function safeDown(){
			$this->renameTable('truck', 'car');
			$this->dropForeignKey('fk_truck_created_by', 'car');
			$this->dropForeignKey('fk_truck_updated_by', 'car');
			$this->addForeignKey('fk_car_created_by',
			                     'car', 'created_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_car_updated_by',
			                     'car', 'updated_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
		}

	}
