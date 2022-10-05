<?php
	use yii\db\Migration;

	class m190805_052924_user_report extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%user_report}}',
				[
					'user_id' => $this->integer(11)->notNull(),
					'report_id' => $this->integer(11)->notNull(),
					'created_at' => $this->datetime()->null()->defaultValue(null),
				], $tableOptions
			);
			$this->createIndex('idx-user_report-report_id', '{{%user_report}}', ['report_id'], false);
			$this->createIndex('idx-user_report-user_id', '{{%user_report}}', ['user_id'], false);
			$this->addPrimaryKey('pk_on_user_report', '{{%user_report}}', ['user_id', 'report_id']);
		}

		public function safeDown(){
			$this->dropPrimaryKey('pk_on_user_report', '{{%user_report}}');
			$this->dropIndex('idx-user_report-report_id', '{{%user_report}}');
			$this->dropIndex('idx-user_report-user_id', '{{%user_report}}');
			$this->dropTable('{{%user_report}}');
		}
	}
