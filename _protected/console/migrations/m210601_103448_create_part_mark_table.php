<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%part_mark}}`.
 */
class m210601_103448_create_part_mark_table extends Migration
{
  /**
   * {@inheritdoc}
   */
  public function safeUp()
  {
    $tableOptions = 'COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
    $this->createTable(
      '{{%part_mark}}',
      [
        'id' => $this->primaryKey(),
        'name' => $this->string()->notNull(),
      ],
      $tableOptions
    );

    $this->createIndex('name', '{{%part_mark}}', ['name'], true);

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

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
    $this->dropIndex('name', '{{%part_mark}}');
    $this->dropTable('{{%part_mark}}');

    $authManager = Yii::$app->getAuthManager();
    Yii::$app->db->createCommand(
      "DELETE FROM `auth_item` WHERE `name` IN ('part-mark-index','part-mark-create','part-mark-update','part-mark-delete','part-mark-xls')"
    )->execute();
    $authManager->invalidateCache();
  }
}
