<?php
use yii\db\Migration;

/**
 * Class m210323_103554_add_management_role
 */
class m210323_103554_add_management_role extends Migration {

  /**
   * {@inheritdoc}
   */
  public function safeUp() {
    $authManager = Yii::$app->getAuthManager();
    Yii::$app->db->createCommand(
      "INSERT IGNORE `auth_item`(`name`, `type`) 
				VALUES('management',1)				
				"
    )->execute();
    Yii::$app->db->createCommand(
      "INSERT IGNORE `auth_item_child`(`parent`, `child`) 
              VALUES	
              ('management', 'conveyor-type-create'),
              ('management', 'conveyor-type-delete'),
              ('management', 'conveyor-type-index'),
              ('management', 'conveyor-type-update'),
              ('management', 'conveyor-type-view'),
              ('management', 'document-index'),
              ('management', 'document-pending-alert'),
              ('management', 'document-view'),
              ('management', 'report-index'),
              ('management', 'stock-index')
            "
    )->execute();
    $authManager->invalidateCache();
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown() {
    $authManager = Yii::$app->getAuthManager();
    Yii::$app->db->createCommand("DELETE FROM auth_item WHERE name='management'")->execute();
    $authManager->invalidateCache();
  }

}
