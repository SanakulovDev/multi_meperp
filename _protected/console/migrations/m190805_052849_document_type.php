<?php
	use yii\db\Migration;

	class m190805_052849_document_type extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%document_type}}',
				[
					'id' => $this->primaryKey(11),
					'code' => $this->string(3)->notNull(),
					'name' => $this->string(50)->notNull(),
					'description' => $this->text()->null()->defaultValue(null),
					'yyyy' => $this->smallInteger(6)->unsigned()->null()->defaultValue(null),
					'sequence' => $this->integer()->unsigned()->null()->defaultValue(null),
					'created_by' => $this->integer(11)->notNull(),
					'created_at' => $this->integer(11)->notNull(),
					'updated_by' => $this->integer(11)->null()->defaultValue(null),
					'updated_at' => $this->integer(11)->null()->defaultValue(null),
				], $tableOptions
			);
			$this->createIndex('code', '{{%document_type}}', ['code'], true);
			$this->createIndex('name', '{{%document_type}}', ['name'], true);
			$this->createIndex('uniq_idx-document_type-code-yyyy-seq', '{{%document_type}}', ['code', 'yyyy', 'sequence'], false);
			$this->createIndex('idx-document_type-crt-user_id', '{{%document_type}}', ['created_by'], false);
			$this->createIndex('idx-document_type-updt-user_id', '{{%document_type}}', ['updated_by'], false);
		}

		public function safeDown(){
			$this->dropIndex('code', '{{%document_type}}');
			$this->dropIndex('name', '{{%document_type}}');
			$this->dropIndex('uniq_idx-document_type-code-yyyy-seq', '{{%document_type}}');
			$this->dropIndex('idx-document_type-crt-user_id', '{{%document_type}}');
			$this->dropIndex('idx-document_type-updt-user_id', '{{%document_type}}');
			$this->dropTable('{{%document_type}}');
		}
	}
