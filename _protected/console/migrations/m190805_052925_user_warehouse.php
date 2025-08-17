<?php
	use yii\db\Migration;

	class m190805_052925_user_warehouse extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%user_warehouse}}',
				[
					'user_id' => $this->integer(11)->notNull(),
					'warehouse_id' => $this->integer(11)->notNull(),
				], $tableOptions
			);
			$this->createIndex('fk-user_warehouse-warehouse_id', '{{%user_warehouse}}', ['warehouse_id'], false);
			$this->addPrimaryKey('pk_on_user_warehouse', '{{%user_warehouse}}', ['user_id', 'warehouse_id']);
		}

		public function safeDown(){
			$this->dropPrimaryKey('pk_on_user_warehouse', '{{%user_warehouse}}');
			$this->dropIndex('fk-user_warehouse-warehouse_id', '{{%user_warehouse}}');
			$this->dropTable('{{%user_warehouse}}');
		}
	}
