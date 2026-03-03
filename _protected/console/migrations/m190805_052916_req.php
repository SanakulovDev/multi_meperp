<?php
	use yii\db\Migration;

	class m190805_052916_req extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%req}}',
				[
					'id' => $this->primaryKey(11),
					'part_id' => $this->integer(11)->notNull(),
					'whbal' => $this->decimal(20, 5)->null()->defaultValue(null),
					'linebal' => $this->decimal(20, 5)->null()->defaultValue(null),
					'outsourcing' => $this->decimal(20, 5)->null()->defaultValue(null),
					'arrive' => $this->decimal(20, 5)->null()->defaultValue(null),
					'calc_at' => $this->datetime()->null()->defaultValue(null),
					'days_count' => $this->integer(11)->null()->defaultValue(null),
				], $tableOptions
			);
			$this->createIndex('part_id', '{{%req}}', ['part_id']);
		}

		public function safeDown(){
			$this->dropIndex('part_id', '{{%req}}');
			$this->dropTable('{{%req}}');
		}
	}
