<?php
	use yii\db\Migration;

	class m200330_174846_part_part_version extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'ENGINE=InnoDB';
			$this->createTable(
				'{{part_part_version}}',
				[
					'id' => $this->primaryKey(10)->unsigned(),
					'version' => $this->integer(10)->unsigned()->notNull(),
					'action' => $this->string(1)->notNull()->comment('[+] - Add, [-] - remove'),
					'part_id' => $this->integer(11)->notNull(),
					'sub_part_id' => $this->integer(11)->notNull(),
					'usage_qty' => $this->decimal(25, 10)->notNull(),
					'warehouse_id' => $this->integer(11)->notNull(),
					'remark' => $this->string(255)->null()->defaultValue(null),
					'status' => $this->tinyInteger(1)->notNull(),
					'created_by' => $this->integer(11)->notNull(),
					'created_at' => $this->integer(11)->notNull(),
					'updated_by' => $this->integer(11)->null()->defaultValue(null),
					'updated_at' => $this->integer(11)->null()->defaultValue(null),
				], $tableOptions
			);
		}

		public function safeDown(){
			$this->dropTable('{{part_part_version}}');
		}
	}
