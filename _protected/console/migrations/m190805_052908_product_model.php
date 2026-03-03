<?php
	use yii\db\Migration;

	class m190805_052908_product_model extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
			$this->createTable(
				'{{%product_model}}',
				[
					'id' => $this->primaryKey(11),
					'modelname' => $this->string(50)->notNull(),
				], $tableOptions
			);
			$this->createIndex('modelname', '{{%product_model}}', ['modelname'], true);

      $authManager = Yii::$app->getAuthManager();
      Yii::$app->db->createCommand(
        "INSERT IGNORE `auth_item`(`name`, `type`) 
				VALUES('part-mark-index',2),
				('part-mark-create',2),
				('part-mark-update',2),
				('part-mark-delete',2),
				('part-mark-xls',2)"
      )->execute();
      Yii::$app->db->createCommand(
        "INSERT IGNORE `auth_item_child`(`parent`, `child`) 
				VALUES	
				('admin', 'part-mark-index'),
				('admin', 'part-mark-create'),
				('admin', 'part-mark-update'),
				('admin', 'part-mark-delete'),
				('admin', 'part-mark-xls'),
				
				('superadmin', 'part-mark-index'),
				('superadmin', 'part-mark-create'),
				('superadmin', 'part-mark-update'),
				('superadmin', 'part-mark-delete'),
				('superadmin', 'part-mark-xls')"
      )->execute();
      $authManager->invalidateCache();

		}

		public function safeDown(){
			$this->dropIndex('modelname', '{{%product_model}}');
			$this->dropTable('{{%product_model}}');

      $authManager = Yii::$app->getAuthManager();
      Yii::$app->db->createCommand(
        "DELETE FROM `auth_item` WHERE `name` IN ('part-color-index','part-color-create','part-color-update','part-color-delete','part-color-xls')"
      )->execute();
      $authManager->invalidateCache();
		}
	}
