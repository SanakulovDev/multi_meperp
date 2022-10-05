<?php
use yii\db\Migration;

/**
 * Class m210312_104107_add_authitems_for_part_production_monitor
 */
class m210312_104107_add_authitems_for_part_production_monitor extends Migration {

  /**
   * {@inheritdoc}
   */
  public function safeUp() {
    $authManager = Yii::$app->getAuthManager();
    Yii::$app->db->createCommand(
      "INSERT IGNORE `auth_item`(`name`, `type`) VALUES
				('part-production-monitor-index',2),
				('part-production-monitor-edit',2),
				('part-production-monitor-confirm',2), 
				('part-production-monitor-unconfirm',2), 
				('part-production-monitor-complete',2),
				('part-production-monitor-uncomplete',2),
				('part-production-monitor-xls',2)
			"
    )->execute();
    Yii::$app->db->createCommand(
      "INSERT IGNORE `auth_item_child`(`parent`, `child`) 
				VALUES				
				('admin', 'part-production-monitor-index'),
				('admin', 'part-production-monitor-edit'),
				('admin', 'part-production-monitor-confirm'),
				('admin', 'part-production-monitor-unconfirm'),
				('admin', 'part-production-monitor-complete'),
				('admin', 'part-production-monitor-uncomplete'),
				('admin', 'part-production-monitor-xls'),				
				('superadmin', 'part-production-monitor-index'),
				('superadmin', 'part-production-monitor-edit'),
				('superadmin', 'part-production-monitor-confirm'),
				('superadmin', 'part-production-monitor-unconfirm'),
				('superadmin', 'part-production-monitor-complete'),
				('superadmin', 'part-production-monitor-uncomplete'),
				('superadmin', 'part-production-monitor-xls')
			"
    )->execute();
    $authManager->invalidateCache();
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown() {
    $authManager = Yii::$app->getAuthManager();
    Yii::$app->db->createCommand("DELETE FROM `auth_item` WHERE `name` IN (
                                         'part-production-monitor-index', 
                                         'part-production-monitor-edit', 
                                         'part-production-monitor-confirm', 
                                         'part-production-monitor-unconfirm', 
                                         'part-production-monitor-complete', 
                                         'part-production-monitor-uncomplete', 
                                         'part-production-monitor-xls'
				)"
    )->execute();
    $authManager->invalidateCache();
  }

}
