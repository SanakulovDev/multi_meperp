<?php
	use yii\db\Migration;

	class m190805_052900_part_order extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%part_order}}',
				[
					'id' => $this->primaryKey(11),
					'order_no' => $this->string(100)->notNull(),
					'iss_dt' => $this->date()->notNull()->comment('issued date'),
					'mr_dt' => $this->date()->notNull()->comment('material required date'),
					'contract_id' => $this->integer(11)->notNull(),
					'created_by' => $this->integer(11)->notNull(),
					'created_at' => $this->integer(11)->notNull(),
					'updated_by' => $this->integer(11)->null()->defaultValue(null),
					'updated_at' => $this->integer(11)->null()->defaultValue(null),
				], $tableOptions
			);
			$this->createIndex('part_order-order_no-unique', '{{%part_order}}', ['order_no'], true);
			$this->createIndex('fk-part_order-contract_id', '{{%part_order}}', ['contract_id'], false);
			$this->createIndex('fk-part_order-crt-user_id', '{{%part_order}}', ['created_by'], false);
			$this->createIndex('fk-part_order-updt-user_id', '{{%part_order}}', ['updated_by'], false);
		}

		public function safeDown(){
			$this->dropIndex('part_order-order_no-unique', '{{%part_order}}');
			$this->dropIndex('fk-part_order-contract_id', '{{%part_order}}');
			$this->dropIndex('fk-part_order-crt-user_id', '{{%part_order}}');
			$this->dropIndex('fk-part_order-updt-user_id', '{{%part_order}}');
			$this->dropTable('{{%part_order}}');
		}
	}
