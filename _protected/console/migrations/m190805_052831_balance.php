<?php
	use yii\db\Migration;

	class m190805_052831_balance extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%balance}}',
				[
					'id' => $this->primaryKey(11),
					'yyyy' => $this->smallInteger(6)->unsigned()->notNull(),
					'mm' => $this->smallInteger(6)->unsigned()->notNull(),
					'part_id' => $this->integer(11)->notNull(),
					'warehouse_id' => $this->integer(11)->notNull(),
					'begin_qty' => $this->decimal(20, 5)->notNull()->defaultValue('0.00000'),
					'receipt_qty' => $this->decimal(20, 5)->notNull()->defaultValue('0.00000'),
					'issue_qty' => $this->decimal(20, 5)->notNull()->defaultValue('0.00000'),
					'end_qty' => $this->decimal(20, 5)->notNull()->defaultValue('0.00000'),
					'created_by' => $this->integer(11)->notNull(),
					'created_at' => $this->integer(11)->notNull(),
				], $tableOptions
			);
			$this->createIndex('uniq_idx-balance-yyyymm-part-wh', '{{%balance}}', ['yyyy', 'mm', 'part_id', 'warehouse_id'], false);
			$this->createIndex('idx-balance-part_id', '{{%balance}}', ['part_id'], false);
			$this->createIndex('idx-balance-warehouse_id', '{{%balance}}', ['warehouse_id'], false);
			$this->createIndex('idx-balance-crt-user_id', '{{%balance}}', ['created_by'], false);
		}

		public function safeDown(){
			$this->dropIndex('uniq_idx-balance-yyyymm-part-wh', '{{%balance}}');
			$this->dropIndex('idx-balance-part_id', '{{%balance}}');
			$this->dropIndex('idx-balance-warehouse_id', '{{%balance}}');
			$this->dropIndex('idx-balance-crt-user_id', '{{%balance}}');
			$this->dropTable('{{%balance}}');
		}
	}
