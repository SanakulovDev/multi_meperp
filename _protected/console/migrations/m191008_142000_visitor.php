<?php
	use yii\db\Migration;

	class m191008_142000_visitor extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%visitor}}',
				[
					'id' => $this->primaryKey(11),
					'user_id' => $this->integer(11)->null(),
					'controller' => $this->string()->null(),
					'action' => $this->string()->null(),
					'user_ip' => $this->string()->null(),
					'user_agent' => $this->string()->null(),
					'user_browser' => $this->string()->null(),
					'user_browser_version' => $this->string()->null(),
					'user_platform' => $this->string()->null(),
					'user_device_type' => $this->string()->null(),
					'visited_at' => $this->datetime()->null()
				], $tableOptions
			);
			$this->addForeignKey('fk_visitor_user_id', '{{%visitor}}', 'user_id', '{{%user}}', 'id');
		}

		public function safeDown(){
			$this->dropForeignKey('fk_visitor_user_id', '{{%visitor}}');
			$this->dropTable('{{%visitor}}');
		}
	}
