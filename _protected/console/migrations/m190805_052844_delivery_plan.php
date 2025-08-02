<?php
	use yii\db\Migration;

	class m190805_052844_delivery_plan extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%delivery_plan}}',
				[
					'id' => $this->primaryKey(11),
					'product_id' => $this->integer(11)->notNull(),
					'for_date' => $this->date()->notNull(),
					'qty' => $this->integer(11)->null()->defaultValue(0),
				], $tableOptions
			);
			$this->createIndex('frk-delivery_plan-product_id', '{{%delivery_plan}}', ['product_id'], false);
		}

		public function safeDown(){
			$this->dropIndex('frk-delivery_plan-product_id', '{{%delivery_plan}}');
			$this->dropTable('{{%delivery_plan}}');
		}
	}
