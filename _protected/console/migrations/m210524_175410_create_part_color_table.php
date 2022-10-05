<?php
use yii\db\Migration;

/**
 * Handles the creation of table `{{%part_color}}`.
 */
class m210524_175410_create_part_color_table extends Migration {

  /**
   * {@inheritdoc}
   */
  public function safeUp() {
    $tableOptions = 'ENGINE=InnoDB';
    $this->createTable('{{%part_color}}', [
      'id' => $this->primaryKey(),
      'name' => $this->string()->notNull(),
    ], $tableOptions);
    $authManager = Yii::$app->getAuthManager();
    Yii::$app->db->createCommand(
      "INSERT IGNORE `auth_item`(`name`, `type`) 
				VALUES('part-color-index',2),
				('part-color-create',2),
				('part-color-update',2),
				('part-color-delete',2),
				('part-color-xls',2)"
    )->execute();
    Yii::$app->db->createCommand(
      "INSERT IGNORE `auth_item_child`(`parent`, `child`) 
				VALUES	
				('admin', 'part-color-index'),
				('admin', 'part-color-create'),
				('admin', 'part-color-update'),
				('admin', 'part-color-delete'),
				('admin', 'part-color-xls'),
				
				('superadmin', 'part-color-index'),
				('superadmin', 'part-color-create'),
				('superadmin', 'part-color-update'),
				('superadmin', 'part-color-delete'),
				('superadmin', 'part-color-xls')"
    )->execute();
    $authManager->invalidateCache();
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown() {
    Yii::$app->db->createCommand(
      "DELETE FROM `auth_item` WHERE `name` IN ('part-color-index','part-color-create','part-color-update','part-color-delete','part-color-xls')"
    )->execute();

    $this->dropTable('{{%part_color}}');
  }

}
