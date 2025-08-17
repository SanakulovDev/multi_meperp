<?php

use yii\db\Migration;

/**
 * Class m200430_043146_add_document_upload_permission
 */
class m200430_043146_add_document_upload_permission extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand(
            "INSERT IGNORE `auth_item`(`name`, `type`) VALUES ('document-upload',2);
             INSERT IGNORE `auth_item_child`(`parent`, `child`) 
                      VALUES 
                      ('mrp','document-upload'),
                      ('mrpc','document-upload'),
                      ('admin','document-upload'),
                      ('superadmin','document-upload');
             UPDATE `document_type` SET `code`='FG' WHERE `code`='SF1';         
             INSERT IGNORE `document_type`(`code`, `name`, `description`, `created_by`, `created_at`, `updated_by`, `updated_at`) 
                    VALUES ('FG', 'Счет-фактура', 'Счет-фактура', 1, 1587959961, 1, 1587959961);
            "
          )->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return true;
    }

   
}
