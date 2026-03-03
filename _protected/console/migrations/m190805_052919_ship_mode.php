<?php
	use yii\db\Migration;

	class m190805_052919_ship_mode extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%ship_mode}}',
				[
					'id' => $this->primaryKey(11),
					'name' => $this->string(255)->null()->defaultValue(null),
					'description' => $this->string(255)->null()->defaultValue(null),
				], $tableOptions
			);
		}

		public function safeDown(){
			$this->dropTable('{{%ship_mode}}');
		}
	}
