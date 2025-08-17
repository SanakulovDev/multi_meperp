<?php
	use yii\db\Migration;

	class m190805_052850_history_document extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%history_document}}',
				[
					'id' => $this->primaryKey(11),
					'his_action' => $this->string(50)->null()->defaultValue(null),
					'his_user_id' => $this->integer(11)->null()->defaultValue(null),
					'his_date' => $this->datetime()->null()->defaultValue(null),
					'document_id' => $this->integer(11)->null()->defaultValue(null),
					'docnum' => $this->string(255)->null()->defaultValue(null),
					'docdate' => $this->date()->null()->defaultValue(null),
					'document_type_id' => $this->integer(11)->null()->defaultValue(null),
					'from_warehouse_id' => $this->integer(11)->null()->defaultValue(null),
					'to_warehouse_id' => $this->integer(11)->null()->defaultValue(null),
					'supplier' => $this->string(255)->null()->defaultValue(null),
					'series' => $this->string(255)->null()->defaultValue(null),
					'product_name' => $this->string(255)->null()->defaultValue(null),
					'status' => $this->tinyInteger(1)->null()->defaultValue(null),
					'created_by' => $this->integer(11)->null()->defaultValue(null),
					'created_at' => $this->integer(11)->null()->defaultValue(null),
					'updated_by' => $this->integer(11)->null()->defaultValue(null),
					'updated_at' => $this->integer(11)->null()->defaultValue(null),
				], $tableOptions
			);
		}

		public function safeDown(){
			$this->dropTable('{{%history_document}}');
		}
	}
