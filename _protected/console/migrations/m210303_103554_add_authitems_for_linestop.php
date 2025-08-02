<?php

use yii\db\Migration;

/**
 * Class m210303_103554_add_authitems_for_linestop
 */
class m210303_103554_add_authitems_for_linestop extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $authManager = Yii::$app->getAuthManager();

      Yii::$app->db->createCommand(
        "INSERT IGNORE `auth_item`(`name`, `type`) 
				VALUES('maintenance',1),
	
				('line-stop-reason-index',2),
				('line-stop-reason-create',2),
				('line-stop-reason-update',2),
				('line-stop-reason-delete',2),
				('line-stop-reason-xls',2),
				
				('line-stop-index',2),
				('line-stop-create',2),
				('line-stop-update',2),
				('line-stop-delete',2),
				('line-stop-accept',2),
				('line-stop-reject',2),
				('line-stop-xls',2),
				
				('production-monitor-index',2),
				('production-monitor-create',2),
				('production-monitor-update',2),
				('production-monitor-delete',2),
				('production-monitor-xls',2),

				('part-production-monitor-index',2),
				('part-production-monitor-create',2),
				('part-production-monitor-update',2),
				('part-production-monitor-delete',2),
				('part-production-monitor-xls',2)
				"
      )->execute();

      Yii::$app->db->createCommand(
        "INSERT IGNORE `auth_item_child`(`parent`, `child`) 
				VALUES	
				('admin', 'line-stop-reason-index'),
				('admin', 'line-stop-reason-create'),
				('admin', 'line-stop-reason-update'),
				('admin', 'line-stop-reason-delete'),
				('admin', 'line-stop-reason-xls'),
				
				('admin', 'line-stop-index'),
				('admin', 'line-stop-create'),
				('admin', 'line-stop-update'),
				('admin', 'line-stop-delete'),
				('admin', 'line-stop-accept'),
				('admin', 'line-stop-reject'),
				('admin', 'line-stop-xls'),
				
				('admin', 'production-monitor-index'),
				('admin', 'production-monitor-create'),
				('admin', 'production-monitor-update'),
				('admin', 'production-monitor-delete'),
				('admin', 'production-monitor-xls'),

				('admin', 'part-production-monitor-index'),
				('admin', 'part-production-monitor-create'),
				('admin', 'part-production-monitor-update'),
				('admin', 'part-production-monitor-delete'),
				('admin', 'part-production-monitor-xls'),
	
				('superadmin', 'line-stop-reason-index'),
				('superadmin', 'line-stop-reason-create'),
				('superadmin', 'line-stop-reason-update'),
				('superadmin', 'line-stop-reason-delete'),
				('superadmin', 'line-stop-reason-xls'),
				
				('superadmin', 'line-stop-index'),
				('superadmin', 'line-stop-create'),
				('superadmin', 'line-stop-update'),
				('superadmin', 'line-stop-delete'),
				('superadmin', 'line-stop-accept'),
				('superadmin', 'line-stop-reject'),
				('superadmin', 'line-stop-xls'),
				
				('superadmin', 'production-monitor-index'),
				('superadmin', 'production-monitor-create'),
				('superadmin', 'production-monitor-update'),
				('superadmin', 'production-monitor-delete'),
				('superadmin', 'production-monitor-xls'),
				('superadmin', 'production-monitor-quality-confirm'),
				('superadmin', 'production-monitor-shift-complete'),

				('superadmin', 'part-production-monitor-index'),
				('superadmin', 'part-production-monitor-create'),
				('superadmin', 'part-production-monitor-update'),
				('superadmin', 'part-production-monitor-delete'),
				('superadmin', 'part-production-monitor-xls')
			"
      )->execute();

      $authManager->invalidateCache();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $authManager = Yii::$app->getAuthManager();

      Yii::$app->db->createCommand("DELETE FROM `auth_item` WHERE `name` IN (
				'line-stop-reason-index', 'line-stop-reason-create',	'line-stop-reason-update',	'line-stop-reason-delete',	'line-stop-reason-xls',	'line-stop-index',
				'line-stop-create',	'line-stop-update',	'line-stop-delete',	'line-stop-accept',	'line-stop-reject',	'line-stop-xls',	'production-monitor-index',
				'production-monitor-create',	'production-monitor-update',	'production-monitor-delete',	'production-monitor-xls',	'production-monitor-quality-confirm',	'production-monitor-shift-complete',
				'part-production-monitor-index',	'part-production-monitor-create',	'part-production-monitor-update',	'part-production-monitor-delete',	'part-production-monitor-xls')"
      )->execute();
      $authManager->invalidateCache();
    }

}
