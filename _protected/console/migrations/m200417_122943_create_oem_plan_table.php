<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%oem_plan}}`.
 */
class m200417_122943_create_oem_plan_table extends Migration {
	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
		$this->createTable('{{%oem_plan}}', [
			'id' => $this->primaryKey(),
			'model_id' => $this->integer(11)->notNull(),
			'target_date' => $this->date()->notNull(),
			'quantity' => $this->integer()->notNull()
        ], $tableOptions);
        
        // create permission and assignments
        Yii::$app->db->createCommand(
			"INSERT IGNORE `auth_item`(`name`, `type`) 
                      VALUES 
                      ('oem-plan-create',2),
                      ('oem-plan-delete',2),
                      ('oem-plan-index',2),
                      ('oem-plan-update',2),
                      ('oem-plan-upload',2),
                      ('oem-plan-xls',2);

            INSERT IGNORE `auth_item_child`(`parent`, `child`) 
                      VALUES 
                      ('superadmin','oem-plan-create'),
                      ('superadmin','oem-plan-delete'),
                      ('superadmin','oem-plan-index'),
                      ('superadmin','oem-plan-update'),
                      ('superadmin','oem-plan-upload'),
                      ('superadmin','oem-plan-xls'),
                      ('admin','oem-plan-create'),
                      ('admin','oem-plan-delete'),
                      ('admin','oem-plan-index'),
                      ('admin','oem-plan-update'),
                      ('admin','oem-plan-upload'),
                      ('admin','oem-plan-xls'),
                      ('plan','oem-plan-create'),
                      ('plan','oem-plan-delete'),
                      ('plan','oem-plan-index'),
                      ('plan','oem-plan-update'),
                      ('plan','oem-plan-upload'),
                      ('plan','oem-plan-xls');")->execute();
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable('{{%oem_plan}}');
	}
}
