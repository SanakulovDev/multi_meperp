<?php
	use yii\db\Migration;

	class m190805_052901_part_order_detail extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%part_order_detail}}',
				[
					'id' => $this->primaryKey(11),
					'part_order_id' => $this->integer(11)->notNull(),
					'part_id' => $this->integer(11)->notNull(),
					'qty' => $this->integer(11)->notNull()->comment('Qty'),
					'exwrk_plan' => $this->date()->null()->defaultValue(null),
					'exwrk_actual' => $this->date()->null()->defaultValue(null),
					'created_by' => $this->integer(11)->notNull(),
					'created_at' => $this->integer(11)->notNull(),
					'updated_by' => $this->integer(11)->null()->defaultValue(null),
					'updated_at' => $this->integer(11)->null()->defaultValue(null),
				], $tableOptions
			);
			$this->createIndex('fk-part_order_detail-part_order_id', '{{%part_order_detail}}', ['part_order_id'], false);
			$this->createIndex('fk-part_order_detail-part_id', '{{%part_order_detail}}', ['part_id'], false);
			$this->createIndex('fk-part_order_detail-crt-user_id', '{{%part_order_detail}}', ['created_by'], false);
			$this->createIndex('fk-part_order_detail-updt-user_id', '{{%part_order_detail}}', ['updated_by'], false);
		}

		public function safeDown(){
			$this->dropIndex('fk-part_order_detail-part_order_id', '{{%part_order_detail}}');
			$this->dropIndex('fk-part_order_detail-part_id', '{{%part_order_detail}}');
			$this->dropIndex('fk-part_order_detail-crt-user_id', '{{%part_order_detail}}');
			$this->dropIndex('fk-part_order_detail-updt-user_id', '{{%part_order_detail}}');
			$this->dropTable('{{%part_order_detail}}');
		}
	}
