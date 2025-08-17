<?php
	use yii\db\Migration;

	class m190805_052903_part_part_wide extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%part_part_wide}}',
				[
					'id' => $this->primaryKey(11),
					'part_id' => $this->integer(11)->notNull(),
					'sub_part_id' => $this->integer(11)->notNull(),
					'usage_qty' => $this->decimal(20, 5)->notNull()->defaultValue('1.00000'),
					'warehouse_id' => $this->integer(11)->notNull(),
				], $tableOptions
			);
			$this->createIndex('frk-part_part_wide-part_id', '{{%part_part_wide}}', ['part_id'], false);
			$this->createIndex('frk-part_part_wide-sub_part_id', '{{%part_part_wide}}', ['sub_part_id'], false);
			$this->createIndex('frk-part_part_wide-warehouse_id', '{{%part_part_wide}}', ['warehouse_id'], false);
		}

		public function safeDown(){
			$this->dropIndex('frk-part_part_wide-part_id', '{{%part_part_wide}}');
			$this->dropIndex('frk-part_part_wide-sub_part_id', '{{%part_part_wide}}');
			$this->dropIndex('frk-part_part_wide-warehouse_id', '{{%part_part_wide}}');
			$this->dropTable('{{%part_part_wide}}');
		}
	}
