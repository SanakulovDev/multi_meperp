<?php

use yii\db\Migration;

/**
 * Class m200603_071645_manage_ps_permissions
 */
class m200622_145400_manage_document_permissions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      Yii::$app->db->createCommand(
        "INSERT IGNORE `auth_item`(`name`, `type`, `description`, `created_at`, `updated_at`) 
              VALUES ('document-receipt-local-kd', 2, 'document-receipt-local-kd', 1580562767, 1580562767),
                    ('document-receipt-local-con', 2, 'document-receipt-local-con', 1580562767, 1580562767);
             INSERT IGNORE `auth_item_child`(`parent`, `child`) VALUES 
                ('superadmin','document-receipt-local-kd'),
                ('admin','document-receipt-local-kd'),
                ('mrp','document-receipt-local-kd'),
                ('mrpc','document-receipt-local-kd'),
                ('superadmin','document-receipt-local-con'),
                ('admin','document-receipt-local-con'),
                ('mrp','document-receipt-local-con'),
                ('mrpc','document-receipt-local-con')
                ;"
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
