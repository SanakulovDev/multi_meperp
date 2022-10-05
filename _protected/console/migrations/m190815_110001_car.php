<?php
	use yii\db\Migration;

	class m190815_110001_car extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'car',
				[
					'id' => $this->primaryKey(11),
					'model' => $this->string(50)->notNull(),
					'number' => $this->string(20)->notNull(),
					'created_by' => $this->integer(11)->notNull(),
					'created_at' => $this->integer(11)->notNull(),
					'updated_by' => $this->integer(11)->null()->defaultValue(null),
					'updated_at' => $this->integer(11)->null()->defaultValue(null),
				], $tableOptions
			);
			$this->createIndex('car_model_number', 'car', ['model', 'number'], true);
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

		public function Down(){
			$this->dropIndex('car_model_number', 'car');
			$this->dropForeignKey('fk_car_created_by', 'car');
			$this->dropForeignKey('fk_car_updated_by', 'car');
			$this->dropTable('car');
		}
	}
