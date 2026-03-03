<?php

use yii\db\Migration;

/**
 * Class m200603_071645_manage_ps_permissions
 */
class m200603_071645_manage_ps_permissions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      Yii::$app->db->createCommand(
        "INSERT IGNORE `auth_item`(`name`, `type`, `description`, `created_at`, `updated_at`) 
              VALUES ('product-specification-view', 2, 'View product specification', 1580562767, 1580562767),
                    ('product-specification-activate', 2, 'Activate product specification', 1580562767, 1580562767);
             INSERT IGNORE `auth_item_child`(`parent`, `child`) 
                VALUES ('superadmin','product-specification-view'),
                ('admin','product-specification-view'),
                ('buyer','product-specification-view'),
                ('counter','product-specification-view'),
                ('mfu','product-specification-view'),
                ('mrp','product-specification-view'),
                ('mrp-logx','product-specification-view'),
                ('observer','product-specification-view'),
                ('pe','product-specification-view'),
                ('plan','product-specification-view'),
                ('sales','product-specification-view'),
                ('shipper','product-specification-view'),
                ('superadmin','product-specification-activate'),
                ('admin','product-specification-activate'),
                ('admin','product-specification-activate');"
      )->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200603_071645_manage_ps_permissions cannot be reverted.\n";

        return false;
    }

}
